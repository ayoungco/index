<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemEvent;
use App\Services\ImageCompressionService;
use App\Services\QrCodeRenderService;
use App\Services\QrVerificationService;
use App\Support\AuthRedirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function __construct(
        private readonly ImageCompressionService $imageCompression,
        private readonly QrVerificationService $qrVerification,
        private readonly QrCodeRenderService $qrRenderer,
    ) {}

    public function show(Request $request, string $uuid): View
    {
        $returnToUrl = AuthRedirect::rememberScannedItemUrl($request, $uuid);
        $user = $request->user();
        $item = Item::query()
            ->where('uuid', $uuid)
            ->with(['creator', 'events.author'])
            ->first();

        if (! $item) {
            if (! $user) {
                return view('items.missing-guest', [
                    'uuid' => $uuid,
                    'loginUrl' => route('login', ['returnTo' => $returnToUrl], false),
                ]);
            }

            return view('items.missing', [
                'uuid' => $uuid,
                'canInitialize' => (bool) $user->email_verified_at,
            ]);
        }

        if (! $user) {
            $itemUrl = route('items.show', ['uuid' => $item->uuid], true);

            return view('items.public', [
                'item' => $item,
                'itemUrl' => $itemUrl,
                'qrSvg' => $this->qrRenderer->renderSvg($itemUrl, 280),
                'loginUrl' => route('login', ['returnTo' => $returnToUrl], false),
            ]);
        }

        $itemUrl = route('items.show', ['uuid' => $item->uuid], true);

        return view('items.show', [
            'item' => $item,
            'itemUrl' => $itemUrl,
            'qrSvg' => $this->qrRenderer->renderSvg($itemUrl, 280),
            'isAuthenticated' => true,
            'canPost' => (bool) $user->email_verified_at,
        ]);
    }

    public function initialize(Request $request, string $uuid): RedirectResponse
    {
        $item = Item::query()->where('uuid', $uuid)->first();

        if ($item) {
            return redirect()->route('items.show', ['uuid' => $uuid]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        Item::query()->create([
            'uuid' => $uuid,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('items.show', ['uuid' => $uuid])
            ->with('status', 'Object initialized.')
            ->with('statusType', 'notice');
    }

    public function addPhoto(Request $request, string $uuid): RedirectResponse
    {
        $item = Item::query()
            ->where('uuid', $uuid)
            ->firstOrFail();

        $validated = $request->validate([
            'photo' => ['required', 'file', 'mimetypes:image/jpeg,image/jpg,image/png,image/webp,image/heic,image/heif,image/heic-sequence,image/heif-sequence,image/gif', 'max:30720'],
        ]);

        try {
            $relativePath = $this->imageCompression->compressAndStore($validated['photo'], $uuid);
        } catch (\Throwable $exception) {
            logger()->warning('Image compression failed, storing original upload.', [
                'uuid' => $uuid,
                'error' => $exception->getMessage(),
            ]);

            $relativePath = $validated['photo']->store('items/'.$uuid, 'public');
        }

        $absolutePath = storage_path('app/public/'.$relativePath);
        $isQrVerified = false;

        if (is_file($absolutePath)) {
            try {
                $isQrVerified = $this->qrVerification->verifyImageContainsUuid($absolutePath, $uuid);
            } catch (\Throwable $exception) {
                logger()->warning('QR verification failed for uploaded photo.', [
                    'uuid' => $uuid,
                    'image_path' => $relativePath,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        ItemEvent::query()->create([
            'item_id' => $item->id,
            'user_id' => $request->user()->id,
            'image_path' => $relativePath,
            'is_qr_verified' => $isQrVerified,
        ]);

        $status = $isQrVerified
            ? 'Photo added and QR verified.'
            : 'Photo added, but QR could not be verified. Flagged for review.';

        return redirect()
            ->route('items.show', ['uuid' => $uuid])
            ->with('status', $status)
            ->with('statusType', $isQrVerified ? 'notice' : 'critical');
    }

    public function print(Request $request, string $uuid): View
    {
        $item = Item::query()
            ->where('uuid', $uuid)
            ->firstOrFail();
        $itemUrl = route('items.show', ['uuid' => $item->uuid], true);

        return view('items.print', [
            'item' => $item,
            'itemUrl' => $itemUrl,
            'qrSvg' => $this->qrRenderer->renderSvg($itemUrl, 280),
            'isAuthenticated' => (bool) $request->user(),
        ]);
    }
}
