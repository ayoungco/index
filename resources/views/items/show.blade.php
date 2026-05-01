<x-layouts.app :title="$item->name">
    <section class="terminal-shell text-sm">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <a
                href="{{ route('items.print', ['uuid' => $item->uuid]) }}"
                class="terminal-btn terminal-btn-accent"
            >
                Print Label
            </a>
        </div>
        <div class="terminal-panel">
            <div class="terminal-divider grid gap-4 border-b pb-4">
                @include('items.partials.label', ['item' => $item, 'qrSvg' => $qrSvg])

                <p class="break-all text-xs">{{ $itemUrl }}</p>

                @if ($item->description)
                    <p class="terminal-muted max-w-3xl">{{ $item->description }}</p>
                @endif
            </div>

            @if (session('status'))
                <p class="{{ session('statusType') === 'critical' ? 'terminal-notice-critical' : 'terminal-notice' }} mt-4 font-semibold">{{ session('status') }}</p>
            @endif

            @if ($isAuthenticated)
                <div class="terminal-divider mt-4 border p-4">
                    <h2 class="terminal-accent text-base font-semibold">Timeline Entry</h2>

                    @if ($canPost)
                        @php
                            $pickerId = 'photo-input-'.$item->id;
                            $nameId = 'photo-name-'.$item->id;
                        @endphp

                        <form method="POST" action="{{ route('items.events.store', ['uuid' => $item->uuid]) }}" enctype="multipart/form-data" class="mt-3 grid gap-3">
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

                            <label class="grid gap-1">
                                <span class="terminal-accent text-xs uppercase tracking-[0.16em]">Comment</span>
                                <textarea
                                    name="comment"
                                    rows="2"
                                    maxlength="1000"
                                    class="terminal-field resize-y"
                                    placeholder="Optional timeline note"
                                >{{ old('comment') }}</textarea>
                            </label>

                            <div class="flex flex-wrap items-center gap-2">
                                <button
                                    id="photo-trigger-{{ $item->id }}"
                                    type="button"
                                    class="terminal-camera-btn"
                                    aria-label="Take or choose a photo"
                                    title="Take or choose a photo"
                                >
                                    <span class="terminal-camera-btn__body" aria-hidden="true">
                                        <span class="terminal-camera-btn__lens"></span>
                                    </span>
                                </button>
                                <span id="{{ $nameId }}" class="terminal-muted text-xs">No file selected</span>
                            </div>

                            <p class="terminal-muted text-xs">Upload starts after selecting a photo.</p>

                            <noscript>
                                <label class="grid gap-1">
                                    <span class="terminal-accent">Photo</span>
                                    <input
                                        type="file"
                                        name="photo"
                                        accept="image/*,.heic,.heif"
                                        capture="environment"
                                        required
                                        class="terminal-field"
                                    >
                                </label>
                            </noscript>

                            @error('photo')
                                <p class="terminal-notice-critical text-xs">{{ $message }}</p>
                            @enderror
                            @error('comment')
                                <p class="terminal-notice-critical text-xs">{{ $message }}</p>
                            @enderror

                            <div class="hidden" data-js-submit-fallback="{{ $item->id }}">
                                <button type="submit" class="terminal-btn terminal-btn-accent">
                                    Add Photo
                                </button>
                            </div>
                        </form>
                    @else
                        <p class="terminal-muted mt-2">Verify your Auth0 email before posting timeline events.</p>
                    @endif
                </div>
            @endif

            <div class="mt-5">
                <h2 class="terminal-accent text-base font-semibold">Timeline</h2>

                @if ($timeline->isEmpty())
                    <p class="terminal-muted mt-2">No events yet.</p>
                @else
                    <ul class="mt-3 grid gap-3">
                        @foreach ($timeline as $event)
                            <li class="terminal-divider border p-3">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div>
                                        <p>{{ $event['occurred_at']?->toDateTimeString() }}</p>
                                        <p class="terminal-accent mt-1 text-xs uppercase tracking-[0.14em]">{{ $event['title'] }}</p>
                                    </div>
                                    @if ($event['type'] === 'photo')
                                        <span class="terminal-chip {{ $event['is_qr_verified'] ? 'terminal-chip-highlight' : 'terminal-chip-critical' }}">
                                            {{ $event['is_qr_verified'] ? 'QR verified' : 'QR flagged' }}
                                        </span>
                                    @endif
                                </div>
                                <p class="terminal-muted mt-1">
                                    @if ($event['flag'])
                                        <span aria-hidden="true">{{ $event['flag'] }}</span>
                                    @endif
                                    {{ $event['actor'] }}
                                </p>
                                @if ($event['comment'])
                                    <p class="mt-3 whitespace-pre-wrap">{{ $event['comment'] }}</p>
                                @endif

                                @if ($event['image_path'])
                                    @if ($isAuthenticated)
                                        <img
                                            src="{{ asset('storage/'.$event['image_path']) }}"
                                            alt="Item event image"
                                            class="terminal-divider mt-3 max-h-72 w-full border object-contain"
                                            loading="lazy"
                                        >
                                    @else
                                        <div class="terminal-divider terminal-muted mt-3 border p-2">
                                            Image attached. Log in for full-resolution timeline images.
                                        </div>
                                    @endif
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </section>

    @once
        <script>
            window.bindItemPhotoPicker = window.bindItemPhotoPicker || function (id) {
                const picker = document.getElementById(`photo-input-${id}`);
                const trigger = document.getElementById(`photo-trigger-${id}`);
                const name = document.getElementById(`photo-name-${id}`);
                const fallback = document.querySelector(`[data-js-submit-fallback="${id}"]`);
                const form = picker?.closest('form');

                if (! picker || picker.dataset.bound === '1') {
                    return;
                }

                picker.dataset.bound = '1';

                trigger?.addEventListener('click', () => {
                    picker.click();
                });

                fallback?.classList.add('hidden');

                picker.addEventListener('change', () => {
                    const file = picker.files?.[0];
                    if (name) {
                        name.textContent = file ? file.name : 'No file selected';
                    }

                    if (! file || ! form) {
                        return;
                    }

                    if (trigger) {
                        trigger.disabled = true;
                        trigger.setAttribute('aria-label', 'Uploading photo');
                        trigger.setAttribute('title', 'Uploading photo');
                        trigger.classList.add('is-uploading');
                    }

                    form.submit();
                });
            };
        </script>
    @endonce

    <script>
        window.bindItemPhotoPicker('{{ $item->id }}');
    </script>
</x-layouts.app>
