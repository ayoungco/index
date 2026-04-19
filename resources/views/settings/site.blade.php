<x-layouts.app :title="__('Site Settings')">
    <section class="w-full">
        @include('partials.settings-heading')

        <x-settings.layout :heading="__('Site Settings')" :subheading="__('Placeholder admin controls for public branding and scanner copy')">
            @if (session('status'))
                <div class="mb-6 rounded-lg border border-emerald-500 bg-emerald-950/50 px-4 py-3 text-sm text-emerald-200">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('settings.site.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <flux:input
                    name="site_name"
                    :label="__('Site name')"
                    type="text"
                    required
                    :value="old('site_name', $defaults['site_name'])"
                />

                <flux:input
                    name="site_url"
                    :label="__('Public site URL')"
                    type="url"
                    required
                    :value="old('site_url', $defaults['site_url'])"
                />

                <flux:input
                    name="scanner_title"
                    :label="__('Scanner headline')"
                    type="text"
                    required
                    :value="old('scanner_title', $defaults['scanner_title'])"
                />

                <div class="space-y-2">
                    <label class="text-sm font-medium text-zinc-950 dark:text-white">{{ __('Scanner description') }}</label>
                    <textarea name="scanner_tagline" rows="3" class="terminal-field w-full">{{ old('scanner_tagline', $defaults['scanner_tagline']) }}</textarea>
                    @error('scanner_tagline')<p class="text-sm text-rose-400">{{ $message }}</p>@enderror
                </div>

                <flux:input
                    name="label_name"
                    :label="__('Printed label name')"
                    type="text"
                    required
                    :value="old('label_name', $defaults['label_name'])"
                />

                <div class="space-y-2">
                    <label class="text-sm font-medium text-zinc-950 dark:text-white">{{ __('Printed label footer') }}</label>
                    <textarea name="label_tagline" rows="3" class="terminal-field w-full">{{ old('label_tagline', $defaults['label_tagline']) }}</textarea>
                    @error('label_tagline')<p class="text-sm text-rose-400">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-3">
                    <label class="text-sm font-medium text-zinc-950 dark:text-white">{{ __('Brand logo') }}</label>
                    @if ($defaults['logo_path'])
                        <div class="rounded-lg border border-zinc-300 p-3 dark:border-zinc-700">
                            <img src="{{ $siteSettings['logo_url'] }}" alt="{{ $siteSettings['site_name'] }}" class="h-16 w-auto">
                        </div>

                        <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                            <input type="checkbox" name="remove_logo" value="1">
                            <span>{{ __('Remove current logo') }}</span>
                        </label>
                    @endif

                    <input type="file" name="logo" accept="image/*" class="terminal-field w-full">
                    @error('logo')<p class="text-sm text-rose-400">{{ $message }}</p>@enderror
                    @error('remove_logo')<p class="text-sm text-rose-400">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center gap-4">
                    <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
                </div>
            </form>
        </x-settings.layout>
    </section>
</x-layouts.app>
