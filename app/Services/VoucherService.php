<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherRedemption;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Validation\ValidationException;

class VoucherService
{
    public function normalizeCode(?string $code): ?string
    {
        $normalized = strtoupper(trim((string) $code));

        return $normalized === '' ? null : $normalized;
    }

    public function evaluate(?string $code, User|int $user, float $subtotal, bool $lock = false): array
    {
        $normalizedCode = $this->normalizeCode($code);
        $subtotal = round(max(0, $subtotal), 2);

        if ($normalizedCode === null) {
            return $this->emptyApplication();
        }

        $query = Voucher::query()->code($normalizedCode);

        if ($lock) {
            $query->lockForUpdate();
        }

        $voucher = $query->first();

        if (! $voucher) {
            throw ValidationException::withMessages([
                'voucher_code' => 'Voucher code is invalid or unavailable.',
            ]);
        }

        $this->ensureVoucherCanBeUsed($voucher, $user, $subtotal);

        $discount = $this->calculateDiscount($voucher, $subtotal);

        return [
            'voucher' => $voucher,
            'code' => $voucher->code,
            'discount' => $discount,
            'label' => $this->labelFor($voucher),
            'seller_id' => $voucher->seller_id,
            'eligible_subtotal' => $subtotal,
        ];
    }

    public function evaluateForCart(?string $code, User|int $user, Collection $cartItems, CheckoutSummaryService $checkoutSummaryService, bool $lock = false): array
    {
        $normalizedCode = $this->normalizeCode($code);

        if ($normalizedCode === null) {
            return $this->emptyApplication();
        }

        $query = Voucher::query()->code($normalizedCode);

        if ($lock) {
            $query->lockForUpdate();
        }

        $voucher = $query->first();

        if (! $voucher) {
            throw ValidationException::withMessages([
                'voucher_code' => 'Voucher code is invalid or unavailable.',
            ]);
        }

        $eligibleItems = $voucher->isSellerOwned()
            ? $cartItems->filter(fn ($item) => (int) ($item->product?->user_id ?? 0) === (int) $voucher->seller_id)->values()
            : $cartItems;

        if ($eligibleItems->isEmpty()) {
            throw ValidationException::withMessages([
                'voucher_code' => 'This seller voucher does not apply to the selected items.',
            ]);
        }

        $subtotal = $checkoutSummaryService->totals($eligibleItems)['subtotal'];
        $application = $this->evaluate($voucher->code, $user, $subtotal, $lock);
        $application['eligible_subtotal'] = $subtotal;

        return $application;
    }

    public function redeem(array $application, User|int $user, Order $order, float $discountAmount): ?VoucherRedemption
    {
        $voucher = $application['voucher'] ?? null;

        if (! $voucher instanceof Voucher || $discountAmount <= 0) {
            return null;
        }

        return VoucherRedemption::create([
            'voucher_id' => $voucher->id,
            'user_id' => $user instanceof User ? $user->id : (int) $user,
            'order_id' => $order->id,
            'checkout_group' => $order->checkout_group,
            'code' => $voucher->code,
            'discount_amount' => round($discountAmount, 2),
            'redeemed_at' => now(),
        ]);
    }

    public function activeSellerVouchers(User|int $seller, int $limit = 6): Collection
    {
        $sellerId = $seller instanceof User ? $seller->id : (int) $seller;

        return $this->formatVoucherCollection(
            Voucher::query()
                ->where('seller_id', $sellerId)
                ->where('is_active', true)
                ->where(function ($query) {
                    $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                })
                ->where(function ($query) {
                    $query->whereNull('ends_at')->orWhere('ends_at', '>=', now());
                })
                ->withCount('redemptions')
                ->orderByRaw('ends_at is null')
                ->orderBy('ends_at')
                ->latest('id')
                ->limit($limit)
                ->get()
        );
    }

