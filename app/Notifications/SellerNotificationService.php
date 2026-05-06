<?php

namespace App\Notifications;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Order;
use App\Models\Product;
use App\Models\Report;
use App\Models\Review;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;

class SellerNotificationService
{
    public function send(
        User $seller,
        string $category,
        string $action,
        string $title,
        string $message,
        ?string $route = null,
        array $routeParams = [],
        ?string $url = null,
        ?string $relatedType = null,
        ?int $relatedId = null,
        ?string $dedupeKey = null,
    ): ?DatabaseNotification {
        if (! $seller->isSeller()) {
            return null;
        }

        if ($dedupeKey && $this->alreadyExists($seller, $dedupeKey)) {
            return null;
        }

        $seller->notify(new SellerNotification(
            $category,
            $action,
            $title,
            $message,
            $route,
            $routeParams,
            $url,
            $relatedType,
            $relatedId,
            $dedupeKey,
        ));

        return $dedupeKey
            ? $seller->notifications()->where('data->dedupe_key', $dedupeKey)->latest()->first()
            : $seller->notifications()->latest()->first();
    }

    public function newOrder(Order $order): ?DatabaseNotification
    {
        $order->loadMissing(['seller.sellerProfile', 'user', 'items']);
        $seller = $this->sellerForOrder($order);

        if (! $seller) {
            return null;
        }

        return $this->send(
            $seller,
            'orders',
            'new_order',
            'New order received',
            'Order #' . $order->id . ' from ' . ($order->user?->name ?? 'a buyer') . ' is waiting for shipment.',
            'seller.orders',
            ['order' => $order->id],
            relatedType: Order::class,
            relatedId: $order->id,
            dedupeKey: 'seller:new-order:' . $order->id,
        );
    }

    public function orderCancelled(Order $order): ?DatabaseNotification
    {
        $order->loadMissing(['seller.sellerProfile', 'user']);
        $seller = $this->sellerForOrder($order);

        if (! $seller) {
            return null;
        }

        return $this->send(
            $seller,
            'orders',
            'order_cancelled',
            'Order cancelled by buyer',
            'Order #' . $order->id . ' was cancelled by ' . ($order->user?->name ?? 'the buyer') . '.',
            'seller.orders',
            ['order' => $order->id],
            relatedType: Order::class,
            relatedId: $order->id,
            dedupeKey: 'seller:order-cancelled:' . $order->id,
        );
    }

    public function buyerConfirmedReceipt(Order $order): ?DatabaseNotification
    {
        $order->loadMissing(['seller.sellerProfile', 'user']);
        $seller = $this->sellerForOrder($order);

        if (! $seller) {
            return null;
        }

        return $this->send(
            $seller,
            'orders',
            'buyer_confirmed_receipt',
            'Buyer confirmed receipt',
            ($order->user?->name ?? 'The buyer') . ' confirmed receipt for order #' . $order->id . '.',
            'seller.orders',
            ['order' => $order->id],
            relatedType: Order::class,
            relatedId: $order->id,
            dedupeKey: 'seller:buyer-confirmed-receipt:' . $order->id,
        );
    }

    public function orderCompleted(Order $order): ?DatabaseNotification
    {
        $order->loadMissing(['seller.sellerProfile', 'user']);
        $seller = $this->sellerForOrder($order);

        if (! $seller) {
            return null;
        }

        return $this->send(
            $seller,
            'orders',
            'order_completed',
            'Order marked as completed',
            'Order #' . $order->id . ' has been marked as completed.',
            'seller.orders',
            ['order' => $order->id],
            relatedType: Order::class,
            relatedId: $order->id,
            dedupeKey: 'seller:order-completed:' . $order->id,
        );
    }

    public function buyerMessage(Message $message): ?DatabaseNotification
    {
        $message->loadMissing(['conversation.buyer', 'conversation.seller', 'sender']);
        $conversation = $message->conversation;

        if (! $conversation || (int) $message->sender_id === (int) $conversation->seller_id) {
            return null;
        }

        $seller = $conversation->seller;

        if (! $seller) {
            return null;
        }

        return $this->send(
            $seller,
            'messages',
            'buyer_message',
            'Buyer sent a message',
            ($message->sender?->name ?? $conversation->buyer?->name ?? 'A buyer') . ' sent you a message.',
            'seller.messages.show',
            ['conversation' => $conversation->id],
            relatedType: Conversation::class,
            relatedId: $conversation->id,
            dedupeKey: 'seller:buyer-message:' . $message->id,
        );
    }

