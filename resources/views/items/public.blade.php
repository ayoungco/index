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
                    <ul class="mt-3 grid gap-3">
                        @foreach ($timeline as $event)
                            <li class="terminal-divider border p-3">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div>
                                        <p>{{ $event['occurred_at']?->toDateTimeString() }}</p>
                                        <p class="terminal-accent mt-1 text-xs uppercase tracking-[0.14em]">{{ $event['title'] }}</p>
                                    </div>
                                    @if ($event['type'] === 'photo')
                                        <span class="terminal-chip {{ $event['is_qr_verified'] ? 'terminal-chip-highlight' : 'terminal-chip-critical' }}">
                                            {{ $event['is_qr_verified'] ? 'QR verified' : 'QR flagged' }}
                                        </span>
                                    @endif
                                </div>
                                <p class="terminal-muted mt-1">
                                    @if ($event['flag'])
                                        <span aria-hidden="true">{{ $event['flag'] }}</span>
                                    @endif
                                    {{ $event['actor'] }}
                                </p>
                                @if ($event['comment'])
                                    <p class="mt-3 whitespace-pre-wrap">{{ $event['comment'] }}</p>
                                @endif

                                @if ($event['image_path'])
                                    <div class="terminal-divider terminal-muted mt-3 border p-2">
                                        Image attached. Log in for full-resolution timeline images.
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </section>
    </main>
</body>
</html>
