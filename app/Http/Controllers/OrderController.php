<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $currentStatus = trim((string) $request->get('status', 'all'));
        $allowedStatuses = ['all', 'pending', 'to ship', 'to receive', 'delivered', 'cancelled'];

        if (!in_array($currentStatus, $allowedStatuses, true)) {
            $currentStatus = 'all';
        }

        $orders = Order::with(['items.product'])
            ->where('user_id', Auth::id())
            ->when($currentStatus !== 'all', function ($query) use ($currentStatus) {
                $query->where('status', $currentStatus);
            })
            ->latest()
            ->get();

        $statusCounts = Order::query()
            ->selectRaw('status, COUNT(*) as count')
            ->where('user_id', Auth::id())
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('buyer.orders', compact('orders', 'currentStatus', 'statusCounts'));
    }
}
