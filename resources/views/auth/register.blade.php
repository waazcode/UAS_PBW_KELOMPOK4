<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-midnight">Daftar</h1>
        <p class="text-sm text-grape-mist mt-1">Buat akun SafeZone baru</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
            <a class="text-sm text-neptune hover:text-midnight underline underline-offset-2" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
            <x-primary-button class="w-full sm:w-auto justify-center">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
