<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $siteSettings['label_name'] }} - {{ $item->uuid }}</title>
    <style>
        body {
            margin: 0;
            background: #fff;
        }

        .print-label--horizontal .index-min-label {
            width: min(100%, 6in);
        }

        @media print {
            @page {
                margin: 0;
            }

            .print-label--vertical {
                page: vertical-label;
            }

            .print-label--horizontal {
                page: horizontal-label;
            }

            @page vertical-label {
                size: 4in 7in portrait;
                margin: 0;
            }

            @page horizontal-label {
                size: 6in 4in landscape;
                margin: 0;
            }
        }
    </style>
</head>
<body class="print-label--{{ $layout }}">
    @include('items.partials.label', ['item' => $item, 'qrSvg' => $qrSvg, 'layout' => $layout])
</body>
</html>
