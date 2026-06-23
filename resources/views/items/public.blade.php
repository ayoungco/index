<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body>
    <main class="item-page text-sm">
        <div class="item-page__label">
            @include('items.partials.label', ['item' => $item, 'qrSvg' => $qrSvg])
        </div>

        <section class="item-page__content">
            <div class="app-divider grid gap-3 border-b pb-4">
                <p class="break-all text-xs">{{ $itemUrl }}</p>

                @if ($item->description)
                    <p class="app-muted">{{ $item->description }}</p>
                @endif
            </div>

            <p class="app-muted mt-3">Public view. Log in to post timeline media.</p>
            <a href="{{ $loginUrl }}" class="app-btn app-btn mt-4">Login With Auth0</a>

            @include('items.partials.wikidata', ['wikidata' => $wikidata, 'semanticUrl' => $semanticUrl])

            <div class="mt-6">
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
        </section>
    </main>
    <script>
        document.querySelectorAll('[data-timeline-image]').forEach((details) => {
            details.addEventListener('toggle', () => {
                const image = details.querySelector('img[data-src]');

                if (details.open && image) {
                    image.src = image.dataset.src;
                    image.removeAttribute('data-src');
                }
            }, { once: true });
        });
    </script>
</body>
</html>
