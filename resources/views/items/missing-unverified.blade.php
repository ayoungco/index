@extends('items.layout', ['title' => 'Verify Email Required'])

@section('content')
<section class="border border-white p-4 space-y-2">
    <p class="text-orange-500">EMAIL VERIFICATION REQUIRED</p>
    <p class="break-all">{{ $uuid }}</p>
    <p class="text-sm text-zinc-400">Your Auth0 email must be verified before initializing new objects or posting events.</p>
</section>
@endsection
