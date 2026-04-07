<div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Meus Documentos</h1>
        <p class="text-gray-500 mt-2 font-medium">Baixe os CRVs e Contratos dos veículos que você arrematou. Os arquivos ficam disponíveis por 60 dias.</p>
    </div>

    @if($documents->isEmpty())
        <div class="bg-white rounded border border-gray-200 p-12 text-center h-64 flex flex-col justify-center items-center">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <h3 class="mt-4 text-[15px] font-bold text-gray-700">Nenhum documento disponível</h3>
            <p class="mt-1 text-sm text-gray-500">Seus documentos aparecerão aqui após a conclusão da documentação técnica.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($documents as $doc)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-md transition">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-orange-50 text-orange-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ $doc->created_at->format('d/m/Y') }}</span>
                    </div>
                    <h3 class="text-[16px] font-bold text-gray-900 mb-1 line-clamp-2" title="{{ $doc->title }}">{{ $doc->title }}</h3>
                    @if($doc->vehicle)
                        <p class="text-[13px] font-semibold text-gray-500 mb-4">{{ $doc->vehicle->brand }} {{ $doc->vehicle->model }} <span class="text-gray-800">| PLACA **{{ substr($doc->vehicle->plate, -4) }}</span></p>
                    @endif
                    <div class="mt-auto">
                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="flex items-center justify-center w-full py-2.5 px-4 bg-gray-50 hover:bg-orange_cta hover:text-white hover:border-orange_cta border border-gray-200 text-gray-700 font-bold text-[14px] rounded-lg transition-colors group">
                            Baixar PDF
                            <svg class="w-4 h-4 ml-2 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
