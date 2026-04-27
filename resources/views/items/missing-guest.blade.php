<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body>
    <main class="terminal-shell text-sm">
        <div class="mb-3 flex justify-end">
            <x-theme-toggle />
        </div>

        <section class="terminal-panel max-w-3xl">
            <p class="terminal-accent">[SCAN TARGET: {{ $uuid }}]</p>
            <h1 class="terminal-title mt-2">Object Not Initialized</h1>
            <p class="terminal-muted mt-3">This UUID is unclaimed. {{ $siteSettings['scanner_tagline'] }}</p>
            <a href="{{ $loginUrl }}" class="terminal-btn terminal-btn-accent mt-4">Login With Auth0</a>
        </section>
    </main>
</body>
</html>
