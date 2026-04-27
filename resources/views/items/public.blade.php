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
                <p class="terminal-accent text-xs uppercase tracking-[0.2em]">Temporary Label</p>

                <div class="terminal-label-grid max-w-xl">
                    <div class="terminal-label-square">
                        <img src="{{ asset('index-150x150.png') }}" alt="Index square logo">
                    </div>
                    <div class="terminal-label-square">
                        {!! $qrSvg !!}
                    </div>
                </div>

                <div>
                    <p class="break-all text-xs">{{ $itemUrl }}</p>
                    <p class="terminal-accent mt-2">UUID: {{ $item->uuid }}</p>
                    <h1 class="terminal-title mt-2">{{ $item->name }}</h1>

                    @if ($item->description)
                        <p class="terminal-muted mt-2">{{ $item->description }}</p>
                    @endif
                </div>
            </div>

            <p class="terminal-muted mt-3">Public view. Log in for full timeline media and posting controls.</p>
            <a href="{{ $loginUrl }}" class="terminal-btn terminal-btn-accent mt-4">Login With Auth0</a>

            <div class="mt-6">
                <h2 class="terminal-accent text-base font-semibold">Timeline</h2>

                @if ($item->events->isEmpty())
                    <p class="terminal-muted mt-2">No events yet.</p>
                @else
                    <ul class="mt-3 grid gap-3">
                        @foreach ($item->events as $event)
                            <li class="terminal-divider border p-3">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p>{{ $event->created_at?->toDateTimeString() }}</p>
                                    <span class="terminal-chip {{ $event->is_qr_verified ? 'terminal-chip-highlight' : 'terminal-chip-critical' }}">
                                        {{ $event->is_qr_verified ? 'QR verified' : 'QR flagged' }}
                                    </span>
                                </div>
                                <p class="terminal-muted mt-1">Posted by: {{ $event->author?->name ?? 'Unknown user' }}</p>

                                @if ($event->image_path)
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
