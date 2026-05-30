<x-layouts.app :title="__('Register Object')">
    <section class="app-shell text-sm">
        <div class="app-panel max-w-4xl">
            <p class="app-accent">[SCAN TARGET: {{ $uuid }}]</p>
            <h1 class="app-title mt-2">New Object</h1>

            @if (session('status'))
                <p class="{{ session('statusType') === 'critical' ? 'app-notice' : 'app-notice' }} mt-4">{{ session('status') }}</p>
            @endif

            @if ($canInitialize)
                <p class="app-muted mt-4">Take a photo to register this object. The image becomes the first entry in its timeline.</p>

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
                            class="app-btn"
                            aria-label="Take or choose a photo"
                            title="Take or choose a photo"
                        >
                            <span class="app-camera-btn__body" aria-hidden="true">
                                <span class="app-camera-btn__lens"></span>
                            </span>
                        </button>
                        <span id="{{ $nameId }}" class="app-muted">No photo selected</span>
                    </div>

                    <div id="init-fields" class="hidden grid gap-4">
                        <label class="grid gap-1">
                            <span class="app-accent text-xs uppercase tracking-[0.16em]">Name <span class="app-muted normal-case">(optional)</span></span>
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                maxlength="255"
                                class="app-field"
                                placeholder="Leave blank to use the object ID"
                            >
                        </label>

                        <label class="grid gap-1">
                            <span class="app-accent text-xs uppercase tracking-[0.16em]">Note <span class="app-muted normal-case">(optional)</span></span>
                            <textarea
                                name="comment"
                                rows="2"
                                maxlength="2000"
                                class="app-field resize-y"
                                placeholder="Condition, location, context…"
                            >{{ old('comment') }}</textarea>
                        </label>

                        <label class="grid gap-1">
                            <span class="app-accent text-xs uppercase tracking-[0.16em]">Wikidata QID <span class="app-muted normal-case">(optional)</span></span>
                            <input
                                type="text"
                                name="wikidata_qid"
                                value="{{ old('wikidata_qid') }}"
                                maxlength="32"
                                pattern="Q[1-9][0-9]*"
                                class="app-field"
                                placeholder="Q629"
                            >
                        </label>

                        <button type="submit" class="app-btn app-btn w-full sm:w-auto">
                            Register Object
                        </button>
                    </div>

                    @error('photo')
                        <p class="app-notice text-xs">{{ $message }}</p>
                    @enderror
                    @error('name')
                        <p class="app-notice text-xs">{{ $message }}</p>
                    @enderror
                    @error('comment')
                        <p class="app-notice text-xs">{{ $message }}</p>
                    @enderror
                    @error('wikidata_qid')
                        <p class="app-notice text-xs">{{ $message }}</p>
                    @enderror

                    <noscript>
                        <div class="grid gap-4">
                            <label class="grid gap-1">
                                <span class="app-accent">Photo</span>
                                <input type="file" name="photo" accept="image/*,.heic,.heif" capture="environment" required class="app-field">
                            </label>
                            <label class="grid gap-1">
                                <span class="app-accent">Name <span class="app-muted">(optional)</span></span>
                                <input type="text" name="name" maxlength="255" class="app-field">
                            </label>
                            <label class="grid gap-1">
                                <span class="app-accent">Wikidata QID <span class="app-muted">(optional)</span></span>
                                <input type="text" name="wikidata_qid" maxlength="32" pattern="Q[1-9][0-9]*" class="app-field">
                            </label>
                            <button type="submit" class="app-btn app-btn">Register Object</button>
                        </div>
                    </noscript>
                </form>
            @else
                <p class="app-muted mt-4">Your Auth0 account email must be verified before registering objects.</p>
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
