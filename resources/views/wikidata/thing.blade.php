<x-layouts.app :title="'Wikidata: ' . ($label ?? $qid)">
    <div class="max-w-3xl mx-auto p-4">
        <h1 class="text-2xl font-semibold">{{ $label }} <span class="text-gray-500">({{ $qid }})</span></h1>
        @if(!empty($desc))
            <p class="text-gray-700 mb-3">{{ $desc }}</p>
        @endif

        @if(!empty($instances) && count($instances))
            <p class="mb-2"><strong>Instance of:</strong>
                @foreach($instances as $i)
                    <a class="underline" href="{{ url('/wd/item/'.$i) }}">{{ $i }}</a>@if(!$loop->last),@endif
                @endforeach
            </p>
        @endif

        <details class="mt-4">
            <summary class="cursor-pointer underline">Raw entity JSON (debug)</summary>
            <pre class="text-xs overflow-x-auto">{{ json_encode($entity, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
        </details>
    </div>
</x-layouts.app>
