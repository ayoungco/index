<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <div class="border-b border-neutral-200 px-4 py-3 dark:border-neutral-700">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral-700 dark:text-neutral-200">
                    Existing Objects
                </h2>
            </div>

            @if($items->isEmpty())
                <div class="px-4 py-6 text-sm text-neutral-600 dark:text-neutral-300">
                    No objects yet.
                </div>
            @else
                <ul class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @foreach($items as $item)
                        <li class="px-4 py-3">
                            <a
                                href="{{ route('items.show', ['uuid' => $item->uuid]) }}"
                                class="text-sm text-neutral-900 hover:underline dark:text-neutral-100"
                            >
                                {{ $item->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-layouts.app>
