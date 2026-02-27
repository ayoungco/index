@extends('items.layout', ['title' => 'Unknown UUID'])

@section('content')
<section class="border border-white p-4 space-y-2">
    <p class="text-orange-500">UUID NOT REGISTERED</p>
    <p class="break-all">{{ $uuid }}</p>
    <p class="text-sm text-zinc-400">Log in with Auth0 and verify your email to initialize this object.</p>
    <a class="inline-block border border-orange-500 px-3 py-2 text-orange-400" href="{{ route('login') }}">LOGIN</a>
</section>
@endsection
