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
    </body>
</html>
