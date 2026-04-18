<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Elite Repasse - Portal do Lojista. Acesse veículos seminovos com preços abaixo da FIPE.">

        <title>{{ config('app.name', 'Elite Repasse') }} — Portal do Lojista</title>

        <!-- Favicon -->
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @php
            $landingSetting = \App\Models\LandingSetting::first();
            $systemLogo = ($landingSetting && $landingSetting->logo_path) ? asset($landingSetting->logo_path) : asset('build/assets/logo.png');
        @endphp

        <style>
            .auth-gradient { background: linear-gradient(135deg, #0f2d4e 0%, #1a3a5c 30%, #1e4f8a 70%, #1a3a5c 100%); }
            .float-car { animation: floatCar 6s ease-in-out infinite; }
            @keyframes floatCar { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-12px); } }
            .slide-up { animation: slideUp 0.6s ease-out; }
            @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
            /* Esconde badge flutuante do reCAPTCHA v3 (exibimos aviso textual customizado) */
            .grecaptcha-badge { visibility: hidden !important; }
        </style>
    </head>
    <body class="font-sans antialiased min-h-screen flex selection:bg-orange-500 selection:text-white">

        {{-- ─── Lado Esquerdo — Hero Branding ──────────────────────── --}}
        <div class="hidden lg:flex lg:w-[50%] xl:w-[55%] auth-gradient relative overflow-hidden flex-col justify-between p-12">
            {{-- Efeitos de fundo --}}
            <div class="absolute top-0 right-0 w-96 h-96 bg-blue-400 opacity-5 blur-3xl rounded-full -translate-y-1/3 translate-x-1/4"></div>
            <div class="absolute bottom-0 left-0 w-72 h-72 bg-orange-400 opacity-5 blur-3xl rounded-full translate-y-1/4 -translate-x-1/4"></div>
            <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-white opacity-[0.02] blur-2xl rounded-full -translate-x-1/2 -translate-y-1/2"></div>

            {{-- Logo --}}
            <div class="relative z-10">
                <a href="/" class="flex items-center gap-4 group">
                    <img src="{{ $systemLogo }}" class="h-10 transition group-hover:scale-105" alt="Elite Repasse"
                        onerror="this.src='https://placehold.co/200x50/ffffff/1a3a5c?text=Elite+Repasse'">
                    <span class="text-white text-opacity-30 text-2xl font-light">|</span>
                    <span class="text-white font-black text-lg tracking-tight">Portal do Lojista</span>
                </a>
            </div>

            {{-- Conteúdo central --}}
            <div class="relative z-10 slide-up">
                <div class="text-6xl mb-6 float-car">🚗</div>
                <h1 class="text-white text-4xl xl:text-5xl font-black leading-tight tracking-tight">
                    Veículos seminovos<br>
                    <span class="text-orange-400">abaixo da FIPE</span>
                </h1>
                <p class="text-blue-200 text-lg mt-4 max-w-md leading-relaxed">
                    Acesse o maior portfólio de veículos com preço de repasse. Compre direto, sem intermediários.
                </p>

                {{-- Benefícios --}}
                <div class="mt-8 space-y-3">
                    @foreach([
                        ['icon' => '💰', 'text' => 'Preços até 30% abaixo da tabela FIPE'],
                        ['icon' => '📋', 'text' => 'Laudos e documentação verificados'],
                        ['icon' => '🛡️', 'text' => 'Garantia de procedência em todos os veículos'],
                        ['icon' => '📊', 'text' => 'Painel financeiro completo com boletos e NF'],
                    ] as $benefit)
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white bg-opacity-10 flex items-center justify-center text-sm flex-shrink-0">{{ $benefit['icon'] }}</div>
                            <p class="text-blue-100 text-sm font-medium">{{ $benefit['text'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Footer --}}
            <div class="relative z-10 text-blue-300 text-opacity-60 text-xs">
                © {{ date('Y') }} Elite Repasse — Todos os direitos reservados
            </div>
        </div>

        {{-- ─── Lado Direito — Formulário ──────────────────────────── --}}
        <div class="flex-1 flex flex-col min-h-screen bg-[#f8fafc]">

            {{-- Header mobile --}}
            <header class="lg:hidden bg-white border-b border-gray-200 py-4 px-6 shadow-sm">
                <a href="/" class="flex items-center gap-3 group">
                    <img src="{{ $systemLogo }}" class="h-8 transition group-hover:scale-105" alt="Elite Repasse"
                        onerror="this.src='https://placehold.co/160x40/1f5a7c/white?text=Elite+Repasse'">
                    <span class="text-gray-300 font-light text-xl">|</span>
                    <span class="text-gray-700 font-black text-sm tracking-tight">Portal do Lojista</span>
                </a>
            </header>

            {{-- Flash de cadastro pendente --}}
            @if(session('cadastro_pendente'))
                <div class="mx-6 mt-6 lg:mx-auto lg:max-w-[480px] lg:w-full bg-emerald-50 border border-emerald-200 rounded-2xl px-5 py-4">
                    <div class="flex items-start gap-3">
                        <span class="text-2xl">✅</span>
                        <div>
                            <h3 class="font-black text-emerald-800 text-sm">Cadastro enviado com sucesso!</h3>
                            <p class="text-xs text-emerald-600 mt-0.5">Sua conta será analisada pela equipe. Você receberá um e-mail quando o acesso for liberado.</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Form container --}}
            <div class="flex-grow flex items-center justify-center py-8 px-6">
                <div class="max-w-[480px] w-full slide-up">
                    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/40 border border-gray-100 p-8 lg:p-10">
                        {{ $slot }}
                    </div>

                    {{-- Trust badges --}}
                    <div class="mt-6 flex items-center justify-center gap-6 text-xs text-gray-400">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Conexão segura
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Dados protegidos
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
