@extends('items.layout', ['title' => $item->name])

@section('content')
<section class="border border-white p-4 space-y-2">
    <h1 class="text-orange-500 text-lg">{{ $item->name }}</h1>
    <p class="text-xs break-all">UUID: {{ $item->uuid }}</p>
    <p class="text-zinc-300">{{ $item->description }}</p>
    <a href="{{ route('items.print', $item->uuid) }}" class="inline-block border border-orange-500 px-3 py-2 text-orange-400">PRINT LABEL</a>
</section>

<section class="border border-zinc-700 p-4 space-y-3">
    <h2 class="text-sm text-zinc-300">Add photo event</h2>
    <form method="post" action="{{ route('items.photo.store', $item->uuid) }}" enctype="multipart/form-data" class="space-y-3">
        @csrf
        <input type="file" name="photo" accept="image/*" capture="environment" class="block w-full text-sm">
        <button class="border border-orange-500 text-orange-400 px-3 py-2">UPLOAD</button>
    </form>
</section>

<section class="border border-zinc-700 p-4 space-y-3">
    <h2 class="text-sm text-zinc-300">Full timeline</h2>
    @forelse ($item->events as $event)
        <article class="border border-zinc-700 p-2 text-sm space-y-2">
            <p>{{ $event->created_at?->toDayDateTimeString() }} by {{ $event->user?->name ?? 'unknown' }}</p>
            <p class="{{ $event->is_qr_verified ? 'text-green-400' : 'text-orange-400' }}">
                {{ $event->is_qr_verified ? 'QR verified' : 'Flagged: QR not readable/matching' }}
            </p>
            <img src="{{ asset('storage/'.$event->image_path) }}" alt="Event photo" class="w-full border border-zinc-700">
        </article>
    @empty
        <p class="text-zinc-500 text-sm">No timeline events yet.</p>
    @endforelse
</section>
@endsection
