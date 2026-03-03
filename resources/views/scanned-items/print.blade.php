<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Label - {{ $item->uuid }}</title>
    <style>
        body {
            margin: 0;
            font-family: "Courier New", Courier, monospace;
            background: #fff;
            color: #000;
        }

        .actions {
            padding: 12px;
            border-bottom: 1px solid #000;
        }

        .label {
            width: 6in;
            height: 4in;
            box-sizing: border-box;
            border: 2px solid #000;
            margin: 12px auto;
            padding: 0.2in;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 0.2in;
            align-items: center;
        }

        .meta h1 {
            margin: 0;
            font-size: 20px;
            line-height: 1.2;
            word-break: break-word;
        }

        .uuid {
            margin-top: 8px;
            font-size: 13px;
            word-break: break-all;
        }

        .qr svg,
        .qr img {
            width: 2.3in;
            height: 2.3in;
            display: block;
        }

        @media print {
            .actions { display: none; }
            .label { margin: 0; border-width: 1px; }
            @page { size: 6in 4in; margin: 0.1in; }
        }
    </style>
</head>
<body>
    <div class="actions">
        <button onclick="window.print()">Print 6x4 Label</button>
        <a href="{{ route('scanned-items.show', ['uuid' => $item->uuid]) }}">Back</a>
    </div>

    <div class="label">
        <div class="meta">
            <h1>{{ $item->name }}</h1>
            <div class="uuid">UUID: {{ $item->uuid }}</div>
        </div>
        <div class="qr">{!! $qrSvg !!}</div>
    </div>
</body>
</html>
