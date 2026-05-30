<x-layouts.auth :title="__('Verify email')">
    <div class="mt-4 grid gap-4">
        <p class="text-center">
            {{ __('Please verify your email address by clicking on the link we just emailed to you.') }}
        </p>

        @if (session('status') == 'verification-link-sent')
            <p class="text-center font-medium">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </p>
        @endif

        <div class="grid gap-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="app-btn w-full">
                    {{ __('Resend verification email') }}
                </button>
            </form>

            <a class="underline" href="{{ url('/logout') }}">{{ __('Log out') }}</a>
        </div>
    </div>
</x-layouts.auth>
