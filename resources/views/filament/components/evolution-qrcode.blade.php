@if($qrCode)
    <div class="flex flex-col items-center justify-center p-6 space-y-4">
        <p class="text-sm font-medium text-gray-500 text-center">Abra o WhatsApp no seu celular e aponte a câmera para parear este número com o servidor.</p>
        <div class="p-2 border-2 border-gray-100 rounded-2xl bg-white shadow-sm">
            <img src="{{ $qrCode }}" alt="WhatsApp QR Code" class="w-64 h-64 object-contain">
        </div>
        <div class="mt-4 flex items-center gap-2 text-xs text-orange-600 font-bold bg-orange-50 px-4 py-2 rounded-full border border-orange-100">
            <svg class="w-4 h-4 animate-spin text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            Aguardando leitura...
        </div>
    </div>
@else
    <div class="flex flex-col items-center justify-center p-8 text-center space-y-3">
        <div class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <h3 class="text-lg font-black text-gray-900">Falha na Comunicação</h3>
        <p class="text-sm text-gray-500 max-w-sm">Não foi possível recuperar a imagem do QrCode. Verifique se o container da Evolution API está rodando e se a URL Base em <b>.env</b> está correta.</p>
    </div>
@endif
