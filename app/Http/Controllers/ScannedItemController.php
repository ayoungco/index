<?php

namespace App\Http\Controllers;

use App\Models\ItemEvent;
use App\Models\ScannedItem;
use App\Services\ImageCompressionService;
use App\Services\QrCodeRenderService;
use App\Services\QrVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScannedItemController extends Controller
{
    public function __construct(
        private readonly ImageCompressionService $imageCompression,
        private readonly QrVerificationService $qrVerification,
        private readonly QrCodeRenderService $qrRenderer,
    ) {}

    public function show(Request $request, string $uuid): View
    {
        $user = $request->user();
        $item = ScannedItem::query()
            ->where('uuid', $uuid)
            ->with(['creator', 'events.author'])
            ->first();

        if (! $item) {
            if (! $user) {
                return view('scanned-items.missing-guest', [
                    'uuid' => $uuid,
                ]);
            }

            return view('scanned-items.missing', [
                'uuid' => $uuid,
                'canInitialize' => (bool) $user->email_verified_at,
            ]);
        }

        if (! $user) {
            return view('scanned-items.public', [
                'item' => $item,
            ]);
        }

        return view('scanned-items.show', [
            'item' => $item,
            'isAuthenticated' => true,
            'canPost' => (bool) $user->email_verified_at,
        ]);
    }

    public function initialize(Request $request, string $uuid): RedirectResponse
    {
        $item = ScannedItem::query()->where('uuid', $uuid)->first();

        if ($item) {
            return redirect()->route('scanned-items.show', ['uuid' => $uuid]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        ScannedItem::query()->create([
            'uuid' => $uuid,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('scanned-items.show', ['uuid' => $uuid])
            ->with('status', 'Object initialized.');
    }

    public function addPhoto(Request $request, string $uuid): RedirectResponse
    {
        $item = ScannedItem::query()
            ->where('uuid', $uuid)
            ->firstOrFail();

        $validated = $request->validate([
            'photo' => ['required', 'image', 'max:15360'],
        ]);

        $relativePath = $this->imageCompression->compressAndStore($validated['photo'], $uuid);
        $absolutePath = storage_path('app/public/'.$relativePath);
        $isQrVerified = $this->qrVerification->verifyImageContainsUuid($absolutePath, $uuid);

        ItemEvent::query()->create([
            'scanned_item_id' => $item->id,
            'user_id' => $request->user()->id,
            'image_path' => $relativePath,
            'is_qr_verified' => $isQrVerified,
        ]);

        $status = $isQrVerified
            ? 'Photo added and QR verified.'
            : 'Photo added, but QR could not be verified. Flagged for review.';

        return redirect()
            ->route('scanned-items.show', ['uuid' => $uuid])
            ->with('status', $status);
    }

    public function print(Request $request, string $uuid): View
    {
        $item = ScannedItem::query()
            ->where('uuid', $uuid)
            ->firstOrFail();

        return view('scanned-items.print', [
            'item' => $item,
            'qrSvg' => $this->qrRenderer->renderSvg($item->uuid, 280),
            'isAuthenticated' => (bool) $request->user(),
        ]);
    }
}
