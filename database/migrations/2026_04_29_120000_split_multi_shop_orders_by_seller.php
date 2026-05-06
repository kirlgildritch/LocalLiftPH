<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('orders', 'seller_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreignId('seller_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('orders', 'checkout_group')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('checkout_group', 64)->nullable()->after('seller_id')->index();
            });
        }

        $this->splitExistingOrdersBySeller();
        $this->normalizeCompletedStatuses();
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'checkout_group')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropIndex(['checkout_group']);
                $table->dropColumn('checkout_group');
            });
        }

        if (Schema::hasColumn('orders', 'seller_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropConstrainedForeignId('seller_id');
            });
        }
    }

    private function splitExistingOrdersBySeller(): void
    {
        DB::transaction(function () {
            $orders = DB::table('orders')->orderBy('id')->get();

            foreach ($orders as $order) {
                $orderItems = DB::table('order_items as order_items')
                    ->join('products as products', 'products.id', '=', 'order_items.product_id')
                    ->where('order_items.order_id', $order->id)
                    ->select([
                        'order_items.id',
                        'order_items.quantity',
                        'order_items.price',
                        'order_items.shipping_fee',
                        'products.user_id as seller_id',
                    ])
                    ->get()
                    ->groupBy(fn ($item) => (int) $item->seller_id);

                $checkoutGroup = $order->checkout_group ?: (string) Str::uuid();

                if ($orderItems->isEmpty()) {
                    DB::table('orders')
                        ->where('id', $order->id)
                        ->update([
                            'checkout_group' => $checkoutGroup,
                        ]);

                    continue;
                }

                $cancellation = DB::table('order_cancellations')
                    ->where('order_id', $order->id)
                    ->first();

                $isFirstSeller = true;

                foreach ($orderItems as $sellerId => $itemsForSeller) {
                    $shippingFee = (float) $itemsForSeller->sum(function ($item) {
                        return (float) $item->shipping_fee * (int) $item->quantity;
                    });
                    $subtotal = (float) $itemsForSeller->sum(function ($item) {
                        return (float) $item->price * (int) $item->quantity;
                    });
                    $totalPrice = $subtotal + $shippingFee;

                    if ($isFirstSeller) {
                        DB::table('orders')
                            ->where('id', $order->id)
                            ->update([
                                'seller_id' => $sellerId,
                                'checkout_group' => $checkoutGroup,
                                'shipping_fee' => $shippingFee,
                                'total_price' => $totalPrice,
                            ]);

                        $isFirstSeller = false;
                        continue;
                    }

                    $newOrderId = DB::table('orders')->insertGetId([
                        'user_id' => $order->user_id,
                        'seller_id' => $sellerId,
                        'checkout_group' => $checkoutGroup,
                        'shipping_fee' => $shippingFee,
                        'total_price' => $totalPrice,
                        'status' => $order->status,
                        'shipping_status' => $order->shipping_status,
                        'created_at' => $order->created_at,
                        'updated_at' => $order->updated_at,
                    ]);

                    DB::table('order_items')
                        ->whereIn('id', $itemsForSeller->pluck('id')->all())
                        ->update([
                            'order_id' => $newOrderId,
                            'updated_at' => $order->updated_at,
                        ]);

                    if ($cancellation) {
                        DB::table('order_cancellations')->insert([
                            'order_id' => $newOrderId,
                            'user_id' => $cancellation->user_id,
                            'reasons' => $cancellation->reasons,
                            'other_reason' => $cancellation->other_reason,
                            'status_before_cancellation' => $cancellation->status_before_cancellation,
                            'created_at' => $cancellation->created_at,
                            'updated_at' => $cancellation->updated_at,
                        ]);
                    }
                }
            }
        });
    }

    private function normalizeCompletedStatuses(): void
    {
        DB::table('orders')
            ->whereIn('shipping_status', ['delivered', 'out_for_delivery'])
            ->update(['shipping_status' => 'completed']);

        DB::table('orders')
            ->where('status', 'delivered')
            ->update(['status' => 'completed']);
    }
};
