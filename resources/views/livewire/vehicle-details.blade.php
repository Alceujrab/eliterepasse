<div class="max-w-[1240px] mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-10 bg-white min-h-screen">
    
    <!-- Top Bar -->
    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
        <a href="{{ route('dashboard') }}" class="flex items-center text-[15px] font-bold text-gray-800 hover:text-primary transition" wire:navigate>
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            Voltar
        </a>
        
        <div class="flex items-center gap-6">
            <button wire:click="toggleFavorite" class="flex items-center gap-2 text-[14px] font-bold text-gray-700 hover:text-red-500 transition group focus:outline-none">
                <svg class="w-5 h-5 {{ $isFavorited ? 'text-red-500 fill-current' : 'text-gray-500 group-hover:text-red-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                Favoritar
            </button>
            <button class="flex items-center gap-2 text-[14px] font-bold text-gray-700 hover:text-primary transition group focus:outline-none">
                <svg class="w-5 h-5 text-gray-500 group-hover:text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                Compartilhar
            </button>
        </div>
    </div>

    @php 
        $media = json_decode($vehicle->media, true); 
        $images = (is_array($media) && count($media) > 0) ? $media : ['https://placehold.co/800x600?text=Sem+Foto'];
        $margin = $vehicle->fipe_price > 0 ? round((($vehicle->fipe_price - $vehicle->sale_price) / $vehicle->fipe_price) * 100) : 0;
        
        $features = is_string($vehicle->features) ? json_decode($vehicle->features, true) : $vehicle->features;
        if(!is_array($features)) $features = [];
    @endphp

    <div class="flex flex-col lg:flex-row gap-10">
        
        <!-- Left Area (Image & Specs) -->
        <div class="w-full lg:w-[65%]">
            
            <!-- Main Image -->
            <div class="bg-[#f3f4f6] rounded-xl overflow-hidden aspect-[4/3] w-full relative group">
                <img src="{{ $images[0] }}" class="w-full h-full object-cover">
                <!-- Foto 360 badge -->
                <div class="absolute bottom-4 left-4 bg-gray-900/80 text-white text-[11px] font-bold px-3 py-1.5 rounded-full flex items-center gap-1.5 backdrop-blur-sm cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                    Foto Externas
                </div>
            </div>

            <!-- Sobre esse carro -->
            <div class="mt-12">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Sobre esse carro</h2>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="border border-gray-200 rounded-lg p-4 flex flex-col items-center justify-center gap-2 text-gray-600 hover:border-gray-400 transition cursor-default">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="text-[12px] font-medium">{{ number_format($vehicle->mileage, 0, ',', '.') }} km</span>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 flex flex-col items-center justify-center gap-2 text-gray-600 hover:border-gray-400 transition cursor-default">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="text-[12px] font-medium">{{ $vehicle->manufacture_year }}/{{ $vehicle->model_year }}</span>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 flex flex-col items-center justify-center gap-2 text-gray-600 hover:border-gray-400 transition cursor-default">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                        <span class="text-[12px] font-medium">{{ explode(' ', $vehicle->transmission)[0] }}</span>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 flex flex-col items-center justify-center gap-2 text-gray-600 hover:border-gray-400 transition cursor-default">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16h14zm-4-4h2"></path></svg>
                        <span class="text-[12px] font-medium">{{ explode(' ', $vehicle->fuel_type)[0] }}</span>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 flex flex-col items-center justify-center gap-2 text-gray-600 hover:border-gray-400 transition cursor-default">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        <span class="text-[12px] font-medium">Motor -</span>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 flex flex-col items-center justify-center gap-2 text-gray-600 hover:border-gray-400 transition cursor-default">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="text-[12px] font-medium">Placa Final {{ substr($vehicle->plate, -1) }}</span>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 flex flex-col items-center justify-center gap-2 text-gray-600 hover:border-gray-400 transition cursor-default">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                        <span class="text-[12px] font-medium">{{ $vehicle->color }}</span>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 flex flex-col items-center justify-center gap-2 text-gray-600 hover:border-gray-400 transition cursor-default">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        <span class="text-[12px] font-medium">{{ $vehicle->category }}</span>
                    </div>
                </div>
            </div>

            <!-- Acessórios -->
            <div class="mt-12">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Acessórios e outros</h2>
                
                @if(count($features) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8">
                        @foreach($features as $feature)
                            <div class="flex items-center text-[14px] text-gray-600">
                                <svg class="w-4 h-4 mr-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                {{ $feature }}
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 italic">Lista de acessórios não informada.</p>
                @endif
            </div>

            <!-- Localização -->
            <div class="mt-12 pt-10 border-t border-gray-100 mb-10">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Localização do carro</h2>
                    <a href="#" class="text-[13px] font-bold text-orange-600 hover:text-orange-700 flex items-center gap-1 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        ver mais carros dessa loja
                    </a>
                </div>
                
                <div class="bg-white border border-gray-200 rounded-lg p-5">
                    <h4 class="text-[15px] font-bold text-gray-900 mb-4">Pátio Externo Elite</h4>
                    <div class="flex items-start text-[14px] text-gray-600 mb-3">
                        <svg class="w-5 h-5 mr-3 text-orange-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        (00) 0000-0000
                    </div>
                    <div class="flex items-start text-[14px] text-gray-600">
                        <svg class="w-5 h-5 mr-3 text-orange-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Matriz Elite Veículos, Brasil
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Area (Sticky Sidebar Specs & CTA) -->
        <div class="w-full lg:w-[35%] relative">
            <div class="sticky top-28 bg-white border border-transparent lg:border-white">
                
                <!-- Category/Brand Breadcrumb -->
                <p class="text-[12px] font-black text-orange-500 uppercase tracking-widest mb-2">{{ $vehicle->brand }}</p>
                
                <h1 class="text-3xl font-black text-gray-900 leading-tight mb-2 tracking-tight uppercase">
                    {{ $vehicle->model }}
                    <span class="font-normal block text-[24px] text-gray-700 mt-1">{{ $vehicle->version }}</span>
                </h1>
                
                <p class="text-[14px] text-gray-500 font-medium mb-6">
                    {{ $vehicle->manufacture_year }}/{{ $vehicle->model_year }} &bull; {{ number_format($vehicle->mileage, 0, ',', '.') }} km
                </p>

                <!-- Location minor block -->
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg mb-8 border border-gray-100">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <div class="text-[12px]">
                        <p class="font-bold text-gray-700">Pátio Externo Elite</p>
                        <p class="text-gray-500">Matriz</p>
                    </div>
                </div>

                <!-- Price and CTA -->
                <div class="mb-8 flex items-end justify-between border-b border-gray-100 pb-6">
                    <div class="text-[34px] font-black text-gray-900 leading-none tracking-tight">
                        R$ {{ number_format($vehicle->sale_price, 2, ',', '.') }}
                    </div>
                    @if($margin > 0)
                        <div class="flex flex-col items-end">
                            <span class="text-[11px] font-bold text-gray-800 bg-gray-100 rounded-full px-2 py-0.5 whitespace-nowrap">
                                <span class="text-green-600">↓</span> {{ $margin }}% abaixo FIPE
                            </span>
                        </div>
                    @endif
                </div>

                <button class="w-full bg-[#f97316] hover:bg-[#ea580c] text-white font-black py-3.5 px-6 rounded-lg transition-transform hover:scale-[1.01] shadow-md text-[17px] mb-8">
                    Tenho Interesse
                </button>

                <!-- Comparative FIPE Box -->
                <div class="border border-green-300 rounded-lg overflow-hidden">
                    <div class="bg-green-50 text-center py-2 border-b border-green-300">
                        <span class="text-[13px] font-bold text-gray-700">Compare os preços</span>
                    </div>
                    <div class="p-4 grid grid-cols-2 gap-y-4">
                        <div>
                            <p class="text-[12px] font-bold text-green-700">Preço FIPE</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[13px] font-black text-green-800">R$ {{ number_format($vehicle->fipe_price, 2, ',', '.') }}</p>
                        </div>
                        
                        <div class="col-span-2 h-px bg-gray-100"></div>

                        <div>
                            <p class="text-[11px] font-bold text-gray-500">Distância FIPE</p>
                            <p class="text-[14px] font-black text-gray-900 mt-0.5">R$ {{ number_format($vehicle->fipe_price - $vehicle->sale_price, 2, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[11px] font-bold text-gray-500">Margem</p>
                            <p class="text-[14px] font-black text-green-700 mt-0.5">↓ {{ number_format($margin, 2, ',', '.') }} %</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
