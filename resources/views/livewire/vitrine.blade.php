<div class="w-full bg-[#f8fafc] min-h-screen">
    <!-- Top Banners mimicking Localiza -->
    <div class="w-full bg-gradient-to-r from-primary to-blue-900 relative overflow-hidden mb-6 shadow-sm border-b border-gray-200">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16 flex justify-between items-center h-24 md:h-32">
            <div class="text-white z-10 w-full flex justify-between items-center">
                <div>
                    <h1 class="text-3xl md:text-[44px] font-black tracking-tight italic uppercase leading-none">Giro Rápido</h1>
                    <p class="text-lg md:text-xl font-medium mt-1">Com margem de até <span class="text-orange-400 font-black">20% Abaixo FIPE</span></p>
                </div>
            </div>
            <!-- Decorative Elements -->
            <div class="absolute right-0 top-0 h-full w-1/2 opacity-20 pointer-events-none" style="background-image: radial-gradient(white 1px, transparent 1px); background-size: 16px 16px;"></div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row gap-6 pb-16">
        <!-- Sidebar / Filtros (Print 2 logic) -->
        <div class="w-full md:w-[280px] flex-shrink-0 space-y-6">
            <div class="bg-white sticky top-24 shadow-sm border border-gray-200 rounded min-h-[400px]">
                <div class="flex items-center justify-between p-4 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        <h3 class="text-[15px] font-bold text-gray-800">Filtrar por</h3>
                    </div>
                </div>
                
                <div class="divide-y divide-gray-100">
                    <!-- Search Accordion -->
                    <div x-data="{ open: true }" class="p-4">
                        <button @click="open = !open" class="flex justify-between items-center w-full focus:outline-none">
                            <span class="text-[13px] font-bold text-gray-800 tracking-wide">BUSCA</span>
                            <svg class="w-4 h-4 text-gray-500 transform transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" class="mt-4">
                            <input wire:model.live.debounce.300ms="searchTerm" type="text" class="w-full rounded border-gray-300 focus:border-primary focus:ring-1 focus:ring-primary shadow-sm text-[13px] py-2 px-3 placeholder-gray-400" placeholder="Digite marca ou modelo...">
                        </div>
                    </div>

                    <!-- Marca Accordion -->
                    <div x-data="{ open: true }" class="p-4">
                        <button @click="open = !open" class="flex justify-between items-center w-full focus:outline-none">
                            <span class="text-[13px] font-bold text-gray-800 tracking-wide">MARCAS</span>
                            <svg class="w-4 h-4 text-gray-500 transform transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" class="mt-4 space-y-2">
                            @foreach($availableBrands as $b)
                                <label class="flex items-center cursor-pointer group">
                                    <input type="checkbox" wire:model.live="brands" value="{{ $b }}" class="form-checkbox h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary transition duration-150 ease-in-out">
                                    <span class="ml-2 text-[13px] text-gray-700 group-hover:text-gray-900 transition">{{ $b }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Categoria Accordion -->
                    <div x-data="{ open: true }" class="p-4">
                        <button @click="open = !open" class="flex justify-between items-center w-full focus:outline-none">
                            <span class="text-[13px] font-bold text-gray-800 tracking-wide">CARROCERIA</span>
                            <svg class="w-4 h-4 text-gray-500 transform transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" class="mt-4 space-y-2 mb-2">
                            @foreach($availableCategories as $c)
                                <label class="flex items-center cursor-pointer group">
                                    <input type="checkbox" wire:model.live="categories" value="{{ $c }}" class="form-checkbox h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary transition duration-150 ease-in-out">
                                    <span class="ml-2 text-[13px] text-gray-700 group-hover:text-gray-900 transition">{{ $c }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vitrine Grid -->
        <div class="flex-1 w-full min-w-0">
            <!-- Active Filters (Selected Tags) -->
            <div class="mb-4 text-sm flex gap-2 flex-wrap items-center">
                @if(count($brands) > 0 || count($categories) > 0)
                    <span class="text-[12px] text-gray-500 font-bold mr-2">Filtros ativos:</span>
                @endif
                
                @foreach($brands as $ab)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[12px] font-bold bg-white border border-gray-300 text-gray-700 shadow-sm">
                        {{ $ab }}
                        <button wire:click="removeFilter('brands', '{{ $ab }}')" class="hover:text-red-500 transition focus:outline-none"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                    </span>
                @endforeach
                
                @foreach($categories as $ac)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[12px] font-bold bg-white border border-gray-300 text-gray-700 shadow-sm">
                        {{ $ac }}
                        <button wire:click="removeFilter('categories', '{{ $ac }}')" class="hover:text-red-500 transition focus:outline-none"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                    </span>
                @endforeach
            </div>

            <div class="mb-4 flex justify-between items-center bg-transparent py-1 border-b border-gray-200/50 pb-4">
                <h2 class="text-[18px] font-bold text-gray-800">Veículos em Destaque</h2>
                <span class="text-[12px] font-medium text-gray-500 bg-white border border-gray-200 px-3 py-1 rounded-sm">{{ count($vehicles) }} resultados</span>
            </div>

            @if(count($vehicles) === 0)
                <div class="bg-white rounded border border-gray-200 p-12 text-center h-64 flex flex-col justify-center items-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <h3 class="mt-4 text-sm font-bold text-gray-700">Nenhum veículo encontrado</h3>
                    <p class="mt-1 text-[13px] text-gray-500">Limpe as seleções dos filtros laterais para ver as ofertas.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($vehicles as $vehicle)
                        @php 
                            $media = json_decode($vehicle->media, true); 
                            $image = (is_array($media) && count($media) > 0) ? $media[0] : 'https://placehold.co/600x400?text=Sem+Foto';
                            $margin = $vehicle->fipe_price > 0 ? round((($vehicle->fipe_price - $vehicle->sale_price) / $vehicle->fipe_price) * 100) : 0;
                            $isFav = in_array($vehicle->id, $userFavorites);
                        @endphp
                        <a href="{{ route('vehicle.details', $vehicle->id) }}" wire:navigate class="bg-white rounded border border-gray-200 overflow-hidden flex flex-col group hover:shadow-lg hover:border-gray-300 transition-all block">
                            <!-- Imagem -->
                            <div class="relative h-[210px] bg-gray-100 overflow-hidden cursor-pointer">
                                <img src="{{ $image }}" alt="{{ $vehicle->model }}" class="w-full h-full object-cover">
                                <!-- Badge Ano -->
                                <div class="absolute top-3 left-3 bg-orange-500 text-white text-[10px] uppercase font-black px-2 py-0.5 rounded-sm shadow-sm">
                                    {{ $vehicle->manufacture_year }}/{{ $vehicle->model_year }}
                                </div>
                                <!-- Heart -->
                                <button wire:click.prevent="toggleFavorite({{ $vehicle->id }})" class="absolute top-3 right-3 text-gray-300 hover:text-red-500 drop-shadow-md transition z-10 focus:outline-none">
                                    <svg class="w-7 h-7 {{ $isFav ? 'text-red-500 fill-current' : 'fill-white' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                </button>
                            </div>
                            
                            <!-- Corpo -->
                            <div class="p-4 flex flex-col flex-grow">
                                <div class="mb-3">
                                    <h3 class="text-[17px] font-black text-primary uppercase leading-tight tracking-tight">{{ $vehicle->brand }} {{ $vehicle->model }}</h3>
                                    <p class="text-[12px] font-medium text-gray-500 uppercase mt-1">{{ $vehicle->version }}</p>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-x-2 gap-y-2 mt-2 mb-5">
                                    <div class="flex items-center gap-1.5 text-[11px] font-semibold text-gray-500">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> 
                                        {{ number_format($vehicle->mileage, 0, ',', '.') }} km
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[11px] font-semibold text-gray-500">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg> 
                                        {{ explode(' ', $vehicle->transmission)[0] }}
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[11px] font-semibold text-gray-500">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16h14zm-4-4h2"></path></svg> 
                                        {{ explode(' ', $vehicle->fuel_type)[0] }}
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[11px] font-semibold text-gray-500">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg> 
                                        {{ $vehicle->color }}
                                    </div>
                                </div>

                                <div class="mt-auto border-t border-gray-100 pt-4">
                                    <div class="flex items-center justify-between mb-0.5">
                                        <span class="text-[11px] text-gray-400 font-bold line-through">FIPE: R$ {{ number_format($vehicle->fipe_price, 2, ',', '.') }}</span>
                                        @if($margin > 0)
                                            <span class="text-[9px] font-black tracking-widest text-[#e06512] border border-[#e06512]/30 px-1 py-0.5 rounded-sm uppercase">Super Oferta</span>
                                        @endif
                                    </div>
                                    <div class="text-[24px] font-black text-gray-900 tracking-tight leading-none mb-4 mt-1 group-hover:text-primary transition-colors">
                                        R$ {{ number_format($vehicle->sale_price, 2, ',', '.') }}
                                    </div>
                                    
                                    <button class="w-full bg-orange_cta group-hover:bg-[#e06512] text-white font-black py-2.5 px-4 rounded transition-colors text-[14px] flex items-center justify-center gap-2">
                                        Tenho Interesse
                                    </button>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
