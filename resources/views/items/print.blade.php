<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $siteSettings['label_name'] }} - {{ $item->name }} - {{ ucfirst($layout) }}</title>
    <style>
        :root {
            color-scheme: light dark;
        }

        body {
            display: grid;
            min-height: 100svh;
            place-items: center;
            box-sizing: border-box;
            margin: 0;
            padding: 1rem;
            background: #fff;
        }

        @media (prefers-color-scheme: dark) {
            body {
                background: #000;
            }
        }

        .print-label--horizontal .index-min-label {
            width: min(100%, 6in);
        }

        .print-label--compact .index-min-label,
        .print-label--qr .index-min-label {
            width: min(100%, 28rem);
        }

        @media print {
            @page {
                margin: 0;
            }

            body {
                display: block;
                min-height: 0;
                padding: 0;
                background: #fff;
            }

            .print-label--vertical {
                page: vertical-label;
            }

            .print-label--horizontal {
                page: horizontal-label;
            }

            .print-label--compact {
                page: compact-label;
            }

            .print-label--qr {
                page: qr-label;
            }

            @page vertical-label {
                size: 4in 7in portrait;
                margin: 0;
            }

            @page horizontal-label {
                size: 6in 4in landscape;
                margin: 0;
            }

            @page compact-label {
                size: 2.25in 2.75in portrait;
                margin: 0;
            }

            @page qr-label {
                size: 2in 2.25in portrait;
                margin: 0;
            }

            .print-label--compact .index-min-label {
                width: 2.25in;
            }

            .print-label--qr .index-min-label {
                width: 2in;
            }
        }
    </style>
</head>
<body class="print-label--{{ $layout }}">
    @include('items.partials.label', ['item' => $item, 'qrSvg' => $qrSvg, 'layout' => $layout])
</body>
</html>
