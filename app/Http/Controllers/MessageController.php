<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MessageController extends Controller
{
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

    protected function conversationsQueryForCurrentUser()
    {
        $currentUserId = $this->currentUserId();

        return Conversation::with([
            'buyer',
            'seller',
            'latestMessage.sender',
        ])->where(function ($query) {
            $query->where('seller_id', $this->currentUserId())
                ->orWhere('buyer_id', $this->currentUserId());
        })->when(!$currentUserId, function ($query) {
            $query->whereRaw('1 = 0');
        });
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
            return $providedConversation;
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
            $activeConversation->load(['messages.sender', 'buyer', 'seller']);
        }

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
                    'preview' => \Illuminate\Support\Str::limit(optional($conversation->latestMessage)->message ?? 'Start chatting from a product or shop page.', 52),
                    'updated_at' => optional($conversation->latestMessage?->created_at)->diffForHumans() ?? 'No messages yet',
                    'show_url' => route($this->messageShowRouteName(), $conversation),
                    'active' => optional($activeConversation)->id === $conversation->id,
                ];
            })->values(),
            'active_conversation' => $activeConversation ? [
                'id' => $activeConversation->id,
                'name' => optional($activeConversation->otherParticipant($currentUser))->name ?? 'Conversation',
                'role_label' => $this->isSellerContext() ? 'Buyer conversation' : 'Seller conversation',
                'send_url' => $this->isSellerContext()
                    ? route('seller.messages.store', $activeConversation)
                    : route('messages.store', $activeConversation),
                'messages' => $activeConversation->messages->map(function ($message) {
                    $isCurrentUser = (int) $message->sender_id === (int) $this->currentUserId();

                    return [
                        'id' => $message->id,
                        'sender_label' => $isCurrentUser ? 'You' : ($message->sender->name ?? 'User'),
                        'message' => $message->message,
                        'time' => $message->created_at->format('M d, h:i A'),
                        'is_current_user' => $isCurrentUser,
                    ];
                })->values(),
            ] : null,
            'meta' => [
                'count' => $conversations->count(),
                'widget_route' => route($this->widgetRouteName()),
            ],
        ];
    }

    public function widget(Request $request)
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

    public function start(User $seller): RedirectResponse
    {
        abort_if((int) $seller->id === (int) $this->currentUserId(), 403);
        abort_unless($seller->isSeller(), 404);

        $conversation = Conversation::firstOrCreate([
            'buyer_id' => $this->currentUserId(),
            'seller_id' => $seller->id,
        ]);

        return redirect()->route('messages.show', $conversation);
    }

    public function store(Request $request, Conversation $conversation): RedirectResponse
    {
        $this->authorizedConversation($conversation);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->currentUserId(),
            'message' => $validated['message'],
        ]);

        $conversation->touch();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id,
                'widget' => $this->widgetPayload(new Request(['conversation' => $conversation->id]), $conversation),
            ]);
        }

        return redirect()->route($this->messageShowRouteName(), $conversation);
    }
}