    public function activeSellerVouchersForSellers(Collection $sellerIds, int $limitPerSeller = 3): Collection
    {
        $ids = $sellerIds
            ->map(fn ($sellerId) => (int) $sellerId)
            ->filter()
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        $vouchers = Voucher::query()
            ->whereIn('seller_id', $ids)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->withCount('redemptions')
            ->orderByRaw('ends_at is null')
            ->orderBy('ends_at')
            ->latest('id')
            ->get()
            ->groupBy('seller_id')
            ->map(fn (EloquentCollection|Collection $sellerVouchers) => $this->formatVoucherCollection($sellerVouchers->take($limitPerSeller)));

        return $ids->mapWithKeys(fn ($sellerId) => [$sellerId => $vouchers->get($sellerId, collect())]);
    }

    public function emptyApplication(): array
    {
        return [
            'voucher' => null,
            'code' => null,
            'discount' => 0.0,
            'label' => null,
            'seller_id' => null,
            'eligible_subtotal' => 0.0,
        ];
    }

    protected function ensureVoucherCanBeUsed(Voucher $voucher, User|int $user, float $subtotal): void
    {
        if (! $voucher->is_active) {
            $this->throwUnavailable();
        }

        if ($voucher->starts_at && now()->lt($voucher->starts_at)) {
            $this->throwUnavailable();
        }

        if ($voucher->ends_at && now()->gt($voucher->ends_at)) {
            throw ValidationException::withMessages([
                'voucher_code' => 'Voucher code has expired.',
            ]);
        }

        if ((float) $voucher->minimum_subtotal > 0 && $subtotal < (float) $voucher->minimum_subtotal) {
            throw ValidationException::withMessages([
                'voucher_code' => 'This voucher requires a minimum subtotal of PHP ' . number_format((float) $voucher->minimum_subtotal, 2) . '.',
            ]);
        }

        if ($voucher->usage_limit !== null && $voucher->redemptions()->count() >= (int) $voucher->usage_limit) {
            throw ValidationException::withMessages([
                'voucher_code' => 'Voucher code has already reached its usage limit.',
            ]);
        }

        $userId = $user instanceof User ? $user->id : (int) $user;

        if (
            $voucher->per_user_limit !== null
            && $voucher->redemptions()->where('user_id', $userId)->count() >= (int) $voucher->per_user_limit
        ) {
            throw ValidationException::withMessages([
                'voucher_code' => 'You have already used this voucher.',
            ]);
        }
    }

    protected function calculateDiscount(Voucher $voucher, float $subtotal): float
    {
        $value = max(0, (float) $voucher->value);

        $discount = $voucher->type === Voucher::TYPE_PERCENT
            ? $subtotal * min($value, 100) / 100
            : $value;

        if ($voucher->maximum_discount !== null) {
            $discount = min($discount, (float) $voucher->maximum_discount);
        }

        return round(min($discount, $subtotal), 2);
    }

    protected function labelFor(Voucher $voucher): string
    {
        if ($voucher->type === Voucher::TYPE_PERCENT) {
            $value = rtrim(rtrim(number_format((float) $voucher->value, 2), '0'), '.');

            return "{$value}% off";
        }

        return 'PHP ' . number_format((float) $voucher->value, 2) . ' off';
    }

    protected function formatVoucherCollection(EloquentCollection|Collection $vouchers): Collection
    {
        return $vouchers
            ->filter(function (Voucher $voucher) {
                return $voucher->usage_limit === null
                    || (int) $voucher->redemptions_count < (int) $voucher->usage_limit;
            })
            ->map(fn (Voucher $voucher) => [
                'code' => $voucher->code,
                'name' => $voucher->name ?: 'Seller voucher',
                'label' => $this->labelFor($voucher),
                'minimum_subtotal' => (float) $voucher->minimum_subtotal,
                'maximum_discount' => $voucher->maximum_discount !== null ? (float) $voucher->maximum_discount : null,
                'ends_at' => $voucher->ends_at,
                'seller_id' => $voucher->seller_id,
                'usage_left' => $voucher->usage_limit !== null
                    ? max(0, (int) $voucher->usage_limit - (int) $voucher->redemptions_count)
                    : null,
            ])
            ->values();
    }

    protected function throwUnavailable(): void
    {
        throw ValidationException::withMessages([
            'voucher_code' => 'Voucher code is invalid or unavailable.',
        ]);
    }
}
