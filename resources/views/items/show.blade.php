<x-layouts.app :title="$item->name">
    <section class="terminal-shell text-sm">
        <div class="terminal-panel">
            <div class="terminal-divider flex flex-wrap items-start justify-between gap-3 border-b pb-4">
                <div>
                    <p class="terminal-accent">UUID: {{ $item->uuid }}</p>
                    <h1 class="terminal-title mt-2">{{ $item->name }}</h1>
                    @if ($item->description)
                        <p class="terminal-muted mt-2 max-w-3xl">{{ $item->description }}</p>
                    @endif
                </div>

                @if ($isAuthenticated)
                    <a
                        href="{{ route('items.print', ['uuid' => $item->uuid]) }}"
                        class="terminal-btn terminal-btn-accent"
                    >
                        Print Label
                    </a>
                @endif
            </div>

            @if (session('status'))
                <p class="mt-4 border border-emerald-500 bg-emerald-950 p-3 text-emerald-300">{{ session('status') }}</p>
            @endif

            @if ($isAuthenticated)
                <div class="terminal-divider mt-4 border p-4">
                    <h2 class="terminal-accent text-base font-semibold">Add Photo Event</h2>
                    <p class="terminal-muted mt-1">Use camera capture or choose an existing image. Uploads are compressed and QR-verified server-side.</p>

                    @if ($canPost)
                        @php
                            $pickerId = 'photo-input-'.$item->id;
                            $cameraId = 'camera-trigger-'.$item->id;
                            $galleryId = 'gallery-trigger-'.$item->id;
                            $nameId = 'photo-name-'.$item->id;
                        @endphp

                        <form method="POST" action="{{ route('items.events.store', ['uuid' => $item->uuid]) }}" enctype="multipart/form-data" class="mt-3 grid gap-3">
                            @csrf

                            <input
                                id="{{ $pickerId }}"
                                type="file"
                                name="photo"
                                accept="image/*"
                                capture="environment"
                                required
                                class="sr-only"
                            >

                            <div class="flex flex-wrap items-center gap-2">
                                <button id="{{ $cameraId }}" type="button" class="terminal-btn terminal-btn-accent">Open Camera</button>
                                <button id="{{ $galleryId }}" type="button" class="terminal-btn">Choose Existing</button>
                                <span id="{{ $nameId }}" class="terminal-muted text-xs">No file selected</span>
                            </div>

                            <noscript>
                                <label class="grid gap-1">
                                    <span class="terminal-accent">Photo</span>
                                    <input
                                        type="file"
                                        name="photo"
                                        accept="image/*"
                                        capture="environment"
                                        required
                                        class="terminal-field"
                                    >
                                </label>
                            </noscript>

                            <div>
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

                @if ($item->events->isEmpty())
                    <p class="terminal-muted mt-2">No events yet.</p>
                @else
                    <ul class="mt-3 grid gap-3">
                        @foreach ($item->events as $event)
                            <li class="terminal-divider border p-3">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p>{{ $event->created_at?->toDateTimeString() }}</p>
                                    <span class="border px-2 py-0.5 text-xs {{ $event->is_qr_verified ? 'border-emerald-500 text-emerald-500' : 'border-amber-500 text-amber-500' }}">
                                        {{ $event->is_qr_verified ? 'QR verified' : 'QR flagged' }}
                                    </span>
                                </div>
                                <p class="terminal-muted mt-1">Posted by: {{ $event->author?->name ?? 'Unknown user' }}</p>

                                @if ($event->image_path)
                                    @if ($isAuthenticated)
                                        <img
                                            src="{{ asset('storage/'.$event->image_path) }}"
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
                const camera = document.getElementById(`camera-trigger-${id}`);
                const gallery = document.getElementById(`gallery-trigger-${id}`);
                const name = document.getElementById(`photo-name-${id}`);

                if (! picker || picker.dataset.bound === '1') {
                    return;
                }

                picker.dataset.bound = '1';

                camera?.addEventListener('click', () => {
                    picker.setAttribute('capture', 'environment');
                    picker.click();
                });

                gallery?.addEventListener('click', () => {
                    picker.removeAttribute('capture');
                    picker.click();
                });

                picker.addEventListener('change', () => {
                    const file = picker.files?.[0];
                    if (name) {
                        name.textContent = file ? file.name : 'No file selected';
                    }
                });
            };
        </script>
    @endonce

    <script>
        window.bindItemPhotoPicker('{{ $item->id }}');
    </script>
</x-layouts.app>
