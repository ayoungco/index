<?php

namespace App\Services;

class QrVerificationService
{
    public function verifyImageContainsUuid(string $absoluteImagePath, string $expectedUuid): bool
    {
        $decoded = $this->decodeWithKhanamiryan($absoluteImagePath);

        if (! is_string($decoded) || trim($decoded) === '') {
            return false;
        }

        return trim($decoded) === trim($expectedUuid);
    }

    private function decodeWithKhanamiryan(string $absoluteImagePath): ?string
    {
        if (! class_exists(\Zxing\QrReader::class)) {
            return null;
        }

        try {
            $reader = new \Zxing\QrReader($absoluteImagePath);
            $text = $reader->text();

            return is_string($text) ? $text : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
