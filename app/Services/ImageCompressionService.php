<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ImageCompressionService
{
    public function compressAndStore(UploadedFile $file, string $uuid): string
    {
        $disk = Storage::disk('public');
        $directory = 'scanned-items/'.$uuid;
        $disk->makeDirectory($directory);

        $filename = Str::uuid().'.jpg';
        $relativePath = $directory.'/'.$filename;

        // Prefer Imagick for reliability; fall back to GD if needed.
        if (class_exists(\Imagick::class)) {
            $image = new \Imagick($file->getRealPath());
            $image->setImageOrientation(\Imagick::ORIENTATION_TOPLEFT);
            $image->stripImage();

            if ($image->getImageWidth() > 1080) {
                $image->resizeImage(1080, 0, \Imagick::FILTER_LANCZOS, 1);
            }

            $image->setImageFormat('jpeg');
            $image->setImageCompressionQuality(75);
            $disk->put($relativePath, (string) $image->getImageBlob());
            $image->clear();
            $image->destroy();

            return $relativePath;
        }

        $contents = file_get_contents($file->getRealPath());

        if ($contents === false) {
            throw new RuntimeException('Unable to read uploaded image.');
        }

        $resource = @imagecreatefromstring($contents);

        if (! $resource) {
            throw new RuntimeException('Unsupported image format.');
        }

        $width = imagesx($resource);
        $height = imagesy($resource);

        if ($width > 1080) {
            $newWidth = 1080;
            $newHeight = (int) round(($height / $width) * $newWidth);
            $resized = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resized, $resource, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($resource);
            $resource = $resized;
        }

        ob_start();
        imagejpeg($resource, null, 75);
        $jpegData = ob_get_clean();
        imagedestroy($resource);

        if (! is_string($jpegData)) {
            throw new RuntimeException('Failed to encode compressed image.');
        }

        $disk->put($relativePath, $jpegData);

        return $relativePath;
    }
}
