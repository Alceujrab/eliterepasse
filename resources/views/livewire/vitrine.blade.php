<div class="flex flex-col md:flex-row gap-6 min-h-screen">
    <!-- Sidebar / Filtros -->
    <div class="w-full md:w-1/4 lg:w-1/5 space-y-6">
        <div class="glass-panel p-5 sticky top-6 bg-white shadow-sm border border-gray-100 rounded-xl">
            <h3 class="text-xl font-bold text-primary mb-4 border-b pb-2">Filtros</h3>
            
            <div class="space-y-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar (Modelo/Placa)</label>
                    <input wire:model.live.debounce.300ms="searchTerm" type="text" class="w-full rounded border-gray-300 focus:border-primary focus:ring-primary shadow-sm" placeholder="Ex: T-Cross">
                </div>

                <!-- Categoria -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                    <select wire:model.live="category" class="w-full rounded border-gray-300 focus:border-primary focus:ring-primary shadow-sm">
                        <option value="">Todas</option>
                        <option value="Hatch">Hatch</option>
                        <option value="Sedan">Sedan</option>
                        <option value="SUV">SUV</option>
                        <option value="Picape">Picape</option>
                    </select>
                </div>

                <!-- Marca -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                    <select wire:model.live="brand" class="w-full rounded border-gray-300 focus:border-primary focus:ring-primary shadow-sm">
                        <option value="">Todas</option>
                        <option value="Volkswagen">Volkswagen</option>
                        <option value="Jeep">Jeep</option>
                        <option value="Chevrolet">Chevrolet</option>
                        <option value="Fiat">Fiat</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content / Vitrine Grid -->
    <div class="w-full md:w-3/4 lg:w-4/5 pt-0">
        <div class="mb-6 flex justify-between items-center bg-white p-4 rounded-xl shadow-sm">
            <h2 class="text-2xl font-bold text-gray-800">Veículos em Destaque</h2>
            <span class="text-sm font-medium text-gray-500 bg-gray-100 px-3 py-1 rounded-full">{{ count($vehicles) }} resultados</span>
        </div>

        @if(count($vehicles) === 0)
            <div class="bg-white rounded-xl shadow-sm p-12 text-center border border-gray-100">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-gray-900">Nenhum veículo encontrado</h3>
                <p class="mt-1 text-sm text-gray-500">Ajuste os filtros laterais para ver as ofertas.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($vehicles as $vehicle)
                    @php 
                        $media = json_decode($vehicle->media, true); 
                        $image = (is_array($media) && count($media) > 0) ? $media[0] : 'https://placehold.co/600x400?text=Sem+Foto';
                    @endphp
                    <div class="bg-white rounded-xl shadow hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden flex flex-col group relative transform hover:-translate-y-1">
                        <div class="relative h-56 overflow-hidden bg-gray-200">
                            <img src="{{ $image }}" alt="{{ $vehicle->model }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute top-2 left-2 bg-orange_cta text-white text-xs font-bold px-3 py-1 rounded-full shadow border-2 border-white/20">
                                {{ $vehicle->manufacture_year }}/{{ $vehicle->model_year }}
                            </div>
                        </div>
                        
                        <div class="p-5 flex flex-col flex-grow">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h3 class="text-lg font-black text-primary leading-tight uppercase">{{ $vehicle->brand }} {{ $vehicle->model }}</h3>
                                    <p class="text-sm font-medium text-gray-500 mt-1">{{ $vehicle->version }}</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-2 my-4 text-xs font-medium text-gray-700 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                <div class="flex items-center"><svg class="w-3 h-3 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> {{ number_format($vehicle->mileage, 0, ',', '.') }} km</div>
                                <div class="flex items-center"><svg class="w-3 h-3 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> {{ $vehicle->transmission }}</div>
                                <div class="flex items-center"><svg class="w-3 h-3 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg> {{ $vehicle->color }}</div>
                                <div class="flex items-center"><svg class="w-3 h-3 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg> {{ $vehicle->fuel_type }}</div>
                            </div>

                            <div class="mt-auto pt-4 border-t border-gray-100">
                                <div class="text-xs text-gray-500 mb-1 line-through">FIPE: R$ {{ number_format($vehicle->fipe_price, 2, ',', '.') }}</div>
                                <div class="flex justify-between items-baseline mb-4">
                                    <span class="text-2xl font-black text-gray-900">R$ {{ number_format($vehicle->sale_price, 2, ',', '.') }}</span>
                                    <span class="text-xs font-bold text-orange-600 bg-orange-50 border border-orange-100 px-2 py-1 rounded-md uppercase">Super Oferta</span>
                                </div>
                                <a href="#" class="flex justify-center items-center w-full bg-orange_cta hover:bg-[#e06512] text-white font-bold py-3 px-4 rounded-lg transition-colors duration-300 shadow-md hover:shadow-lg">
                                    <span>Tenho Interesse</span>
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
