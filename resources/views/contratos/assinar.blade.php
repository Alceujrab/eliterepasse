<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Assinar Contrato {{ $contract->numero }} — Elite Repasse</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; background: #0f172a; }
        #signature-canvas { touch-action: none; cursor: crosshair; background: #fff; }
        .info-card { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 16px; }
    </style>
</head>
<body class="min-h-screen py-8 px-4">

    <div class="max-w-2xl mx-auto">

        {{-- Logo --}}
        <div class="flex items-center justify-center gap-2.5 mb-8">
            <div class="w-9 h-9 rounded-lg bg-orange-500 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2-2h8z"/>
                </svg>
            </div>
            <div class="flex flex-col leading-none">
                <span class="text-[15px] font-black text-white">Elite</span>
                <span class="text-[9px] font-bold text-orange-400 uppercase tracking-widest">Repasse</span>
            </div>
        </div>

        {{-- Header do Contrato --}}
        <div class="bg-gray-800/60 border border-gray-700 rounded-2xl p-6 mb-5">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h1 class="text-xl font-black text-white">Contrato de Compra e Venda</h1>
                    <p class="text-sm text-gray-400 mt-1">Nº {{ $contract->numero }}</p>
                </div>
                <span class="px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-bold">
                    ⏳ Aguardando Assinatura
                </span>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="info-card">
                    <p class="text-xs text-gray-500 mb-1 uppercase tracking-wide">Comprador</p>
                    <p class="text-sm font-bold text-white">{{ $contract->dados_comprador['razao_social'] ?? $contract->dados_comprador['name'] }}</p>
                    <p class="text-xs text-gray-400">{{ $contract->dados_comprador['cnpj'] ?? '' }}</p>
                </div>
                <div class="info-card">
                    <p class="text-xs text-gray-500 mb-1 uppercase tracking-wide">Veículo</p>
                    <p class="text-sm font-bold text-white">
                        {{ ($contract->dados_veiculo['brand'] ?? '') }} {{ ($contract->dados_veiculo['model'] ?? '') }}
                    </p>
                    <p class="text-xs text-gray-400">
                        {{ ($contract->dados_veiculo['model_year'] ?? '') }} — {{ ($contract->dados_veiculo['plate'] ?? '') }}
                    </p>
                </div>
                <div class="info-card col-span-2">
                    <p class="text-xs text-gray-500 mb-1 uppercase tracking-wide">Valor Contratado</p>
                    <p class="text-xl font-black text-green-400">
                        R$ {{ number_format((float) $contract->valor_contrato, 2, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-400">{{ $contract->forma_pagamento }}</p>
                </div>
            </div>
        </div>

        {{-- Formulário de Assinatura --}}
        <div class="bg-gray-800/60 border border-gray-700 rounded-2xl p-6"
             x-data="{
                lat: null, lng: null, enderecoGeo: '',
                gpsOk: false, gpsErro: false, gpsCarregando: true,
                assinou: false, processando: false,

                init() {
                    // Fallback: libera o botão após 5s mesmo sem resposta do GPS
                    const gpsFallback = setTimeout(() => {
                        if (this.gpsCarregando) {
                            this.gpsErro = true;
                            this.gpsCarregando = false;
                        }
                    }, 5000);

                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (pos) => {
                                clearTimeout(gpsFallback);
                                this.lat = pos.coords.latitude;
                                this.lng = pos.coords.longitude;
                                this.gpsOk = true;
                                this.gpsCarregando = false;
                            },
                            (err) => {
                                clearTimeout(gpsFallback);
                                this.gpsErro = true;
                                this.gpsCarregando = false;
                            },
                            { enableHighAccuracy: true, timeout: 10000 }
                        );
                    } else {
                        clearTimeout(gpsFallback);
                        this.gpsErro = true;
                        this.gpsCarregando = false;
                    }

                    // Inicializar canvas de assinatura
                    this.$nextTick(() => this.iniciarCanvas());
                },

                iniciarCanvas() {
                    const canvas = document.getElementById('signature-canvas');
                    const ctx = canvas.getContext('2d');
                    let desenhando = false;
                    let pontos = [];

                    const obterPos = (e) => {
                        const rect = canvas.getBoundingClientRect();
                        if (e.touches) {
                            return { x: e.touches[0].clientX - rect.left, y: e.touches[0].clientY - rect.top };
                        }
                        return { x: e.clientX - rect.left, y: e.clientY - rect.top };
                    };

                    const iniciar = (e) => { e.preventDefault(); desenhando = true; const p = obterPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); pontos.push(p); };
                    const desenhar = (e) => { e.preventDefault(); if (!desenhando) return; const p = obterPos(e); ctx.lineWidth = 2.5; ctx.strokeStyle = '#1e293b'; ctx.lineCap = 'round'; ctx.lineTo(p.x, p.y); ctx.stroke(); this.assinou = true; };
                    const parar = () => { desenhando = false; };

                    canvas.addEventListener('mousedown', iniciar);
                    canvas.addEventListener('mousemove', desenhar);
                    canvas.addEventListener('mouseup', parar);
                    canvas.addEventListener('touchstart', iniciar, { passive: false });
                    canvas.addEventListener('touchmove', desenhar, { passive: false });
                    canvas.addEventListener('touchend', parar);
                },

                limparAssinatura() {
                    const canvas = document.getElementById('signature-canvas');
                    const ctx = canvas.getContext('2d');
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    this.assinou = false;
                },

                enviarAssinatura() {
                    if (!this.assinou) { alert('Por favor, desenhe sua assinatura.'); return; }

                    const canvas = document.getElementById('signature-canvas');
                    const base64 = canvas.toDataURL('image/png');

                    document.getElementById('input-assinatura').value = base64;
                    document.getElementById('input-lat').value = this.lat ?? '';
                    document.getElementById('input-lng').value = this.lng ?? '';

                    this.processando = true;
                    document.getElementById('form-assinatura').submit();
                }
             }">

            <h2 class="text-lg font-black text-white mb-5">✍️ Assinatura Digital</h2>

            {{-- Status GPS --}}
            <div class="mb-5">
                <div x-show="gpsCarregando" class="flex items-center gap-2 text-sm text-amber-400">
                    <div class="w-4 h-4 border-2 border-amber-400 border-t-transparent rounded-full animate-spin"></div>
                    Obtendo sua localização GPS...
                </div>
                <div x-show="gpsOk && !gpsCarregando" class="flex items-center gap-2 text-sm text-green-400">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    Localização capturada com sucesso
                    <span class="text-gray-500 text-xs ml-1" x-text="lat ? `(Lat: ${lat.toFixed(4)}, Lng: ${lng.toFixed(4)})` : ''"></span>
                </div>
                <div x-show="gpsErro" class="flex items-center gap-2 text-sm text-amber-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Localização não disponível — a assinatura continuará sem registro de GPS.
                </div>
            </div>

            {{-- Canvas de Assinatura --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-300 mb-2">
                    Desenhe sua assinatura abaixo:
                </label>
                <div class="rounded-xl overflow-hidden border-2 border-gray-600 hover:border-orange-500/50 transition">
                    <canvas id="signature-canvas" width="580" height="180" class="w-full"></canvas>
                </div>
                <button type="button" @click="limparAssinatura()"
                    class="mt-2 text-xs text-gray-500 hover:text-red-400 transition flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Limpar assinatura
                </button>
            </div>

            {{-- Declaração --}}
            <div class="bg-gray-900/50 border border-gray-700 rounded-xl p-4 mb-6 text-xs text-gray-400 leading-relaxed">
                Ao assinar este documento, declaro que li e aceito os termos do contrato de compra e venda de veículo automotor entre as partes acima identificadas. Esta assinatura eletrônica tem validade jurídica conforme a <strong class="text-gray-300">Lei nº 14.063/2020</strong> e o <strong class="text-gray-300">Decreto nº 10.543/2020</strong>. Minha localização geográfica está sendo registrada como prova de autenticidade.
            </div>

            {{-- Form oculto --}}
            <form id="form-assinatura" method="POST" action="{{ route('contrato.assinar.store', $signature->token_assinatura) }}">
                @csrf
                <input type="hidden" id="input-assinatura" name="assinatura_base64">
                <input type="hidden" id="input-lat" name="lat">
                <input type="hidden" id="input-lng" name="lng">
            </form>

            @if($errors->any())
                <div class="mb-4 bg-red-500/10 border border-red-500/30 text-red-400 text-sm rounded-xl p-3">
                    {{ $errors->first() }}
                </div>
            @endif

            <button type="button" @click="enviarAssinatura()"
                :disabled="processando || gpsCarregando"
                class="w-full flex items-center justify-center gap-2 py-4 px-6 rounded-xl text-white font-black text-[16px] transition-all"
                :class="processando ? 'bg-gray-600 cursor-not-allowed' : 'bg-orange-500 hover:bg-orange-600 shadow-lg shadow-orange-500/20 hover:-translate-y-0.5'">
                <span x-show="!processando">
                    <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    Assinar Contrato
                </span>
                <span x-show="processando" class="flex items-center gap-2">
                    <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                    Processando...
                </span>
            </button>
        </div>

        <p class="text-center text-gray-600 text-xs mt-6">
            Elite Repasse — Portal B2B de Veículos · Contrato gerado em {{ $contract->created_at->format('d/m/Y H:i') }}
        </p>
    </div>
</body>
</html>
