<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')
            ->select([
                'id',
                'status',
                'shipping_status',
                'payment_method',
                'payment_status',
                'paid_at',
                'seller_earning_status',
                'updated_at',
                'created_at',
            ])
            ->orderBy('id')
            ->chunkById(100, function ($orders): void {
                foreach ($orders as $order) {
                    $shippingStatus = $this->normalizeShippingStatus($order->shipping_status, $order->status);
                    $updates = [
                        'payment_method' => filled($order->payment_method) ? $order->payment_method : 'cod',
                    ];

                    switch ($shippingStatus) {
                        case 'completed':
                            $updates['payment_status'] = 'paid';
                            $updates['seller_earning_status'] = 'available';
                            $updates['paid_at'] = $order->paid_at ?: ($order->updated_at ?: $order->created_at);
                            break;

                        case 'cancelled':
                            $updates['payment_status'] = 'cancelled';
                            $updates['seller_earning_status'] = 'reversed';
                            break;

                        case 'shipped':
                            $updates['payment_status'] = 'pending';
                            $updates['seller_earning_status'] = 'on_hold';
                            break;

                        default:
                            $updates['payment_status'] = 'pending';
                            $updates['seller_earning_status'] = 'pending';
                            break;
                    }

                    DB::table('orders')
                        ->where('id', $order->id)
                        ->update($updates);
                }
            });
    }

    public function down(): void
    {
    }

    private function normalizeShippingStatus(?string $shippingStatus, ?string $legacyStatus): string
    {
        $resolved = $shippingStatus ?: match ($legacyStatus) {
            'completed', 'delivered' => 'completed',
            'confirmed', 'processing' => 'to_ship',
            'shipped' => 'shipped',
            'cancelled' => 'cancelled',
            default => 'pending',
        };

        return match ($resolved) {
            'out_for_delivery', 'delivered' => 'completed',
            default => $resolved ?: 'pending',
        };
    }
};
