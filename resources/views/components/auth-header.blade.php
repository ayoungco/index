@props([
    'title',
    'description',
])

<div class="mb-4">
    <h1 class="app-title">{{ $title }}</h1>
    <p class="app-muted mt-1 text-sm">{{ $description }}</p>
</div>
