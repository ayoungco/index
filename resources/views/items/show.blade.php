<x-layouts.app :title="$item->name">
    <section class="item-page text-sm">
        <div class="item-page__label">
            @include('items.partials.label', ['item' => $item, 'qrSvg' => $qrSvg])
        </div>

        <div class="item-page__content">
            <div class="app-divider grid gap-3 border-b pb-4">
                <p class="break-all text-xs">{{ $itemUrl }}</p>
                <nav class="flex flex-wrap gap-x-3 gap-y-2 text-xs" aria-label="Open printable or scannable label">
                    <a href="{{ route('items.print', ['uuid' => $item->uuid, 'layout' => 'vertical']) }}">Portrait 4 × 7</a>
                    <a href="{{ route('items.print', ['uuid' => $item->uuid, 'layout' => 'horizontal']) }}">Landscape 6 × 4</a>
                    <a href="{{ route('items.print', ['uuid' => $item->uuid, 'layout' => 'compact']) }}">Compact 2.25 × 2.75</a>
                    <a href="{{ route('items.print', ['uuid' => $item->uuid, 'layout' => 'qr']) }}">Scan 2 × 2.25</a>
                </nav>

                @if ($item->description)
                    <p class="app-muted max-w-3xl">{{ $item->description }}</p>
                @endif

                @if ($isAuthenticated && $canPost)
                    <form method="POST" action="{{ route('items.update', ['uuid' => $item->uuid]) }}" class="app-compact-form">
                        @csrf
                        @method('PATCH')

                        <label for="item-description" class="app-form__label">Description</label>
                        <div class="app-compact-form__row">
                            <textarea
                                id="item-description"
                                name="description"
                                rows="2"
                                maxlength="5000"
                                class="app-field app-compact-form__field"
                                placeholder="Add context, condition, location, or usage notes"
                            >{{ old('description', $item->description) }}</textarea>
                            <button type="submit" class="app-btn app-compact-form__submit">Save</button>
                        </div>
                        @error('description')
                            <p class="app-notice text-xs">{{ $message }}</p>
                        @enderror
                    </form>
                @endif
            </div>

            @if (session('status'))
                <p class="{{ session('statusType') === 'critical' ? 'app-notice' : 'app-notice' }} mt-4 font-semibold">{{ session('status') }}</p>
            @endif

            @include('items.partials.wikidata', ['wikidata' => $wikidata, 'semanticUrl' => $semanticUrl])

            <div class="app-divider mt-4 border p-4">
                <h2 class="app-accent text-base font-semibold">Record scan location</h2>
                <p class="app-muted mt-1 text-xs">Uses this device’s location, then resolves the surrounding place. Room and container stay local to this record.</p>
                <form method="POST" action="{{ route('items.location.store', ['uuid' => $item->uuid]) }}" class="mt-3 grid gap-3" data-location-form>
                    @csrf
                    <input type="hidden" name="latitude" data-location-latitude>
                    <input type="hidden" name="longitude" data-location-longitude>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="app-form__field">
                            <span class="app-form__label">Room <span class="app-form__optional">Optional</span></span>
                            <input name="room" maxlength="120" class="app-field" placeholder="Receiving room">
                        </label>
                        <label class="app-form__field">
                            <span class="app-form__label">Container <span class="app-form__optional">Optional</span></span>
                            <input name="container" maxlength="120" class="app-field" placeholder="Shelf A / tote 4">
                        </label>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <button type="button" class="app-btn" data-location-capture>Use device location</button>
                        <button type="submit" class="app-btn app-btn-primary" disabled data-location-submit>Save location</button>
                        <span class="app-muted text-xs" data-location-status></span>
                    </div>
                </form>
            </div>

            @if ($isAuthenticated)
                <div class="app-divider mt-4 border p-4">
                    <h2 class="app-accent text-base font-semibold">Add timeline entry</h2>

                    @if ($canPost)
                        @php
                            $pickerId = 'photo-input-'.$item->id;
                            $nameId = 'photo-name-'.$item->id;
                        @endphp

                        <form method="POST" action="{{ route('items.events.store', ['uuid' => $item->uuid]) }}" enctype="multipart/form-data" class="app-event-form">
                            @csrf

                            <input
                                id="{{ $pickerId }}"
                                type="file"
                                name="photo"
                                accept="image/*,.heic,.heif"
                                capture="environment"
                                required
                                data-max-bytes="{{ \App\Support\UploadLimits::maxBytes() }}"
                                data-max-label="{{ \App\Support\UploadLimits::label() }}"
                                class="sr-only"
                            >

                            <div class="app-event-form__fields">
                                <label class="app-form__field">
                                    <span class="app-form__label">Note <span class="app-form__optional">Optional</span></span>
                                    <textarea
                                        name="comment"
                                        rows="2"
                                        maxlength="2000"
                                        class="app-field app-event-form__note"
                                        placeholder="Condition, location, handoff, or other update"
                                    >{{ old('comment') }}</textarea>
                                </label>

                                <label class="app-form__field">
                                    <span class="app-form__label">Tags <span class="app-form__optional">Optional</span></span>
                                    <input
                                        type="text"
                                        name="tags"
                                        value="{{ old('tags') }}"
                                        maxlength="500"
                                        class="app-field"
                                        placeholder="handoff, bay-4, fragile"
                                    >
                                </label>
                            </div>

                            <div class="app-event-form__upload">
                                <button
                                    id="photo-trigger-{{ $item->id }}"
                                    type="button"
                                    class="app-btn"
                                    aria-label="Take or choose a photo"
                                    title="Take or choose a photo"
                                >
                                    <span class="app-camera-btn__body" aria-hidden="true">
                                        <span class="app-camera-btn__lens"></span>
                                    </span>
                                    Add photo
                                </button>
                                <span id="{{ $nameId }}" class="app-muted text-xs">No file selected. Maximum {{ \App\Support\UploadLimits::label() }}.</span>
                            </div>

                            <noscript>
                                <label class="grid gap-1">
                                    <span class="app-accent">Photo</span>
                                    <input
                                        type="file"
                                        name="photo"
                                        accept="image/*,.heic,.heif"
                                        capture="environment"
                                        required
                                        class="app-field"
                                    >
                                </label>
                            </noscript>

                            @error('photo')
                                <p class="app-notice text-xs">{{ $message }}</p>
                            @enderror
                            <p class="app-notice hidden text-xs" data-upload-error></p>
                            @error('comment')
                                <p class="app-notice text-xs">{{ $message }}</p>
                            @enderror
                            @error('tags')
                                <p class="app-notice text-xs">{{ $message }}</p>
                            @enderror

                            <div class="hidden" data-js-submit-fallback="{{ $item->id }}">
                                <button type="submit" class="app-btn">
                                    Add Photo
                                </button>
                            </div>
                        </form>
                    @else
                        <p class="app-muted mt-2">Verify your Auth0 email before posting timeline events.</p>
                    @endif
                </div>
            @endif

            <div class="mt-5">
                <h2 class="app-accent text-base font-semibold">Timeline</h2>

                @if ($timeline->isEmpty())
                    <p class="app-muted mt-2">No events yet.</p>
                @else
                    <table class="compose-log mt-3" aria-label="Timeline log">
                        <colgroup>
                            <col class="compose-log__rail-col">
                            <col class="compose-log__time-col">
                            <col class="compose-log__source-col">
                            <col>
                        </colgroup>
                        <tbody>
                            @foreach ($timeline as $event)
                                @php
                                    $message = trim(collect([
                                        $event['title'],
                                        $event['comment'] ?: null,
                                    ])->filter()->implode(' | '));
                                @endphp
                                <tr class="compose-log__line {{ $event['image_url'] ? 'compose-log__line--media' : '' }}">
                                    <td class="compose-log__rail-cell" aria-hidden="true">
                                        <span class="compose-log__rail">
                                            <span class="compose-log__dot"></span>
                                        </span>
                                    </td>
                                    <td class="compose-log__cell compose-log__cell--time">
                                        <time class="compose-log__time" datetime="{{ $event['occurred_at']?->toIso8601String() }}">
                                            {{ $event['occurred_at']?->format('Y-m-d H:i:s') }}
                                        </time>
                                    </td>
                                    <td class="compose-log__cell compose-log__cell--source">
                                        <span class="compose-log__source">{{ $event['actor'] }}</span>
                                    </td>
                                    <td class="compose-log__cell compose-log__cell--message">
                                        <span class="compose-log__message">{{ $message }}</span>
                                        @if ($event['image_url'])
                                            <details class="mt-2" data-timeline-image>
                                                <summary class="cursor-pointer">Show image</summary>
                                                <a href="{{ $event['image_url'] }}" class="compose-log__thumb" aria-label="Open timeline image for {{ $event['occurred_at']?->format('Y-m-d H:i:s') }}">
                                                    <img data-src="{{ $event['image_url'] }}" alt="Timeline image for {{ $item->name }}" loading="lazy" decoding="async">
                                                </a>
                                                @if ($canPost)
                                                    <form method="POST" action="{{ route('items.featured-photo.update', ['uuid' => $item->uuid, 'event' => $event['id']]) }}" class="mt-2">
                                                        @csrf
                                                        <button type="submit" class="app-btn">{{ $item->featured_event_id === $event['id'] ? 'Featured photo' : 'Set as featured photo' }}</button>
                                                    </form>
                                                @endif
                                            </details>
                                        @endif
                                        @if (! empty($event['tags']))
                                            <div class="compose-log__tags" aria-label="Tags">
                                                @foreach ($event['tags'] as $tag)
                                                    <span class="compose-log__tag">{{ $tag }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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

                    if (! picker.checkValidity()) {
                        return;
                    }

                    if (trigger) {
                        trigger.disabled = true;
                        trigger.setAttribute('aria-label', 'Uploading photo');
                        trigger.setAttribute('title', 'Uploading photo');
                        trigger.classList.add('is-uploading');
                    }

                    form.requestSubmit();
                });
            };
        </script>
    @endonce

    <script>
        window.bindItemPhotoPicker('{{ $item->id }}');

        document.querySelectorAll('[data-timeline-image]').forEach((details) => {
            details.addEventListener('toggle', () => {
                const image = details.querySelector('img[data-src]');

                if (details.open && image) {
                    image.src = image.dataset.src;
                    image.removeAttribute('data-src');
                }
            }, { once: true });
        });

        document.querySelectorAll('[data-location-form]').forEach((form) => {
            const capture = form.querySelector('[data-location-capture]');
            const submit = form.querySelector('[data-location-submit]');
            const status = form.querySelector('[data-location-status]');
            capture?.addEventListener('click', () => {
                if (! navigator.geolocation) {
                    status.textContent = 'Location is unavailable in this browser.';
                    return;
                }
                status.textContent = 'Getting location…';
                navigator.geolocation.getCurrentPosition((position) => {
                    form.querySelector('[data-location-latitude]').value = position.coords.latitude;
                    form.querySelector('[data-location-longitude]').value = position.coords.longitude;
                    submit.disabled = false;
                    status.textContent = 'Location ready to save.';
                }, () => {
                    status.textContent = 'Location permission was not granted.';
                }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 });
            });
        });
    </script>
</x-layouts.app>
