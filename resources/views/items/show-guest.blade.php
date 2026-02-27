@extends('items.layout', ['title' => $item->name])

@section('content')
<section class="border border-white p-4 space-y-2">
    <h1 class="text-orange-500 text-lg">{{ $item->name }}</h1>
    <p class="text-xs break-all">{{ $item->uuid }}</p>
    <p class="text-zinc-300">{{ $item->description }}</p>
</section>

<section class="border border-zinc-700 p-4 space-y-3">
    <h2 class="text-sm text-zinc-300">Condensed timeline</h2>
    @forelse ($item->events->take(5) as $event)
        <article class="border border-zinc-700 p-2 text-sm">
            <p>{{ $event->created_at?->diffForHumans() }}</p>
            <p class="{{ $event->is_qr_verified ? 'text-green-400' : 'text-orange-400' }}">
                {{ $event->is_qr_verified ? 'QR verified' : 'Unverified QR upload' }}
            </p>
        </article>
    @empty
        <p class="text-zinc-500 text-sm">No timeline events yet.</p>
    @endforelse
</section>
@endsection