    public function buyerLeftReview(Review $review): ?DatabaseNotification
    {
        $review->loadMissing(['product.user.sellerProfile', 'user']);
        $product = $review->product;
        $seller = $product?->user;

        if (! $product || ! $seller) {
            return null;
        }

        return $this->send(
            $seller,
            'reviews',
            'buyer_review',
            'Buyer left a review',
            ($review->user?->name ?? 'A buyer') . ' rated ' . $product->name . ' ' . $review->rating . ' star' . ((int) $review->rating === 1 ? '' : 's') . '.',
            'seller.products.reviews',
            ['product' => $product->id],
            relatedType: Review::class,
            relatedId: $review->id,
            dedupeKey: 'seller:buyer-review:' . $review->id,
        );
    }

    public function checkProductStock(Product $product, ?int $previousStock = null): ?DatabaseNotification
    {
        $product->loadMissing('user.sellerProfile');
        $seller = $product->user;

        if (! $seller) {
            return null;
        }

        $currentStock = (int) $product->stock;

        if ($currentStock <= 0 && ($previousStock === null || $previousStock > 0)) {
            return $this->send(
                $seller,
                'products',
                'product_out_of_stock',
                'Product out of stock',
                $product->name . ' is now out of stock.',
                'seller.products.edit',
                ['product' => $product->id],
                relatedType: Product::class,
                relatedId: $product->id,
                dedupeKey: 'seller:product-out-of-stock:' . $product->id,
            );
        }

        $threshold = max(1, (int) ($seller->sellerProfile?->low_stock_threshold ?? 5));

        if (
            $currentStock > 0
            && $currentStock <= $threshold
            && ($previousStock === null || $previousStock > $threshold)
        ) {
            return $this->send(
                $seller,
                'products',
                'product_low_stock',
                'Product low stock',
                $product->name . ' has only ' . $currentStock . ' item' . ($currentStock === 1 ? '' : 's') . ' left.',
                'seller.products.edit',
                ['product' => $product->id],
                relatedType: Product::class,
                relatedId: $product->id,
                dedupeKey: 'seller:product-low-stock:' . $product->id . ':' . $currentStock,
            );
        }

        return null;
    }

    public function productEdited(Product $product, array $changedFields = []): ?DatabaseNotification
    {
        $product->loadMissing('user.sellerProfile');
        $seller = $product->user;

        if (! $seller) {
            return null;
        }

        $changedSummary = count($changedFields) > 0
            ? ' Updated: ' . implode(', ', array_values(array_unique($changedFields))) . '.'
            : '';

        return $this->send(
            $seller,
            'products',
            'product_edited',
            'Product edited successfully',
            $product->name . ' was updated successfully.' . $changedSummary,
            'seller.products.edit',
            ['product' => $product->id],
            relatedType: Product::class,
            relatedId: $product->id,
            dedupeKey: 'seller:product-edited:' . $product->id . ':' . optional($product->updated_at)->timestamp,
        );
    }

    public function productReported(Report $report): ?DatabaseNotification
    {
        $report->loadMissing(['product.user.sellerProfile', 'user']);
        $product = $report->product;
        $seller = $product?->user;

        if (! $product || ! $seller) {
            return null;
        }

        return $this->send(
            $seller,
            'admin',
            'product_reported',
            'Product reported',
            $product->name . ' was reported for review.',
            'seller.products.index',
            ['status' => 'live'],
            relatedType: Report::class,
            relatedId: $report->id,
            dedupeKey: 'seller:product-reported:' . $report->id,
        );
    }

    public function productApproved(Product $product): ?DatabaseNotification
    {
        $product->loadMissing('user.sellerProfile');
        $seller = $product->user;

        if (! $seller) {
            return null;
        }

        return $this->send(
            $seller,
            'admin',
            'product_approved',
            'Product approved',
            $product->name . ' has been approved and is now visible to buyers.',
            'seller.products.index',
            ['status' => 'live'],
            relatedType: Product::class,
            relatedId: $product->id,
            dedupeKey: 'seller:product-approved:' . $product->id,
        );
    }

