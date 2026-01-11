<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white text-zinc-900">
        <main class="mx-auto flex w-full max-w-3xl flex-col gap-6 px-6 py-12">
            <a class="text-sm font-semibold text-zinc-500 hover:text-zinc-900" href="/">index</a>

            <header class="space-y-2">
                <p class="text-xs uppercase tracking-[0.2em] text-zinc-400">Thing</p>
                <h1 class="text-3xl font-semibold">
                    {{ $thing->slug ?: 'Unnamed Thing' }}
                </h1>
                @if (! empty($requestedSlug) && $requestedSlug !== $thing->slug)
                    <p class="text-sm text-zinc-500">Requested: {{ $requestedSlug }}</p>
                @endif
            </header>

            <section class="rounded-lg border border-zinc-200 bg-zinc-50/60 p-6">
                <dl class="grid gap-4 text-sm">
                    <div>
                        <dt class="text-zinc-500">ID</dt>
                        <dd class="font-medium text-zinc-900">{{ $thing->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-500">Slug</dt>
                        <dd class="font-medium text-zinc-900">{{ $thing->slug ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-500">Created</dt>
                        <dd class="font-medium text-zinc-900">{{ optional($thing->created_at)->toDayDateTimeString() ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-500">Updated</dt>
                        <dd class="font-medium text-zinc-900">{{ optional($thing->updated_at)->toDayDateTimeString() ?? '—' }}</dd>
                    </div>
                </dl>
            </section>

            <p class="text-sm text-zinc-500">
                This is a registered Thing in index. Share this URL to reference it anywhere.
            </p>
        </main>
    </body>
</html>
