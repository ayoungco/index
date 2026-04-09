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

        return $this->payloadMatchesItem(trim($decoded), trim($expectedUuid));
    }

    public function payloadMatchesItem(string $decodedPayload, string $expectedUuid): bool
    {
        $normalizedPayload = strtolower(trim($decodedPayload));
        $normalizedUuid = strtolower(trim($expectedUuid));

        if ($normalizedPayload === '' || $normalizedUuid === '') {
            return false;
        }

        if ($normalizedPayload === $normalizedUuid) {
            return true;
        }

        if (preg_match('/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/i', $decodedPayload, $match) === 1) {
            return strtolower($match[0]) === $normalizedUuid;
        }

        $path = parse_url($decodedPayload, PHP_URL_PATH);

        if (! is_string($path)) {
            return false;
        }

        foreach (explode('/', trim($path, '/')) as $segment) {
            if (strtolower($segment) === $normalizedUuid) {
                return true;
            }
        }

        return false;
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
