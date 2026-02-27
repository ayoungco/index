<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemEvent;
use App\Services\ItemImageProcessor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function showByUuid(Request $request, string $uuid): View|RedirectResponse
    {
        $item = Item::query()->with(['creator', 'events.user'])->where('uuid', $uuid)->first();

        if (! $item) {
            if (! $request->user()) {
                return view('items.missing-guest', compact('uuid'));
            }

            if (! $request->user()->email_verified_at) {
                return view('items.missing-unverified', compact('uuid'));
            }

            return view('items.initialize', compact('uuid'));
        }

        if ($request->user()) {
            return view('items.show-auth', compact('item'));
        }

        return view('items.show-guest', compact('item'));
    }

    public function storeInitialized(Request $request, string $uuid): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:3000'],
        ]);

        Item::query()->updateOrCreate(
            ['uuid' => $uuid],
            [
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'user_id' => $request->user()->id,
            ]
        );

        return redirect()->route('items.lookup', $uuid)->with('status', 'Object initialized successfully.');
    }

    public function storePhoto(Request $request, string $uuid, ItemImageProcessor $processor): RedirectResponse
    {
        $item = Item::query()->where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'photo' => ['required', 'image', 'max:12288'],
        ]);

        $result = $processor->process($validated['photo'], $uuid);

        ItemEvent::query()->create([
            'scanned_item_id' => $item->id,
            'user_id' => $request->user()->id,
            'image_path' => $result['path'],
            'is_qr_verified' => $result['verified'],
        ]);

        return back()->with('status', $result['verified']
            ? 'Photo added and QR match verified.'
            : 'Photo added, but QR could not be verified and was flagged for review.');
    }

    public function printLabel(string $uuid): View
    {
        $item = Item::query()->where('uuid', $uuid)->firstOrFail();

        return view('items.print', compact('item'));
    }
}
