<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body>
        @php
            $user = auth()->user();
            $brandDomain = parse_url($siteSettings['site_url'], PHP_URL_HOST) ?: $siteSettings['site_name'];
        @endphp

        <header class="app-header">
            <div class="app-header__inner">
                <a href="{{ $user ? route('dashboard') : route('home') }}" class="app-brand" aria-label="{{ $brandDomain }} home">
                    <x-app-logo-mark class="app-brand__mark" />
                    <span class="app-brand__domain">{{ $brandDomain }}</span>
                </a>

                @if ($user)
                    <nav class="app-header__nav" aria-label="Main navigation">
                        <a href="{{ route('dashboard') }}">Objects</a>
                        <a href="{{ route('labels.create') }}" @if (request()->routeIs('labels.*')) aria-current="page" @endif>Print labels</a>
                    </nav>
                    <form
                        method="GET"
                        action="{{ route('dashboard') }}"
                        class="app-header__search"
                        role="search"
                        data-search-form
                        data-search-url="{{ route('dashboard.search') }}"
                    >
                        <label for="app-header-search" class="sr-only">Search objects</label>
                        <input
                            id="app-header-search"
                            name="q"
                            type="search"
                            value="{{ request()->routeIs('dashboard') ? request('q') : '' }}"
                            placeholder="Search assets"
                            class="app-header__search-input"
                            autocomplete="off"
                            aria-autocomplete="list"
                            aria-controls="app-header-search-results"
                            aria-expanded="false"
                            data-search-input
                        >
                        <button type="submit" class="app-header__search-button">
                            Search
                        </button>
                        @if (request()->routeIs('dashboard') && request()->filled('q'))
                            <a href="{{ route('dashboard') }}" class="app-header__search-clear" aria-label="Clear search">
                                Clear
                            </a>
                        @endif
                        <div
                            id="app-header-search-results"
                            class="app-header__search-results"
                            data-search-results
                            role="listbox"
                            hidden
                        ></div>
                    </form>
                @endif

                <nav class="app-header__actions" aria-label="Account">
                    @if ($user)
                        <span class="app-header__identity" title="{{ $user->email }}">{{ $user->email }}</span>
                        <a href="{{ route('settings.profile') }}">Settings</a>
                        <a href="{{ url('/logout') }}">Log Out</a>
                    @else
                        <a href="{{ route('login') }}">Log in</a>
                    @endif
                </nav>
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>
    </body>
</html>
