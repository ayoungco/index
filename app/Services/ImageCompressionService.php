<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use RuntimeException;

class ImageCompressionService
{
    public function compressAndStore(UploadedFile $file, string $uuid, ?string $directory = null): string
    {
        $disk = Storage::disk('uploads');
        $directory ??= 'items/'.$uuid;
        $disk->makeDirectory($directory);

        $filename = Str::uuid().'.jpg';
        $relativePath = $directory.'/'.$filename;

        if (class_exists(ImageManager::class)) {
            $manager = $this->buildInterventionManager();

            if ($manager) {
                $image = $manager->read($file->getRealPath());
                $image->scaleDown(width: 1080);
                $encoded = $image->toJpeg(75);

                if (! $disk->put($relativePath, (string) $encoded)) {
                    throw new RuntimeException('Failed to store compressed image.');
                }

                return $relativePath;
            }
        }

        // Re-encode every upload as JPEG. Never preserve an uploaded file as-is:
        // files on the private upload disk must be application-generated images.
        // This is also an image decoder fallback when Intervention Image is unavailable.
        if (class_exists(\Imagick::class)) {
            $image = new \Imagick($file->getRealPath());
            $image->setImageOrientation(\Imagick::ORIENTATION_TOPLEFT);
            $image->stripImage();

            if ($image->getImageWidth() > 1080) {
                $image->resizeImage(1080, 0, \Imagick::FILTER_LANCZOS, 1);
            }

            $image->setImageFormat('jpeg');
            $image->setImageCompressionQuality(75);
            if (! $disk->put($relativePath, (string) $image->getImageBlob())) {
                throw new RuntimeException('Failed to store compressed image.');
            }
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

        if (! $disk->put($relativePath, $jpegData)) {
            throw new RuntimeException('Failed to store compressed image.');
        }

        return $relativePath;
    }

    private function buildInterventionManager(): ?ImageManager
    {
        if (! class_exists(ImageManager::class)) {
            return null;
        }

        if (extension_loaded('imagick') && class_exists(\Imagick::class) && class_exists(Driver::class)) {
            return new ImageManager(new Driver);
        }

        if (extension_loaded('gd') && class_exists(\Intervention\Image\Drivers\Gd\Driver::class)) {
            return new ImageManager(new \Intervention\Image\Drivers\Gd\Driver);
        }

        return null;
    }
}
