<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Item Terminal' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black text-white font-mono min-h-screen">
<div class="max-w-3xl mx-auto p-4 sm:p-6 space-y-4">
    <header class="border border-orange-500 p-3">
        <p class="text-orange-500">index://terminal</p>
        <p class="text-xs text-zinc-400">Scan log + object timeline</p>
    </header>

    @if (session('status'))
        <div class="border border-orange-500 p-3 text-orange-300">{{ session('status') }}</div>
    @endif

    @if (session('error'))
        <div class="border border-red-500 p-3 text-red-300">{{ session('error') }}</div>
    @endif

    @yield('content')
</div>
</body>
</html>
