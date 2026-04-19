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
    protected function conversationsQueryForCurrentUser()
    {
        return Conversation::with([
            'buyer',
            'seller',
            'latestMessage.sender',
        ])->where(function ($query) {
            $query->where('seller_id', Auth::id())
                ->orWhere('buyer_id', Auth::id());
        });
    }

    protected function authorizedConversation(Conversation $conversation): void
    {
        abort_unless(
            (int) $conversation->buyer_id === (int) Auth::id()
            || (int) $conversation->seller_id === (int) Auth::id(),
            403
        );
    }

    public function index(Request $request): View
    {
        $conversations = $this->conversationsQueryForCurrentUser()
            ->latest('updated_at')
            ->get();

        $activeConversation = null;

        if ($request->filled('conversation')) {
            $activeConversation = $conversations->firstWhere('id', (int) $request->input('conversation'));
        }

        if (!$activeConversation && $conversations->isNotEmpty()) {
            $activeConversation = $conversations->first();
        }

        if ($activeConversation) {
            $activeConversation->load(['messages.sender', 'buyer', 'seller']);
        }

        return view('messages.index', compact('conversations', 'activeConversation'));
    }

    public function show(Conversation $conversation): View
    {
        $this->authorizedConversation($conversation);

        $conversations = $this->conversationsQueryForCurrentUser()
            ->latest('updated_at')
            ->get();

        $conversation->load(['messages.sender', 'buyer', 'seller']);

        return view('messages.index', [
            'conversations' => $conversations,
            'activeConversation' => $conversation,
        ]);
    }

    public function start(User $seller): RedirectResponse
    {
        abort_if((int) $seller->id === (int) Auth::id(), 403);
        abort_unless($seller->isSeller(), 404);

        $conversation = Conversation::firstOrCreate([
            'buyer_id' => Auth::id(),
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
            'sender_id' => Auth::id(),
            'message' => $validated['message'],
        ]);

        $conversation->touch();

        return redirect()->route('messages.show', $conversation);
    }
}
