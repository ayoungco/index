@extends('items.layout', ['title' => 'Initialize Item'])

@section('content')
<section class="border border-white p-4 space-y-3">
    <h1 class="text-orange-500">INITIALIZE OBJECT</h1>
    <p class="text-sm break-all">UUID: {{ $uuid }}</p>

    <form method="post" action="{{ route('items.initialize.store', $uuid) }}" class="space-y-3">
        @csrf
        <div>
            <label class="block text-sm mb-1">Name</label>
            <input name="name" class="w-full bg-black border border-zinc-600 p-2" required>
        </div>
        <div>
            <label class="block text-sm mb-1">Description</label>
            <textarea name="description" rows="4" class="w-full bg-black border border-zinc-600 p-2"></textarea>
        </div>
        <button class="border border-orange-500 text-orange-400 px-3 py-2">SAVE OBJECT</button>
    </form>
</section>
@endsection
