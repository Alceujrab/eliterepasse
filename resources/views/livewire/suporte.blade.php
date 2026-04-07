<div x-data="{
         scrollToBottom() {
             let chatBox = document.getElementById('chat-messages');
             if (chatBox) { chatBox.scrollTop = chatBox.scrollHeight; }
         }
     }" 
     x-init="setTimeout(() => scrollToBottom(), 100)"
     @message-sent.window="setTimeout(() => scrollToBottom(), 100)">
     
    <div class="h-[calc(100vh-65px)] flex">
        
        <!-- Sidebar - Contatos/Tickets -->
        <div class="w-full md:w-1/3 lg:w-1/4 bg-white border-r border-gray-200 flex flex-col h-full">
            <div class="p-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-800">Meus Chamados</h2>
                <button class="mt-2 text-sm text-primary font-bold bg-white border border-gray-300 w-full py-2 rounded shadow-sm hover:bg-gray-50">+ Novo Chamado</button>
            </div>
            
            <div class="flex-1 overflow-y-auto">
                @if(count($tickets) === 0)
                    <div class="p-6 text-center text-gray-500 text-sm">Sem chamados abertos.</div>
                @else
                    <ul class="divide-y divide-gray-100">
                        @foreach($tickets as $ticket)
                            <li wire:click="selectTicket({{ $ticket->id }})" class="p-4 cursor-pointer hover:bg-orange-50 transition-colors {{ $activeTicketId == $ticket->id ? 'bg-orange-50 border-l-4 border-orange_cta' : '' }}">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="font-bold text-sm text-gray-900 truncate">Ticket #{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    <span class="text-xs text-gray-400">{{ $ticket->created_at->format('d/m') }}</span>
                                </div>
                                <div class="text-xs font-semibold text-gray-700 truncate mb-1">{{ $ticket->type }}</div>
                                <div class="text-xs text-gray-500 truncate">
                                    @if($ticket->messages->isNotEmpty())
                                        {{ $ticket->messages->first()->message }}
                                    @else
                                        ...
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <!-- Área de Chat -->
        <div class="hidden md:flex flex-1 flex-col h-full bg-[#f0f2f5] relative">
            @if(!$activeTicket)
                <!-- Placeholder vazio -->
                <div class="flex-1 flex flex-col items-center justify-center text-center p-10">
                    <div class="bg-white p-6 rounded-full shadow-sm mb-4">
                        <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Selecione um Chamado</h3>
                    <p class="text-gray-500 mt-2">Clique em um ticket na lateral para visualizar o histórico ou interagir com nosso suporte.</p>
                </div>
            @else
                <!-- Header do Chat -->
                <div class="bg-white px-6 py-3 flex items-center justify-between border-b border-gray-200 shadow-sm z-10">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-primary text-white rounded-full flex justify-center items-center font-bold mr-3 shadow-sm">
                            EL
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">Suporte Elite Repasse</h3>
                            <p class="text-xs text-gray-500">{{ $activeTicket->type }}</p>
                        </div>
                    </div>
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                            {{ strtoupper($activeTicket->status) }}
                        </span>
                    </div>
                </div>

                <!-- Mensagens -->
                <div id="chat-messages" class="flex-1 p-6 overflow-y-auto space-y-4" style="background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');">
                    @foreach($activeTicket->messages as $msg)
                        @if($msg->user->is_admin)
                            <!-- Recebida (Admin) -->
                            <div class="flex mb-4">
                                <div class="max-w-[75%] bg-white border border-gray-100 rounded-lg rounded-tl-none p-3 shadow-sm relative">
                                    <p class="text-sm text-gray-800">{{ $msg->message }}</p>
                                    <span class="text-[10px] text-gray-400 block mt-1 text-right">{{ $msg->created_at->format('H:i') }}</span>
                                </div>
                            </div>
                        @else
                            <!-- Enviada (Lojista/User) -->
                            <div class="flex mb-4 justify-end">
                                <div class="max-w-[75%] bg-[#dcf8c6] border border-[#c4eab0] rounded-lg rounded-tr-none p-3 shadow-sm relative">
                                    <p class="text-sm text-gray-800">{{ $msg->message }}</p>
                                    <span class="text-[10px] text-gray-500 block mt-1 text-right">{{ $msg->created_at->format('H:i') }} <span class="text-blue-500">✓✓</span></span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <!-- Input Message -->
                <div class="bg-gray-100 p-4 border-t border-gray-200">
                    <form wire:submit.prevent="sendMessage" class="flex items-center space-x-2 relative w-full">
                        <input wire:model="newMessage" type="text" placeholder="Digite uma mensagem..." class="flex-1 w-full rounded-full border-gray-300 focus:ring-primary focus:border-primary shadow-sm px-4 py-3 text-sm">
                        
                        <button type="submit" class="w-12 h-12 bg-primary hover:bg-blue-800 text-white rounded-full flex justify-center items-center shadow transition-colors flex-shrink-0" @click="$dispatch('message-sent')">
                            <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
