<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aguardando Aprovação — Portal Elite Repasse</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; background: #0f172a; }
        .pulse-ring { animation: pulseRing 2s ease-out infinite; }
        @keyframes pulseRing {
            0% { transform: scale(0.95); opacity: 1; }
            70% { transform: scale(1.25); opacity: 0; }
            100% { transform: scale(1.25); opacity: 0; }
        }
        .float { animation: float 3s ease-in-out infinite; }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">
    <div class="max-w-lg w-full text-center">

        {{-- Logo --}}
        <div class="flex items-center justify-center gap-2.5 mb-10">
            <div class="w-10 h-10 rounded-xl bg-orange-500 flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2-2h8z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 8h4l3 3v5h-7V8z"/>
                </svg>
            </div>
            <div class="flex flex-col leading-none">
                <span class="text-[17px] font-black text-white">Elite</span>
                <span class="text-[10px] font-bold text-orange-400 uppercase tracking-widest">Repasse</span>
            </div>
        </div>

        {{-- Ícone animado --}}
        <div class="relative flex items-center justify-center mb-8">
            <div class="absolute w-32 h-32 rounded-full bg-amber-500/20 pulse-ring"></div>
            <div class="relative w-24 h-24 rounded-full bg-amber-500/10 border-2 border-amber-400/30 flex items-center justify-center float">
                <svg class="w-10 h-10 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        {{-- Conteúdo --}}
        <div class="bg-gray-800/60 backdrop-blur-md border border-gray-700/50 rounded-2xl p-8 shadow-2xl">
            <h1 class="text-2xl font-black text-white mb-3">
                ⏳ Sua conta está sendo analisada
            </h1>
            <p class="text-gray-400 leading-relaxed mb-6">
                Olá, <span class="text-white font-semibold">{{ auth()->user()->razao_social ?? auth()->user()->nome_fantasia ?? auth()->user()->name }}</span>!
                Recebemos seu cadastro e nossa equipe está verificando as informações.
                <br><br>
                Você receberá um <span class="text-orange-400 font-semibold">e-mail e mensagem no WhatsApp</span> assim que seu acesso for liberado.
            </p>

            {{-- Steps --}}
            <div class="space-y-3 text-left mb-8">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-full bg-green-500/20 border border-green-500/40 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></path></svg>
                    </span>
                    <span class="text-sm text-gray-300">Cadastro enviado com sucesso</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-full bg-amber-500/20 border border-amber-500/40 flex items-center justify-center flex-shrink-0">
                        <div class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></div>
                    </span>
                    <span class="text-sm text-gray-300">Análise de documentos em andamento</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-full bg-gray-700/50 border border-gray-600/40 flex items-center justify-center flex-shrink-0">
                        <div class="w-2 h-2 rounded-full bg-gray-500"></div>
                    </span>
                    <span class="text-sm text-gray-500">Liberação de acesso ao portal</span>
                </div>
            </div>

            {{-- Ações --}}
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ url('/suporte') }}"
                   class="flex-1 inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-gray-700 hover:bg-gray-600 text-white text-sm font-semibold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    Falar com Suporte
                </a>

                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-orange-500/10 hover:bg-orange-500/20 border border-orange-500/30 text-orange-400 text-sm font-semibold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Sair da conta
                    </button>
                </form>
            </div>
        </div>

        <p class="text-gray-600 text-xs mt-6">
            Prazo médio de análise: <span class="text-gray-400">1 a 2 dias úteis</span>
        </p>
    </div>
</body>
</html>
