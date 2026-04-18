<div class="min-h-screen bg-[#f1f5f9]">

    @php
        $media   = $this->getMedia();
        $images  = count($media) > 0 ? $media : [];
        $margin  = $vehicle->fipe_price > 0 ? round(($vehicle->fipe_price - $vehicle->sale_price) / $vehicle->fipe_price * 100) : 0;
        $economia = $vehicle->fipe_price - $vehicle->sale_price;

        $accessories = is_string($vehicle->accessories) ? json_decode($vehicle->accessories, true) : $vehicle->accessories;
        if (! is_array($accessories)) $accessories = [];
    @endphp

    {{-- ─── Breadcrumb ────────────────────────────────────────────────── --}}
    <div class="bg-white border-b border-gray-200">
        <div class="page-container py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-2 text-base text-gray-500">
                <a href="{{ route('dashboard') }}" wire:navigate class="hover:text-blue-600 transition font-semibold">Vitrine</a>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-400">{{ $vehicle->brand }}</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="font-bold text-gray-800">{{ $vehicle->model }}</span>
            </div>
            <div class="flex items-center gap-5">
                <button wire:click="toggleFavorite"
                    class="flex items-center gap-2 text-base font-bold transition {{ $isFavorited ? 'text-red-500' : 'text-gray-500 hover:text-red-500' }}">
                    <svg class="w-5 h-5" fill="{{ $isFavorited ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    {{ $isFavorited ? 'Favoritado' : 'Favoritar' }}
                </button>
                <button onclick="navigator.share?.({title: '{{ $vehicle->brand }} {{ $vehicle->model }}', url: window.location.href}).catch(()=>navigator.clipboard.writeText(window.location.href))"
                    class="flex items-center gap-2 text-base font-bold text-gray-500 hover:text-blue-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m9.316 4.684a3 3 0 10-5.367-2.684 3 3 0 005.367 2.684zm0-9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                    Compartilhar
                </button>
            </div>
        </div>
    </div>

    {{-- ─── Flash message ─────────────────────────────────────────────── --}}
    @if(session()->has('message'))
        <div class="max-w-7xl mx-auto px-6 mt-4">
            <div class="elite-card px-5 py-4 text-base font-bold text-emerald-700">
                {{ session('message') }}
            </div>
        </div>
    @endif

    <div class="page-container py-6">
        <div class="flex flex-col lg:flex-row gap-8">

            {{-- ─── Coluna Esquerda (Galeria + Specs) ──────────────────── --}}
            <div class="w-full lg:w-[62%]">

                {{-- Galeria --}}
                @if(count($images) > 0)
                    <div class="relative rounded-2xl overflow-hidden bg-gray-200 shadow-lg" style="aspect-ratio: 16/10;">
                        <img src="{{ $images[$fotoAtual] ?? $images[0] }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}"
                            class="w-full h-full object-cover transition-opacity duration-300"/>

                        {{-- Badges --}}
                        <div class="absolute top-3 left-3 flex gap-2 flex-wrap">
                            @if($vehicle->is_on_sale)
                                <span class="badge bg-orange-500 text-white shadow-lg">🏷️ OFERTA</span>
                            @endif
                            @if($vehicle->has_factory_warranty)
                                <span class="badge bg-green-500 text-white shadow-lg">🛡️ GARANTIA</span>
                            @endif
                            @if($vehicle->has_report)
                                <span class="badge bg-blue-500 text-white shadow-lg">📋 LAUDO</span>
                            @endif
                            @if($vehicle->is_just_arrived)
                                <span class="badge bg-purple-600 text-white shadow-lg">🆕 NOVO</span>
                            @endif
                        </div>

                        {{-- Navegação --}}
                        @if(count($images) > 1)
                            <button wire:click="fotoAnterior"
                                class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-black bg-opacity-40 hover:bg-opacity-60 text-white rounded-full flex items-center justify-center backdrop-blur-sm transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button wire:click="fotoProxima"
                                class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-black bg-opacity-40 hover:bg-opacity-60 text-white rounded-full flex items-center justify-center backdrop-blur-sm transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </button>

                            {{-- Contador --}}
                            <div class="absolute bottom-3 right-3 bg-black bg-opacity-50 text-white text-xs font-bold px-3 py-1.5 rounded-full backdrop-blur-sm">
                                {{ $fotoAtual + 1 }} / {{ count($images) }}
                            </div>
                        @endif
                    </div>

                    {{-- Miniaturas --}}
                    @if(count($images) > 1)
                        <div class="flex gap-2 mt-3 overflow-x-auto pb-1">
                            @foreach($images as $idx => $img)
                                <button wire:click="$set('fotoAtual', {{ $idx }})"
                                    class="w-16 h-12 rounded-lg overflow-hidden flex-shrink-0 border-2 transition
                                        {{ $fotoAtual === $idx ? 'border-orange-500 opacity-100' : 'border-transparent opacity-60 hover:opacity-90' }}">
                                    <img src="{{ $img }}" alt="Foto {{ $idx + 1 }}" class="w-full h-full object-cover"/>
                                </button>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="rounded-2xl bg-gray-200 flex items-center justify-center text-5xl" style="aspect-ratio: 16/10;">🚗</div>
                @endif

                {{-- ─── Especificações ─────────────────────────────────── --}}
                <div class="mt-8">
                    <h2 class="section-title mb-4">📋 Sobre este veículo</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @php
                            $specs = [
                                ['icon' => '📏', 'label' => 'Quilometragem', 'valor' => number_format($vehicle->mileage, 0, ',', '.') . ' km'],
                                ['icon' => '📅', 'label' => 'Ano', 'valor' => "{$vehicle->manufacture_year}/{$vehicle->model_year}"],
                                ['icon' => '⚙️', 'label' => 'Câmbio', 'valor' => ucfirst($vehicle->transmission ?? '-')],
                                ['icon' => '⛽', 'label' => 'Combustível', 'valor' => ucfirst($vehicle->fuel_type ?? '-')],
                                ['icon' => '🔧', 'label' => 'Motor', 'valor' => $vehicle->engine ?? '-'],
                                ['icon' => '🎨', 'label' => 'Cor', 'valor' => $vehicle->color ?? '-'],
                                ['icon' => '🚪', 'label' => 'Portas', 'valor' => $vehicle->doors ?? '-'],
                                ['icon' => '🏷️', 'label' => 'Categoria', 'valor' => $vehicle->category ?? '-'],
                            ];
                        @endphp
                        @foreach($specs as $spec)
                            <div class="elite-card p-4 text-center">
                                <span class="text-2xl">{{ $spec['icon'] }}</span>
                                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mt-1">{{ $spec['label'] }}</p>
                                <p class="text-base font-black text-gray-800 mt-0.5">{{ $spec['valor'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ─── Acessórios ──────────────────────────────────────── --}}
                <div class="mt-8">
                    <h2 class="section-title mb-4">✨ Acessórios</h2>
                    @if(count($accessories) > 0)
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2.5">
                            @foreach($accessories as $acc)
                                <div class="flex items-center gap-2.5 elite-card px-4 py-3 text-base">
                                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-gray-700 font-medium">{{ $acc }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-base text-gray-400 italic">Lista de acessórios não informada.</p>
                    @endif
                </div>

                {{-- ─── Localização ─────────────────────────────────────── --}}
                @if($vehicle->location)
                    <div class="mt-8 elite-card p-6">
                        <h2 class="section-title mb-3">📍 Localização</h2>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-xl">🏢</div>
                            <div>
                                <p class="text-base font-bold text-gray-800">{{ $vehicle->location['name'] ?? '' }}</p>
                                <p class="text-sm text-gray-400">Pátio de armazenamento</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ─── Coluna Direita (Sticky — Preço + CTA) ──────────────── --}}
            <div class="w-full lg:w-[38%]">
                <div class="sticky top-24 space-y-5">

                    {{-- Card principal --}}
                    <div class="elite-card p-6">
                        <p class="text-sm font-black text-orange-500 uppercase tracking-widest mb-1">{{ $vehicle->brand }}</p>
                        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 leading-tight tracking-tight">
                            {{ $vehicle->model }}
                        </h1>
                        <p class="text-base text-gray-500 mt-0.5">{{ $vehicle->version }}</p>
                        <p class="text-sm text-gray-400 mt-1">{{ $vehicle->manufacture_year }}/{{ $vehicle->model_year }} · {{ number_format($vehicle->mileage, 0, ',', '.') }} km · {{ ucfirst($vehicle->color) }}</p>

                        {{-- Preço --}}
                        <div class="mt-5 pt-5 border-t border-gray-100">
                            @if($vehicle->fipe_price && $margin > 0)
                                <p class="text-base text-gray-400 line-through mb-0.5">FIPE: R$ {{ number_format($vehicle->fipe_price, 0, ',', '.') }}</p>
                            @endif
                            <p class="text-3xl sm:text-4xl font-black text-[#1a3a5c]">R$ {{ number_format($vehicle->sale_price, 0, ',', '.') }}</p>
                            @if($margin > 0)
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="badge bg-emerald-100 text-emerald-700">↓ {{ $margin }}% abaixo FIPE</span>
                                    <span class="text-sm text-emerald-600 font-bold">R$ {{ number_format($economia, 0, ',', '.') }} de economia</span>
                                </div>
                            @endif
                        </div>

                        {{-- CTA --}}
                        <button wire:click="$set('showProposta', true)"
                            class="btn-cta-lg w-full mt-6 text-lg">
                            🚀 Tenho Interesse
                        </button>

                        {{-- WhatsApp --}}
                        <a href="https://wa.me/{{ \App\Models\LandingSetting::first()?->whatsapp_number ?? '5566992184925' }}?text={{ urlencode("Olá! Tenho interesse no veículo {$vehicle->brand} {$vehicle->model} {$vehicle->model_year}, anunciado por R$ " . number_format($vehicle->sale_price, 0, ',', '.')) }}"
                            target="_blank"
                            class="w-full mt-3 flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#1ebe5d] text-white font-bold py-3.5 rounded-xl transition text-base">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                            Falar por WhatsApp
                        </a>

                        {{-- Placa --}}
                        <div class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-3">
                            <div class="bg-gray-100 rounded-xl px-4 py-2 text-center">
                                <p class="text-xs text-gray-400 font-bold uppercase">Placa</p>
                                <p class="text-base font-black text-gray-700 font-mono">{{ $vehicle->plate ? substr($vehicle->plate, 0, 3) . '•' . substr($vehicle->plate, 3) : '–' }}</p>
                            </div>
                            @if($vehicle->fipe_code)
                                <div class="bg-gray-100 rounded-xl px-4 py-2 text-center">
                                    <p class="text-xs text-gray-400 font-bold uppercase">Código FIPE</p>
                                    <p class="text-base font-black text-gray-700 font-mono">{{ $vehicle->fipe_code }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Comparativo FIPE --}}
                    @if($vehicle->fipe_price && $margin > 0)
                        <div class="bg-emerald-50 rounded-2xl border border-emerald-200 overflow-hidden">
                            <div class="bg-emerald-100 px-5 py-3 text-center">
                                <span class="text-sm font-black text-emerald-800 uppercase tracking-wider">💰 Comparativo FIPE</span>
                            </div>
                            <div class="p-5 space-y-3">
                                <div class="flex justify-between text-base">
                                    <span class="text-emerald-700 font-semibold">Tabela FIPE</span>
                                    <span class="font-black text-emerald-800">R$ {{ number_format($vehicle->fipe_price, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-base">
                                    <span class="text-emerald-700 font-semibold">Preço Elite</span>
                                    <span class="font-black text-emerald-800">R$ {{ number_format($vehicle->sale_price, 0, ',', '.') }}</span>
                                </div>
                                <div class="border-t border-emerald-200 pt-3 flex justify-between">
                                    <span class="text-emerald-700 font-black text-base">Sua economia</span>
                                    <span class="text-2xl font-black text-emerald-700">R$ {{ number_format($economia, 0, ',', '.') }}</span>
                                </div>
                                <div class="bg-emerald-600 text-white rounded-xl py-2.5 text-center text-base font-black">
                                    {{ $margin }}% abaixo da tabela FIPE
                                </div>
                                @if($vehicle->profit_margin)
                                    <p class="text-xs text-emerald-600 text-center font-semibold">
                                        Margem de lucro estimada: {{ number_format($vehicle->profit_margin, 1) }}%
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Badges de confiança --}}
                    <div class="grid grid-cols-2 gap-3">
                        @if($vehicle->has_report)
                            <div class="elite-card p-4 text-center">
                                <span class="text-2xl">📋</span>
                                <p class="text-sm font-bold text-blue-700 mt-1">Laudo aprovado</p>
                            </div>
                        @endif
                        @if($vehicle->has_factory_warranty)
                            <div class="elite-card p-4 text-center">
                                <span class="text-2xl">🛡️</span>
                                <p class="text-sm font-bold text-green-700 mt-1">Garantia de Fábrica</p>
                            </div>
                        @endif
                        <div class="elite-card p-4 text-center">
                            <span class="text-2xl">✅</span>
                            <p class="text-sm font-bold text-gray-700 mt-1">Procedência verificada</p>
                        </div>
                        <div class="elite-card p-4 text-center">
                            <span class="text-2xl">📃</span>
                            <p class="text-sm font-bold text-gray-700 mt-1">Doc. regularizado</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── Modal de proposta ──────────────────────────────────────── --}}
        @if($showProposta)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[60] p-4" wire:click.self="$set('showProposta', false)">
                <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden">
                    <div class="bg-gradient-to-r from-[#1a3a5c] to-[#1e4f8a] px-6 py-5 text-white">
                        <h3 class="font-black text-xl">🚀 Solicitar Proposta</h3>
                        <p class="text-blue-200 text-base">{{ $vehicle->brand }} {{ $vehicle->model }} {{ $vehicle->model_year }}</p>
                    </div>
                    <div class="px-6 py-6">
                        <div class="bg-orange-50 border border-orange-200 rounded-xl p-5 mb-5">
                            <p class="text-base font-bold text-orange-800">R$ {{ number_format($vehicle->sale_price, 0, ',', '.') }}</p>
                            @if($margin > 0) <p class="text-sm text-orange-600">↓ {{ $margin }}% abaixo da FIPE</p> @endif
                        </div>
                        <label class="block text-sm font-bold text-gray-600 uppercase tracking-wider mb-2">Observações (opcional)</label>
                        <textarea wire:model="observacoes" rows="3" placeholder="Forma de pagamento desejada, condições, etc."
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-base focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                        <div class="flex gap-3 mt-5">
                            <button wire:click="$set('showProposta', false)"
                                class="flex-1 py-3.5 bg-gray-100 rounded-xl font-bold text-gray-600 hover:bg-gray-200 transition text-base">
                                Cancelar
                            </button>
                            <button wire:click="solicitarProposta"
                                class="btn-cta-lg flex-1">
                                Enviar Proposta
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ─── Veículos Similares ─────────────────────────────────────── --}}
        @if($this->similares->isNotEmpty())
            <div class="mt-10 pt-8 border-t border-gray-200">
                <h2 class="section-title mb-5">🔍 Veículos Similares</h2>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
                    @foreach($this->similares as $sim)
                        @php
                            $simMedia = is_string($sim->media) ? json_decode($sim->media, true) : $sim->media;
                            $simThumb = is_array($simMedia) && count($simMedia) > 0 ? $simMedia[0] : null;
                            $simDisc  = $sim->fipe_price > 0 ? round(($sim->fipe_price - $sim->sale_price) / $sim->fipe_price * 100) : 0;
                        @endphp
                        <a href="{{ route('vehicle.details', $sim->id) }}" wire:navigate
                            class="elite-card overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition group">
                            <div class="h-36 bg-gray-100 overflow-hidden relative">
                                @if($simThumb)
                                    <img src="{{ $simThumb }}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition duration-500"/>
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-3xl">🚗</div>
                                @endif
                                @if($simDisc > 0)
                                    <span class="absolute bottom-2 right-2 badge bg-emerald-500 text-white">↓{{ $simDisc }}%</span>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="font-black text-gray-900 text-base leading-tight">{{ $sim->brand }} {{ $sim->model }}</h3>
                                <p class="text-sm text-gray-400 mt-0.5">{{ $sim->model_year }} · {{ number_format($sim->mileage, 0, ',', '.') }} km</p>
                                <p class="text-lg font-black text-[#1a3a5c] mt-1.5">R$ {{ number_format($sim->sale_price, 0, ',', '.') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Bottom nav agora no layout compartilhado --}}
</div>
