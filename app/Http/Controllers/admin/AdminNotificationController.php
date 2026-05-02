<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class AdminNotificationController extends Controller
{
    public function index(Request $request): View
    {
        $admin = auth('admin')->user();

        $notifications = $admin->notifications()
            ->when($request->filled('status'), function ($query) use ($request) {
                if ($request->status === 'unread') {
                    $query->whereNull('read_at');
                }

                if ($request->status === 'read') {
                    $query->whereNotNull('read_at');
                }
            })
            ->when($request->filled('type') && $request->type !== 'all', function ($query) use ($request) {
                $query->where('data->type', $request->type);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%' . $request->search . '%';

                $query->where(function ($query) use ($search) {
                    $query->where('data->title', 'like', $search)
                        ->orWhere('data->message', 'like', $search);
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $unreadCount = $admin->unreadNotifications()->count();
        $readCount = $admin->notifications()->whereNotNull('read_at')->count();

        return view('admin.notifications.index', compact('notifications', 'unreadCount', 'readCount'));
    }

    public function feed(): JsonResponse
    {
        $admin = auth('admin')->user();
        $notifications = $admin->notifications()->latest()->limit(5)->get();

        return response()->json([
            'unreadCount' => $admin->unreadNotifications()->count(),
            'readCount' => $admin->notifications()->whereNotNull('read_at')->count(),
            'notifications' => $notifications->map(function (DatabaseNotification $notification) {
                $data = $notification->data ?? [];

                return [
                    'id' => $notification->id,
                    'type' => $data['type'] ?? 'info',
                    'title' => $data['title'] ?? 'Notification',
                    'message' => $data['message'] ?? 'You have a new notification.',
                    'url' => $data['url'] ?? null,
                    'route' => $data['route'] ?? null,
                    'route_params' => $data['route_params'] ?? [],
                    'read_at' => $notification->read_at?->toIso8601String(),
                    'created_at_human' => $notification->created_at?->diffForHumans(),
                ];
            })->values(),
        ]);
    }

    public function markAsRead(Request $request, DatabaseNotification $notification): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $this->authorizeAdminNotification($notification);

        $notification->markAsRead();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Notification marked as read.',
                'unreadCount' => auth('admin')->user()->unreadNotifications()->count(),
                'readCount' => auth('admin')->user()->notifications()->whereNotNull('read_at')->count(),
            ]);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $admin = auth('admin')->user();
        $markedCount = $admin->unreadNotifications()->count();

        $admin->unreadNotifications->markAsRead();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'All notifications marked as read.',
                'markedCount' => $markedCount,
                'unreadCount' => $admin->unreadNotifications()->count(),
                'readCount' => $admin->notifications()->whereNotNull('read_at')->count(),
            ]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(Request $request, DatabaseNotification $notification): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $this->authorizeAdminNotification($notification);

        $wasUnread = $notification->read_at === null;
        $notification->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Notification deleted.',
                'deletedId' => $notification->id,
                'wasUnread' => $wasUnread,
                'unreadCount' => auth('admin')->user()->unreadNotifications()->count(),
                'readCount' => auth('admin')->user()->notifications()->whereNotNull('read_at')->count(),
            ]);
        }

        return back()->with('success', 'Notification deleted.');
    }

    public function clearRead(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $admin = auth('admin')->user();

        $deletedCount = $admin
            ->notifications()
            ->whereNotNull('read_at')
            ->count();

        $admin
            ->notifications()
            ->whereNotNull('read_at')
            ->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Read notifications cleared.',
                'deletedCount' => $deletedCount,
                'unreadCount' => $admin->unreadNotifications()->count(),
                'readCount' => $admin->notifications()->whereNotNull('read_at')->count(),
            ]);
        }

        return back()->with('success', 'Read notifications cleared.');
    }

    public function open(DatabaseNotification $notification): RedirectResponse
    {
        $this->authorizeAdminNotification($notification);

        $notification->markAsRead();

        $data = $notification->data ?? [];

        if (!empty($data['url'])) {
            return redirect($data['url']);
        }

        if (!empty($data['route']) && \Route::has($data['route'])) {
            return redirect()->route($data['route'], $data['route_params'] ?? []);
        }

        return redirect()->route('admin.notifications.index');
    }

    private function authorizeAdminNotification(DatabaseNotification $notification): void
    {
        abort_unless(
            $notification->notifiable_type === get_class(auth('admin')->user())
            && (string) $notification->notifiable_id === (string) auth('admin')->id(),
            403
        );
    }
}
