<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => 'AYOUNGCO, LLC'])
    </head>
    <body>
        <main class="terminal-shell flex min-h-screen items-center">
            <section class="terminal-panel w-full">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="terminal-divider inline-flex h-16 w-16 items-center justify-center rounded-full border text-xl font-bold tracking-widest">
                            AY
                        </span>
                        <p class="text-xs uppercase tracking-[0.3em] text-zinc-400">AYOUNGCO, LLC</p>
                    </div>
                    <a class="terminal-btn" href="{{ route('home') }}">Back to Main</a>
                </div>

                <h1 class="terminal-title mt-6">AYOUNGCO, LLC</h1>
                <p class="terminal-muted mt-3 max-w-2xl">
                    AYOUNGCO, LLC is the company page for Adam Young. This space can host company profile details, project highlights,
                    and contact information.
                </p>

                <div class="mt-6 flex flex-wrap gap-2">
                    <a class="terminal-btn terminal-btn-accent" href="mailto:hello@ayoungco.com">Contact</a>
                    <a class="terminal-btn" href="{{ route('home') }}">Return Home</a>
                </div>
            </section>
        </main>
    </body>
</html>
