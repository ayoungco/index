<x-layouts.app :title="__('Profile')">
    <section class="app-shell w-full">
        @include('partials.settings-heading')

        <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
            <form method="POST" action="{{ route('settings.profile.update') }}" class="my-6 w-full space-y-6">
                @csrf
                @method('PUT')

                <label class="grid gap-1 text-sm">
                    <span>{{ __('Name') }}</span>
                    <input class="app-field" name="name" value="{{ old('name', auth()->user()->name) }}" type="text" required autofocus autocomplete="name">
                    @error('name')
                        <span class="app-notice text-xs">{{ $message }}</span>
                    @enderror
                </label>

                <div>
                    <label class="grid gap-1 text-sm">
                        <span>{{ __('Email') }}</span>
                        <input class="app-field" name="email" value="{{ old('email', auth()->user()->email) }}" type="email" required autocomplete="email">
                        @error('email')
                            <span class="app-notice text-xs">{{ $message }}</span>
                        @enderror
                    </label>

                    @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                        <p class="mt-4 text-sm">
                            {{ __('Your email address is unverified.') }}
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    @endif
                </div>

                <div class="flex items-center gap-4">
                    <button class="app-btn" type="submit">{{ __('Save') }}</button>

                    @if (session('status') === 'profile-updated')
                        <p class="text-sm">{{ __('Saved.') }}</p>
                    @endif
                </div>
            </form>

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button class="underline" type="submit">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </form>
            @endif
        </x-settings.layout>
    </section>
</x-layouts.app>
