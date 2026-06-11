<x-layouts.app :title="__('Site Settings')">
    <section class="w-full">
        @include('partials.settings-heading')

        <x-settings.layout :heading="__('Site Settings')" :subheading="__('Public identity, copy, and colors')">
            @if (session('status'))
                <div class="app-notice mb-6 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('settings.site.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <label class="grid gap-1 text-sm">
                    <span>{{ __('Site name') }}</span>
                    <input class="app-field" name="site_name" type="text" required value="{{ old('site_name', $defaults['site_name']) }}">
                </label>

                <label class="grid gap-1 text-sm">
                    <span>{{ __('Public site URL') }}</span>
                    <input class="app-field" name="site_url" type="url" required value="{{ old('site_url', $defaults['site_url']) }}">
                </label>

                <label class="grid gap-1 text-sm">
                    <span>{{ __('Scanner headline') }}</span>
                    <input class="app-field" name="scanner_title" type="text" required value="{{ old('scanner_title', $defaults['scanner_title']) }}">
                </label>

                <div class="space-y-2">
                    <label class="text-sm font-medium">{{ __('Scanner description') }}</label>
                    <textarea name="scanner_tagline" rows="3" class="app-field w-full">{{ old('scanner_tagline', $defaults['scanner_tagline']) }}</textarea>
                    @error('scanner_tagline')<p class="text-sm text-rose-400">{{ $message }}</p>@enderror
                </div>

                <label class="grid gap-1 text-sm">
                    <span>{{ __('Printed label name') }}</span>
                    <input class="app-field" name="label_name" type="text" required value="{{ old('label_name', $defaults['label_name']) }}">
                </label>

                <div class="space-y-2">
                    <label class="text-sm font-medium">{{ __('Printed label footer') }}</label>
                    <textarea name="label_tagline" rows="3" class="app-field w-full">{{ old('label_tagline', $defaults['label_tagline']) }}</textarea>
                    @error('label_tagline')<p class="text-sm text-rose-400">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-3">
                    <label class="text-sm font-medium">{{ __('Brand logo') }}</label>
                    @if ($defaults['logo_path'])
                        <div class="border border-current p-3">
                            <img src="{{ $siteSettings['logo_url'] }}" alt="{{ $siteSettings['site_name'] }}" class="h-16 w-auto">
                        </div>

                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="remove_logo" value="1">
                            <span>{{ __('Remove current logo') }}</span>
                        </label>
                    @endif

                    <input type="file" name="logo" accept="image/*" class="app-field w-full">
                    @error('logo')<p class="text-sm text-rose-400">{{ $message }}</p>@enderror
                    @error('remove_logo')<p class="text-sm text-rose-400">{{ $message }}</p>@enderror
                </div>

                <fieldset class="space-y-3 border border-current p-3">
                    <legend class="px-1 text-sm font-medium">{{ __('Colors') }}</legend>
                    <p class="app-muted text-sm">{{ __('Changes preview immediately. The default palette follows browser light and dark mode.') }}</p>

                    @foreach ([
                        'primary_color' => ['Primary', '--app-primary'],
                        'background_color' => ['Background', '--app-background'],
                        'highlight_color' => ['Highlight', '--app-highlight'],
                    ] as $name => [$label, $property])
                        <label class="flex items-center justify-between gap-4 text-sm">
                            <span>{{ __($label) }}</span>
                            <input
                                class="app-color-field"
                                name="{{ $name }}"
                                type="color"
                                value="{{ old($name, $defaults[$name]) }}"
                                data-theme-color="{{ $property }}"
                            >
                        </label>
                        @error($name)<p class="text-sm app-accent">{{ $message }}</p>@enderror
                    @endforeach
                </fieldset>

                <div class="flex items-center gap-4">
                    <button class="app-btn" type="submit">{{ __('Save') }}</button>
                </div>
            </form>
        </x-settings.layout>
    </section>
</x-layouts.app>
