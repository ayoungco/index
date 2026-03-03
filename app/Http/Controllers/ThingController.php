<?php

namespace App\Http\Controllers;

use App\Models\Thing;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ThingController extends Controller
{
    /**
     * Display a public view of a thing by slug.
     */
    public function showBySlug(Request $request, string $slug)
    {
        $decodedSlug = urldecode($slug);

        $thing = Thing::query()
            ->where('slug', $decodedSlug)
            ->first();

        if (! $thing && $decodedSlug !== $slug) {
            $thing = Thing::query()
                ->where('slug', $slug)
                ->first();
        }

        if (! $thing) {
            abort(404);
        }

        if ($request->user()) {
            $this->startOrRefreshActiveScan($request, $thing);
        }

        return $this->renderThingPage($request, $thing, ['requestedSlug' => $decodedSlug]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $things = Thing::all();
        return view('things.index', compact('things'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('things.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Validation rules
        ]);
        $thing = Thing::create($validated);
        return redirect()->route('things.show', $thing);
    }

    /**
     * Display the specified resource.
     */
    public function show(Thing $thing)
    {
        return $this->renderThingPage(request(), $thing);
    }

    public function storeScanPhoto(Request $request, Thing $thing)
    {
        $activeScan = $request->session()->get('active_thing_scan');

        if (! is_array($activeScan) || (int) ($activeScan['thing_id'] ?? 0) !== (int) $thing->id) {
            return redirect()
                ->route('things.show', $thing)
                ->with('status', 'No active scan session found for this Thing.');
        }

        $validated = $request->validate([
            'photo' => ['required', 'image', 'max:10240'],
        ]);

        $storedPath = $validated['photo']->store('thing-scans', 'public');

        $this->appendThingSessionHistory($request, $thing->id, [
            'type' => 'scan.photo_added',
            'title' => 'QR photo captured',
            'description' => 'A photo was attached right after scanning this QR.',
            'occurred_at' => now()->toIso8601String(),
            'scan_id' => $activeScan['scan_id'] ?? null,
            'photo_url' => Storage::disk('public')->url($storedPath),
            'photo_path' => $storedPath,
        ]);

        $activeScan['photo_captured'] = true;
        $activeScan['photo_captured_at'] = now()->toIso8601String();
        $request->session()->put('active_thing_scan', $activeScan);

        return redirect()
            ->route('things.show', $thing)
            ->with('status', 'Photo uploaded to this Thing timeline.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Thing $thing)
    {
        return view('things.edit', compact('thing'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Thing $thing)
    {
        $validated = $request->validate([
            // Validation rules
        ]);
        $thing->update($validated);
        return redirect()->route('things.show', $thing);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Thing $thing)
    {
        $thing->delete();
        return redirect()->route('things.index');
    }

    protected function renderThingPage(Request $request, Thing $thing, array $extra = [])
    {
        $sessionHistory = $request->session()->get("thing_history.{$thing->id}", []);
        $activeScan = $request->session()->get('active_thing_scan');
        $hasActiveScan = is_array($activeScan) && (int) ($activeScan['thing_id'] ?? 0) === (int) $thing->id;
        $needsScanPhoto = $request->user() && $hasActiveScan && ! (bool) ($activeScan['photo_captured'] ?? false);

        return view('things.show', array_merge($extra, [
            'thing' => $thing,
            'status' => $request->session()->get('status'),
            'timeline' => $thing->timeline(is_array($sessionHistory) ? $sessionHistory : []),
            'needsScanPhoto' => $needsScanPhoto,
        ]));
    }

    protected function startOrRefreshActiveScan(Request $request, Thing $thing): void
    {
        $current = $request->session()->get('active_thing_scan');
        $scanChanged = ! is_array($current) || (int) ($current['thing_id'] ?? 0) !== (int) $thing->id;

        if ($scanChanged) {
            $current = [
                'scan_id' => (string) Str::uuid(),
                'thing_id' => $thing->id,
                'slug' => $thing->slug,
                'scanned_at' => now()->toIso8601String(),
                'photo_captured' => false,
            ];

            $this->appendThingSessionHistory($request, $thing->id, [
                'type' => 'scan.detected',
                'title' => 'QR scanned',
                'description' => 'This Thing was opened from a scanned QR.',
                'occurred_at' => $current['scanned_at'],
                'scan_id' => $current['scan_id'],
            ]);
        } else {
            $current['last_seen_at'] = now()->toIso8601String();
        }

        $request->session()->put('active_thing_scan', $current);
    }

    /**
     * @param  array<string, mixed>  $entry
     */
    protected function appendThingSessionHistory(Request $request, int $thingId, array $entry): void
    {
        $key = "thing_history.{$thingId}";
        $history = $request->session()->get($key, []);
        $history = is_array($history) ? $history : [];
        $history[] = $entry;

        usort($history, function (array $left, array $right) {
            $leftAt = Carbon::parse($left['occurred_at'] ?? now())->getTimestamp();
            $rightAt = Carbon::parse($right['occurred_at'] ?? now())->getTimestamp();

            return $leftAt <=> $rightAt;
        });

        $request->session()->put($key, array_slice($history, -50));
    }
}
