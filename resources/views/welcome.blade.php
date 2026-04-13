<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => 'Adam Young, digital architect, project manager'])
    </head>
    <body>
        <main class="terminal-shell flex min-h-screen items-center">
            <section class="terminal-panel w-full">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="terminal-divider inline-flex h-16 w-16 items-center justify-center rounded-full border text-xl font-bold tracking-widest">
                            AY
                        </span>
                        <div>
                            <p class="text-xs uppercase tracking-[0.3em] text-zinc-400">ayoungco @ main</p>
                            <p class="text-sm text-zinc-300">Adam Young monogram</p>
                        </div>
                    </div>
                    <button type="button" data-theme-toggle class="terminal-btn terminal-btn-accent text-xs">Switch to Dark</button>
                </div>

                @if (session('status'))
                    <p class="mt-6 border border-emerald-500 bg-emerald-950 p-3 text-emerald-300">{{ session('status') }}</p>
                @endif

                <h1 class="terminal-title mt-6">Adam Young, digital architect, project manager</h1>
                <p class="terminal-muted mt-3 max-w-2xl">
                    {{ $siteSettings['scanner_tagline'] }}
                </p>

                <div class="mt-6 flex flex-wrap gap-2">
                    <a class="terminal-btn terminal-btn-accent" href="{{ route('login') }}">Log in</a>
                    <a class="terminal-btn" href="{{ route('company') }}">AYOUNGCO, LLC</a>
                    @auth
                        <a class="terminal-btn" href="{{ url('/dashboard') }}">Dashboard</a>
                    @else
                        <a class="terminal-btn" href="{{ route('login') }}">Auth0 Access</a>
                    @endauth
                </div>
            </section>
        </main>
    </body>
</html>
