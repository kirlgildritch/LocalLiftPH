<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MessageController extends Controller
{
    protected int $typingTtlSeconds = 5;

    protected function currentUser()
    {
        return Auth::guard('seller')->user() ?? Auth::guard('web')->user();
    }

    protected function currentUserId(): ?int
    {
        return $this->currentUser()?->id;
    }

    protected function isSellerContext(): bool
    {
        return Auth::guard('seller')->check();
    }

    protected function messageShowRouteName(): string
    {
        return $this->isSellerContext() ? 'seller.messages.show' : 'messages.show';
    }

    protected function widgetRouteName(): string
    {
        return $this->isSellerContext() ? 'seller.chat.widget' : 'chat.widget';
    }

    protected function typingRouteName(): string
    {
        return $this->isSellerContext() ? 'seller.messages.typing' : 'messages.typing';
    }

    protected function conversationTypingCacheKey(Conversation $conversation, int $userId): string
    {
        return "conversation:{$conversation->id}:typing:{$userId}";
    }

    protected function imageUploadRules(): array
    {
        return ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'];
    }

    protected function formatMessage(Message $message): array
    {
        $isCurrentUser = (int) $message->sender_id === (int) $this->currentUserId();

        return [
            'id' => $message->id,
            'sender_label' => $isCurrentUser ? 'You' : ($message->sender->name ?? 'User'),
            'message' => (string) ($message->message ?? ''),
            'image_url' => $message->image_url,
            'has_image' => $message->has_image,
            'has_text' => $message->has_text,
            'time' => $message->created_at->format('M d, h:i A'),
            'is_current_user' => $isCurrentUser,
            'is_seen' => $message->is_seen,
            'status_label' => $isCurrentUser ? ($message->is_seen ? 'Seen' : 'Sent') : null,
        ];
    }

    protected function conversationPreview(?Message $latestMessage): string
    {
        if (! $latestMessage) {
            return 'Start chatting from a product or shop page.';
        }

        if ($latestMessage->has_image && ! $latestMessage->has_text) {
            return 'Sent an image';
        }

        if ($latestMessage->has_image && $latestMessage->has_text) {
            return 'Image: ' . Str::limit($latestMessage->message, 40);
        }

        return Str::limit($latestMessage->message ?? 'Start chatting from a product or shop page.', 52);
    }

    protected function activeConversationTypingText(?Conversation $conversation, ?User $currentUser): ?string
    {
        if (! $conversation || ! $currentUser) {
            return null;
        }

        $otherParticipant = $conversation->otherParticipant($currentUser);
        if (! $otherParticipant) {
            return null;
        }

        $isTyping = Cache::get($this->conversationTypingCacheKey($conversation, (int) $otherParticipant->id), false);

        return $isTyping ? ($otherParticipant->name . ' is typing...') : null;
    }

    protected function conversationsQueryForCurrentUser()
    {
        $currentUserId = $this->currentUserId();

        return Conversation::with([
            'buyer',
            'seller',
            'latestMessage.sender',
        ])->withCount([
            'messages as unread_count' => function ($query) use ($currentUserId) {
                $query->whereNull('read_at')
                    ->where('sender_id', '!=', $currentUserId);
            },
        ])->where(function ($query) {
            $query->where('seller_id', $this->currentUserId())
                ->orWhere('buyer_id', $this->currentUserId());
        })->when(! $currentUserId, function ($query) {
            $query->whereRaw('1 = 0');
        });
    }

    protected function markConversationAsRead(Conversation $conversation): void
    {
        $currentUserId = $this->currentUserId();

        if (! $currentUserId) {
            return;
        }

        $conversation->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $currentUserId)
            ->update(['read_at' => now()]);
    }

    protected function authorizedConversation(Conversation $conversation): void
    {
        $currentUserId = (int) $this->currentUserId();

        abort_unless(
            (int) $conversation->buyer_id === $currentUserId
            || (int) $conversation->seller_id === $currentUserId,
            403
        );
    }

    protected function resolveActiveConversation(Request $request, $conversations, ?Conversation $providedConversation = null): ?Conversation
    {
        if ($providedConversation) {
            return $conversations->firstWhere('id', $providedConversation->id) ?? $providedConversation;
        }

        if ($request->filled('conversation')) {
            return $conversations->firstWhere('id', (int) $request->input('conversation'));
        }

        return $conversations->first();
    }

    protected function widgetPayload(Request $request, ?Conversation $providedConversation = null): array
    {
        $currentUser = $this->currentUser();
        $conversations = $this->conversationsQueryForCurrentUser()
            ->latest('updated_at')
            ->get();

        $activeConversation = $this->resolveActiveConversation($request, $conversations, $providedConversation);

        if ($activeConversation) {
            $this->markConversationAsRead($activeConversation);
            $activeConversation->load(['messages.sender', 'buyer', 'seller']);
        }

        $activeOtherParticipant = $activeConversation?->otherParticipant($currentUser);

        return [
            'conversations' => $conversations->map(function ($conversation) use ($currentUser, $activeConversation) {
                $otherParticipant = $conversation->otherParticipant($currentUser);

                return [
                    'id' => $conversation->id,
                    'name' => $otherParticipant->name ?? 'Conversation',
                    'avatar_url' => !empty($otherParticipant?->profile_image)
                        ? asset('storage/' . $otherParticipant->profile_image)
                        : null,
                    'avatar_initials' => strtoupper(substr($otherParticipant->name ?? 'LL', 0, 2)),
                    'preview' => $this->conversationPreview($conversation->latestMessage),
                    'updated_at' => optional($conversation->latestMessage?->created_at)->diffForHumans() ?? 'No messages yet',
                    'show_url' => route($this->messageShowRouteName(), $conversation),
                    'active' => optional($activeConversation)->id === $conversation->id,
                    'unread_count' => optional($activeConversation)->id === $conversation->id
                        ? 0
                        : (int) ($conversation->unread_count ?? 0),
                ];
            })->values(),
            'active_conversation' => $activeConversation ? [
                'id' => $activeConversation->id,
                'name' => $activeOtherParticipant->name ?? 'Conversation',
                'avatar_url' => ! empty($activeOtherParticipant?->profile_image)
                    ? asset('storage/' . $activeOtherParticipant->profile_image)
                    : null,
                'avatar_initials' => strtoupper(substr($activeOtherParticipant->name ?? 'LL', 0, 2)),
                'role_label' => $this->isSellerContext() ? 'Buyer conversation' : 'Seller conversation',
                'shop_url' => ! $this->isSellerContext() && $activeConversation->seller
                    ? route('shops.show', $activeConversation->seller)
                    : null,
                'send_url' => $this->isSellerContext()
                    ? route('seller.messages.store', $activeConversation)
                    : route('messages.store', $activeConversation),
                'typing_url' => $this->isSellerContext()
                    ? route('seller.messages.typing', $activeConversation)
                    : route('messages.typing', $activeConversation),
                'typing_text' => $this->activeConversationTypingText($activeConversation, $currentUser),
                'messages' => $activeConversation->messages->map(fn ($message) => $this->formatMessage($message))->values(),
            ] : null,
            'meta' => [
                'count' => $conversations->count(),
                'widget_route' => route($this->widgetRouteName()),
            ],
        ];
    }

    public function widget(Request $request): JsonResponse
    {
        return response()->json($this->widgetPayload($request));
    }

    public function index(Request $request): View
    {
        return view('messages.index', [
            'chatWidgetAutoOpen' => true,
            'chatWidgetConversationId' => (int) $request->input('conversation', 0),
        ]);
    }

    public function show(Conversation $conversation): View
    {
        $this->authorizedConversation($conversation);

        return view('messages.index', [
            'chatWidgetAutoOpen' => true,
            'chatWidgetConversationId' => $conversation->id,
        ]);
    }

    public function start(Request $request, User $seller): RedirectResponse|JsonResponse
    {
        abort_if((int) $seller->id === (int) $this->currentUserId(), 403);
        abort_unless($seller->isSeller(), 404);

        $conversation = Conversation::firstOrCreate([
            'buyer_id' => $this->currentUserId(),
            'seller_id' => $seller->id,
        ]);

        if ($request->expectsJson()) {
            $freshConversation = Conversation::with(['messages.sender', 'buyer', 'seller', 'latestMessage.sender'])
                ->findOrFail($conversation->id);

            return response()->json([
                'conversation_id' => $freshConversation->id,
                'redirect_url' => route('messages.show', $freshConversation),
                'widget' => $this->widgetPayload(new Request(['conversation' => $freshConversation->id]), $freshConversation),
            ]);
        }

        return redirect()->route('messages.show', $conversation);
    }

    public function store(Request $request, Conversation $conversation): RedirectResponse|JsonResponse
    {
        $this->authorizedConversation($conversation);

        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:2000'],
            'image' => $this->imageUploadRules(),
        ]);

        if (! filled($validated['message'] ?? null) && ! $request->hasFile('image')) {
            throw ValidationException::withMessages([
                'message' => 'Enter a message or upload an image.',
            ]);
        }

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('chat_images', 'public')
            : null;

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->currentUserId(),
            'message' => trim((string) ($validated['message'] ?? '')) ?: null,
            'image_path' => $imagePath,
        ]);

        $message->load('sender');
        $conversation->touch();
        Cache::forget($this->conversationTypingCacheKey($conversation, (int) $this->currentUserId()));

        $freshConversation = Conversation::with(['messages.sender', 'buyer', 'seller', 'latestMessage.sender'])
            ->findOrFail($conversation->id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id,
                'message' => $this->formatMessage($message),
                'widget' => $this->widgetPayload(new Request(['conversation' => $conversation->id]), $freshConversation),
            ]);
        }

        return redirect()->route($this->messageShowRouteName(), $conversation);
    }

    public function typing(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizedConversation($conversation);

        $validated = $request->validate([
            'typing' => ['required', 'boolean'],
        ]);

        $cacheKey = $this->conversationTypingCacheKey($conversation, (int) $this->currentUserId());

        if ($validated['typing']) {
            Cache::put($cacheKey, true, now()->addSeconds($this->typingTtlSeconds));
        } else {
            Cache::forget($cacheKey);
        }

        return response()->json(['success' => true]);
    }
}
