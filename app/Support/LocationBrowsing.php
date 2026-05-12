<?php

namespace App\Support;

use App\Models\Address;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class LocationBrowsing
{
    public static function normalized(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    public static function applySellerLocationFilter(Builder $query, ?string $province, ?string $city): Builder
    {
        $province = self::normalized($province);
        $city = self::normalized($city);

        if (! $province && ! $city) {
            return $query;
        }

        return $query->whereHas('user.sellerProfile', function (Builder $sellerQuery) use ($province, $city) {
            if ($province) {
                $sellerQuery->where('province', $province);
            }

            if ($city) {
                $sellerQuery->where('city', $city);
            }
        });
    }

    public static function applyShopLocationFilter(Builder $query, ?string $province, ?string $city): Builder
    {
        $province = self::normalized($province);
        $city = self::normalized($city);

        if (! $province && ! $city) {
            return $query;
        }

        return $query->whereHas('sellerProfile', function (Builder $sellerQuery) use ($province, $city) {
            if ($province) {
                $sellerQuery->where('province', $province);
            }

            if ($city) {
                $sellerQuery->where('city', $city);
            }
        });
    }

    public static function orderByNearest(Builder|QueryBuilder $query, string $sellerAlias, ?Address $address): Builder|QueryBuilder
    {
        if (! $address) {
            return $query;
        }

        return $query->orderByRaw(
            "CASE
                WHEN {$sellerAlias}.city = ? THEN 0
                WHEN {$sellerAlias}.province = ? THEN 1
                WHEN {$sellerAlias}.region = ? THEN 2
                ELSE 3
            END",
            [
                (string) $address->city,
                (string) $address->province,
                (string) $address->region,
            ]
        );
    }

    public static function matchLabel($sellerProfile, ?Address $address): ?string
    {
        if (! $sellerProfile) {
            return null;
        }

        if ($address && filled($sellerProfile->city) && $sellerProfile->city === $address->city) {
            return 'Same city';
        }

        if ($address && filled($sellerProfile->province) && $sellerProfile->province === $address->province) {
            return 'Same province';
        }

        if (filled($sellerProfile->city) && filled($sellerProfile->province)) {
            return $sellerProfile->city . ', ' . $sellerProfile->province;
        }

        if (filled($sellerProfile->province)) {
            return $sellerProfile->province;
        }

        return null;
    }
}
