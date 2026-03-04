<x-layouts.app :title="__('Scan')">
    <section class="terminal-shell text-sm">
        <div class="terminal-panel max-w-4xl">
            <p class="terminal-accent">[SCAN TARGET: {{ $uuid }}]</p>
            <h1 class="terminal-title mt-2">Object Not Initialized</h1>

            @if (session('status'))
                <p class="mt-4 border border-emerald-500 bg-emerald-950 p-3 text-emerald-300">{{ session('status') }}</p>
            @endif

            @if ($canInitialize)
                <p class="terminal-muted mt-4">Initialize this object now.</p>

                <form method="POST" action="{{ route('items.initialize', ['uuid' => $uuid]) }}" class="mt-4 grid gap-4">
                    @csrf
                    <label class="grid gap-1">
                        <span class="terminal-accent">Name</span>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            class="terminal-field"
                        >
                    </label>

                    <label class="grid gap-1">
                        <span class="terminal-accent">Description</span>
                        <textarea
                            name="description"
                            rows="4"
                            class="terminal-field"
                        >{{ old('description') }}</textarea>
                    </label>

                    <button type="submit" class="terminal-btn terminal-btn-accent w-full sm:w-auto">
                        Initialize Object
                    </button>
                </form>
            @else
                <p class="terminal-muted mt-4">Your Auth0 account email must be verified before initializing objects.</p>
            @endif
        </div>
    </section>
</x-layouts.app>
