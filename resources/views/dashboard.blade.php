<x-layouts.app :title="__('Dashboard')">
    <section class="app-shell space-y-4">
        <div class="app-panel">
            <div class="app-divider border-b px-4 py-3">
                <h2 class="app-muted text-sm font-semibold uppercase tracking-[0.2em]">
                    Create From Photo
                </h2>
            </div>

            @if (session('status'))
                <p class="{{ session('statusType') === 'critical' ? 'app-notice' : 'app-notice' }} mx-4 mt-4 font-semibold">{{ session('status') }}</p>
            @endif

            @if ($canCreateFromPhoto)
                <form method="POST" action="{{ route('items.from-photo.store') }}" enctype="multipart/form-data" class="app-quick-create px-4 py-4">
                    @csrf

                    <div class="app-quick-create__row">
                        <label for="create-photo-name" class="sr-only">Object title</label>
                        <input
                            id="create-photo-name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            maxlength="255"
                            class="app-field app-quick-create__title"
                            placeholder="Title"
                        >
                        <input
                            id="create-photo-input"
                            type="file"
                            name="photo"
                            accept="image/*,.heic,.heif"
                            capture="environment"
                            required
                            data-max-bytes="{{ \App\Support\UploadLimits::maxBytes() }}"
                            data-max-label="{{ \App\Support\UploadLimits::label() }}"
                            data-title-target="#create-photo-name"
                            data-file-name-target="#create-photo-selected"
                            class="sr-only"
                        >
                        <label for="create-photo-input" class="app-btn app-quick-create__camera">
                            Camera
                        </label>
                        <button type="submit" class="app-btn app-btn-primary app-quick-create__submit">
                            Create
                        </button>
                    </div>
                    <p id="create-photo-selected" class="app-form__hint">Choose a photo. Maximum {{ \App\Support\UploadLimits::label() }}.</p>

                    @error('photo')
                        <p class="app-notice text-xs">{{ $message }}</p>
                    @enderror
                    <p class="app-notice hidden text-xs" data-upload-error></p>
                    @error('name')
                        <p class="app-notice text-xs">{{ $message }}</p>
                    @enderror
                </form>
            @else
                <p class="app-muted px-4 py-4 text-sm">Verify your Auth0 email before creating objects from photos.</p>
            @endif
        </div>

        <div class="app-panel">
            <div class="app-divider flex items-center justify-between gap-4 border-b px-4 py-3">
                <div>
                    <h2 class="app-muted text-sm font-semibold uppercase tracking-[0.2em]">Label sheets</h2>
                    <p class="app-muted mt-1 text-xs">Print QR labels now; register each object after it is applied.</p>
                </div>
                <a href="{{ route('labels.create') }}" class="app-btn">Print labels</a>
            </div>
        </div>

        <div class="app-panel">
            <div class="app-divider border-b px-4 py-3">
                <h2 class="app-muted text-sm font-semibold uppercase tracking-[0.2em]">
                    {{ $search !== '' ? 'Search Results' : 'Existing Objects' }}
                </h2>
            </div>

            @if($search !== '')
                <div class="app-divider flex flex-wrap items-center gap-3 border-b px-4 py-3 text-sm">
                    <span class="app-muted">Showing matches for "{{ $search }}".</span>
                    <a href="{{ route('dashboard') }}">Clear search</a>
                </div>
            @endif

            @if($items->isEmpty())
                <div class="app-muted px-4 py-6 text-sm">
                    @if($search !== '')
                        No objects matched "{{ $search }}".
                    @else
                        No objects yet.
                    @endif
                </div>
            @else
                <ul class="app-divider divide-y">
                    @foreach($items as $item)
                        <li class="px-4 py-3">
                            @php $semanticUrl = $item->semanticUrl(); @endphp
                            @if ($item->featuredPhotoPath())
                                <a href="{{ $semanticUrl ?? route('items.show', ['uuid' => $item->uuid]) }}" class="mb-3 block">
                                    <img src="{{ route('media.show', ['path' => $item->featuredPhotoPath()]) }}" alt="Featured photo for {{ $item->name }}" class="max-h-48 w-full rounded object-cover" loading="lazy" decoding="async">
                                </a>
                            @endif
                            <a
                                href="{{ $semanticUrl ?? route('items.show', ['uuid' => $item->uuid]) }}"
                                class="app-accent text-sm hover:underline"
                            >
                                {{ $item->name }}
                            </a>
                            <p class="app-muted mt-1 text-xs">{{ $item->typeLabel() }}</p>
                            @if($item->wikidata_qid || $semanticUrl)
                                <p class="app-muted mt-1 break-all text-xs">
                                    @if($semanticUrl)
                                        {{ $semanticUrl }}
                                    @endif
                                    @if($item->wikidata_qid)
                                        {{ $semanticUrl ? ' | ' : '' }}{{ $item->wikidata_qid }}
                                    @endif
                                </p>
                            @endif
                            @if(!empty($item->description))
                                <p class="app-muted mt-1 text-xs">
                                    {{ $item->description }}
                                </p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>
</x-layouts.app>
