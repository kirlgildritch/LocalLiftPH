<?php

namespace App\Http\Controllers;

use App\Notifications\SellerNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class SellerNotificationController extends Controller
{
    public function index(Request $request, SellerNotificationService $sellerNotifications): View
    {
        $seller = auth('seller')->user();
        $sellerNotifications->syncPendingOrdersNotShipped($seller);

        $filter = $this->normalizeFilter((string) $request->query('filter', 'all'));

        $notifications = $this->filteredNotifications($request, $seller->notifications(), $filter)
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('seller.notifications', [
            'notifications' => $notifications,
            'filter' => $filter,
            'unreadCount' => $seller->unreadNotifications()->count(),
            'readCount' => $seller->notifications()->whereNotNull('read_at')->count(),
        ]);
    }

    public function feed(SellerNotificationService $sellerNotifications): JsonResponse
    {
        $seller = auth('seller')->user();
        $sellerNotifications->syncPendingOrdersNotShipped($seller);

        $notifications = $seller->notifications()->latest()->limit(5)->get();

        return response()->json([
            'unreadCount' => $seller->unreadNotifications()->count(),
            'readCount' => $seller->notifications()->whereNotNull('read_at')->count(),
            'notifications' => $notifications->map(fn (DatabaseNotification $notification) => $this->formatNotification($notification))->values(),
        ]);
    }

    public function markAsRead(Request $request, DatabaseNotification $notification): RedirectResponse|JsonResponse
    {
        $this->authorizeSellerNotification($notification);

        $notification->markAsRead();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Notification marked as read.',
                'notification' => $this->formatNotification($notification->fresh()),
                'unreadCount' => auth('seller')->user()->unreadNotifications()->count(),
                'readCount' => auth('seller')->user()->notifications()->whereNotNull('read_at')->count(),
            ]);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead(Request $request): RedirectResponse|JsonResponse
    {
        $seller = auth('seller')->user();
        $markedCount = $seller->unreadNotifications()->count();

        $seller->unreadNotifications()->update(['read_at' => now()]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'All notifications marked as read.',
                'markedCount' => $markedCount,
                'unreadCount' => $seller->unreadNotifications()->count(),
                'readCount' => $seller->notifications()->whereNotNull('read_at')->count(),
            ]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(Request $request, DatabaseNotification $notification): RedirectResponse|JsonResponse
    {
        $this->authorizeSellerNotification($notification);

        $wasUnread = $notification->read_at === null;
        $notification->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Notification deleted.',
                'deletedId' => $notification->id,
                'wasUnread' => $wasUnread,
                'unreadCount' => auth('seller')->user()->unreadNotifications()->count(),
                'readCount' => auth('seller')->user()->notifications()->whereNotNull('read_at')->count(),
            ]);
        }

        return back()->with('success', 'Notification deleted.');
    }

    public function clearRead(Request $request): RedirectResponse|JsonResponse
    {
        $seller = auth('seller')->user();
        $deletedCount = $seller->notifications()->whereNotNull('read_at')->count();

        $seller->notifications()->whereNotNull('read_at')->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Read notifications cleared.',
                'deletedCount' => $deletedCount,
                'unreadCount' => $seller->unreadNotifications()->count(),
                'readCount' => $seller->notifications()->whereNotNull('read_at')->count(),
            ]);
        }

        return back()->with('success', 'Read notifications cleared.');
    }

    public function open(DatabaseNotification $notification): RedirectResponse
    {
        $this->authorizeSellerNotification($notification);

        $notification->markAsRead();

        $data = $notification->data ?? [];

        if (! empty($data['url'])) {
            return redirect($data['url']);
        }

        if (! empty($data['route']) && Route::has($data['route'])) {
            return redirect()->route($data['route'], $data['route_params'] ?? []);
        }

        return redirect()->route('seller.notifications.index');
    }

    private function filteredNotifications(Request $request, $query, string $filter)
    {
        return $query
            ->when($filter === 'unread', fn ($query) => $query->whereNull('read_at'))
            ->when(in_array($filter, ['orders', 'messages', 'reviews', 'admin'], true), fn ($query) => $query->where('data->type', $filter))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%' . $request->search . '%';

                $query->where(function ($query) use ($search) {
                    $query->where('data->title', 'like', $search)
                        ->orWhere('data->message', 'like', $search);
                });
            });
    }

    private function normalizeFilter(string $filter): string
    {
        return in_array($filter, ['all', 'unread', 'orders', 'messages', 'reviews', 'admin'], true)
            ? $filter
            : 'all';
    }

    private function formatNotification(?DatabaseNotification $notification): array
    {
        if (! $notification) {
            return [];
        }

        $data = $notification->data ?? [];
        $type = $data['type'] ?? $data['category'] ?? 'admin';

        return [
            'id' => $notification->id,
            'type' => $type,
            'category' => $type,
            'action' => $data['action'] ?? 'notification',
            'title' => $data['title'] ?? 'Notification',
            'message' => $data['message'] ?? 'You have a new notification.',
            'open_url' => route('seller.notifications.open', $notification),
            'read_url' => route('seller.notifications.read', $notification),
            'delete_url' => route('seller.notifications.destroy', $notification),
            'read_at' => $notification->read_at?->toIso8601String(),
            'created_at_human' => $notification->created_at?->diffForHumans() ?? 'Just now',
            'created_at_formatted' => $notification->created_at?->format('M d, Y h:i A') ?? '',
        ];
    }

    private function authorizeSellerNotification(DatabaseNotification $notification): void
    {
        abort_unless(
            $notification->notifiable_type === get_class(auth('seller')->user())
            && (string) $notification->notifiable_id === (string) auth('seller')->id(),
            403
        );
    }
}
