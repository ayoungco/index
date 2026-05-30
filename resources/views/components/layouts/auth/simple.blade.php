<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body>
        <main class="app-shell flex min-h-screen items-center justify-center">
            <div class="w-full max-w-sm">
                <p class="mb-6">
                    <a href="{{ route('home') }}">{{ config('app.name', 'Index') }}</a>
                </p>

                {{ $slot }}
            </div>
        </main>
    </body>
</html>