    public function productRejected(Product $product): ?DatabaseNotification
    {
        $product->loadMissing('user.sellerProfile');
        $seller = $product->user;

        if (! $seller) {
            return null;
        }

        $reason = trim((string) $product->rejection_reason);

        return $this->send(
            $seller,
            'admin',
            'product_rejected',
            'Product rejected',
            $product->name . ' was rejected.' . ($reason !== '' ? ' Reason: ' . $reason : ''),
            'seller.products.index',
            ['status' => 'violation'],
            relatedType: Product::class,
            relatedId: $product->id,
            dedupeKey: 'seller:product-rejected:' . $product->id . ':' . md5($reason),
        );
    }

    public function shopReviewed(Seller $sellerProfile, string $status): ?DatabaseNotification
    {
        $sellerProfile->loadMissing('user');
        $seller = $sellerProfile->user;

        if (! $seller) {
            return null;
        }

        $isApproved = $status === Seller::STATUS_APPROVED;
        $title = $isApproved ? 'Shop verified' : 'Shop review update';
        $message = $isApproved
            ? 'Your shop has been verified and approved for selling.'
            : 'Your shop application needs attention.' . ($sellerProfile->review_notes ? ' Note: ' . $sellerProfile->review_notes : '');

        return $this->send(
            $seller,
            'admin',
            $isApproved ? 'shop_verified' : 'shop_flagged',
            $title,
            $message,
            'seller.dashboard',
            relatedType: Seller::class,
            relatedId: $sellerProfile->id,
            dedupeKey: 'seller:shop-reviewed:' . $sellerProfile->id . ':' . $status . ':' . md5((string) $sellerProfile->review_notes),
        );
    }

    public function shopFlagged(Report $report): ?DatabaseNotification
    {
        $report->loadMissing(['seller.sellerProfile', 'user']);
        $seller = $report->seller;

        if (! $seller) {
            return null;
        }

        return $this->send(
            $seller,
            'admin',
            'shop_flagged',
            'Shop flagged for review',
            'Your shop was reported and is pending admin review.',
            'seller.dashboard',
            relatedType: Report::class,
            relatedId: $report->id,
            dedupeKey: 'seller:shop-flagged:' . $report->id,
        );
    }

    public function adminViolation(?User $seller, string $title, string $message, string $action, ?int $reportId): ?DatabaseNotification
    {
        if (! $seller) {
            return null;
        }

        return $this->send(
            $seller,
            'admin',
            $action,
            $title,
            $message,
            'seller.dashboard',
            relatedType: Report::class,
            relatedId: $reportId,
            dedupeKey: 'seller:admin-action:' . $action . ':' . ($reportId ?: 'none') . ':' . md5($message),
        );
    }

    public function syncPendingOrdersNotShipped(User $seller): void
    {
        Order::with(['seller.sellerProfile', 'user'])
            ->where('seller_id', $seller->id)
            ->whereIn('shipping_status', [Order::SHIPPING_PENDING, Order::SHIPPING_TO_SHIP])
            ->where('created_at', '<=', now()->subDay())
            ->latest()
            ->get()
            ->each(function (Order $order) use ($seller): void {
                $this->send(
                    $seller,
                    'orders',
                    'pending_order_not_shipped',
                    'Pending order not shipped',
                    'Order #' . $order->id . ' has not been shipped yet.',
                    'seller.orders',
                    ['order' => $order->id],
                    relatedType: Order::class,
                    relatedId: $order->id,
                    dedupeKey: 'seller:pending-order-not-shipped:' . $order->id,
                );
            });
    }

    private function alreadyExists(User $seller, string $dedupeKey): bool
    {
        return $seller
            ->notifications()
            ->where('data->dedupe_key', $dedupeKey)
            ->exists();
    }

    private function sellerForOrder(Order $order): ?User
    {
        if ($order->seller) {
            return $order->seller;
        }

        $order->loadMissing('items.product.user.sellerProfile');

        return $order->items
            ->map(fn ($item) => $item->product?->user)
            ->filter()
            ->first();
    }
}
