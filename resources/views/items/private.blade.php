<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body>
    <main class="item-page text-sm">
        <section class="item-page__content">
            <p class="app-accent text-xs font-semibold uppercase tracking-[0.16em]">Private object</p>
            <h1 class="app-title mt-2">Sign in to view this record</h1>
            <p class="app-muted mt-3 max-w-xl">This object is not published. Sign in to view its timeline and use the operator features.</p>
            <a href="{{ $loginUrl }}" class="app-btn app-btn-primary mt-4">Log in to continue</a>
        </section>
    </main>
</body>
</html>
