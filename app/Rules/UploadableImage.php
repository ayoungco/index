<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class UploadableImage implements ValidationRule
{
    /**
     * @var list<string>
     */
    private const IMAGE_MIMES = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/webp',
        'image/heic',
        'image/heif',
        'image/heic-sequence',
        'image/heif-sequence',
        'image/gif',
    ];

    /**
     * @var list<string>
     */
    private const IOS_HEIC_EXTENSIONS = [
        'heic',
        'heif',
    ];

    /**
     * @var list<string>
     */
    private const GENERIC_IOS_MIMES = [
        'application/octet-stream',
        'application/x-empty',
        'binary/octet-stream',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile) {
            $fail('The :attribute must be an image file.');

            return;
        }

        $serverMime = strtolower((string) $value->getMimeType());
        $clientMime = strtolower((string) $value->getClientMimeType());
        $extension = strtolower($value->getClientOriginalExtension());

        if (in_array($serverMime, self::IMAGE_MIMES, true) || in_array($clientMime, self::IMAGE_MIMES, true)) {
            return;
        }

        $isHeicExtension = in_array($extension, self::IOS_HEIC_EXTENSIONS, true);
        $hasGenericMime = in_array($serverMime, self::GENERIC_IOS_MIMES, true)
            || in_array($clientMime, self::GENERIC_IOS_MIMES, true)
            || $serverMime === ''
            || $clientMime === '';

        if ($isHeicExtension && $hasGenericMime) {
            return;
        }

        $fail('The :attribute must be a JPEG, PNG, WebP, HEIC, HEIF, or GIF image.');
    }
}
