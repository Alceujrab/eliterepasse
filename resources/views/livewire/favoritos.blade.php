<div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Meus Favoritos</h1>
            <p class="text-gray-500 mt-2 font-medium">Acompanhe rápido os veículos que você gostou. Não deixe a oportunidade passar!</p>
        </div>
        <a href="{{ route('dashboard') }}" class="text-[13px] font-bold text-orange-600 hover:text-orange-700 bg-orange-50 px-4 py-2 rounded-lg flex items-center transition" wire:navigate>
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Voltar para Vitrine
        </a>
    </div>

    @if($favorites->isEmpty())
        <div class="bg-white rounded border border-gray-200 p-12 text-center h-[300px] flex flex-col justify-center items-center mt-6">
            <svg class="mx-auto h-14 w-14 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
            <h3 class="mt-4 text-[16px] font-bold text-gray-800">Sua lista está vazia</h3>
            <p class="mt-1 text-sm text-gray-500">Volte à vitrine e clique no coração dos veículos de seu interesse.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($favorites as $fav)
                @if($fav->vehicle)
                    @php 
                        $vehicle = $fav->vehicle;
                        $media = json_decode($vehicle->media, true); 
                        $image = (is_array($media) && count($media) > 0) ? $media[0] : 'https://placehold.co/600x400?text=Sem+Foto';
                    @endphp
                    <div class="bg-white rounded border border-gray-200 overflow-hidden flex flex-col group relative">
                        <a href="{{ route('vehicle.details', $vehicle->id) }}" class="block absolute inset-0 z-0" wire:navigate></a>
                        
                        <!-- Header Image -->
                        <div class="relative h-[200px] bg-gray-100 overflow-hidden">
                            <img src="{{ $image }}" class="w-full h-full object-cover">
                            <button wire:click="removeFavorite({{ $fav->id }})" title="Remover dos favoritos" class="absolute top-2 right-2 p-2 bg-white/80 hover:bg-white rounded-full text-red-500 shadow-sm transition z-10">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path></svg>
                            </button>
                        </div>

                        <!-- Info -->
                        <div class="p-4 flex flex-col z-10 pointer-events-none">
                            <h3 class="text-[15px] font-black text-gray-900 uppercase leading-tight mb-1 truncate">{{ $vehicle->brand }} {{ $vehicle->model }}</h3>
                            <p class="text-[12px] text-gray-500 mb-3">{{ $vehicle->manufacture_year }}/{{ $vehicle->model_year }} &bull; {{ number_format($vehicle->mileage, 0, ',', '.') }} km</p>
                            
                            <div class="mt-auto flex items-end justify-between">
                                <div class="text-[18px] font-black text-primary">R$ {{ number_format($vehicle->sale_price, 2, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
