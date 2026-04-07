<div class="relative w-full">
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md shadow-sm border-b border-gray-100 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <img src="{{ asset('build/assets/logo.png') }}" class="h-10" alt="Elite Repasse" onerror="this.src='https://placehold.co/200x50/1f5a7c/white?text=Elite+Repasse'">
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#beneficios" class="text-sm font-bold text-gray-500 hover:text-primary transition uppercase tracking-wide">Benefícios</a>
                    <a href="#faq" class="text-sm font-bold text-gray-500 hover:text-primary transition uppercase tracking-wide">Perguntas Frequentes</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-sm text-primary border border-primary hover:bg-primary hover:text-white px-5 py-2 rounded-full font-bold transition">Entrar</a>
                    <a href="{{ route('register') }}" class="text-sm bg-orange_cta hover:bg-[#e06512] text-white px-5 py-2 rounded-full font-bold shadow-md transition transform hover:-translate-y-0.5">Cadastrar</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 lg:pt-48 lg:pb-32 bg-primary relative overflow-hidden" style="background-image: linear-gradient(rgba(31, 90, 124, 0.92), rgba(31, 90, 124, 0.98)), url('https://images.unsplash.com/photo-1542282088-fe8426682b8f?q=80&w=2000&auto=format&fit=crop'); background-size: cover; background-position: center; background-attachment: fixed;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <span class="inline-block py-1 px-3 rounded-full bg-blue-900/50 border border-blue-400/30 text-blue-200 text-sm font-bold tracking-widest uppercase mb-6 backdrop-blur">
                O Maior Portal B2B do Brasil
            </span>
            <h1 class="text-4xl md:text-5xl lg:text-7xl font-black text-white tracking-tight mb-6 leading-tight">
                {{ $settings->hero_title ?? 'Acelere sua venda de carros com a Elite Repasse' }}
            </h1>
            <p class="mt-4 text-xl text-blue-100 max-w-3xl mx-auto mb-10 font-medium">
                {{ $settings->hero_subtitle ?? 'Compre seminovos com as melhores condições do mercado para o seu negócio de forma 100% online.' }}
            </p>
            <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-6">
                <a href="{{ route('register') }}" class="bg-orange_cta hover:bg-orange-600 text-white px-8 py-4 rounded-full font-black text-lg shadow-xl shadow-orange-900/20 transition transform hover:-translate-y-1 hover:scale-105 w-full sm:w-auto">
                    QUERO COMPRAR AGORA
                </a>
            </div>
        </div>
    </section>

    <!-- Benefícios Section -->
    <section id="beneficios" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20">
                <h2 class="text-4xl font-black text-gray-900 tracking-tight">Por que escolher o Portal da Elite?</h2>
                <div class="h-1.5 w-24 bg-orange_cta mx-auto mt-6 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                @if($settings->features && is_array($settings->features))
                    @foreach($settings->features as $feature)
                        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 text-center group">
                            <div class="w-20 h-20 bg-blue-50 text-primary rounded-full flex items-center justify-center mx-auto mb-8 group-hover:bg-primary group-hover:text-white transition-colors">
                               <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="text-2xl font-black text-gray-900 mb-4">{{ $feature['title'] ?? '' }}</h3>
                            <p class="text-gray-600 text-lg leading-relaxed">{{ $feature['description'] ?? '' }}</p>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-24 bg-white relative">
        <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-black text-gray-900 tracking-tight">Perguntas Frequentes</h2>
                <p class="mt-4 text-xl text-gray-600">Tudo o que você precisa saber sobre o nosso portal B2B.</p>
            </div>

            <div class="space-y-4" x-data="{ active: 0 }">
                @if($settings->faq && is_array($settings->faq))
                    @foreach($settings->faq as $index => $faq)
                        <div class="border border-gray-200 rounded-2xl overflow-hidden transition-all duration-300 shadow-sm hover:shadow-md bg-white">
                            <button @click="active = active === {{ $index }} ? null : {{ $index }}" class="w-full flex justify-between items-center p-6 text-left focus:outline-none">
                                <span class="font-bold text-xl text-gray-800">{{ $faq['question'] ?? '' }}</span>
                                <div class="flex-shrink-0 ml-4">
                                    <div class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center text-gray-500 transition-colors" :class="{'bg-primary text-white border-primary': active === {{ $index }}}">
                                        <svg class="w-5 h-5 transform transition-transform" :class="{'rotate-180': active === {{ $index }}}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </button>
                            <div x-show="active === {{ $index }}" x-collapse class="px-6 pb-6 pt-0">
                                <p class="text-gray-600 text-lg leading-relaxed">{{ $faq['answer'] ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    <!-- CTA Final -->
    <section class="py-24 bg-gray-900 text-center relative overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: url('https://www.transparenttextures.com/patterns/carbon-fibre.png');"></div>
        <div class="max-w-4xl mx-auto px-4 relative z-10">
            <h2 class="text-4xl md:text-5xl font-black text-white mb-6">Pronto para acelerar os negócios?</h2>
            <p class="text-gray-400 mb-10 text-xl max-w-2xl mx-auto">Crie sua conta agora e tenha acesso imediato a milhares de veículos revisados com preço de repasse.</p>
            <a href="{{ route('register') }}" class="inline-block bg-orange_cta hover:bg-orange-500 text-white px-12 py-5 rounded-full font-black text-xl shadow-2xl transition transform hover:scale-105">
                CADASTRAR MINHA LOJA
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-black text-gray-400 py-16 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center pb-8 border-b border-gray-800">
                <div class="mb-8 md:mb-0">
                    <img src="{{ asset('build/assets/logo.png') }}" class="h-10 opacity-70 grayscale hover:grayscale-0 transition duration-500" alt="Elite Repasse" onerror="this.src='https://placehold.co/200x50/333/777?text=Elite+Repasse'">
                </div>
                <div class="flex space-x-8 text-sm font-bold uppercase tracking-wider">
                    <a href="#" class="hover:text-white transition">Termos de Uso</a>
                    <a href="#" class="hover:text-white transition">Privacidade</a>
                    <a href="#" class="hover:text-white transition">Contato</a>
                </div>
            </div>
            <div class="pt-8 text-center text-sm text-gray-600">
                <p>© {{ date('Y') }} Elite Veículos Repasse B2B. CNPJ: 00.000.000/0001-00. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/{{ $settings->whatsapp_number ?? '' }}" target="_blank" class="fixed bottom-6 right-6 bg-[#25D366] hover:bg-[#1ebe57] text-white p-4 rounded-full shadow-2xl transition transform hover:-translate-y-1 hover:scale-110 z-50 flex items-center justify-center group" aria-label="WhatsApp">
        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.711.92 2.8.92 3.18 0 5.767-2.587 5.767-5.766 0-3.18-2.586-5.766-5.767-5.766zm3.361 7.842c-.174.457-1.026.85-1.425.908-.328.048-.752.174-1.928-.277-1.411-.544-2.316-1.986-2.385-2.079-.068-.095-.572-.76-.572-1.45 0-.689.362-1.03.491-1.168.129-.138.291-.157.387-.157.096 0 .193.003.277.007.094.004.226-.035.353.272.13.314.444 1.082.483 1.16.039.079.065.171.015.269-.049.098-.075.156-.139.231-.065.076-.137.165-.195.231-.065.075-.125.138-.049.269.076.13 1.096 1.786 2.378 1.905.076.007.121-.004.161-.059.049-.064.208-.242.261-.325.044-.068.087-.058.156-.033.069.025.438.206.514.244.076.038.127.057.146.089.028.047.028.271-.146.728z"/></svg>
        <span class="absolute overflow-hidden w-0 opacity-0 group-hover:w-auto group-hover:opacity-100 right-full mr-4 bg-gray-900 text-white px-3 py-2 text-sm font-bold rounded shadow-lg whitespace-nowrap transition-all duration-300 flex items-center">
            Fale com Especialista
            <div class="absolute right-[-4px] top-1/2 transform -translate-y-1/2 w-2 h-2 bg-gray-900 rotate-45"></div>
        </span>
    </a>
</div>
