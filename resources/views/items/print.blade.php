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

        @media print {
            @page {
                size: 4in 6in portrait;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    @include('items.partials.label', ['item' => $item, 'qrSvg' => $qrSvg])
</body>
</html>
