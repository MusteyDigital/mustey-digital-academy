<x-guest-layout>
    <div class="mb-5">
        <h1 class="text-xl font-bold text-slate-800">Verify your email</h1>
        <p class="text-sm text-slate-500 mt-2">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 font-medium text-sm text-green-700">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="flex items-center justify-between flex-wrap gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <x-primary-button>
                {{ __('Resend Verification Email') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="text-sm text-slate-500 hover:text-slate-700 hover:underline font-medium">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>