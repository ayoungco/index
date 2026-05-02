<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body>
    <main class="terminal-shell text-sm">
        <div class="mb-3 flex justify-end">
            <x-theme-toggle />
        </div>

        <section class="terminal-panel">
            <div class="terminal-divider grid gap-4 border-b pb-4">
                @include('items.partials.label', ['item' => $item, 'qrSvg' => $qrSvg])

                <p class="break-all text-xs">{{ $itemUrl }}</p>

                @if ($item->description)
                    <p class="terminal-muted">{{ $item->description }}</p>
                @endif
            </div>

            <p class="terminal-muted mt-3">Public view. Log in for full timeline media and posting controls.</p>
            <a href="{{ $loginUrl }}" class="terminal-btn terminal-btn-accent mt-4">Login With Auth0</a>

            <div class="mt-6">
                <h2 class="terminal-accent text-base font-semibold">Timeline</h2>

                @if ($timeline->isEmpty())
                    <p class="terminal-muted mt-2">No events yet.</p>
                @else
                    <ul class="compose-log mt-3" aria-label="Timeline log">
                        @foreach ($timeline as $event)
                            @php
                                $source = $event['type'] === 'photo'
                                    ? ($event['is_qr_verified'] ? 'camera' : 'camera!')
                                    : 'index';
                                $sourceClass = $event['type'] === 'photo'
                                    ? ($event['is_qr_verified'] ? 'compose-log__source--camera' : 'compose-log__source--alert')
                                    : 'compose-log__source--index';
                                $message = trim(collect([
                                    $event['title'],
                                    $event['actor'] ? 'by '.$event['actor'] : null,
                                    $event['comment'] ?: null,
                                    $event['image_path'] ? '[image attached]' : null,
                                ])->filter()->implode(' | '));
                            @endphp
                            <li class="compose-log__line">
                                <span class="compose-log__source {{ $sourceClass }}">{{ $source }}</span>
                                <span class="compose-log__pipe">|</span>
                                <time class="compose-log__time" datetime="{{ $event['occurred_at']?->toIso8601String() }}">
                                    {{ $event['occurred_at']?->format('Y-m-d H:i:s') }}
                                </time>
                                <span class="compose-log__message">{{ $message }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </section>
    </main>
</body>
</html>
