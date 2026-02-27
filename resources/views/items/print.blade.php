<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Print Label {{ $item->uuid }}</title>
    <style>
        @page { size: 6in 4in; margin: 0.2in; }
        body { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; margin: 0; }
        .label { width: 5.6in; height: 3.6in; border: 3px solid #000; padding: 0.2in; display: flex; gap: 0.2in; }
        .meta { flex: 1; }
        .uuid { font-size: 14px; word-break: break-all; }
        .name { font-size: 24px; margin-bottom: 0.2in; }
        img { width: 2in; height: 2in; border: 1px solid #000; }
    </style>
</head>
<body>
<div class="label">
    <div class="meta">
        <div class="name">{{ $item->name }}</div>
        <div class="uuid">{{ $item->uuid }}</div>
        <div>Scan URL: {{ url('/'.$item->uuid) }}</div>
    </div>
    <div>
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=320x320&data={{ urlencode(url('/'.$item->uuid)) }}" alt="QR Code">
    </div>
</div>
</body>
</html>
