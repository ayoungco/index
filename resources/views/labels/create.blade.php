<x-layouts.app :title="__('Print Label Sheet')">
    <section class="app-shell text-sm">
        <div class="app-panel max-w-xl">
            <div class="app-divider border-b px-4 py-3">
                <h1 class="app-title">Print label sheet</h1>
            </div>

            <form method="POST" action="{{ route('labels.print') }}" class="app-form px-4 py-4">
                @csrf
                <label class="app-form__field">
                    <span class="app-form__label">Labels</span>
                    <input
                        type="number"
                        name="quantity"
                        min="1"
                        max="30"
                        value="{{ old('quantity', 30) }}"
                        class="app-field"
                        required
                        autofocus
                    >
                    <span class="app-form__hint">Up to 30 QR labels on one US Letter sheet. Each code opens an uninitialized object URL.</span>
                </label>

                @error('quantity')
                    <p class="app-notice text-xs">{{ $message }}</p>
                @enderror

                <div class="app-form__actions">
                    <button type="submit" class="app-btn app-btn-primary">Generate printable sheet</button>
                </div>
            </form>
        </div>
    </section>
</x-layouts.app>
