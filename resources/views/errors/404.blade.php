<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white text-zinc-900">
        @php
            $requestedPath = urldecode(request()->path());
            $loginUrl = Route::has('login')
                ? route('login')
                : (Route::has('local.login') ? route('local.login') : '/login');
            $registerUrl = Route::has('register')
                ? route('register')
                : (Route::has('local.register') ? route('local.register') : '/register');
        @endphp

        <main class="mx-auto flex w-full max-w-3xl flex-col gap-6 px-6 py-12">
            <a class="text-sm font-semibold text-zinc-500 hover:text-zinc-900" href="/">index</a>

            <header class="space-y-2">
                <p class="text-xs uppercase tracking-[0.2em] text-zinc-400">404</p>
                <h1 class="text-3xl font-semibold">We couldn't find that Item.</h1>
                <p class="text-sm text-zinc-500">
                    Tried to resolve: <span class="font-medium text-zinc-700">{{ $requestedPath }}</span>
                </p>
            </header>

            <section class="rounded-lg border border-zinc-200 bg-zinc-50/60 p-6 text-sm text-zinc-600">
                <p>Log in or register to create this Item in index.</p>
                <div class="mt-4 flex flex-wrap gap-3">
                    <a class="rounded-full border border-zinc-200 bg-white px-4 py-2 font-semibold text-zinc-700 hover:border-zinc-300" href="{{ $loginUrl }}">Log in</a>
                    <a class="rounded-full bg-zinc-900 px-4 py-2 font-semibold text-white hover:bg-zinc-800" href="{{ $registerUrl }}">Register</a>
                </div>
            </section>

            <p class="text-sm text-zinc-500">
                If this should already exist, double-check the URL or contact support.
            </p>
        </main>
    </body>
</html>
