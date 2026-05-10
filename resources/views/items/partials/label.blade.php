@once
    <style>
        .index-min-label {
            display: inline-block;
            width: min(100%, 4in);
            margin: 0;
            padding: 0;
            background: #fff;
            color: #000;
            font-family: "IBM Plex Mono", "Courier New", monospace;
            line-height: 1;
        }

        .index-min-label__top {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            margin: 0;
            padding: 0;
        }

        .index-min-label__logo,
        .index-min-label__qr {
            display: block;
            width: 100%;
            aspect-ratio: 1 / 1;
            margin: 0;
            padding: 0;
        }

        .index-min-label__logo img,
        .index-min-label__qr svg,
        .index-min-label__qr img {
            display: block;
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            object-fit: contain;
        }

        .index-min-label__title {
            display: block;
            margin: 0;
            padding: 0.08in 0.1in;
            background: #000;
            color: #fff;
            font-size: clamp(1rem, 7vw, 1.65rem);
            font-weight: 800;
            line-height: 1.05;
            overflow-wrap: anywhere;
            text-transform: uppercase;
        }

        .index-min-label__latest {
            display: block;
            width: 100%;
            aspect-ratio: 1 / 1;
            margin: 0;
            padding: 0;
            background: #fff;
            overflow: hidden;
        }

        .index-min-label__latest img {
            display: block;
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            object-fit: cover;
        }
    </style>
@endonce

@php
    $latestImagePath = $item->relationLoaded('events')
        ? $item->events->firstWhere('image_path')?->image_path
        : $item->events()->whereNotNull('image_path')->latest()->value('image_path');
    $latestImageUrl = $latestImagePath
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($latestImagePath)
        : null;
@endphp

<div class="index-min-label" aria-label="Index QR label for {{ $item->name }}">
    <div class="index-min-label__top">
        <span class="index-min-label__logo">
            <img src="{{ asset('index-150x150.png') }}" alt="Index logo">
        </span>
        <span class="index-min-label__qr">
            {!! $qrSvg !!}
        </span>
    </div>
    <div class="index-min-label__title">{{ $item->name }}</div>
    @if ($latestImageUrl)
        <span class="index-min-label__latest">
            <img src="{{ $latestImageUrl }}" alt="Latest image for {{ $item->name }}" loading="lazy" decoding="async">
        </span>
    @endif
</div>
