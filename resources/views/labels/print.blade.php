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
        .sheet { display: grid; grid-template-columns: repeat({{ $validated['columns'] }}, minmax(0, 1fr)); grid-template-rows: repeat({{ $validated['rows'] }}, minmax(0, 1fr)); width: {{ $validated['media_width'] }}in; height: {{ $validated['media_height'] }}in; }
        .label { display: grid; grid-template-rows: minmax(0, 1fr) auto minmax(.18in, .35fr); min-width: 0; min-height: 0; overflow: hidden; border: 1px dotted #777; padding: .08in; }
        .label__qr { display: block; align-self: start; aspect-ratio: 1; max-height: 100%; max-width: 100%; width: 100%; }
        .label__qr svg { display: block; height: 100%; max-height: 100%; max-width: 100%; width: 100%; }
        .label__uuid { flex: 0 0 auto; margin-top: .05in; font-size: clamp(5pt, 1.8vw, 8pt); line-height: 1.1; letter-spacing: -.04em; overflow-wrap: anywhere; text-align: center; }
        .label__brand { display: flex; flex: 0 1 30%; min-height: .18in; align-items: center; justify-content: center; padding-top: .06in; }
        .label__brand svg { width: min(70%, 1.1in); max-height: 100%; }
        @media print {
            @page { size: {{ $validated['media_width'] }}in {{ $validated['media_height'] }}in; margin: 0; }
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
                <span class="label__uuid">{{ $label['uuid'] }}</span>
                <span class="label__brand" aria-label="index">@include('components.app-logo-mark')</span>
            </article>
        @endforeach
    </main>
</body>
</html>
