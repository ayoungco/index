<x-layouts.app :title="__('Dashboard')">
    <section class="terminal-shell">
        <div class="terminal-panel">
            <div class="terminal-divider border-b px-4 py-3">
                <h2 class="terminal-muted text-sm font-semibold uppercase tracking-[0.2em]">
                    Existing Objects
                </h2>
            </div>

            @if($items->isEmpty())
                <div class="terminal-muted px-4 py-6 text-sm">
                    No objects yet.
                </div>
            @else
                <ul class="terminal-divider divide-y">
                    @foreach($items as $item)
                        <li class="px-4 py-3">
                            <a
                                href="{{ route('items.show', ['uuid' => $item->uuid]) }}"
                                class="terminal-accent text-sm hover:underline"
                            >
                                {{ $item->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>
</x-layouts.app>
