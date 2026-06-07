<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SafeZone') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-midnight antialiased bg-cheviot">
        <div class="min-h-screen flex flex-col">
            @include('layouts.navigation')

            <div class="flex-1 flex flex-col justify-center items-center px-4 py-12">
                <div class="w-full max-w-md">
                    <div class="card p-6 sm:p-8">
                        {{ $slot }}
                    </div>
                    <p class="text-center text-sm text-grape-mist mt-6">
                        &copy; {{ date('Y') }} SafeZone
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
