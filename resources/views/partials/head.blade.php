<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=ibm-plex-mono:400,500,600,700" rel="stylesheet" />

<script>
    (() => {
        let stored = null;

        try {
            stored = localStorage.getItem('index_theme_preference') || localStorage.getItem('flux.appearance');
        } catch (error) {
            stored = null;
        }

        const theme = stored === 'paper' || stored === 'light'
            ? 'paper'
            : stored === 'amber'
                ? 'amber'
                : 'crt';

        document.documentElement.classList.toggle('dark', theme !== 'paper');
        document.documentElement.dataset.theme = theme;
    })();
</script>
@vite(['resources/css/app.css', 'resources/js/app.js'])
