<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemAccess;
use App\Models\ItemEvent;
use App\Services\ImageCompressionService;
use App\Services\QrCodeRenderService;
use App\Services\QrVerificationService;
use App\Support\AuthRedirect;
use Illuminate\Support\Collection;
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
            ->with(['creator', 'events.author', 'accesses.user'])
            ->first();

        if (! $item) {
            if (! $user) {
                return view('items.missing-guest', [
                    'uuid' => $uuid,
                    'loginUrl' => route('login', ['returnTo' => $returnToUrl], true),
                ]);
            }

            return view('items.missing', [
                'uuid' => $uuid,
                'canInitialize' => (bool) $user->email_verified_at,
            ]);
        }

        if (! $user) {
            $this->recordAccess($request, $item);
            $item->load(['events.author', 'accesses.user']);

            $itemUrl = route('items.show', ['uuid' => $item->uuid], true);

            return view('items.public', [
                'item' => $item,
                'itemUrl' => $itemUrl,
                'qrSvg' => $this->qrRenderer->renderSvg($itemUrl, 280),
                'timeline' => $this->timelineFor($item),
                'loginUrl' => route('login', ['returnTo' => $returnToUrl], true),
            ]);
        }

        $this->recordAccess($request, $item);
        $item->load(['events.author', 'accesses.user']);

        $itemUrl = route('items.show', ['uuid' => $item->uuid], true);

        return view('items.show', [
            'item' => $item,
            'itemUrl' => $itemUrl,
            'qrSvg' => $this->qrRenderer->renderSvg($itemUrl, 280),
            'isAuthenticated' => true,
            'canPost' => (bool) $user->email_verified_at,
            'timeline' => $this->timelineFor($item),
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
        try {
            $item = Item::query()
                ->where('uuid', $uuid)
                ->firstOrFail();

            $validated = $request->validate([
                'photo' => ['required', 'file', 'mimetypes:image/jpeg,image/jpg,image/png,image/webp,image/heic,image/heif,image/heic-sequence,image/heif-sequence,image/gif', 'max:30720'],
                'comment' => ['nullable', 'string', 'max:1000'],
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
                'comment' => $validated['comment'] ?? null,
                'is_qr_verified' => $isQrVerified,
            ]);

            $status = $isQrVerified
                ? 'Photo added and QR verified.'
                : 'Photo added, but QR could not be verified. Flagged for review.';

            return redirect()
                ->route('items.show', ['uuid' => $uuid])
                ->with('status', $status)
                ->with('statusType', $isQrVerified ? 'notice' : 'critical');
        } catch (\Throwable $exception) {
            logger()->error('Failed to add photo event.', [
                'uuid' => $uuid,
                'error' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('items.show', ['uuid' => $uuid])
                ->with('status', 'Upload failed due to a temporary connection issue. Please try again.')
                ->with('statusType', 'critical');
        }
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

    private function recordAccess(Request $request, Item $item): void
    {
        $countryCode = $this->countryCode($request);

        ItemAccess::query()->create([
            'item_id' => $item->id,
            'user_id' => $request->user()?->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'browser' => $this->browserName((string) $request->userAgent()),
            'city' => $this->cityName($request),
            'country' => $this->countryName($request, $countryCode),
            'country_code' => $countryCode,
        ]);
    }

    private function timelineFor(Item $item): Collection
    {
        $created = collect([[
            'type' => 'created',
            'occurred_at' => $item->created_at,
            'title' => 'Object created',
            'actor' => $item->creator?->name ?? 'Unknown user',
            'flag' => null,
            'comment' => $item->description,
            'image_path' => null,
            'is_qr_verified' => null,
        ]]);

        $accesses = $item->accesses->map(fn (ItemAccess $access): array => [
            'type' => 'accessed',
            'occurred_at' => $access->created_at,
            'title' => 'Object accessed',
            'actor' => $access->actorLabel(),
            'flag' => $access->countryFlag(),
            'comment' => null,
            'image_path' => null,
            'is_qr_verified' => null,
        ]);

        $events = $item->events->map(fn (ItemEvent $event): array => [
            'type' => 'photo',
            'occurred_at' => $event->created_at,
            'title' => 'Photo uploaded',
            'actor' => $event->author?->name ?? 'Unknown user',
            'flag' => null,
            'comment' => $event->comment,
            'image_path' => $event->image_path,
            'is_qr_verified' => $event->is_qr_verified,
        ]);

        return $created
            ->merge($accesses)
            ->merge($events)
            ->sortByDesc('occurred_at')
            ->values();
    }

    private function browserName(string $userAgent): string
    {
        return match (true) {
            str_contains($userAgent, 'CriOS') => 'Chrome on iOS',
            str_contains($userAgent, 'FxiOS') => 'Firefox on iOS',
            str_contains($userAgent, 'EdgiOS') => 'Edge on iOS',
            str_contains($userAgent, 'Edg/') => 'Edge',
            str_contains($userAgent, 'Chrome/') => 'Chrome',
            str_contains($userAgent, 'Firefox/') => 'Firefox',
            str_contains($userAgent, 'Safari/') => 'Safari',
            default => 'unknown browser',
        };
    }

    private function cityName(Request $request): ?string
    {
        $city = $request->headers->get('CF-IPCity')
            ?? $request->headers->get('X-Vercel-IP-City')
            ?? $request->headers->get('CloudFront-Viewer-City');

        return is_string($city) && $city !== '' ? urldecode($city) : null;
    }

    private function countryCode(Request $request): ?string
    {
        $code = $request->headers->get('CF-IPCountry')
            ?? $request->headers->get('X-Vercel-IP-Country')
            ?? $request->headers->get('CloudFront-Viewer-Country');

        $code = strtoupper((string) $code);

        return preg_match('/^[A-Z]{2}$/', $code) ? $code : null;
    }

    private function countryName(Request $request, ?string $countryCode): ?string
    {
        $country = $request->headers->get('X-Vercel-IP-Country-Region')
            ?? $request->headers->get('CloudFront-Viewer-Country-Name');

        if (is_string($country) && $country !== '') {
            return urldecode($country);
        }

        if (! $countryCode) {
            return null;
        }

        if (class_exists(\Locale::class)) {
            return \Locale::getDisplayRegion('-'.$countryCode, 'en') ?: $countryCode;
        }

        return $countryCode;
    }
}
