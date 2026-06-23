<div class="flex items-center">
    <img
        src="{{ app(\App\Support\SiteSettings::class)->logoUrl() }}"
        alt="{{ config('app.name') }}"
        {{ $attributes->class('h-14 w-auto logo-adaptive') }}
    >
</div>
