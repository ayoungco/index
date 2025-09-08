<x-layouts.app :title="'Wikidata Type: ' . ucfirst($type)">
    <div class="max-w-3xl mx-auto p-4">
        <h1 class="text-2xl font-semibold">Type: {{ ucfirst($type) }} (wd:{{ $qid }})</h1>
        <ul class="list-disc ml-5 mt-3">
            @foreach($items as $it)
                <li>
                    <a class="underline" href="{{ url('/wd/thing/'.$it['qid']) }}">
                        {{ $it['label'] ?? $it['qid'] }} ({{ $it['qid'] }})
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</x-layouts.app>

