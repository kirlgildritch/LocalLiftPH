<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class PublicAssetUrl
{
    public static function for(?string $path): ?string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        $normalized = str_replace('\\', '/', $path);
        $normalized = preg_replace('#^/?public/#', '', $normalized) ?? $normalized;
        $normalized = ltrim($normalized, '/');

        if ($normalized === '') {
            return null;
        }

        if (str_starts_with($normalized, 'storage/')) {
            return asset($normalized);
        }

        return Storage::disk('public')->url($normalized);
    }
}
