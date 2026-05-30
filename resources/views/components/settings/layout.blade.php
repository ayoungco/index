<div class="grid gap-6 md:grid-cols-[12rem_1fr]">
    <nav class="flex flex-col gap-2 text-sm">
        <a href="{{ route('settings.site') }}">{{ __('Site') }}</a>
        <a href="{{ route('settings.profile') }}">{{ __('Profile') }}</a>
    </nav>

    <section>
        <h2 class="font-bold">{{ $heading ?? '' }}</h2>
        <p class="app-muted mt-1 text-sm">{{ $subheading ?? '' }}</p>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </section>
</div>
