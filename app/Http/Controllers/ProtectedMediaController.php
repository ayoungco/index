<?php

namespace App\Http\Controllers;

use App\Models\ItemEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProtectedMediaController extends Controller
{
    public function show(Request $request, string $path): BinaryFileResponse
    {
        abort_unless($this->isAllowedPath($path), 404);

        $isPublic = str_starts_with($path, 'branding/');

        if (str_starts_with($path, 'items/')) {
            $event = ItemEvent::query()
                ->with('item')
                ->where('image_path', $path)
                ->first();

            abort_unless($event?->item && ($event->item->isPublic() || $request->user()), 404);
            $isPublic = (bool) $event->item->isPublic();
        }

        if (! $isPublic) {
            abort_unless($request->user(), 401);
        }

        $disk = Storage::disk('uploads');

        // During deployment, allow existing files to be read through this
        // authenticated endpoint until they are moved off the legacy public disk.
        if (! $disk->exists($path)) {
            $disk = Storage::disk('public');
        }

        abort_unless($disk->exists($path), 404);

        $response = response()->file($disk->path($path), [
            'Content-Type' => $disk->mimeType($path) ?: 'application/octet-stream',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => $isPublic ? 'public, max-age=3600' : 'private, max-age=3600',
        ]);

        $response->setContentDisposition('inline', basename($path));

        return $response;
    }

    private function isAllowedPath(string $path): bool
    {
        return (bool) preg_match(
            '#^(?:items/[a-f0-9-]+/|thing-scans/|branding/(?:site-logo-)?)[a-f0-9-]+\.(?:jpe?g|png|webp|gif|heic|heif)$#i',
            $path,
        );
    }
}
