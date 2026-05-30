<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body>
        @php($user = auth()->user())

        <header class="border-b border-current">
            <div class="mx-auto flex max-w-6xl flex-wrap items-center gap-3 px-4 py-3">
                <a href="{{ route('dashboard') }}" class="font-bold no-underline">
                    {{ config('app.name', 'Index') }}
                </a>

                <nav class="flex flex-wrap gap-3 text-sm">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="{{ route('things.index') }}">Things</a>
                    <a href="{{ route('properties.index') }}">Properties</a>
                    <a href="{{ route('relations.index') }}">Relations</a>
                    <a href="{{ route('messages.index') }}">Messages</a>
                </nav>

                <div class="ms-auto flex flex-wrap items-center gap-3 text-sm">
                    @if ($user)
                        <span>{{ $user->email }}</span>
                        <a href="{{ route('settings.profile') }}">Settings</a>
                        <a href="{{ url('/logout') }}">Log Out</a>
                    @else
                        <a href="{{ route('login') }}">Log in</a>
                    @endif
                </div>
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>
    </body>
</html>
