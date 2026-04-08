<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#1a3a5c">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#f1f5f9] text-gray-900 selection:bg-orange_cta selection:text-white">
        <div class="min-h-screen flex flex-col">
            <livewire:layout.navigation />

            <!-- Page Content -->
            <main class="flex-1 w-full pb-2">
                {{ $slot }}
            </main>

            <!-- Bottom Nav Mobile (compartilhado) -->
            <nav class="mobile-bottom-nav">
                <div class="flex">
                    @php
                        $navItems = [
                            ['route' => 'dashboard',    'icon' => '🏠', 'label' => 'Vitrine'],
                            ['route' => 'meus-pedidos', 'icon' => '📋', 'label' => 'Pedidos'],
                            ['route' => 'financeiro',   'icon' => '💳', 'label' => 'Financeiro'],
                            ['route' => 'suporte',      'icon' => '💬', 'label' => 'Suporte'],
                            ['route' => 'favoritos',    'icon' => '❤️', 'label' => 'Favoritos'],
                        ];
                    @endphp
                    @foreach($navItems as $item)
                        <a href="{{ route($item['route']) }}" wire:navigate
                            class="{{ request()->routeIs($item['route']) ? 'active' : 'inactive' }}">
                            <span class="nav-icon">{{ $item['icon'] }}</span>
                            <span class="nav-label">{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </nav>
            <div class="safe-bottom"></div>
        </div>
    </body>
</html>
