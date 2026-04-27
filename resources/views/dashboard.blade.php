<x-layouts.app :title="__('Dashboard')">
    <section class="terminal-shell space-y-4">
        <div class="terminal-panel">
            <div class="terminal-divider border-b px-4 py-3">
                <h2 class="terminal-muted text-sm font-semibold uppercase tracking-[0.2em]">
                    Existing Objects
                </h2>
            </div>

            <form method="GET" action="{{ route('dashboard') }}" class="terminal-divider border-b px-4 py-3">
                <label for="item-search" class="terminal-muted mb-2 block text-xs font-semibold uppercase tracking-[0.2em]">
                    Rapid Full Text Search
                </label>
                <div class="flex gap-2">
                    <input
                        id="item-search"
                        name="q"
                        type="search"
                        value="{{ $search }}"
                        placeholder="Search item names and descriptions"
                        class="terminal-divider w-full rounded-md border bg-transparent px-3 py-2 text-sm"
                        autocomplete="off"
                    >
                    <button type="submit" class="terminal-divider rounded-md border px-3 py-2 text-sm">
                        Search
                    </button>
                    @if($search !== '')
                        <a href="{{ route('dashboard') }}" class="terminal-divider rounded-md border px-3 py-2 text-sm">
                            Clear
                        </a>
                    @endif
                </div>
            </form>

            @if($items->isEmpty())
                <div class="terminal-muted px-4 py-6 text-sm">
                    @if($search !== '')
                        No objects matched "{{ $search }}".
                    @else
                        No objects yet.
                    @endif
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
                            @if(!empty($item->description))
                                <p class="terminal-muted mt-1 text-xs">
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
