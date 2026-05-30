<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body>
    <main class="app-shell text-sm">
        <section class="app-panel max-w-3xl">
            <p class="app-accent">[SCAN TARGET: {{ $uuid }}]</p>
            <h1 class="app-title mt-2">Object Not Initialized</h1>
            <p class="app-muted mt-3">This UUID is unclaimed. {{ $siteSettings['scanner_tagline'] }}</p>
            <a href="{{ $loginUrl }}" class="app-btn app-btn mt-4">Login With Auth0</a>
        </section>
    </main>
</body>
</html>
