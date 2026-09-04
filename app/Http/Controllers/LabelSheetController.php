<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Services\QrCodeRenderService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LabelSheetController extends Controller
{
    public function __construct(private readonly QrCodeRenderService $qrRenderer) {}

    public function create(): View
    {
        return view('labels.create');
    }

    public function print(Request $request): View
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:30'],
            'media_width' => ['required', 'numeric', 'min:1', 'max:24'],
            'media_height' => ['required', 'numeric', 'min:1', 'max:36'],
            'columns' => ['required', 'integer', 'min:1', 'max:10'],
            'rows' => ['required', 'integer', 'min:1', 'max:15'],
        ]);

        if ($validated['quantity'] > $validated['columns'] * $validated['rows']) {
            throw ValidationException::withMessages(['quantity' => 'Labels to generate cannot exceed the selected grid.']);
        }

        $labels = collect(range(1, $validated['quantity']))
            ->map(function (): array {
                $uuid = $this->uniqueUuid();
                $url = route('items.show', ['uuid' => $uuid], true);

                return [
                    'uuid' => $uuid,
                    'url' => $url,
                    'qrSvg' => $this->qrRenderer->renderSvg($url, 160),
                ];
            });

        return view('labels.print', compact('labels', 'validated'));
    }

    private function uniqueUuid(): string
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $uuid = (string) Str::uuid();

            if (! Item::query()->where('uuid', $uuid)->exists()) {
                return $uuid;
            }
        }

        throw ValidationException::withMessages([
            'quantity' => 'Unable to reserve unique label URLs. Please try again.',
        ]);
    }
}
