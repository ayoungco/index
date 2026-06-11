<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class UploadedImageStorage
{
    public function storeOriginal(UploadedFile $file, string $directory): string
    {
        $filename = Str::uuid().'.'.$this->extensionFor($file);
        $path = $file->storeAs($directory, $filename, 'public');

        if (! is_string($path)) {
            throw new RuntimeException('Failed to store uploaded image.');
        }

        if (! Storage::disk('public')->exists($path)) {
            throw new RuntimeException('Uploaded image was not found after storage.');
        }

        return $path;
    }

    private function extensionFor(UploadedFile $file): string
    {
        $mimeType = strtolower((string) ($file->getMimeType() ?: $file->getClientMimeType()));

        $extension = match ($mimeType) {
            'image/jpeg', 'image/jpg', 'image/pjpeg' => 'jpg',
            'image/png', 'image/x-png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'image/heic', 'image/heic-sequence' => 'heic',
            'image/heif', 'image/heif-sequence' => 'heif',
            default => null,
        };

        if ($extension !== null) {
            return $extension;
        }

        $clientExtension = strtolower($file->getClientOriginalExtension());

        if (in_array($clientExtension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'heic', 'heif'], true)) {
            return $clientExtension === 'jpeg' ? 'jpg' : $clientExtension;
        }

        throw new RuntimeException('Unable to determine uploaded image extension.');
    }
}
