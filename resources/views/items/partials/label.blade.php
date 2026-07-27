@once
    <style>
        .index-min-label {
            display: inline-block;
            width: min(100%, 4in);
            margin: 0;
            padding: 0;
            background: #fff;
            color: #000;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Courier New", monospace;
            line-height: 1;
        }

        .index-min-label__top {
            display: grid;
            gap: 0;
            margin: 0;
            padding: 0;
        }

        .index-min-label--vertical .index-min-label__top {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .index-min-label--horizontal .index-min-label__top {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .index-min-label--compact,
        .index-min-label--qr {
            width: min(100%, 2.25in);
        }

        .index-min-label--compact .index-min-label__top,
        .index-min-label--qr .index-min-label__top {
            grid-template-columns: minmax(0, 1fr);
        }

        .index-min-label__logo,
        .index-min-label__qr,
        .index-min-label__latest {
            display: block;
            width: 100%;
            aspect-ratio: 1 / 1;
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background: #fff;
        }

        .index-min-label__logo {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.2in;
        }

        .index-min-label__qr svg,
        .index-min-label__qr img,
        .index-min-label__latest img {
            display: block;
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            object-fit: contain;
        }

        .index-min-label__logo-mark {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
            --logo-mark-fill: #fff;
            --logo-mark-ink: #000;
        }

        .index-min-label__identity {
            display: block;
            margin: 0;
            padding: 0.08in 0.1in;
            background: #000;
        }

        .index-min-label__title {
            color: #fff;
            font-size: clamp(1rem, 7vw, 1.65rem);
            font-weight: 800;
            line-height: 1.05;
            overflow-wrap: anywhere;
            text-transform: uppercase;
        }

        .index-min-label__subtitle {
            margin-top: 0.04in;
            color: #aaa;
            font-size: clamp(0.65rem, 3vw, 0.85rem);
            font-weight: 700;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .index-min-label--compact .index-min-label__identity {
            padding: 0.08in 0.1in 0.1in;
            text-align: center;
        }

        .index-min-label--compact .index-min-label__title {
            font-size: clamp(0.75rem, 6vw, 1rem);
        }

        .index-min-label--qr {
            width: min(100%, 2in);
            padding: 0.08in;
            box-sizing: border-box;
        }

        .index-min-label--qr .index-min-label__qr {
            aspect-ratio: 1 / 1;
        }

        .index-min-label__prompt {
            display: block;
            padding: 0.04in 0 0.02in;
            color: #000;
            font-size: 0.16in;
            font-weight: 800;
            letter-spacing: 0.08em;
            line-height: 1;
            text-align: center;
            text-transform: uppercase;
        }

        .index-min-label__latest img {
            object-fit: cover;
        }

        .index-min-label__placeholder {
            display: flex;
            width: 100%;
            height: 100%;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 0.6in;
            font-weight: 700;
            line-height: 1;
        }
    </style>
@endonce

@php
    $layout = in_array($layout ?? 'vertical', ['vertical', 'horizontal', 'compact', 'qr'], true)
        ? ($layout ?? 'vertical')
        : 'vertical';
    $latestImageUrl = null;

    if (in_array($layout, ['vertical', 'horizontal'], true)) {
        $latestImagePath = $item->relationLoaded('events')
            ? $item->events->firstWhere('image_path')?->image_path
            : $item->events()->whereNotNull('image_path')->latest()->value('image_path');
        $latestImageUrl = $latestImagePath
            ? route('media.show', ['path' => $latestImagePath])
            : null;
    }
@endphp

<div class="index-min-label index-min-label--{{ $layout }}" aria-label="Index QR label for {{ $item->name }}">
    @if ($layout === 'qr')
        <span class="index-min-label__qr">
            {!! $qrSvg !!}
        </span>
        <span class="index-min-label__prompt">Scan me</span>
    @elseif ($layout === 'compact')
        <div class="index-min-label__top">
            <span class="index-min-label__qr">
                {!! $qrSvg !!}
            </span>
        </div>
        <div class="index-min-label__identity">
            <div class="index-min-label__title">{{ $item->name }}</div>
        </div>
    @else
        <div class="index-min-label__top">
            @if ($layout === 'vertical')
                <span class="index-min-label__logo">
                    <x-app-logo-mark class="index-min-label__logo-mark" />
                </span>
                <span class="index-min-label__qr">
                    {!! $qrSvg !!}
                </span>
            @else
                <span class="index-min-label__qr">
                    {!! $qrSvg !!}
                </span>
                <span class="index-min-label__logo">
                    <x-app-logo-mark class="index-min-label__logo-mark" />
                </span>
                <span class="index-min-label__latest">
                    @if ($latestImageUrl)
                        <img src="{{ $latestImageUrl }}" alt="Latest image for {{ $item->name }}" loading="lazy" decoding="async">
                    @else
                        <span class="index-min-label__placeholder" aria-label="No photo available">?</span>
                    @endif
                </span>
            @endif
        </div>
        <div class="index-min-label__identity">
            <div class="index-min-label__title">{{ $item->name }}</div>
            <div class="index-min-label__subtitle">{{ $item->typeLabel() }}</div>
        </div>
        @if ($layout === 'vertical' && $latestImageUrl)
            <span class="index-min-label__latest">
                <img src="{{ $latestImageUrl }}" alt="Latest image for {{ $item->name }}" loading="lazy" decoding="async">
            </span>
        @endif
    @endif
</div>
