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
                                    maxlength="2000"
                                    class="terminal-field resize-y"
                                    placeholder="Optional timeline note"
                                >{{ old('comment') }}</textarea>
                            </label>

                            <label class="grid gap-1">
                                <span class="terminal-accent text-xs uppercase tracking-[0.16em]">Tags</span>
                                <input
                                    type="text"
                                    name="tags"
                                    value="{{ old('tags') }}"
                                    maxlength="500"
                                    class="terminal-field"
                                    placeholder="handoff, warehouse-a, fragile"
                                >
                                <span class="terminal-muted text-xs">Separate tags with commas.</span>
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

                            <p class="terminal-muted text-xs">Upload starts automatically after selecting a photo.</p>

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
                            @error('tags')
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
                    <ul class="compose-log mt-3" aria-label="Timeline log">
                        @foreach ($timeline as $event)
                            @php
                                $source = $event['type'] === 'photo' ? 'camera' : 'index';
                                $sourceClass = $event['type'] === 'photo'
                                    ? 'compose-log__source--camera'
                                    : 'compose-log__source--index';
                                $message = trim(collect([
                                    $event['title'],
                                    $event['actor'] ? 'by '.$event['actor'] : null,
                                    $event['comment'] ?: null,
                                ])->filter()->implode(' | '));
                            @endphp
                            <li class="compose-log__line {{ $event['image_url'] ? 'compose-log__line--media' : '' }}">
                                <div class="compose-log__meta">
                                    <span class="compose-log__source {{ $sourceClass }}">{{ $source }}</span>
                                    <span class="compose-log__pipe">|</span>
                                    <time class="compose-log__time" datetime="{{ $event['occurred_at']?->toIso8601String() }}">
                                        {{ $event['occurred_at']?->format('Y-m-d H:i:s') }}
                                    </time>
                                    <span class="compose-log__message">{{ $message }}</span>
                                </div>
                                @if ($event['image_url'])
                                    <a href="{{ $event['image_url'] }}" class="compose-log__thumb" aria-label="Open timeline image for {{ $event['occurred_at']?->format('Y-m-d H:i:s') }}">
                                        <img src="{{ $event['image_url'] }}" alt="Timeline image for {{ $item->name }}" loading="lazy" decoding="async">
                                    </a>
                                @endif
                                @if (! empty($event['tags']))
                                    <div class="compose-log__tags" aria-label="Tags">
                                        @foreach ($event['tags'] as $tag)
                                            <span class="compose-log__tag">{{ $tag }}</span>
                                        @endforeach
                                    </div>
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
