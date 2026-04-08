<div class="min-h-screen bg-[#f1f5f9]">

    {{-- ─── Hero Search Bar ──────────────────────────────────────────── --}}
    <div class="bg-gradient-to-br from-[#1a3a5c] to-[#1e4f8a] py-6 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-center gap-4">
                <div class="flex-1">
                    <h1 class="text-white font-black text-2xl tracking-tight mb-1">Vitrine de Veículos</h1>
                    <p class="text-blue-200 text-sm">{{ $totalSemFiltro }} veículo(s) disponíveis</p>
                </div>
                {{-- Search --}}
                <div class="relative flex-1 max-w-xl">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input wire:model.live.debounce.400ms="searchTerm"
                        type="text"
                        placeholder="Marca, modelo, versão ou placa..."
                        class="w-full pl-11 pr-4 py-3.5 rounded-2xl text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent border-0 shadow-lg"/>
                    @if($searchTerm)
                        <button wire:click="$set('searchTerm', '')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Quick switches --}}
            <div class="flex gap-3 mt-4 flex-wrap">
                @php
                    $tags = [
                        'vehiclesOnSale'  => ['🏷️ Em Promoção',   'orange'],
                        'carsWithReport'  => ['📋 Com Laudo',      'blue'],
                        'factoryWarranty' => ['🛡️ Garantia Fab.',  'green'],
                        'justArrived'     => ['🆕 Recém Chegados', 'purple'],
                    ];
                @endphp
                @foreach($tags as $prop => [$label, $color])
                    <button wire:click="$toggle('{{ $prop }}')"
                        class="px-3 py-1.5 rounded-full text-xs font-bold border transition
                            {{ $this->$prop
                                ? "bg-{$color}-500 border-{$color}-500 text-white"
                                : 'bg-white bg-opacity-15 border-white border-opacity-30 text-white hover:bg-opacity-25' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-5">
        <div class="flex gap-6">

            {{-- ─── Sidebar de Filtros ───────────────────────────────── --}}
            <aside class="hidden lg:block w-64 flex-shrink-0">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 sticky top-24 space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="font-black text-gray-900">Filtros</h3>
                        @if($this->activeFiltersCount > 0)
                            <button wire:click="clearFilters"
                                class="text-xs text-red-500 font-bold hover:underline">
                                Limpar ({{ $this->activeFiltersCount }})
                            </button>
                        @endif
                    </div>

                    {{-- Ordenar --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Ordenar por</label>
                        <select wire:model.live="ordenar"
                            class="w-full text-sm rounded-xl border border-gray-200 px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="recentes">Mais Recentes</option>
                            <option value="preco_asc">Menor Preço</option>
                            <option value="preco_desc">Maior Preço</option>
                            <option value="km_asc">Menor KM</option>
                            <option value="ano_desc">Mais Novo</option>
                        </select>
                    </div>

                    {{-- Faixa de Preço --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Preço (R$)</label>
                        <div class="flex gap-2">
                            <input wire:model.blur="priceMin" type="number" placeholder="Mínimo"
                                class="w-full text-xs rounded-xl border border-gray-200 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                            <input wire:model.blur="priceMax" type="number" placeholder="Máximo"
                                class="w-full text-xs rounded-xl border border-gray-200 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                        </div>
                    </div>

                    {{-- Ano --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Ano mínimo</label>
                        <input wire:model.blur="yearMin" type="number" placeholder="Ex: 2020" min="2000" max="{{ date('Y') }}"
                            class="w-full text-sm rounded-xl border border-gray-200 px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                    </div>

                    {{-- Marcas --}}
                    @if($availableBrands->isNotEmpty())
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Marcas</label>
                            <div class="space-y-1.5 max-h-40 overflow-y-auto pr-1">
                                @foreach($availableBrands as $brand)
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input wire:model.live="brands" type="checkbox" value="{{ $brand }}"
                                            class="w-4 h-4 accent-blue-600 rounded"/>
                                        <span class="text-sm text-gray-700 group-hover:text-blue-600 transition">{{ $brand }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Categorias --}}
                    @if($availableCategories->isNotEmpty())
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Categorias</label>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($availableCategories as $cat)
                                    <button wire:click="@if(in_array('{{ $cat }}', $categories)) $set('categories', array_values(array_diff($categories, ['{{ $cat }}']))) @else $set('categories', array_merge($categories, ['{{ $cat }}'])) @endif"
                                        class="px-2.5 py-1 text-xs font-bold rounded-lg border transition
                                            {{ in_array($cat, $categories)
                                                ? 'bg-blue-600 border-blue-600 text-white'
                                                : 'bg-gray-50 border-gray-200 text-gray-600 hover:border-blue-400' }}">
                                        {{ $cat }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Combustível --}}
                    @if($availableFuelTypes->isNotEmpty())
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Combustível</label>
                            <div class="space-y-1.5">
                                @foreach($availableFuelTypes as $fuel)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input wire:model.live="fuelTypes" type="checkbox" value="{{ $fuel }}"
                                            class="w-4 h-4 accent-blue-600 rounded"/>
                                        <span class="text-sm text-gray-700">{{ ucfirst($fuel) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Transmissão --}}
                    @if($availableTransmissions->isNotEmpty())
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Câmbio</label>
                            <div class="flex gap-2 flex-wrap">
                                @foreach($availableTransmissions as $trans)
                                    <button wire:click="@if(in_array('{{ $trans }}', $transmissions)) $set('transmissions', array_values(array_diff($transmissions, ['{{ $trans }}']))) @else $set('transmissions', array_merge($transmissions, ['{{ $trans }}'])) @endif"
                                        class="px-2.5 py-1 text-xs font-bold rounded-lg border transition
                                            {{ in_array($trans, $transmissions)
                                                ? 'bg-blue-600 border-blue-600 text-white'
                                                : 'bg-gray-50 border-gray-200 text-gray-600 hover:border-blue-400' }}">
                                        {{ ucfirst($trans) }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </aside>

            {{-- ─── Grid de Veículos ─────────────────────────────────── --}}
            <div class="flex-1 min-w-0">

                {{-- Toolbar --}}
                <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
                    <div class="flex items-center gap-3">
                        {{-- Filtros mobile --}}
                        <button wire:click="$toggle('showFilters')"
                            class="lg:hidden flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-bold shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Filtros
                            @if($this->activeFiltersCount > 0)
                                <span class="bg-blue-600 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full">{{ $this->activeFiltersCount }}</span>
                            @endif
                        </button>
                        <p class="text-sm text-gray-500">
                            <span class="font-black text-gray-900">{{ $vehicles->total() }}</span> resultado(s)
                            @if($vehicles->total() != $totalSemFiltro)
                                de {{ $totalSemFiltro }}
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        {{-- Ordenar mobile --}}
                        <select wire:model.live="ordenar"
                            class="lg:hidden text-xs rounded-xl border border-gray-200 px-3 py-2 focus:ring-2 focus:ring-blue-500">
                            <option value="recentes">Mais Recentes</option>
                            <option value="preco_asc">Menor Preço</option>
                            <option value="preco_desc">Maior Preço</option>
                            <option value="km_asc">Menor KM</option>
                            <option value="ano_desc">Mais Novo</option>
                        </select>
                        {{-- View toggle --}}
                        <div class="flex bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                            <button wire:click="$set('viewMode','grid')"
                                class="p-2 {{ $viewMode === 'grid' ? 'bg-blue-600 text-white' : 'text-gray-500 hover:bg-gray-50' }} transition">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM14 14h7v7h-7z"/>
                                </svg>
                            </button>
                            <button wire:click="$set('viewMode','list')"
                                class="p-2 {{ $viewMode === 'list' ? 'bg-blue-600 text-white' : 'text-gray-500 hover:bg-gray-50' }} transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Mobile Filters Drawer --}}
                @if($showFilters)
                    <div class="lg:hidden bg-white rounded-2xl border border-gray-200 shadow-sm p-5 mb-4 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Preço Min</label>
                                <input wire:model.blur="priceMin" type="number" placeholder="R$"
                                    class="w-full text-sm rounded-xl border border-gray-200 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Preço Max</label>
                                <input wire:model.blur="priceMax" type="number" placeholder="R$"
                                    class="w-full text-sm rounded-xl border border-gray-200 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Marcas</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($availableBrands as $brand)
                                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border text-xs font-bold cursor-pointer transition
                                        {{ in_array($brand, $brands) ? 'bg-blue-600 border-blue-600 text-white' : 'bg-gray-50 border-gray-200 text-gray-700' }}">
                                        <input wire:model.live="brands" type="checkbox" value="{{ $brand }}" class="hidden"/>
                                        {{ $brand }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex gap-3 pt-2 border-t border-gray-100">
                            <button wire:click="clearFilters" class="flex-1 py-2 bg-gray-100 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-200 transition">Limpar</button>
                            <button wire:click="$set('showFilters', false)" class="flex-1 py-2 bg-blue-600 rounded-xl text-sm font-bold text-white hover:bg-blue-700 transition">Aplicar</button>
                        </div>
                    </div>
                @endif

                {{-- Empty State --}}
                @if($vehicles->isEmpty())
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm py-20 text-center">
                        <div class="text-5xl mb-4">🔍</div>
                        <h3 class="text-lg font-bold text-gray-700 mb-1">Nenhum veículo encontrado</h3>
                        <p class="text-gray-400 text-sm mb-6">Tente ajustar os filtros ou buscar por outros termos.</p>
                        <button wire:click="clearFilters"
                            class="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition">
                            Limpar Filtros
                        </button>
                    </div>

                {{-- Grid View --}}
                @elseif($viewMode === 'grid')
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach($vehicles as $vehicle)
                            @php
                                $isFav   = in_array($vehicle->id, $userFavorites);
                                $media   = is_array($vehicle->media) ? $vehicle->media : json_decode($vehicle->media ?? '[]', true);
                                $thumb   = $media[0] ?? null;
                                $discPct = $vehicle->fipe_price > 0 ? (($vehicle->fipe_price - $vehicle->sale_price) / $vehicle->fipe_price) * 100 : 0;
                            @endphp
                            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition group">
                                {{-- Imagem --}}
                                <div class="relative h-44 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                                    @if($thumb)
                                        <img src="{{ $thumb }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition duration-500"/>
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-4xl">🚗</div>
                                    @endif

                                    {{-- Badges --}}
                                    <div class="absolute top-2 left-2 flex gap-1.5 flex-wrap">
                                        @if($vehicle->is_on_sale)
                                            <span class="bg-orange-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full">🏷️ OFERTA</span>
                                        @endif
                                        @if($vehicle->is_just_arrived)
                                            <span class="bg-purple-600 text-white text-[10px] font-black px-2 py-0.5 rounded-full">🆕 NOVO</span>
                                        @endif
                                        @if($vehicle->has_factory_warranty)
                                            <span class="bg-green-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full">🛡️ GARANTIA</span>
                                        @endif
                                    </div>

                                    {{-- Favorito --}}
                                    <button wire:click="toggleFavorite({{ $vehicle->id }})"
                                        class="absolute top-2 right-2 w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center transition hover:scale-110">
                                        <svg class="w-4 h-4 {{ $isFav ? 'text-red-500 fill-current' : 'text-gray-400' }}" fill="{{ $isFav ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                    </button>

                                    {{-- Desconto FIPE --}}
                                    @if($discPct > 0)
                                        <div class="absolute bottom-2 right-2 bg-emerald-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full">
                                            ↓ {{ number_format($discPct, 0) }}% FIPE
                                        </div>
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div class="p-4">
                                    <div class="flex items-start justify-between mb-1">
                                        <div>
                                            <h3 class="font-black text-gray-900 leading-tight">{{ $vehicle->brand }} {{ $vehicle->model }}</h3>
                                            <p class="text-xs text-gray-500 truncate max-w-[200px]">{{ $vehicle->version }}</p>
                                        </div>
                                        <span class="text-xs bg-gray-100 text-gray-500 font-bold px-2 py-0.5 rounded-lg flex-shrink-0">{{ $vehicle->model_year }}</span>
                                    </div>

                                    {{-- Specs mini --}}
                                    <div class="flex gap-3 text-xs text-gray-500 font-semibold my-2.5">
                                        <span>{{ number_format($vehicle->mileage, 0, ',', '.') }} km</span>
                                        <span>·</span>
                                        <span>{{ ucfirst($vehicle->fuel_type) }}</span>
                                        <span>·</span>
                                        <span>{{ ucfirst($vehicle->transmission) }}</span>
                                    </div>

                                    {{-- Preço --}}
                                    <div class="flex items-end justify-between mt-3">
                                        <div>
                                            @if($vehicle->fipe_price && $discPct > 0)
                                                <p class="text-xs text-gray-400 line-through">R$ {{ number_format($vehicle->fipe_price, 0, ',', '.') }}</p>
                                            @endif
                                            <p class="text-xl font-black text-[#1a3a5c]">R$ {{ number_format($vehicle->sale_price, 0, ',', '.') }}</p>
                                        </div>
                                        <a href="{{ route('vehicle.details', $vehicle->id) }}" wire:navigate
                                            class="px-4 py-2 bg-[#1a3a5c] hover:bg-[#1e4f8a] text-white rounded-xl text-xs font-black transition">
                                            Ver Mais
                                        </a>
                                    </div>

                                    {{-- Laudo --}}
                                    @if($vehicle->has_report)
                                        <div class="mt-2.5 pt-2.5 border-t border-gray-100 flex items-center gap-1.5 text-xs text-emerald-600 font-semibold">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Laudo disponível
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                {{-- List View --}}
                @else
                    <div class="space-y-3">
                        @foreach($vehicles as $vehicle)
                            @php
                                $isFav  = in_array($vehicle->id, $userFavorites);
                                $media  = is_array($vehicle->media) ? $vehicle->media : json_decode($vehicle->media ?? '[]', true);
                                $thumb  = $media[0] ?? null;
                                $discPct = $vehicle->fipe_price > 0 ? (($vehicle->fipe_price - $vehicle->sale_price) / $vehicle->fipe_price) * 100 : 0;
                            @endphp
                            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition flex gap-0">
                                {{-- Thumb --}}
                                <div class="w-36 h-28 sm:w-48 sm:h-36 flex-shrink-0 bg-gray-100 overflow-hidden">
                                    @if($thumb)
                                        <img src="{{ $thumb }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="w-full h-full object-cover"/>
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-3xl">🚗</div>
                                    @endif
                                </div>
                                {{-- Info --}}
                                <div class="flex-1 min-w-0 p-4 flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h3 class="font-black text-gray-900">{{ $vehicle->brand }} {{ $vehicle->model }} {{ $vehicle->model_year }}</h3>
                                            @if($vehicle->is_on_sale) <span class="text-[10px] bg-orange-100 text-orange-700 font-black px-1.5 py-0.5 rounded-full">🏷️ OFERTA</span> @endif
                                            @if($vehicle->has_report) <span class="text-[10px] bg-emerald-100 text-emerald-700 font-black px-1.5 py-0.5 rounded-full">📋 LAUDO</span> @endif
                                        </div>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $vehicle->version }}</p>
                                        <div class="flex gap-3 text-xs text-gray-500 font-semibold mt-1.5">
                                            <span>{{ number_format($vehicle->mileage, 0, ',', '.') }} km</span>
                                            <span>·</span><span>{{ ucfirst($vehicle->fuel_type) }}</span>
                                            <span>·</span><span>{{ ucfirst($vehicle->transmission) }}</span>
                                            @if($vehicle->color) <span>·</span><span>{{ $vehicle->color }}</span> @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between mt-3">
                                        <div>
                                            @if($discPct > 0)
                                                <p class="text-xs text-gray-400 line-through">R$ {{ number_format($vehicle->fipe_price, 0, ',', '.') }}</p>
                                            @endif
                                            <p class="text-lg font-black text-[#1a3a5c]">R$ {{ number_format($vehicle->sale_price, 0, ',', '.') }}</p>
                                            @if($discPct > 0) <p class="text-xs text-emerald-500 font-bold">↓ {{ number_format($discPct, 0) }}% abaixo FIPE</p> @endif
                                        </div>
                                        <div class="flex gap-2">
                                            <button wire:click="toggleFavorite({{ $vehicle->id }})"
                                                class="w-9 h-9 rounded-xl border border-gray-200 flex items-center justify-center hover:border-red-300 transition">
                                                <svg class="w-4 h-4 {{ $isFav ? 'text-red-500 fill-current' : 'text-gray-400' }}" fill="{{ $isFav ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                                </svg>
                                            </button>
                                            <a href="{{ route('vehicle.details', $vehicle->id) }}" wire:navigate
                                                class="px-4 py-2 bg-[#1a3a5c] hover:bg-[#1e4f8a] text-white rounded-xl text-xs font-black transition">
                                                Ver Detalhes
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Paginação --}}
                <div class="mt-6">
                    {{ $vehicles->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Bottom Nav Mobile ───────────────────────────────────────── --}}
    <nav class="lg:hidden fixed bottom-0 inset-x-0 bg-white border-t border-gray-200 shadow-xl z-50">
        <div class="flex">
            @php
                $navItems = [
                    ['route' => 'dashboard',      'icon' => '🏠', 'label' => 'Vitrine'],
                    ['route' => 'meus-pedidos',   'icon' => '📋', 'label' => 'Pedidos'],
                    ['route' => 'financeiro',      'icon' => '💳', 'label' => 'Financeiro'],
                    ['route' => 'suporte',         'icon' => '💬', 'label' => 'Suporte'],
                    ['route' => 'favoritos',       'icon' => '❤️', 'label' => 'Favoritos'],
                ];
            @endphp
            @foreach($navItems as $item)
                <a href="{{ route($item['route']) }}" wire:navigate
                    class="flex-1 flex flex-col items-center justify-center py-2.5 transition
                        {{ request()->routeIs($item['route'])
                            ? 'text-[#1a3a5c]'
                            : 'text-gray-400 hover:text-gray-600' }}">
                    <span class="text-lg leading-none">{{ $item['icon'] }}</span>
                    <span class="text-[9px] font-bold mt-0.5 tracking-wide">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    </nav>
    <div class="lg:hidden h-16"></div>

</div>
