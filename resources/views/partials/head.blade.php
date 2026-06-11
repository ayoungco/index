<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

@if ($siteSettings['theme_is_default'])
    <script>document.documentElement.dataset.theme = 'system';</script>
@else
    <style>
        :root {
            --app-primary: {{ $siteSettings['primary_color'] }};
            --app-background: {{ $siteSettings['background_color'] }};
            --app-highlight: {{ $siteSettings['highlight_color'] }};
        }
    </style>
@endif

@vite(['resources/css/app.css', 'resources/js/app.js'])
