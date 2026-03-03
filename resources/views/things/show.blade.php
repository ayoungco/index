<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-100 text-zinc-950">
        <main class="mx-auto flex w-full max-w-6xl flex-col gap-8 px-4 py-8 sm:px-6 sm:py-10 lg:px-10">
            <a class="text-sm font-semibold uppercase tracking-[0.12em] text-zinc-600 hover:text-zinc-900" href="/">index</a>

            @if (! empty($status))
                <section class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ $status }}
                </section>
            @endif

            <section class="rounded-3xl border-4 border-zinc-950 bg-white p-6 shadow-[8px_8px_0_0_rgba(24,24,27,1)] sm:p-10">
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-zinc-500">Thing Profile</p>
                <h1 class="mt-3 break-all text-4xl font-black leading-none sm:text-6xl">
                    {{ $thing->display_name }}
                </h1>
                @if (! empty($requestedSlug) && $requestedSlug !== $thing->slug)
                    <p class="mt-3 text-sm font-medium text-zinc-500">Requested slug: {{ $requestedSlug }}</p>
                @endif

                <dl class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-2xl border-2 border-zinc-900 bg-zinc-50 p-4">
                        <dt class="text-xs font-bold uppercase tracking-[0.18em] text-zinc-500">ID</dt>
                        <dd class="mt-2 text-3xl font-black">{{ $thing->id }}</dd>
                    </div>
                    <div class="rounded-2xl border-2 border-zinc-900 bg-zinc-50 p-4">
                        <dt class="text-xs font-bold uppercase tracking-[0.18em] text-zinc-500">Slug</dt>
                        <dd class="mt-2 break-all text-xl font-extrabold">{{ $thing->slug ?? '—' }}</dd>
                    </div>
                    <div class="rounded-2xl border-2 border-zinc-900 bg-zinc-50 p-4">
                        <dt class="text-xs font-bold uppercase tracking-[0.18em] text-zinc-500">Created</dt>
                        <dd class="mt-2 text-lg font-extrabold">{{ optional($thing->created_at)->toDayDateTimeString() ?? '—' }}</dd>
                    </div>
                    <div class="rounded-2xl border-2 border-zinc-900 bg-zinc-50 p-4">
                        <dt class="text-xs font-bold uppercase tracking-[0.18em] text-zinc-500">Updated</dt>
                        <dd class="mt-2 text-lg font-extrabold">{{ optional($thing->updated_at)->toDayDateTimeString() ?? '—' }}</dd>
                    </div>
                </dl>

                <p class="mt-6 text-sm font-semibold text-zinc-700">
                    Canonical URL:
                    <a class="underline decoration-2 underline-offset-4 hover:text-zinc-950" href="{{ $thing->canonical_url }}">{{ $thing->canonical_url }}</a>
                </p>
            </section>

            @if ($needsScanPhoto ?? false)
                <section class="rounded-2xl border-2 border-amber-600 bg-amber-50 p-5">
                    <h2 class="text-lg font-black uppercase tracking-[0.12em] text-amber-900">Photo Needed</h2>
                    <p class="mt-2 text-sm font-medium text-amber-900">
                        Active scan detected. Take a photo of the QR you just scanned to complete this check-in.
                    </p>
                    <form class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end" method="POST" action="{{ route('things.scan-photo', $thing) }}" enctype="multipart/form-data">
                        @csrf
                        <label class="flex-1 text-sm font-semibold text-zinc-800">
                            QR photo
                            <input class="mt-2 block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm" type="file" name="photo" accept="image/*" capture="environment" required>
                        </label>
                        <button class="inline-flex items-center justify-center rounded-lg border-2 border-zinc-950 bg-zinc-950 px-5 py-2 text-sm font-bold uppercase tracking-[0.12em] text-white hover:bg-zinc-800" type="submit">
                            Upload
                        </button>
                    </form>
                    @error('photo')
                        <p class="mt-2 text-sm font-semibold text-red-700">{{ $message }}</p>
                    @enderror
                </section>
            @endif

            <section class="rounded-2xl border border-zinc-300 bg-white p-6">
                <h2 class="text-lg font-black uppercase tracking-[0.14em] text-zinc-900">Thing History</h2>
                <div class="mt-5">
                    <ol class="border-l-2 border-zinc-300 pl-5">
                        @forelse (($timeline ?? collect()) as $event)
                            <li class="relative mb-8 last:mb-0">
                                <span class="absolute -left-[1.82rem] top-1 inline-block h-3 w-3 rounded-full border-2 border-zinc-950 bg-zinc-950"></span>
                                <p class="text-xs font-bold uppercase tracking-[0.14em] text-zinc-500">
                                    {{ \Illuminate\Support\Carbon::parse($event['occurred_at'])->toDayDateTimeString() }}
                                </p>
                                <p class="mt-1 text-base font-black text-zinc-950">{{ $event['title'] }}</p>
                                @if (! empty($event['description']))
                                    <p class="mt-1 text-sm font-medium text-zinc-700">{{ $event['description'] }}</p>
                                @endif
                                @if (! empty($event['photo_url']))
                                    <p class="mt-2 text-sm">
                                        <a class="font-semibold underline decoration-2 underline-offset-4" href="{{ $event['photo_url'] }}" target="_blank" rel="noreferrer">View photo</a>
                                    </p>
                                @endif
                            </li>
                        @empty
                            <li class="text-sm font-medium text-zinc-600">No history events yet.</li>
                        @endforelse
                    </ol>
                </div>
            </section>
        </main>
    </body>
</html>
