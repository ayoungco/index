<x-layouts.app :title="__('Print Label Sheet')">
    <section class="app-shell text-sm">
        <div class="app-panel max-w-xl">
            <div class="app-divider border-b px-4 py-3">
                <h1 class="app-title">Print label sheet</h1>
            </div>

            <form method="POST" action="{{ route('labels.print') }}" class="app-form px-4 py-4">
                @csrf
                <p class="app-muted mb-4 text-xs">Set the overall printable media and grid you are loading. For four portrait 4 × 6 labels, use 8 × 12 media with two columns and two rows. Your browser's print dialog remains the final paper-size setting.</p>
                <div class="grid grid-cols-2 gap-3">
                    <label class="app-form__field"><span class="app-form__label">Media width (in)</span><input type="number" name="media_width" min="1" max="24" step="0.01" value="{{ old('media_width', 8) }}" class="app-field" required autofocus></label>
                    <label class="app-form__field"><span class="app-form__label">Media height (in)</span><input type="number" name="media_height" min="1" max="36" step="0.01" value="{{ old('media_height', 12) }}" class="app-field" required></label>
                    <label class="app-form__field"><span class="app-form__label">Columns</span><input type="number" name="columns" min="1" max="10" value="{{ old('columns', 2) }}" class="app-field" required></label>
                    <label class="app-form__field"><span class="app-form__label">Rows</span><input type="number" name="rows" min="1" max="15" value="{{ old('rows', 2) }}" class="app-field" required></label>
                </div>
                <label class="app-form__field mt-3">
                    <span class="app-form__label">Labels to generate</span>
                    <input
                        type="number"
                        name="quantity"
                        min="1"
                        max="30"
                        value="{{ old('quantity', 4) }}"
                        class="app-field"
                        required
                        autofocus
                    >
                    <span class="app-form__hint">Up to 30 uninitialized QR labels. Empty cells stay blank.</span>
                </label>

                @foreach (['quantity', 'media_width', 'media_height', 'columns', 'rows'] as $field)
                    @error($field)<p class="app-notice text-xs">{{ $message }}</p>@enderror
                @endforeach

                <div class="app-form__actions">
                    <button type="submit" class="app-btn app-btn-primary">Generate printable sheet</button>
                </div>
            </form>
        </div>
    </section>
</x-layouts.app>
