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
                <form method="POST" action="{{ route('items.from-photo.store') }}" enctype="multipart/form-data" class="grid gap-4 px-4 py-4">
                    @csrf

                    <label class="grid gap-1">
                        <span class="app-accent text-xs uppercase tracking-[0.16em]">Photo</span>
                        <input
                            type="file"
                            name="photo"
                            accept="image/*,.heic,.heif"
                            capture="environment"
                            required
                            class="app-field"
                        >
                        <span class="app-muted text-xs">Required. Take or choose a photo to create an object before you attach its QR label.</span>
                    </label>

                    <label class="grid gap-1">
                        <span class="app-accent text-xs uppercase tracking-[0.16em]">Name</span>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            maxlength="255"
                            class="app-field"
                            placeholder="Optional — defaults to Photo item timestamp"
                        >
                    </label>

                    <label class="grid gap-1">
                        <span class="app-accent text-xs uppercase tracking-[0.16em]">Initial Note</span>
                        <textarea
                            name="description"
                            rows="3"
                            maxlength="5000"
                            class="app-field resize-y"
                            placeholder="Optional description or context"
                        >{{ old('description') }}</textarea>
                    </label>

                    <label class="grid gap-1">
                        <span class="app-accent text-xs uppercase tracking-[0.16em]">Wikidata QID</span>
                        <input
                            type="text"
                            name="wikidata_qid"
                            value="{{ old('wikidata_qid') }}"
                            maxlength="32"
                            pattern="Q[1-9][0-9]*"
                            class="app-field"
                            placeholder="Optional, e.g. Q629"
                        >
                    </label>

                    @error('photo')
                        <p class="app-notice text-xs">{{ $message }}</p>
                    @enderror
                    @error('name')
                        <p class="app-notice text-xs">{{ $message }}</p>
                    @enderror
                    @error('description')
                        <p class="app-notice text-xs">{{ $message }}</p>
                    @enderror
                    @error('wikidata_qid')
                        <p class="app-notice text-xs">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="app-btn app-btn w-full sm:w-auto">
                        Create Item From Photo
                    </button>
                </form>
            @else
                <p class="app-muted px-4 py-4 text-sm">Verify your Auth0 email before creating objects from photos.</p>
            @endif
        </div>

        <div class="app-panel">
            <div class="app-divider border-b px-4 py-3">
                <h2 class="app-muted text-sm font-semibold uppercase tracking-[0.2em]">
                    Existing Objects
                </h2>
            </div>

            <form method="GET" action="{{ route('dashboard') }}" class="app-divider border-b px-4 py-3">
                <label for="item-search" class="app-muted mb-2 block text-xs font-semibold uppercase tracking-[0.2em]">
                    Rapid Full Text Search
                </label>
                <div class="flex gap-2">
                    <input
                        id="item-search"
                        name="q"
                        type="search"
                        value="{{ $search }}"
                        placeholder="Search item names and descriptions"
                        class="app-divider w-full rounded-md border bg-transparent px-3 py-2 text-sm"
                        autocomplete="off"
                    >
                    <button type="submit" class="app-divider rounded-md border px-3 py-2 text-sm">
                        Search
                    </button>
                    @if($search !== '')
                        <a href="{{ route('dashboard') }}" class="app-divider rounded-md border px-3 py-2 text-sm">
                            Clear
                        </a>
                    @endif
                </div>
            </form>

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
                            <a
                                href="{{ $semanticUrl ?? route('items.show', ['uuid' => $item->uuid]) }}"
                                class="app-accent text-sm hover:underline"
                            >
                                {{ $item->name }}
                            </a>
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
