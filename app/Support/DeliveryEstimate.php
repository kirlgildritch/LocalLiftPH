<?php

namespace App\Support;

use App\Models\Address;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class DeliveryEstimate
{
    public static function forSellerCartItems(Collection $cartItems, ?Address $deliveryAddress): array
    {
        $seller = $cartItems->first()?->product?->user;
        $sellerAddress = self::sellerAddress($seller);
        $distanceType = self::distanceType($sellerAddress, $deliveryAddress);
        $range = self::dayRangeFor($distanceType);
        $startDate = self::deliveryDate($range[0]);
        $endDate = self::deliveryDate($range[1]);

        return [
            'label' => self::labelFor($distanceType),
            'date_range' => self::formatDateRange($startDate, $endDate),
            'days' => $range[0] === $range[1]
                ? $range[0] . ' day'
                : $range[0] . '-' . $range[1] . ' days',
            'start_days' => $range[0],
            'end_days' => $range[1],
            'seller_address' => $sellerAddress,
            'is_fallback' => $distanceType === 'standard',
        ];
    }

    public static function combined(Collection $estimates): array
    {
        $starts = $estimates->pluck('start_days')->filter();
        $ends = $estimates->pluck('end_days')->filter();

        if ($starts->isEmpty() || $ends->isEmpty()) {
            return [
                'label' => 'Standard local delivery',
                'date_range' => self::formatDateRange(self::deliveryDate(3), self::deliveryDate(5)),
                'days' => '3-5 days',
            ];
        }

        $startDays = (int) $starts->min();
        $endDays = (int) $ends->max();

        return [
            'label' => 'Estimated delivery window',
            'date_range' => self::formatDateRange(self::deliveryDate($startDays), self::deliveryDate($endDays)),
            'days' => $startDays === $endDays ? $startDays . ' day' : $startDays . '-' . $endDays . ' days',
        ];
    }

    private static function sellerAddress(?User $seller): ?string
    {
        $sellerProfile = $seller?->sellerProfile;

        if (! $sellerProfile) {
            return null;
        }

        return filled($sellerProfile->formattedLocation())
            ? $sellerProfile->formattedLocation()
            : null;
    }

    private static function distanceType(?string $sellerAddress, ?Address $deliveryAddress): string
    {
        if (! $deliveryAddress || blank($sellerAddress)) {
            return 'standard';
        }

        $sellerAddress = mb_strtolower($sellerAddress);
        $city = mb_strtolower((string) $deliveryAddress->city);
        $province = mb_strtolower((string) $deliveryAddress->province);
        $region = mb_strtolower((string) $deliveryAddress->region);

        if (filled($city) && str_contains($sellerAddress, $city)) {
            return 'same_city';
        }

        if (filled($province) && str_contains($sellerAddress, $province)) {
            return 'same_province';
        }

        if (filled($region) && str_contains($sellerAddress, $region)) {
            return 'same_region';
        }

        return 'standard';
    }

    private static function dayRangeFor(string $distanceType): array
    {
        return match ($distanceType) {
            'same_city' => [1, 2],
            'same_province' => [2, 3],
            'same_region' => [2, 4],
            default => [3, 5],
        };
    }

    private static function labelFor(string $distanceType): string
    {
        return match ($distanceType) {
            'same_city' => 'Same-city local delivery',
            'same_province' => 'Provincial local delivery',
            'same_region' => 'Regional courier delivery',
            default => 'Standard local courier',
        };
    }

    private static function deliveryDate(int $days): CarbonImmutable
    {
        return CarbonImmutable::now()->addDays($days);
    }

    private static function formatDateRange(CarbonImmutable $startDate, CarbonImmutable $endDate): string
    {
        if ($startDate->isSameDay($endDate)) {
            return $startDate->format('M d, Y');
        }

        if ($startDate->isSameMonth($endDate)) {
            return $startDate->format('M d') . '-' . $endDate->format('d, Y');
        }

        return $startDate->format('M d') . '-' . $endDate->format('M d, Y');
    }
}
