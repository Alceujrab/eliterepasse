<div class="min-h-screen bg-[#f1f5f9]">

    {{-- ─── Header ─────────────────────────────────────────────────── --}}
    <div class="page-hero">
        <div class="page-container py-8 sm:py-10 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div class="text-white">
                <p class="text-orange-300 text-sm font-bold uppercase tracking-widest mb-1">Portal do Lojista</p>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">❤️ Meus Favoritos</h1>
                <p class="text-blue-200 text-base mt-1">{{ $favorites->count() }} veículo{{ $favorites->count() != 1 ? 's' : '' }} salvo{{ $favorites->count() != 1 ? 's' : '' }}</p>
            </div>
            <a href="{{ route('dashboard') }}" wire:navigate
                class="flex items-center gap-2 bg-white bg-opacity-10 border border-white border-opacity-20 text-white font-bold px-6 py-3.5 rounded-xl transition hover:bg-opacity-20 text-base backdrop-blur-sm">
                🔍 Ver Vitrine
            </a>
        </div>
    </div>

    <div class="page-container py-6">

        @if($favorites->isEmpty())
            <div class="elite-card flex flex-col items-center justify-center text-center py-24 px-6 mt-4">
                <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center mb-5 text-4xl">❤️</div>
                <h3 class="text-xl font-black text-gray-800 mb-2">Nenhum favorito ainda</h3>
                <p class="text-base text-gray-400 mb-6 max-w-sm">Navegue pela vitrine e clique no ❤️ dos veículos que gostar. Eles aparecerão aqui!</p>
                <a href="{{ route('dashboard') }}" wire:navigate class="btn-cta-lg flex items-center gap-2">
                    🔍 Explorar Vitrine
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach($favorites as $fav)
                    @if($fav->vehicle)
                        @php
                            $vehicle = $fav->vehicle;
                            $media = is_string($vehicle->media) ? json_decode($vehicle->media, true) : $vehicle->media;
                            $image = is_array($media) && count($media) > 0 ? $media[0] : null;
                            $margin = $vehicle->fipe_price > 0 ? round(($vehicle->fipe_price - $vehicle->sale_price) / $vehicle->fipe_price * 100) : 0;
                        @endphp
                        <div class="elite-card overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition group relative">

                            {{-- Imagem --}}
                            <a href="{{ route('vehicle.details', $vehicle->id) }}" wire:navigate class="block">
                                <div class="h-48 sm:h-52 bg-gray-100 overflow-hidden relative">
                                    @if($image)
                                        <img src="{{ $image }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500"/>
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-5xl">🚗</div>
                                    @endif

                                    {{-- Badges --}}
                                    <div class="absolute top-2 left-2 flex gap-1.5 flex-wrap">
                                        @if($vehicle->is_on_sale)
                                            <span class="badge bg-orange-500 text-white shadow">🏷️ OFERTA</span>
                                        @endif
                                        @if($margin > 0)
                                            <span class="badge bg-emerald-500 text-white shadow">↓{{ $margin }}% FIPE</span>
                                        @endif
                                    </div>

                                    {{-- Status --}}
                                    @if($vehicle->status !== 'disponivel')
                                        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                            <span class="bg-red-500 text-white font-black px-4 py-2 rounded-lg text-sm">
                                                {{ $vehicle->status === 'vendido' ? '🔴 VENDIDO' : '⏳ RESERVADO' }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </a>

                            {{-- Info --}}
                            <div class="p-5">
                                <a href="{{ route('vehicle.details', $vehicle->id) }}" wire:navigate class="block">
                                    <p class="text-xs font-bold text-orange-500 uppercase tracking-widest">{{ $vehicle->brand }}</p>
                                    <h3 class="text-base font-black text-gray-900 leading-tight mt-0.5">{{ $vehicle->model }}</h3>
                                    <p class="text-sm text-gray-400 mt-0.5">{{ $vehicle->manufacture_year }}/{{ $vehicle->model_year }} · {{ number_format($vehicle->mileage, 0, ',', '.') }} km</p>
                                </a>

                                <div class="flex items-end justify-between mt-3 pt-3 border-t border-gray-100">
                                    <div>
                                        @if($vehicle->fipe_price && $margin > 0)
                                            <p class="text-sm text-gray-400 line-through">R$ {{ number_format($vehicle->fipe_price, 0, ',', '.') }}</p>
                                        @endif
                                        <p class="text-xl font-black text-[#1a3a5c]">R$ {{ number_format($vehicle->sale_price, 0, ',', '.') }}</p>
                                    </div>
                                    <button wire:click="removeFavorite({{ $fav->id }})" wire:confirm="Remover dos favoritos?"
                                        class="w-10 h-10 rounded-xl bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition flex-shrink-0">
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    {{-- Bottom nav agora no layout compartilhado --}}
</div>
