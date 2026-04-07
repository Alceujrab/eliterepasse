<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#f3f4f6] min-h-screen flex flex-col selection:bg-orange_cta selection:text-white">
        <!-- Topo Estilo Localiza -->
        <header class="bg-white border-b border-gray-200 py-5 shadow-sm w-full flex-shrink-0">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center">
                <a href="/" class="flex space-x-4 items-center group">
                    <img src="{{ asset('build/assets/logo.png') }}" class="h-9 transition transform group-hover:scale-105" alt="Elite Repasse" onerror="this.src='https://placehold.co/200x50/1f5a7c/white?text=Elite+Repasse'">
                    <span class="text-gray-300 font-light text-2xl">|</span>
                    <span class="text-gray-700 font-black tracking-tight text-xl">Portal do Lojista</span>
                </a>
            </div>
        </header>

        <div class="flex-grow flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8 relative">
            <div class="absolute inset-0 z-0 bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] [background-size:20px_20px] opacity-50"></div>
            <div class="max-w-[480px] w-full bg-white rounded-xl shadow-2xl shadow-gray-200/50 border border-gray-100 p-10 relative z-10 transition-all hover:shadow-3xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
