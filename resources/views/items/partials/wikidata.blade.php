@if ($wikidata || $semanticUrl)
    <div class="app-divider mt-4 border p-4">
        <h2 class="app-accent text-base font-semibold">Wikidata</h2>

        @if ($wikidata)
            <p class="mt-2 font-semibold">
                <a href="{{ route('wikidata.item.show', ['qid' => $wikidata['qid']]) }}" class="hover:underline">
                    {{ $wikidata['label'] }} ({{ $wikidata['qid'] }})
                </a>
            </p>

            @if ($wikidata['description'])
                <p class="app-muted mt-1">{{ $wikidata['description'] }}</p>
            @endif

            @if (! empty($wikidata['instances']))
                <p class="app-muted mt-2 break-all text-xs">
                    Instance of:
                    @foreach ($wikidata['instances'] as $instance)
                        <a href="{{ route('wikidata.item.show', ['qid' => $instance]) }}" class="app-accent hover:underline">{{ $instance }}</a>@if (! $loop->last), @endif
                    @endforeach
                </p>
            @endif
        @endif

        @if ($semanticUrl)
            <p class="app-muted mt-2 break-all text-xs">Semantic URL: <a href="{{ $semanticUrl }}" class="app-accent hover:underline">{{ $semanticUrl }}</a></p>
        @endif
    </div>
@endif
