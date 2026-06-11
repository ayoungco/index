<?php

namespace App\Support;

class UploadLimits
{
    public static function maxKilobytes(): int
    {
        $configured = max(1, (int) config('uploads.max_megabytes', 30)) * 1024;
        $postLimit = self::iniKilobytes('post_max_size');
        $limits = array_filter([
            self::iniKilobytes('upload_max_filesize'),
            $postLimit === null ? null : max(1, $postLimit - 1024),
        ]);

        return min([$configured, ...$limits]);
    }

    public static function maxBytes(): int
    {
        return self::maxKilobytes() * 1024;
    }

    public static function label(): string
    {
        $megabytes = self::maxKilobytes() / 1024;

        return rtrim(rtrim(number_format($megabytes, 1), '0'), '.').' MB';
    }

    private static function iniKilobytes(string $key): ?int
    {
        $value = trim((string) ini_get($key));

        if ($value === '' || $value === '-1' || $value === '0') {
            return null;
        }

        $unit = strtolower(substr($value, -1));
        $number = (float) $value;

        return match ($unit) {
            'g' => (int) ($number * 1024 * 1024),
            'm' => (int) ($number * 1024),
            'k' => (int) $number,
            default => (int) ceil($number / 1024),
        };
    }
}
