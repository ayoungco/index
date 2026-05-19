<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemAccess;
use App\Models\ItemEvent;
use App\Services\ImageCompressionService;
use App\Services\QrCodeRenderService;
use App\Services\QrVerificationService;
use App\Support\AuthRedirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
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
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'photo' => ['required', 'file', 'mimetypes:image/jpeg,image/jpg,image/png,image/webp,image/heic,image/heif,image/heic-sequence,image/heif-sequence,image/gif', 'max:30720'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $item = Item::query()->create([
            'uuid' => $uuid,
            'name' => filled($validated['name'] ?? null) ? $validated['name'] : $uuid,
            'description' => $validated['description'] ?? null,
            'user_id' => $request->user()->id,
        ]);

        try {
            $relativePath = $this->imageCompression->compressAndStore($validated['photo'], $uuid);
        } catch (\Throwable $exception) {
            logger()->warning('Image compression failed on init photo, storing original.', [
                'uuid' => $uuid,
                'error' => $exception->getMessage(),
            ]);
            $relativePath = $validated['photo']->store('items/'.$uuid, 'public');
        }

        ItemEvent::query()->create([
            'item_id' => $item->id,
            'user_id' => $request->user()->id,
            'image_path' => $relativePath,
            'comment' => filled($validated['comment'] ?? null) ? $validated['comment'] : null,
            'tags' => null,
            'is_qr_verified' => false,
        ]);

        return redirect()
            ->route('items.show', ['uuid' => $uuid])
            ->with('status', 'Object registered.')
            ->with('statusType', 'notice');
    }

    public function storeFromPhoto(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'photo' => ['required', 'file', 'mimetypes:image/jpeg,image/jpg,image/png,image/webp,image/heic,image/heif,image/heic-sequence,image/heif-sequence,image/gif', 'max:30720'],
                'name' => ['nullable', 'string', 'max:255'],
                'description' => ['nullable', 'string', 'max:5000'],
            ]);

            $uuid = (string) Str::uuid();
            $name = trim((string) ($validated['name'] ?? ''));

            if ($name === '') {
                $name = 'Photo item '.now()->format('Y-m-d H:i');
            }

            $item = Item::query()->create([
                'uuid' => $uuid,
                'name' => $name,
                'description' => $validated['description'] ?? null,
                'user_id' => $request->user()->id,
            ]);

            $relativePath = $this->storePhotoForItem($validated['photo'], $uuid);

            ItemEvent::query()->create([
                'item_id' => $item->id,
                'user_id' => $request->user()->id,
                'image_path' => $relativePath,
                'comment' => $validated['description'] ?? null,
                'tags' => null,
                'is_qr_verified' => false,
            ]);

            return redirect()
                ->route('items.show', ['uuid' => $uuid])
                ->with('status', 'Object created from photo. Print a label when you are ready to attach its QR code.')
                ->with('statusType', 'notice');
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            logger()->error('Failed to create item from photo.', [
                'error' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('dashboard')
                ->withInput($request->except('photo'))
                ->with('status', 'Photo item creation failed due to a temporary connection issue. Please try again.')
                ->with('statusType', 'critical');
        }
    }

    public function addPhoto(Request $request, string $uuid): RedirectResponse
    {
        try {
            $item = Item::query()
                ->where('uuid', $uuid)
                ->firstOrFail();

            $validated = $request->validate([
                'photo' => ['required', 'file', 'mimetypes:image/jpeg,image/jpg,image/png,image/webp,image/heic,image/heif,image/heic-sequence,image/heif-sequence,image/gif', 'max:30720'],
                'comment' => ['nullable', 'string', 'max:2000'],
                'tags' => ['nullable', 'string', 'max:500'],
            ]);

            $relativePath = $this->storePhotoForItem($validated['photo'], $uuid);

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

            $normalizedTags = collect(explode(',', (string) ($validated['tags'] ?? '')))
                ->map(fn (string $tag) => trim($tag))
                ->filter()
                ->map(fn (string $tag) => mb_strtolower($tag))
                ->unique()
                ->values()
                ->all();

            ItemEvent::query()->create([
                'item_id' => $item->id,
                'user_id' => $request->user()->id,
                'image_path' => $relativePath,
                'comment' => $validated['comment'] ?? null,
                'tags' => $normalizedTags === [] ? null : $normalizedTags,
                'is_qr_verified' => $isQrVerified,
            ]);

            return redirect()
                ->route('items.show', ['uuid' => $uuid])
                ->with('status', 'Photo added.')
                ->with('statusType', 'notice');
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

    private function storePhotoForItem(UploadedFile $photo, string $uuid): string
    {
        try {
            return $this->imageCompression->compressAndStore($photo, $uuid);
        } catch (\Throwable $exception) {
            logger()->warning('Image compression failed, storing original upload.', [
                'uuid' => $uuid,
                'error' => $exception->getMessage(),
            ]);

            return $photo->store('items/'.$uuid, 'public');
        }
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
            'tags' => null,
            'image_path' => null,
            'image_url' => null,
            'is_qr_verified' => null,
        ]]);

        $accesses = $item->accesses->map(fn (ItemAccess $access): array => [
            'type' => 'accessed',
            'occurred_at' => $access->created_at,
            'title' => 'Object accessed',
            'actor' => $access->actorLabel(),
            'flag' => $access->countryFlag(),
            'comment' => null,
            'tags' => null,
            'image_path' => null,
            'image_url' => null,
            'is_qr_verified' => null,
        ]);

        $events = $item->events->map(fn (ItemEvent $event): array => [
            'type' => 'photo',
            'occurred_at' => $event->created_at,
            'title' => 'Photo uploaded',
            'actor' => $event->author?->name ?? 'Unknown user',
            'flag' => null,
            'comment' => $event->comment,
            'tags' => $event->tags,
            'image_path' => $event->image_path,
            'image_url' => $event->image_path ? Storage::disk('public')->url($event->image_path) : null,
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
