<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR label sheet</title>
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #fff; color: #000; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; }
        .controls { padding: 1rem; text-align: center; }
        button { border: 1px solid #000; border-radius: 0; padding: .5rem .75rem; background: #000; color: #fff; font: inherit; cursor: pointer; }
        .sheet { display: grid; grid-template-columns: repeat(3, 2.625in); grid-auto-rows: 1in; width: 8.5in; min-height: 11in; padding: .5in .3125in; }
        .label { display: grid; grid-template-columns: .82in 1fr; align-items: center; gap: .08in; overflow: hidden; padding: .06in; }
        .label__qr, .label__qr svg { display: block; width: .78in; height: .78in; }
        .label__url { font-size: 7pt; line-height: 1.25; overflow-wrap: anywhere; }
        .label__uuid { margin-top: .04in; color: #444; font-size: 5.5pt; line-height: 1.2; }
        @media print {
            @page { size: letter portrait; margin: 0; }
            .controls { display: none; }
        }
    </style>
</head>
<body>
    <div class="controls"><button type="button" onclick="window.print()">Print</button></div>
    <main class="sheet" aria-label="Printable QR labels">
        @foreach ($labels as $label)
            <article class="label">
                <span class="label__qr">{!! $label['qrSvg'] !!}</span>
                <span>
                    <span class="label__url">{{ $label['url'] }}</span>
                    <span class="label__uuid">{{ $label['uuid'] }}</span>
                </span>
            </article>
        @endforeach
    </main>
</body>
</html>
