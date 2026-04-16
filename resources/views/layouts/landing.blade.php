<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="description" content="Portal B2B de repasse para lojistas: estoque diversificado, contratos digitais, acompanhamento de pedidos e gestao financeira em um unico lugar.">
        <meta name="theme-color" content="#1f5a7c">

        <meta property="og:title" content="Elite Repasse | Portal do Lojista">
        <meta property="og:description" content="Acesse um portal B2B completo para compra de seminovos com praticidade e seguranca.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/') }}">
        <meta property="og:image" content="{{ asset('build/assets/logo.png') }}">

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="Elite Repasse | Portal do Lojista">
        <meta name="twitter:description" content="Compre seminovos no atacado com gestao completa no portal B2B da Elite.">

        <title>Elite Repasse | Portal do Lojista</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-white text-gray-900 scroll-smooth">
        {{ $slot }}

        {{-- Botão Voltar ao Topo --}}
        <button
            x-data="{ visible: false }"
            x-init="window.addEventListener('scroll', () => { visible = window.scrollY > 400 })"
            x-show="visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4"
            @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
            class="fixed bottom-6 right-6 z-50 w-12 h-12 rounded-full bg-[#0f2f4d] text-white shadow-lg shadow-black/20 flex items-center justify-center hover:bg-[#1a4c6e] transition-colors cursor-pointer"
            style="display: none;"
            title="Voltar ao topo"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/>
            </svg>
        </button>
    </body>
</html>
