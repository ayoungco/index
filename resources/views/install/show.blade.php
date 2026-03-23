<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head', ['title' => 'Install '.config('app.name')])
</head>
<body>
    <main class="terminal-shell py-10 text-sm">
        <section class="terminal-panel mx-auto w-full max-w-4xl">
            <p class="terminal-accent">FIRST RUN INSTALLER</p>
            <h1 class="terminal-title mt-2">Configure your self-hosted scanner</h1>
            <p class="terminal-muted mt-3 max-w-2xl">
                Productize this instance by choosing the public site URL, uploading a logo, and defining the copy shown on scanner pages and printed labels.
            </p>

            @if ($errors->any())
                <div class="mt-4 border border-rose-500 bg-rose-950 p-3 text-rose-200">
                    <p class="font-semibold">Please fix the highlighted fields.</p>
                </div>
            @endif

            <form method="POST" action="{{ route('install.store') }}" enctype="multipart/form-data" class="mt-6 grid gap-6 md:grid-cols-2">
                @csrf

                <label class="grid gap-2 md:col-span-1">
                    <span class="terminal-accent">Site name</span>
                    <input type="text" name="site_name" value="{{ old('site_name', $defaults['site_name']) }}" required class="terminal-field">
                    @error('site_name')<span class="text-rose-400">{{ $message }}</span>@enderror
                </label>

                <label class="grid gap-2 md:col-span-1">
                    <span class="terminal-accent">Public site URL</span>
                    <input type="url" name="site_url" value="{{ old('site_url', $defaults['site_url']) }}" required class="terminal-field" placeholder="https://scanner.example.com">
                    @error('site_url')<span class="text-rose-400">{{ $message }}</span>@enderror
                </label>

                <label class="grid gap-2 md:col-span-1">
                    <span class="terminal-accent">Scanner headline</span>
                    <input type="text" name="scanner_title" value="{{ old('scanner_title', $defaults['scanner_title']) }}" required class="terminal-field">
                    @error('scanner_title')<span class="text-rose-400">{{ $message }}</span>@enderror
                </label>

                <label class="grid gap-2 md:col-span-1">
                    <span class="terminal-accent">Printed label name</span>
                    <input type="text" name="label_name" value="{{ old('label_name', $defaults['label_name']) }}" required class="terminal-field">
                    @error('label_name')<span class="text-rose-400">{{ $message }}</span>@enderror
                </label>

                <label class="grid gap-2 md:col-span-2">
                    <span class="terminal-accent">Scanner description</span>
                    <textarea name="scanner_tagline" rows="3" class="terminal-field">{{ old('scanner_tagline', $defaults['scanner_tagline']) }}</textarea>
                    @error('scanner_tagline')<span class="text-rose-400">{{ $message }}</span>@enderror
                </label>

                <label class="grid gap-2 md:col-span-2">
                    <span class="terminal-accent">Printed label footer</span>
                    <textarea name="label_tagline" rows="3" class="terminal-field">{{ old('label_tagline', $defaults['label_tagline']) }}</textarea>
                    @error('label_tagline')<span class="text-rose-400">{{ $message }}</span>@enderror
                </label>

                <label class="grid gap-2 md:col-span-2">
                    <span class="terminal-accent">Brand logo</span>
                    <input type="file" name="logo" accept="image/*" class="terminal-field">
                    <span class="terminal-muted text-xs">PNG, JPG, WebP, or SVG-compatible image up to 4 MB. If omitted, the default logo is kept.</span>
                    @error('logo')<span class="text-rose-400">{{ $message }}</span>@enderror
                </label>

                <div class="md:col-span-2 flex flex-wrap gap-3">
                    <button type="submit" class="terminal-btn terminal-btn-accent">Complete Installation</button>
                    <span class="terminal-muted self-center">You can revisit branding later by editing the stored app settings.</span>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
