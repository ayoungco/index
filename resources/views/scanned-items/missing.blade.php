<x-layouts.app :title="__('Scan')">
    <section class="mx-auto w-full max-w-4xl p-4 font-mono text-sm text-white sm:p-6">
        <div class="border border-emerald-400 bg-black p-4 sm:p-6">
            <p class="text-emerald-400">[SCAN TARGET: {{ $uuid }}]</p>
            <h1 class="mt-2 text-xl font-bold sm:text-2xl">Object Not Initialized</h1>

            @if (session('status'))
                <p class="mt-4 border border-emerald-500 bg-emerald-950 p-3 text-emerald-300">{{ session('status') }}</p>
            @endif

            @if ($canInitialize)
                <p class="mt-4 text-zinc-200">Initialize this object now.</p>

                <form method="POST" action="{{ route('scanned-items.initialize', ['uuid' => $uuid]) }}" class="mt-4 grid gap-4">
                    @csrf
                    <label class="grid gap-1">
                        <span class="text-emerald-300">Name</span>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            class="border border-emerald-400 bg-black px-3 py-2 text-white focus:outline-none focus:ring-1 focus:ring-emerald-400"
                        >
                    </label>

                    <label class="grid gap-1">
                        <span class="text-emerald-300">Description</span>
                        <textarea
                            name="description"
                            rows="4"
                            class="border border-emerald-400 bg-black px-3 py-2 text-white focus:outline-none focus:ring-1 focus:ring-emerald-400"
                        >{{ old('description') }}</textarea>
                    </label>

                    <button type="submit" class="w-full border border-emerald-400 px-4 py-2 text-emerald-300 hover:bg-emerald-400 hover:text-black sm:w-auto">
                        Initialize Object
                    </button>
                </form>
            @else
                <p class="mt-4 text-zinc-200">Your Auth0 account email must be verified before initializing objects.</p>
            @endif
        </div>
    </section>
</x-layouts.app>
