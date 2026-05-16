<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\UpdateOrderShippingStatusRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Notifications\SellerNotificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SellerOrderController extends Controller
{
    public function index(): View
    {
        $seller = Auth::guard('seller')->user();

        $orders = Order::with(['user', 'seller.sellerProfile', 'items.product', 'items.variant', 'returnRequest.media'])
            ->where('seller_id', $seller->id)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('seller.orders', compact('orders'));
    }

    public function updateShippingStatus(UpdateOrderShippingStatusRequest $request, Order $order, SellerNotificationService $sellerNotifications): RedirectResponse
    {
        $order->loadMissing(['items.product', 'items.variant', 'user']);

        $this->authorize('updateShippingStatus', $order);

        $allowedStatuses = $order->nextShippingStatuses();

        if ($allowedStatuses === []) {
            return redirect()
                ->route('seller.orders')
                ->with('error', 'This order can no longer be updated.');
        }

        $shippingStatus = (string) $request->validated('shipping_status');

        $updates = [
            'shipping_status' => $shippingStatus,
            'status' => Order::legacyStatusForShipping($shippingStatus),
        ];

        if ($shippingStatus === Order::SHIPPING_SHIPPED) {
            $updates['seller_earning_status'] = Order::EARNING_ON_HOLD;
        }

        if ($shippingStatus === Order::SHIPPING_CANCELLED) {
            $updates['payment_status'] = Order::PAYMENT_CANCELLED;
            $updates['seller_earning_status'] = Order::EARNING_REVERSED;
        }

        if ($shippingStatus === Order::SHIPPING_CANCELLED) {
            DB::transaction(function () use ($order, $updates): void {
                $restockByVariant = $order->items
                    ->filter(fn ($item) => filled($item->product_variant_id))
                    ->groupBy('product_variant_id')
                    ->map(fn ($items) => (int) $items->sum('quantity'));

                if ($restockByVariant->isNotEmpty()) {
                    ProductVariant::query()
                        ->whereIn('id', $restockByVariant->keys())
                        ->lockForUpdate()
                        ->get()
                        ->each(function (ProductVariant $variant) use ($restockByVariant): void {
                            $variant->increment('stock', (int) ($restockByVariant[$variant->id] ?? 0));
                        });
                }

                $restockByProduct = $order->items
                    ->filter(fn ($item) => filled($item->product_id))
                    ->groupBy('product_id')
                    ->map(fn ($items) => (int) $items->sum('quantity'));

                if ($restockByProduct->isNotEmpty()) {
                    Product::query()
                        ->whereIn('id', $restockByProduct->keys())
                        ->lockForUpdate()
                        ->get()
                        ->each(function (Product $product) use ($restockByProduct): void {
                            $product->increment('stock', (int) ($restockByProduct[$product->id] ?? 0));
                        });
                }

                $order->update($updates);
            });
        } else {
            $order->update($updates);
        }

        if ($shippingStatus === Order::SHIPPING_COMPLETED) {
            $sellerNotifications->orderCompleted($order->fresh(['seller.sellerProfile', 'user']));
        }

        return redirect()
            ->route('seller.orders')
            ->with('success', 'Shipping status updated successfully.');
    }
}
