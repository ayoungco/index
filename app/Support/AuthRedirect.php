<?php

namespace App\Support;

use Illuminate\Http\Request;

class AuthRedirect
{
    public const SCANNED_ITEM_URL_SESSION_KEY = 'auth.scanned_item_url';

    public static function rememberScannedItemUrl(Request $request, string $uuid): string
    {
        $targetUrl = route('items.show', ['uuid' => $uuid], true);

        if ($request->hasSession()) {
            $request->session()->put(self::SCANNED_ITEM_URL_SESSION_KEY, $targetUrl);
        }

        return $targetUrl;
    }

    public static function resolveTarget(Request $request, string $fallbackRoute = 'dashboard'): string
    {
        $targetUrl = $request->hasSession()
            ? $request->session()->get(self::SCANNED_ITEM_URL_SESSION_KEY)
            : null;

        if (is_string($targetUrl) && $targetUrl !== '') {
            return $targetUrl;
        }

        return route($fallbackRoute, absolute: true);
    }
}
