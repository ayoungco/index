<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $item->name }}</title>
    <style>
        body { background: #000; color: #fff; font-family: "Courier New", Courier, monospace; margin: 0; padding: 16px; }
        .panel { max-width: 960px; margin: 0 auto; border: 1px solid #34d399; padding: 16px; }
        .uuid { color: #34d399; }
        .title { margin: 8px 0 0; font-size: 24px; }
        .muted { color: #d4d4d8; }
        .section { margin-top: 20px; }
        .section-title { color: #6ee7b7; font-size: 16px; margin: 0 0 8px; }
        .list { display: grid; gap: 10px; margin: 0; padding: 0; list-style: none; }
        .item { border: 1px solid #3f3f46; padding: 12px; }
        .row { display: flex; justify-content: space-between; gap: 8px; flex-wrap: wrap; }
        .badge { border: 1px solid #a3a3a3; padding: 2px 8px; font-size: 12px; }
        .badge.ok { border-color: #22c55e; color: #34d399; }
        .badge.flag { border-color: #f59e0b; color: #fcd34d; }
        .image-note { margin-top: 10px; border: 1px solid #27272a; padding: 8px; color: #a1a1aa; }
    </style>
</head>
<body>
    <section class="panel">
        <p class="uuid">UUID: {{ $item->uuid }}</p>
        <h1 class="title">{{ $item->name }}</h1>

        @if ($item->description)
            <p class="muted">{{ $item->description }}</p>
        @endif

        <p class="muted">Public view. Log in for full timeline media and posting controls.</p>

        <div class="section">
            <h2 class="section-title">Timeline</h2>

            @if ($item->events->isEmpty())
                <p class="muted">No events yet.</p>
            @else
                <ul class="list">
                    @foreach ($item->events as $event)
                        <li class="item">
                            <div class="row">
                                <p>{{ $event->created_at?->toDateTimeString() }}</p>
                                <span class="badge {{ $event->is_qr_verified ? 'ok' : 'flag' }}">
                                    {{ $event->is_qr_verified ? 'QR verified' : 'QR flagged' }}
                                </span>
                            </div>
                            <p class="muted">Posted by: {{ $event->author?->name ?? 'Unknown user' }}</p>

                            @if ($event->image_path)
                                <div class="image-note">
                                    Image attached. Log in for full-resolution timeline images.
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>
</body>
</html>
