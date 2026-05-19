<x-layouts.app :title="__('Register Object')">
    <section class="terminal-shell text-sm">
        <div class="terminal-panel max-w-4xl">
            <p class="terminal-accent">[SCAN TARGET: {{ $uuid }}]</p>
            <h1 class="terminal-title mt-2">New Object</h1>

            @if (session('status'))
                <p class="{{ session('statusType') === 'critical' ? 'terminal-notice-critical' : 'terminal-notice' }} mt-4">{{ session('status') }}</p>
            @endif

            @if ($canInitialize)
                <p class="terminal-muted mt-4">Take a photo to register this object. The image becomes the first entry in its timeline.</p>

                @php $pickerId = 'init-photo-input'; $nameId = 'init-photo-name'; @endphp

                <form
                    id="init-form"
                    method="POST"
                    action="{{ route('items.initialize', ['uuid' => $uuid]) }}"
                    enctype="multipart/form-data"
                    class="mt-5 grid gap-4"
                >
                    @csrf

                    <input
                        id="{{ $pickerId }}"
                        type="file"
                        name="photo"
                        accept="image/*,.heic,.heif"
                        capture="environment"
                        required
                        class="sr-only"
                    >

                    <div class="flex flex-wrap items-center gap-3">
                        <button
                            id="init-photo-trigger"
                            type="button"
                            class="terminal-camera-btn"
                            aria-label="Take or choose a photo"
                            title="Take or choose a photo"
                        >
                            <span class="terminal-camera-btn__body" aria-hidden="true">
                                <span class="terminal-camera-btn__lens"></span>
                            </span>
                        </button>
                        <span id="{{ $nameId }}" class="terminal-muted">No photo selected</span>
                    </div>

                    <div id="init-fields" class="hidden grid gap-4">
                        <label class="grid gap-1">
                            <span class="terminal-accent text-xs uppercase tracking-[0.16em]">Name <span class="terminal-muted normal-case">(optional)</span></span>
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                maxlength="255"
                                class="terminal-field"
                                placeholder="Leave blank to use the object ID"
                            >
                        </label>

                        <label class="grid gap-1">
                            <span class="terminal-accent text-xs uppercase tracking-[0.16em]">Note <span class="terminal-muted normal-case">(optional)</span></span>
                            <textarea
                                name="comment"
                                rows="2"
                                maxlength="2000"
                                class="terminal-field resize-y"
                                placeholder="Condition, location, context…"
                            >{{ old('comment') }}</textarea>
                        </label>

                        <button type="submit" class="terminal-btn terminal-btn-accent w-full sm:w-auto">
                            Register Object
                        </button>
                    </div>

                    @error('photo')
                        <p class="terminal-notice-critical text-xs">{{ $message }}</p>
                    @enderror
                    @error('name')
                        <p class="terminal-notice-critical text-xs">{{ $message }}</p>
                    @enderror
                    @error('comment')
                        <p class="terminal-notice-critical text-xs">{{ $message }}</p>
                    @enderror

                    <noscript>
                        <div class="grid gap-4">
                            <label class="grid gap-1">
                                <span class="terminal-accent">Photo</span>
                                <input type="file" name="photo" accept="image/*,.heic,.heif" capture="environment" required class="terminal-field">
                            </label>
                            <label class="grid gap-1">
                                <span class="terminal-accent">Name <span class="terminal-muted">(optional)</span></span>
                                <input type="text" name="name" maxlength="255" class="terminal-field">
                            </label>
                            <button type="submit" class="terminal-btn terminal-btn-accent">Register Object</button>
                        </div>
                    </noscript>
                </form>
            @else
                <p class="terminal-muted mt-4">Your Auth0 account email must be verified before registering objects.</p>
            @endif
        </div>
    </section>

    @if ($canInitialize)
        <script>
            (function () {
                const picker  = document.getElementById('{{ $pickerId }}');
                const trigger = document.getElementById('init-photo-trigger');
                const label   = document.getElementById('{{ $nameId }}');
                const fields  = document.getElementById('init-fields');

                trigger?.addEventListener('click', () => picker?.click());

                picker?.addEventListener('change', () => {
                    const file = picker.files?.[0];
                    if (!file) return;

                    label.textContent = file.name;
                    trigger.classList.add('is-uploading');
                    fields?.classList.remove('hidden');
                    fields?.querySelector('input[name="name"]')?.focus();
                });
            })();
        </script>
    @endif
</x-layouts.app>
