<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;

class AdminOrderController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewMonitoring', Order::class);

        $orders = Order::with(['user', 'items.product.user'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.orders', compact('orders'));
    }
}
