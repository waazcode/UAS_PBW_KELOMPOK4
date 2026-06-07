<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-midnight">Masuk</h1>
        <p class="text-sm text-grape-mist mt-1">Akses akun SafeZone Anda</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-grape-mist text-neptune focus:ring-neptune" name="remember">
                <span class="ms-2 text-sm text-neptune/70">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
            @if (Route::has('password.request'))
                <a class="text-sm text-neptune hover:text-midnight underline underline-offset-2" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
            <x-primary-button class="w-full sm:w-auto justify-center">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    @if (Route::has('register'))
        <p class="text-center text-sm text-grape-mist mt-6">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-semibold text-neptune hover:text-midnight">Daftar</a>
        </p>
    @endif
</x-guest-layout>
