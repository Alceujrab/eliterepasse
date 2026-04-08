<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contrato Assinado — Elite Repasse</title>
    <link href="https://fonts.bunny.net/css?family=inter:400,600,700,800,900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Inter', sans-serif; background: #0f172a; }</style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full text-center">
        <div class="w-20 h-20 rounded-full bg-green-500/10 border-2 border-green-500/30 flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-black text-white mb-3">Contrato Assinado! ✅</h1>
        @if(session('contrato_numero'))
            <p class="text-orange-400 font-bold mb-2">Nº {{ session('contrato_numero') }}</p>
        @endif
        <p class="text-gray-400 mb-8 leading-relaxed">
            Sua assinatura foi registrada com sucesso, incluindo sua localização geográfica como prova de autenticidade.<br>
            Você receberá uma cópia via e-mail e WhatsApp em breve.
        </p>
        <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-4 text-left text-sm text-gray-400">
            <p class="text-xs text-gray-600 uppercase tracking-wider mb-2">Hash de verificação</p>
            <p class="font-mono text-xs text-gray-500 break-all">Documento registrado em {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
