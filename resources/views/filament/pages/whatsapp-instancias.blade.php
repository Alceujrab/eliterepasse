<x-filament-panels::page>

    {{-- ─── Header Hero ─────────────────────────────────────────────── --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#128C7E] via-[#075E54] to-[#064940] p-6 mb-6 shadow-xl">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-64 h-64 rounded-full bg-white blur-3xl -translate-y-1/2 translate-x-1/4"></div>
        </div>
        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="text-white">
                <div class="flex items-center gap-3 mb-1">
                    <div class="w-10 h-10 rounded-2xl bg-white bg-opacity-20 flex items-center justify-center text-2xl">💬</div>
                    <div>
                        <h1 class="text-3xl font-black tracking-tight">WhatsApp — Instâncias</h1>
                        <p class="text-green-200 text-base">Evolution GO · {{ $this->instancias->count() }} instância(s) configurada(s)</p>
                    </div>
                </div>
            </div>
            <button wire:click="novaInstancia"
                class="flex items-center gap-2 bg-white text-[#075E54] font-black px-5 py-3 rounded-xl shadow-lg hover:bg-green-50 transition text-base">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nova Instância
            </button>
        </div>

        {{-- Status badges --}}
        <div class="relative flex gap-3 mt-5 flex-wrap">
            @php
                $conectadas   = $this->instancias->where('status_conexao', 'open')->count();
                $desconectadas = $this->instancias->where('status_conexao', 'close')->count();
                $parao        = $this->instancias->where('padrao', true)->first();
            @endphp
            <div class="bg-white bg-opacity-15 backdrop-blur-sm px-4 py-2.5 rounded-xl text-white text-base font-semibold">
                🟢 {{ $conectadas }} conectada(s)
            </div>
            <div class="bg-white bg-opacity-15 backdrop-blur-sm px-4 py-2.5 rounded-xl text-white text-base font-semibold">
                🔴 {{ $desconectadas }} desconectada(s)
            </div>
            @if($parao)
                <div class="bg-white bg-opacity-15 backdrop-blur-sm px-4 py-2.5 rounded-xl text-white text-base font-semibold">
                    ⭐ Padrão: {{ $parao->nome }}
                </div>
            @endif
        </div>
    </div>

    {{-- ─── QR Code Modal ───────────────────────────────────────────── --}}
    @if($qrCodeBase64 !== null || $qrInstanciaNome)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="background: rgba(0,0,0,0.7);">
            <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl p-8 max-w-sm w-full text-center">
                <h3 class="text-xl font-black text-gray-900 dark:text-white mb-1">QR Code WhatsApp</h3>
                <p class="text-base text-gray-500 mb-5">{{ $qrInstanciaNome }}</p>

                @if($qrCodeBase64)
                    <img src="{{ $qrCodeBase64 }}" class="w-56 h-56 mx-auto rounded-2xl shadow-lg mb-4" alt="QR Code"/>
                    <p class="text-sm text-gray-500 mb-2">
                        Abra o WhatsApp → Dispositivos Vinculados → Vincular dispositivo
                    </p>
                    <p class="text-sm text-red-400 font-semibold">⚠️ Expira em ~40 segundos</p>
                @else
                    <div class="py-8 text-gray-400">
                        <div class="text-4xl mb-3">⚠️</div>
                        <p class="text-base">Não foi possível obter o QR Code.<br>A instância pode já estar conectada.</p>
                    </div>
                @endif

                <button wire:click="fecharQr"
                    class="mt-6 w-full py-3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold transition">
                    Fechar
                </button>
            </div>
        </div>
    @endif

    {{-- ─── Modal de Teste ─────────────────────────────────────────── --}}
    @if($testInstanceId)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.6);">
            <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl p-6 max-w-sm w-full">
                <h3 class="text-lg font-black text-gray-900 dark:text-white mb-1">Enviar Mensagem de Teste</h3>
                <p class="text-sm text-gray-500 mb-4">Instância: {{ $this->instancias->find($testInstanceId)?->nome }}</p>

                <label class="block text-base font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Número (com DDD, sem +55)
                </label>
                <input wire:model="testPhone" type="text"
                    placeholder="11987654321"
                    class="w-full rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white px-4 py-3 text-base focus:ring-2 focus:ring-green-500 focus:border-transparent mb-4"/>

                <div class="flex gap-3">
                    <button wire:click="$set('testInstanceId', null)"
                        class="flex-1 py-3 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl font-semibold hover:bg-gray-200 transition text-base">
                        Cancelar
                    </button>
                    <button wire:click="enviarTeste"
                        class="flex-1 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-black transition text-base flex items-center justify-center gap-2">
                        <span>Enviar</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ─── Form Criar/Editar ───────────────────────────────────────── --}}
    @if($showForm)
        <div class="elite-card mb-6 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-green-50 to-white dark:from-gray-800 dark:to-gray-800">
                <h2 class="font-black text-gray-800 dark:text-white">
                    {{ $editingId ? '✏️ Editar Instância' : '➕ Nova Instância' }}
                </h2>
                <button wire:click="$set('showForm', false)" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Nome --}}
                    <div>
                        <label class="block text-base font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Nome (identificador local) *</label>
                        <input wire:model="nome" type="text" placeholder="Ex: Principal, Suporte, Vendas"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 text-base focus:ring-2 focus:ring-green-500 focus:border-transparent"/>
                        <p class="text-xs text-gray-400 mt-1">Apenas para identificação no painel</p>
                        @error('nome') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- URL Base --}}
                    <div>
                        <label class="block text-base font-semibold text-gray-700 dark:text-gray-300 mb-1.5">URL Base da API *</label>
                        <input wire:model="url_base" type="url" placeholder="https://api.auto.inf.br"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 text-base focus:ring-2 focus:ring-green-500 focus:border-transparent"/>
                        <p class="text-xs text-gray-400 mt-1">Sem barra no final</p>
                        @error('url_base') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Token da Instância --}}
                    <div class="md:col-span-2">
                        <label class="block text-base font-semibold text-gray-700 dark:text-gray-300 mb-1.5">🔑 Token da Instância (apikey) *</label>
                        <input wire:model="api_key" type="text" placeholder="Ex: token-vendas-123 ou UUID da instância"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 text-base font-mono focus:ring-2 focus:ring-green-500 focus:border-transparent"/>
                        <p class="text-xs text-gray-400 mt-1">⚠️ Este é o <strong>Token da Instância</strong> (não a API Key Global). Obtido ao criar a instância no Evolution Go.</p>
                        @error('api_key') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Toggles --}}
                <div class="flex gap-6 mt-5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="ativo" type="checkbox" class="w-4 h-4 accent-green-500 rounded"/>
                        <span class="text-base font-semibold text-gray-700 dark:text-gray-300">Instância Ativa</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="padrao" type="checkbox" class="w-4 h-4 accent-green-500 rounded"/>
                        <span class="text-base font-semibold text-gray-700 dark:text-gray-300">⭐ Instância Padrão do Sistema</span>
                    </label>
                </div>

                {{-- Botões --}}
                <div class="flex justify-end gap-3 mt-6 pt-5 border-t border-gray-100 dark:border-gray-700">
                    <button wire:click="$set('showForm', false)"
                        class="px-5 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition text-base">
                        Cancelar
                    </button>
                    <button wire:click="salvar"
                        class="px-6 py-2.5 bg-gradient-to-r from-[#128C7E] to-[#075E54] text-white rounded-xl font-black hover:opacity-90 transition text-base shadow-lg shadow-green-500/20">
                        {{ $editingId ? 'Salvar Alterações' : 'Criar Instância' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ─── Grid de Instâncias ────────────────────────────────────────── --}}
    @if($this->instancias->isEmpty())
        <div class="elite-card py-20 text-center">
            <div class="text-5xl mb-4">📱</div>
            <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300 mb-1">Nenhuma instância configurada</h3>
            <p class="text-gray-400 text-base mb-6">Adicione sua instância Evolution para ativar o WhatsApp.</p>
            <button wire:click="novaInstancia"
                class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-[#128C7E] to-[#075E54] text-white rounded-xl font-bold hover:opacity-90 transition shadow-lg">
                ➕ Adicionar Instância
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($this->instancias as $inst)
                @php
                    $statusColor = match($inst->status_conexao) {
                        'open'  => 'bg-emerald-400',
                        'close' => 'bg-red-400',
                        default => 'bg-yellow-400',
                    };
                    $statusLabel = match($inst->status_conexao) {
                        'open'  => 'Conectado',
                        'close' => 'Desconectado',
                        default => $inst->status_conexao ?? 'Não verificado',
                    };
                @endphp

                <div class="elite-card overflow-hidden hover:shadow-md transition group">

                    {{-- Card Header --}}
                    <div class="px-5 pt-5 pb-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl shadow-sm
                                    {{ $inst->status_conexao === 'open' ? 'bg-green-100' : 'bg-gray-100 dark:bg-gray-700' }}">
                                    💬
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-black text-gray-900 dark:text-white">{{ $inst->nome }}</h3>
                                        @if($inst->padrao)
                                            <span class="text-xs bg-yellow-100 text-yellow-700 font-black px-2 py-0.5 rounded-full">⭐ PADRÃO</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-400 font-mono mt-0.5" title="Token da instância">🔑 {{ Str::mask($inst->api_key, '*', 8) }}</p>
                                </div>
                            </div>

                            {{-- Status badge --}}
                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-bold
                                {{ $inst->status_conexao === 'open' ? 'bg-emerald-100 text-emerald-700' : ($inst->status_conexao === 'close' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                <div class="w-2 h-2 rounded-full {{ $statusColor }} {{ $inst->status_conexao === 'open' ? 'animate-pulse' : '' }}"></div>
                                {{ $statusLabel }}
                            </div>
                        </div>

                        {{-- URL info --}}
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl px-3 py-2 mb-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">🌐 {{ $inst->url_base }}</p>
                            @if($inst->verificado_em)
                                <p class="text-xs text-gray-400 mt-0.5">Testado {{ $inst->verificado_em->diffForHumans() }}</p>
                            @else
                                <p class="text-xs text-gray-400 mt-0.5">Ainda não testado</p>
                            @endif
                        </div>

                        {{-- Status toggle visual --}}
                        <div class="flex items-center justify-between text-sm mb-2">
                            <span class="text-gray-500 dark:text-gray-400">Ativo</span>
                            <span class="{{ $inst->ativo ? 'text-green-600' : 'text-gray-400' }} font-bold">
                                {{ $inst->ativo ? '✅ Sim' : '⛔ Não' }}
                            </span>
                        </div>
                    </div>

                    {{-- Ações --}}
                    <div class="px-4 pb-4 grid grid-cols-2 gap-2">
                        {{-- Testar conexão --}}
                        <button wire:click="testarConexao({{ $inst->id }})"
                            class="flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-bold
                                bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400
                                hover:bg-blue-100 dark:hover:bg-blue-900/40 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                            </svg>
                            Testar Status
                        </button>

                        {{-- QR Code (só quando desconectado) --}}
                        <button wire:click="verQrCode({{ $inst->id }})"
                            class="flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-bold
                                bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400
                                hover:bg-purple-100 dark:hover:bg-purple-900/40 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                            Ver QR Code
                        </button>

                        {{-- Enviar Teste --}}
                        <button wire:click="$set('testInstanceId', {{ $inst->id }})"
                            class="flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-bold
                                bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400
                                hover:bg-green-100 dark:hover:bg-green-900/40 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Enviar Teste
                        </button>

                        {{-- Editar --}}
                        <button wire:click="editarInstancia({{ $inst->id }})"
                            class="flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-bold
                                bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400
                                hover:bg-orange-100 dark:hover:bg-orange-900/40 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editar
                        </button>

                        {{-- Logout (visível só quando conectado, span 2 colunas) --}}
                        @if($inst->status_conexao === 'open')
                            <button wire:click="logout({{ $inst->id }})"
                                wire:confirm="Desconectar o WhatsApp da instância '{{ $inst->nome }}'?"
                                class="col-span-2 flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-bold
                                    bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400
                                    hover:bg-red-100 dark:hover:bg-red-900/40 transition border border-red-200 dark:border-red-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Desconectar WhatsApp (Logout)
                            </button>
                        @endif
                    </div>

                    {{-- Excluir (footer discreto) --}}
                    <div class="border-t border-gray-100 dark:border-gray-700 px-4 py-2.5 flex justify-end">
                        <button wire:click="excluir({{ $inst->id }})"
                            wire:confirm="Excluir a instância '{{ $inst->nome }}'?"
                            class="text-sm text-red-400 hover:text-red-600 dark:hover:text-red-300 font-semibold transition flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Excluir do Painel
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ─── Info Box ──────────────────────────────────────────────────── --}}
    <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl p-5">
        <h4 class="text-base font-black text-blue-800 dark:text-blue-200 mb-3">ℹ️ Como configurar uma instância Evolution GO</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-blue-700 dark:text-blue-300">
            <div class="flex gap-2">
                <span class="font-black flex-shrink-0">1.</span>
                <p>No painel do Evolution Go, crie uma instância e copie o <strong>Token da Instância</strong> (não a API Key Global).</p>
            </div>
            <div class="flex gap-2">
                <span class="font-black flex-shrink-0">2.</span>
                <p>Cadastre aqui com a URL base (ex: <code>https://api.auto.inf.br</code>) e cole o <strong>Token da Instância</strong> no campo apikey.</p>
            </div>
            <div class="flex gap-2">
                <span class="font-black flex-shrink-0">3.</span>
                <p>Clique em <strong>QR Code</strong> para conectar seu WhatsApp. Após conectado, clique em <strong>Testar</strong> para validar.</p>
            </div>
        </div>
    </div>

</x-filament-panels::page>
