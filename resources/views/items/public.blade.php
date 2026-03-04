<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body>
    <main class="terminal-shell text-sm">
        <div class="mb-3 flex justify-end">
            <button type="button" data-theme-toggle class="terminal-btn terminal-btn-accent text-xs">Switch to Dark</button>
        </div>

        <section class="terminal-panel">
            <p class="terminal-accent">UUID: {{ $item->uuid }}</p>
            <h1 class="terminal-title mt-2">{{ $item->name }}</h1>

            @if ($item->description)
                <p class="terminal-muted mt-2">{{ $item->description }}</p>
            @endif

            <p class="terminal-muted mt-3">Public view. Log in for full timeline media and posting controls.</p>

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
                                    <span class="border px-2 py-0.5 text-xs {{ $event->is_qr_verified ? 'border-emerald-500 text-emerald-500' : 'border-amber-500 text-amber-500' }}">
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
