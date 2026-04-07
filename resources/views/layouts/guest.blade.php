<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-white antialiased bg-primary">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-[#1f5a7c] to-blue-900">
            <div>
                <a href="/" wire:navigate class="flex flex-col items-center">
                    <div class="text-3xl font-extrabold tracking-widest text-white mb-2">PORTAL<span class="text-[#f97316]">LOJISTA</span></div>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-8 glass-panel text-gray-900">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
