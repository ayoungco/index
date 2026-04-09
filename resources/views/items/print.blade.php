<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $siteSettings['label_name'] }} - {{ $item->uuid }}</title>
    <style>
        :root {
            --ink: #000;
            --paper: #fff;
            --accent: #ff4f00;
        }

        body {
            margin: 0;
            font-family: "Courier New", Courier, monospace;
            background: #f5f5f5;
            color: var(--ink);
        }

        .actions {
            padding: 12px;
            border-bottom: 1px solid var(--ink);
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .actions button,
        .actions a {
            border: 1px solid var(--ink);
            padding: 8px 12px;
            color: var(--ink);
            text-decoration: none;
            background: var(--paper);
            cursor: pointer;
            font: inherit;
        }

        .actions button:hover,
        .actions a:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .sheet {
            padding: 12px;
            display: grid;
            justify-content: center;
        }

        .label {
            width: 4in;
            min-height: 6in;
            box-sizing: border-box;
            border: 2px solid var(--ink);
            background: var(--paper);
            margin: 0;
            padding: 0.18in;
        }

        .top {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.12in;
        }

        .square {
            aspect-ratio: 1 / 1;
            border: 2px solid var(--ink);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.12in;
            box-sizing: border-box;
        }

        .logo {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .qr svg,
        .qr img {
            width: 100%;
            height: 100%;
            display: block;
        }

        .url {
            margin: 0.16in 0 0;
            font-size: 12px;
            word-break: break-all;
        }

        .meta h1 {
            margin: 0.12in 0 0;
            font-size: 17px;
            line-height: 1.25;
            word-break: break-word;
        }

        .flavor {
            margin: 0.12in 0 0;
            border-top: 1px solid var(--ink);
            padding-top: 0.12in;
            font-size: 13px;
            line-height: 1.4;
            white-space: pre-wrap;
        }

        @media print {
            .actions {
                display: none;
            }

            .sheet {
                padding: 0;
            }

            .label {
                border-width: 1px;
                width: 4in;
                min-height: 6in;
            }

            @page {
                size: 4in 6in portrait;
                margin: 0;
            }
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
                <img src="{{ app(\App\Support\SiteSettings::class)->logoUrl() }}" alt="{{ config('app.name') }} logo" class="logo">
            </div>

        <div class="meta">
            <h1>{{ $item->name }}</h1>
            <div class="uuid">{{ $siteSettings['label_name'] }}</div>
            <div class="uuid">UUID: {{ $item->uuid }}</div>
        </div>

        <div class="flavor">
            {{ $item->description ?: $siteSettings['label_tagline'] }}
        </div>
    </main>
</body>
</html>
