<?php

namespace App\Http\Controllers;

use App\Events\ConversationMessageSent;
use App\Events\ConversationRead;
use App\Events\ConversationTypingUpdated;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MessageController extends Controller
{
    protected int $typingTtlSeconds = 5;
    protected int $lastSeenTtlSeconds = 604800;

    protected function currentSocketId(Request $request): ?string
    {
        return $request->header('X-Socket-ID');
    }

    protected function conversationDisplayName(?User $participant): string
    {
        if (! $participant) {
            return 'Conversation';
        }

        if (! $this->isSellerContext() && $participant->isSeller()) {
            return $participant->sellerProfile?->store_name ?: $participant->name;
        }

        return $participant->name;
    }

    protected function userLastSeenCacheKey(int $userId): string
    {
        return "chat:user:{$userId}:last_seen";
    }

    protected function touchCurrentUserPresence(): void
    {
        if (! $this->currentUserId()) {
            return;
        }

        Cache::put(
            $this->userLastSeenCacheKey((int) $this->currentUserId()),
            now()->toIso8601String(),
            now()->addSeconds($this->lastSeenTtlSeconds)
        );
    }

    protected function userLastSeenIso(?User $user): ?string
    {
        if (! $user) {
            return null;
        }

        return Cache::get($this->userLastSeenCacheKey((int) $user->id));
    }

    protected function userLastSeenLabel(?User $user): ?string
    {
        $lastSeenIso = $this->userLastSeenIso($user);

        if (! $lastSeenIso) {
            return null;
        }

        try {
            return \Illuminate\Support\Carbon::parse($lastSeenIso)->diffForHumans();
        } catch (\Throwable) {
            return null;
        }
    }

    protected function safeBroadcast(object $event, ?string $socketId = null): void
    {
        try {
            if ($socketId) {
                broadcast($event)->toOthers();
                return;
            }

            event($event);
        } catch (\Throwable $exception) {
            Log::warning('Chat broadcast failed.', [
                'event' => $event::class,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    protected function conversationAvatarUrl(?User $participant): ?string
    {
        if (! $participant) {
            return null;
        }

        if (! $this->isSellerContext() && $participant->isSeller() && ! empty($participant->sellerProfile?->shop_logo)) {
            return asset('storage/' . $participant->sellerProfile->shop_logo);
        }

        if (! empty($participant->profile_image)) {
            return asset('storage/' . $participant->profile_image);
        }

        return null;
    }

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
        $productCard = $this->messageProductCard($message->product);

        return [
            'id' => $message->id,
            'sender_label' => $isCurrentUser ? 'You' : ($message->sender->name ?? 'User'),
            'message' => (string) ($message->message ?? ''),
            'image_url' => $message->image_url,
            'has_image' => $message->has_image,
            'has_text' => $message->has_text,
            'has_product' => ! is_null($productCard),
            'product' => $productCard,
            'time' => $message->created_at->format('M d, h:i A'),
            'is_current_user' => $isCurrentUser,
            'is_seen' => $message->is_seen,
            'status_label' => $isCurrentUser ? ($message->is_seen ? 'Seen' : 'Sent') : null,
        ];
    }

    protected function messageProductCard(?Product $product): ?array
    {
        if (! $product) {
            return null;
        }

        $product->loadMissing('user.sellerProfile');

        $shopName = $product->user?->sellerProfile?->store_name ?: $product->user?->name ?: 'LocalLift Seller';

        return [
            'id' => $product->id,
            'name' => $product->name,
            'url' => route('products.show', $product),
            'image_url' => $product->image
                ? asset('storage/' . $product->image)
                : asset('assets/images/default-product.png'),
            'price' => (float) $product->price,
            'price_label' => 'PHP ' . number_format((float) $product->price, 2),
            'shop_name' => $shopName,
            'seller_name' => $product->user?->name ?: $shopName,
        ];
    }

    protected function conversationPreview(?Message $latestMessage): string
    {
        if (! $latestMessage) {
            return 'Start chatting from a product or shop page.';
        }

        if ($latestMessage->product) {
            return 'Product: ' . Str::limit($latestMessage->product->name, 40);
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

        return $isTyping ? ($this->conversationDisplayName($otherParticipant) . ' is typing...') : null;
    }

    protected function conversationsQueryForCurrentUser()
    {
        $currentUserId = $this->currentUserId();

        return Conversation::with([
            'buyer.sellerProfile',
            'seller.sellerProfile',
            'latestMessage.sender',
            'latestMessage.product.user.sellerProfile',
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

    protected function markConversationAsRead(Conversation $conversation): int
    {
        $currentUserId = $this->currentUserId();

        if (! $currentUserId) {
            return 0;
        }

        return $conversation->messages()
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
        $this->touchCurrentUserPresence();

        $currentUser = $this->currentUser();
        $conversations = $this->conversationsQueryForCurrentUser()
            ->latest('updated_at')
            ->get();

        $activeConversation = $this->resolveActiveConversation($request, $conversations, $providedConversation);

        if ($activeConversation) {
            $markedAsRead = $this->markConversationAsRead($activeConversation);
            $activeConversation->load([
                'messages.sender',
                'messages.product.user.sellerProfile',
                'buyer.sellerProfile',
                'seller.sellerProfile',
            ]);

            if ($markedAsRead > 0 && $currentUser) {
                $readEvent = new ConversationRead(
                    $activeConversation->id,
                    (int) $currentUser->id,
                    now()->toIso8601String(),
                );

                $this->safeBroadcast($readEvent, $this->currentSocketId($request));
            }
        }

        $activeOtherParticipant = $activeConversation?->otherParticipant($currentUser);

        return [
            'conversations' => $conversations->map(function ($conversation) use ($currentUser, $activeConversation) {
                $otherParticipant = $conversation->otherParticipant($currentUser);
                $displayName = $this->conversationDisplayName($otherParticipant);

                return [
                    'id' => $conversation->id,
                    'name' => $displayName,
                    'participant_id' => $otherParticipant?->id,
                    'avatar_url' => $this->conversationAvatarUrl($otherParticipant),
                    'avatar_initials' => strtoupper(substr($displayName ?: 'LL', 0, 2)),
                    'preview' => $this->conversationPreview($conversation->latestMessage),
                    'updated_at' => optional($conversation->latestMessage?->created_at)->diffForHumans() ?? 'No messages yet',
                    'updated_at_iso' => optional($conversation->latestMessage?->created_at)?->toIso8601String(),
                    'last_seen_at' => $this->userLastSeenIso($otherParticipant),
                    'last_seen_label' => $this->userLastSeenLabel($otherParticipant),
                    'presence_channel' => "chat.presence.{$conversation->id}",
                    'show_url' => route($this->messageShowRouteName(), $conversation),
                    'active' => optional($activeConversation)->id === $conversation->id,
                    'unread_count' => optional($activeConversation)->id === $conversation->id
                        ? 0
                        : (int) ($conversation->unread_count ?? 0),
                ];
            })->values(),
            'active_conversation' => $activeConversation ? [
                'id' => $activeConversation->id,
                'name' => $this->conversationDisplayName($activeOtherParticipant),
                'participant_id' => $activeOtherParticipant?->id,
                'avatar_url' => $this->conversationAvatarUrl($activeOtherParticipant),
                'avatar_initials' => strtoupper(substr($this->conversationDisplayName($activeOtherParticipant) ?: 'LL', 0, 2)),
                'presence_channel' => "chat.presence.{$activeConversation->id}",
                'last_seen_at' => $this->userLastSeenIso($activeOtherParticipant),
                'last_seen_label' => $this->userLastSeenLabel($activeOtherParticipant),
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
                'current_user_id' => $currentUser?->id,
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
        $chatData = $this->widgetPayload($request);

        return view('messages.index', [
            'chatData' => $chatData,
            'isSellerInbox' => $this->isSellerContext(),
            'disableFloatingChatWidget' => true,
        ]);
    }

    public function show(Conversation $conversation): View
    {
        $this->authorizedConversation($conversation);
        $chatData = $this->widgetPayload(new Request(['conversation' => $conversation->id]), $conversation);

        return view('messages.index', [
            'chatData' => $chatData,
            'isSellerInbox' => $this->isSellerContext(),
            'disableFloatingChatWidget' => true,
        ]);
    }

    public function start(Request $request, User $seller): RedirectResponse|JsonResponse
    {
        $this->touchCurrentUserPresence();

        abort_if((int) $seller->id === (int) $this->currentUserId(), 403);
        abort_unless($seller->isSeller(), 404);

        $validated = $request->validate([
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
        ]);

        $conversation = Conversation::firstOrCreate([
            'buyer_id' => $this->currentUserId(),
            'seller_id' => $seller->id,
        ]);

        if (! empty($validated['product_id'])) {
            $product = Product::with('user.sellerProfile')->findOrFail((int) $validated['product_id']);
            abort_unless((int) $product->user_id === (int) $seller->id, 404);

            $productMessage = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $this->currentUserId(),
                'product_id' => $product->id,
            ]);

            $productMessage->load(['sender', 'product.user.sellerProfile']);
            $conversation->touch();

            $messageSentEvent = new ConversationMessageSent($conversation->id, $this->formatMessage($productMessage));
            $this->safeBroadcast($messageSentEvent, $this->currentSocketId($request));
        }

        if ($request->expectsJson()) {
            $freshConversation = Conversation::with([
                'messages.sender',
                'messages.product.user.sellerProfile',
                'buyer.sellerProfile',
                'seller.sellerProfile',
                'latestMessage.sender',
                'latestMessage.product.user.sellerProfile',
            ])
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
        $this->touchCurrentUserPresence();

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

        $message->load(['sender', 'product.user.sellerProfile']);
        $conversation->touch();
        Cache::forget($this->conversationTypingCacheKey($conversation, (int) $this->currentUserId()));

        $messageSentEvent = new ConversationMessageSent($conversation->id, $this->formatMessage($message));
        $this->safeBroadcast($messageSentEvent, $this->currentSocketId($request));

        $freshConversation = Conversation::with([
            'messages.sender',
            'messages.product.user.sellerProfile',
            'buyer.sellerProfile',
            'seller.sellerProfile',
            'latestMessage.sender',
            'latestMessage.product.user.sellerProfile',
        ])
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
        $this->touchCurrentUserPresence();

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

        $typingEvent = new ConversationTypingUpdated(
            $conversation->id,
            (int) $this->currentUserId(),
            (bool) $validated['typing'],
        );

        $this->safeBroadcast($typingEvent, $this->currentSocketId($request));

        return response()->json(['success' => true]);
    }
}
