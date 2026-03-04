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
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .actions button,
        .actions a {
            border: 1px solid #000;
            padding: 8px 12px;
            color: #000;
            text-decoration: none;
            background: #fff;
            cursor: pointer;
            font: inherit;
        }

        .label {
            width: 4in;
            height: 6in;
            box-sizing: border-box;
            border: 2px solid #000;
            margin: 12px auto;
            padding: 0.16in;
            display: grid;
            grid-template-rows: auto auto 1fr;
            gap: 0.12in;
        }

        .top {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 0.1in;
            align-items: start;
        }

        .logo-wrap {
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
        }

        .logo {
            width: 1.6in;
            max-width: 100%;
            height: auto;
            object-fit: contain;
        }

        .qr svg,
        .qr img {
            width: 1.7in;
            height: 1.7in;
            display: block;
        }

        .meta h1 {
            margin: 0;
            font-size: 18px;
            line-height: 1.2;
            word-break: break-word;
        }

        .uuid {
            margin-top: 8px;
            font-size: 12px;
            word-break: break-all;
        }

        .flavor {
            border-top: 1px solid #000;
            padding-top: 10px;
            font-size: 12px;
            line-height: 1.45;
            white-space: pre-wrap;
            overflow: hidden;
        }

        @media print {
            .actions { display: none; }
            .label { margin: 0; border-width: 1px; }
            @page { size: 4in 6in; margin: 0.05in; }
        }
    </style>
</head>
<body>
    <div class="actions">
        <button onclick="window.print()">Print Portrait Label (6x4)</button>
        <a href="{{ route('items.show', ['uuid' => $item->uuid]) }}">Back</a>
    </div>

    <div class="label">
        <div class="top">
            <div class="logo-wrap">
                <img src="{{ asset('index-triangle-red.png') }}" alt="Index triangle logo" class="logo">
            </div>
            <div class="qr">{!! $qrSvg !!}</div>
        </div>

        <div class="meta">
            <h1>{{ $item->name }}</h1>
            <div class="uuid">UUID: {{ $item->uuid }}</div>
        </div>

        <div class="flavor">
            {{ $item->description ?: 'One trusted source. Scan to access the canonical item record and timeline.' }}
        </div>
    </div>
</body>
</html>
