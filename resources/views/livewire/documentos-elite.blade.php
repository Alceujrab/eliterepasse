<div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Documentos Elite</h1>
        <p class="text-gray-500 mt-2 font-medium">Lista de regulamentos, manuais e contratos gerais da Elite Veículos Repasse.</p>
    </div>

    @if($documents->isEmpty())
        <div class="bg-white rounded border border-gray-200 p-12 text-center h-64 flex flex-col justify-center items-center">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
            <h3 class="mt-4 text-[15px] font-bold text-gray-700">Nenhum documento disponível</h3>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden divide-y divide-gray-100">
            @foreach($documents as $doc)
                <div x-data="{ expanded: false }" class="w-full">
                    <button @click="expanded = !expanded" class="w-full flex items-center justify-between p-6 hover:bg-gray-50 transition focus:outline-none group">
                        <div class="flex items-center gap-4 text-left">
                            <div class="text-gray-400 group-hover:text-primary transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-[17px] font-bold text-gray-900 border-b border-transparent transition-colors leading-tight">{{ $doc->title }}</h3>
                                @if($doc->description)
                                    <p class="text-[13px] text-gray-500 mt-1 font-medium">{{ $doc->description }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank" @click.stop class="inline-flex items-center justify-center py-2 px-6 bg-white border border-gray-300 shadow-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 font-bold text-[13px] rounded-full transition">
                                Visualizar
                            </a>
                            <svg class="w-5 h-5 text-gray-400 transform transition-transform" :class="{'rotate-180': expanded}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </button>
                    <!-- Expanded Content Details -->
                    <div x-show="expanded" style="display: none;" class="px-6 pb-6 pt-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <p class="text-[13px] text-gray-600 font-semibold">Documento publicado em: <span class="font-black">{{ $doc->created_at->format('d/m/Y') }}</span></p>
                        </div>
                        <a href="{{ Storage::url($doc->file_path) }}" download class="text-[14px] font-black text-primary hover:text-blue-800 transition hover:underline flex items-center gap-2 bg-white px-4 py-2 rounded border border-gray-200">
                            Download Direto <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
