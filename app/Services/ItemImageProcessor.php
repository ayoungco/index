<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class ItemImageProcessor
{
    public function process(UploadedFile $file, string $uuid): array
    {
        $source = imagecreatefromstring(file_get_contents($file->getRealPath()));

        if (! $source) {
            throw new \RuntimeException('Unsupported image file.');
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $maxWidth = 1080;

        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) round(($height / $width) * $newWidth);
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $relativePath = 'item-events/'.$uuid.'/'.uniqid('event_', true).'.jpg';
        $fullPath = storage_path('app/public/'.$relativePath);

        if (! is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0775, true);
        }

        imagejpeg($resized, $fullPath, 75);
        imagedestroy($source);
        imagedestroy($resized);

        return [
            'path' => $relativePath,
            'verified' => $this->detectMatchingQr($fullPath, $uuid),
        ];
    }

    private function detectMatchingQr(string $fullPath, string $uuid): bool
    {
        $zbar = trim((string) shell_exec('command -v zbarimg'));

        if ($zbar === '') {
            return false;
        }

        $output = shell_exec($zbar.' --quiet '.escapeshellarg($fullPath).' 2>/dev/null');

        if (! is_string($output) || trim($output) === '') {
            return false;
        }

        return str_contains($output, $uuid);
    }
}
