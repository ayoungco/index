<?php

namespace App\Services;

class QrCodeRenderService
{
    public function renderSvg(string $text, int $size = 220): string
    {
        if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
            return \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                ->size($size)
                ->margin(1)
                ->generate($text);
        }

        // Fallback placeholder when QR library is unavailable in offline environments.
        $safeText = e($text);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$size}" height="{$size}" viewBox="0 0 {$size} {$size}">
  <rect width="100%" height="100%" fill="#fff" stroke="#000"/>
  <text x="50%" y="45%" text-anchor="middle" font-family="monospace" font-size="12" fill="#000">QR package missing</text>
  <text x="50%" y="58%" text-anchor="middle" font-family="monospace" font-size="9" fill="#000">{$safeText}</text>
</svg>
SVG;
    }
}
