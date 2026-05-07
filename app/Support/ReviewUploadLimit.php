<?php

namespace App\Support;

class ReviewUploadLimit
{
    public static function maxFiles(): int
    {
        return max(1, (int) config('reviews.media_max_files', 5));
    }

    public static function appMaxFileKilobytes(): int
    {
        return max(1, (int) config('reviews.media_max_file_kb', 51200));
    }

    public static function appMaxFileBytes(): int
    {
        return static::appMaxFileKilobytes() * 1024;
    }

    public static function phpUploadMaxBytes(): ?int
    {
        return static::parseIniSize(ini_get('upload_max_filesize'));
    }

    public static function phpPostMaxBytes(): ?int
    {
        return static::parseIniSize(ini_get('post_max_size'));
    }

    public static function effectiveSingleFileBytes(): ?int
    {
        return static::smallestPositive([
            static::appMaxFileBytes(),
            static::phpUploadMaxBytes(),
        ]);
    }

    public static function effectiveRequestBytes(): ?int
    {
        return static::smallestPositive([
            static::phpPostMaxBytes(),
        ]);
    }

    public static function humanSize(?int $bytes): string
    {
        if ($bytes === null || $bytes <= 0) {
            return 'unlimited';
        }

        if ($bytes >= 1024 * 1024 * 1024) {
            return round($bytes / 1024 / 1024 / 1024, 1) . ' GB';
        }

        if ($bytes >= 1024 * 1024) {
            return round($bytes / 1024 / 1024, 1) . ' MB';
        }

        return max(1, (int) ceil($bytes / 1024)) . ' KB';
    }

    public static function tooLargeMessage(): string
    {
        $requestLimit = static::humanSize(static::effectiveRequestBytes());
        $singleFileLimit = static::humanSize(static::effectiveSingleFileBytes());
        $phpUpload = static::humanSize(static::phpUploadMaxBytes());
        $phpPost = static::humanSize(static::phpPostMaxBytes());

        return "The selected files are larger than the current server upload limit. "
            . "This form currently accepts up to {$singleFileLimit} per file and {$requestLimit} per upload. "
            . "PHP is currently set to upload_max_filesize={$phpUpload} and post_max_size={$phpPost}.";
    }

    private static function smallestPositive(array $values): ?int
    {
        $values = array_values(array_filter($values, fn ($value) => is_int($value) && $value > 0));

        if ($values === []) {
            return null;
        }

        return min($values);
    }

    private static function parseIniSize(string|false|null $value): ?int
    {
        if ($value === false || $value === null) {
            return null;
        }

        $value = trim((string) $value);

        if ($value === '' || $value === '0') {
            return null;
        }

        $unit = strtolower(substr($value, -1));
        $number = (float) $value;

        return match ($unit) {
            'g' => (int) round($number * 1024 * 1024 * 1024),
            'm' => (int) round($number * 1024 * 1024),
            'k' => (int) round($number * 1024),
            default => (int) round($number),
        };
    }
}
