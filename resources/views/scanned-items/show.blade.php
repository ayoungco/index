<x-layouts.app :title="$item->name">
    <section class="mx-auto w-full max-w-5xl p-4 font-mono text-sm text-white sm:p-6">
        <div class="border border-emerald-400 bg-black p-4 sm:p-6">
            <div class="flex flex-wrap items-start justify-between gap-3 border-b border-zinc-800 pb-4">
                <div>
                    <p class="text-emerald-400">UUID: {{ $item->uuid }}</p>
                    <h1 class="mt-2 text-xl font-bold sm:text-2xl">{{ $item->name }}</h1>
                    @if ($item->description)
                        <p class="mt-2 max-w-3xl text-zinc-200">{{ $item->description }}</p>
                    @endif
                </div>

                @if ($isAuthenticated)
                    <a
                        href="{{ route('scanned-items.print', ['uuid' => $item->uuid]) }}"
                        class="border border-emerald-400 px-4 py-2 text-emerald-300 hover:bg-emerald-400 hover:text-black"
                    >
                        Print Label
                    </a>
                @endif
            </div>

            @if (session('status'))
                <p class="mt-4 border border-emerald-500 bg-emerald-950 p-3 text-emerald-300">{{ session('status') }}</p>
            @endif

            @if ($isAuthenticated)
                <div class="mt-4 border border-zinc-700 p-4">
                    <h2 class="text-base font-semibold text-emerald-300">Add Photo Event</h2>
                    <p class="mt-1 text-zinc-300">Uploads are compressed server-side and checked for matching QR code.</p>

                    @if ($canPost)
                        <form method="POST" action="{{ route('scanned-items.events.store', ['uuid' => $item->uuid]) }}" enctype="multipart/form-data" class="mt-3 grid gap-3 sm:flex sm:items-end sm:gap-4">
                            @csrf
                            <label class="grid gap-1">
                                <span class="text-emerald-300">Photo</span>
                                <input
                                    type="file"
                                    name="photo"
                                    accept="image/*"
                                    capture="environment"
                                    required
                                    class="border border-emerald-400 bg-black px-2 py-2 text-zinc-100 file:mr-3 file:border-0 file:bg-emerald-400 file:px-3 file:py-1 file:font-mono file:text-black"
                                >
                            </label>

                            <button type="submit" class="border border-emerald-400 px-4 py-2 text-emerald-300 hover:bg-emerald-400 hover:text-black">
                                Add Photo
                            </button>
                        </form>
                    @else
                        <p class="mt-2 text-zinc-300">Verify your Auth0 email before posting timeline events.</p>
                    @endif
                </div>
            @endif

            <div class="mt-5">
                <h2 class="text-base font-semibold text-emerald-300">Timeline</h2>

                @if ($item->events->isEmpty())
                    <p class="mt-2 text-zinc-300">No events yet.</p>
                @else
                    <ul class="mt-3 grid gap-3">
                        @foreach ($item->events as $event)
                            <li class="border border-zinc-700 p-3">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p class="text-zinc-100">{{ $event->created_at?->toDateTimeString() }}</p>
                                    <span class="border px-2 py-0.5 text-xs {{ $event->is_qr_verified ? 'border-emerald-500 text-emerald-400' : 'border-amber-500 text-amber-300' }}">
                                        {{ $event->is_qr_verified ? 'QR verified' : 'QR flagged' }}
                                    </span>
                                </div>
                                <p class="mt-1 text-zinc-300">Posted by: {{ $event->author?->name ?? 'Unknown user' }}</p>

                                @if ($event->image_path)
                                    @if ($isAuthenticated)
                                        <img
                                            src="{{ asset('storage/'.$event->image_path) }}"
                                            alt="Item event image"
                                            class="mt-3 max-h-72 w-full border border-zinc-800 object-contain"
                                            loading="lazy"
                                        >
                                    @else
                                        <div class="mt-3 border border-zinc-800 p-2 text-zinc-400">
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
</x-layouts.app>
