{{-- ═══════════════════════════════════════════════════════════
     PÁGINA DE CONTATO — Elite Repasse (v2)
     Layout moderno: info-bar → form+sidebar → mapa inline
═══════════════════════════════════════════════════════════ --}}
<div x-data="{ menuOpen: false }" class="relative min-h-screen overflow-x-hidden bg-slate-50 text-slate-900">

    {{-- ═══════════ HEADER ═══════════ --}}
    <header class="fixed inset-x-0 top-0 z-50 border-b border-white/20 bg-[#0f2f4d]/95 backdrop-blur-xl">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between gap-4 lg:h-[4.5rem]">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ $logoUrl }}" class="h-9" alt="Elite Repasse" onerror="this.src='https://placehold.co/220x60/1f5a7c/ffffff?text=Elite+Repasse'">
                    <span class="hidden text-xs font-bold uppercase tracking-[0.2em] text-blue-200 md:inline">Portal do Lojista</span>
                </a>
                <nav class="hidden items-center gap-6 lg:flex">
                    @foreach($menuItems as $item)
                        <a href="{{ url('/') }}{{ $item['url'] }}" class="text-[13px] font-bold uppercase tracking-wide text-blue-200/80 transition hover:text-white">{{ $item['label'] }}</a>
                    @endforeach
                    <a href="{{ route('contato') }}" class="text-[13px] font-bold uppercase tracking-wide text-white">Contato</a>
                </nav>
                <div class="hidden items-center gap-2.5 sm:flex">
                    <a href="{{ route('login') }}" class="rounded-full border border-white/30 px-5 py-2 text-[13px] font-bold text-white transition hover:border-white hover:bg-white/10">Entrar</a>
                    <a href="{{ route('register') }}" class="rounded-full bg-orange-500 px-5 py-2 text-[13px] font-black text-white shadow-lg shadow-orange-700/30 transition hover:bg-orange-600">Cadastre-se</a>
                </div>
                <button @click="menuOpen = !menuOpen" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-white/30 text-white lg:hidden">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
        <div x-show="menuOpen" x-transition class="border-t border-white/20 bg-[#10395f] lg:hidden">
            <div class="mx-auto max-w-7xl px-4 py-3 space-y-1">
                @foreach($menuItems as $item)
                    <a href="{{ url('/') }}{{ $item['url'] }}" class="block rounded-lg px-4 py-2.5 text-sm font-bold text-blue-100 hover:bg-white/10">{{ $item['label'] }}</a>
                @endforeach
                <a href="{{ route('contato') }}" class="block rounded-lg px-4 py-2.5 text-sm font-bold text-orange-300 bg-white/5">Contato</a>
                <div class="flex gap-2 pt-2">
                    <a href="{{ route('login') }}" class="flex-1 rounded-lg border border-white/30 py-2.5 text-center text-sm font-bold text-white">Entrar</a>
                    <a href="{{ route('register') }}" class="flex-1 rounded-lg bg-orange-500 py-2.5 text-center text-sm font-black text-white">Cadastre-se</a>
                </div>
            </div>
        </div>
    </header>

    {{-- ═══════════ HERO COMPACTO ═══════════ --}}
    <section class="relative bg-gradient-to-br from-[#0b2240] via-[#0f2f4d] to-[#1a4c6e] pt-28 pb-28 sm:pt-32 sm:pb-36">
        <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.07) 1px, transparent 0); background-size: 32px 32px;"></div>
        <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-slate-50 to-transparent"></div>
        <div class="relative mx-auto max-w-3xl px-4 text-center">
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-orange-400">Fale conosco</p>
            <h1 class="mt-3 text-3xl font-black tracking-tight text-white sm:text-4xl lg:text-5xl">Como podemos ajudar?</h1>
            <p class="mx-auto mt-3 max-w-xl text-base text-blue-200/70">Entre em contato com nossa equipe. Estamos prontos para atender sua loja.</p>
        </div>
    </section>

    {{-- ═══════════ INFO-BAR HORIZONTAL (flutuante) ═══════════ --}}
    <section class="relative z-10 -mt-16 mb-10 sm:-mt-20 sm:mb-12">
        <div class="mx-auto max-w-5xl px-4 sm:px-6">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Endereço --}}
                @if(filled($settings->contact_address))
                <div class="flex items-center gap-3.5 rounded-2xl border border-white bg-white px-5 py-4 shadow-lg shadow-slate-900/5">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-[#0f2f4d] text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Endereço</p>
                        <p class="truncate text-sm font-semibold text-slate-800">{{ $settings->contact_city ?? '' }}{{ $settings->contact_state ? ' - ' . $settings->contact_state : '' }}</p>
                    </div>
                </div>
                @endif

                {{-- Telefone --}}
                @if(filled($settings->contact_phone))
                <a href="tel:{{ preg_replace('/\D/', '', $settings->contact_phone) }}" class="flex items-center gap-3.5 rounded-2xl border border-white bg-white px-5 py-4 shadow-lg shadow-slate-900/5 transition hover:shadow-xl">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-[#0f2f4d] text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Telefone</p>
                        <p class="text-sm font-semibold text-slate-800">{{ $settings->contact_phone }}</p>
                    </div>
                </a>
                @endif

                {{-- E-mail --}}
                @if(filled($settings->contact_email))
                <a href="mailto:{{ $settings->contact_email }}" class="flex items-center gap-3.5 rounded-2xl border border-white bg-white px-5 py-4 shadow-lg shadow-slate-900/5 transition hover:shadow-xl">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-[#0f2f4d] text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">E-mail</p>
                        <p class="truncate text-sm font-semibold text-slate-800">{{ $settings->contact_email }}</p>
                    </div>
                </a>
                @endif

                {{-- WhatsApp --}}
                @if(filled($settings->whatsapp_number))
                <a href="https://wa.me/{{ $settings->whatsapp_number }}?text={{ urlencode('Olá! Vim pelo site da Elite Repasse e gostaria de mais informações.') }}" target="_blank" rel="noopener"
                   class="flex items-center gap-3.5 rounded-2xl border border-[#25D366]/20 bg-[#25D366] px-5 py-4 shadow-lg shadow-[#25D366]/15 transition hover:shadow-xl hover:bg-[#22c55e]">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-white/20 text-white">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-white/70">WhatsApp</p>
                        <p class="text-sm font-bold text-white">Fale agora</p>
                    </div>
                    <svg class="ml-auto h-4 w-4 flex-shrink-0 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
                @endif
            </div>
        </div>
    </section>

    {{-- ═══════════ CORPO: FORMULÁRIO + SIDEBAR ═══════════ --}}
    <section class="pb-16 sm:pb-20">
        <div class="mx-auto max-w-5xl px-4 sm:px-6">
            <div class="grid gap-6 lg:grid-cols-5">

                {{-- ════ FORMULÁRIO (principal) ════ --}}
                <div class="lg:col-span-3">
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8">
                        <h2 class="text-xl font-black text-slate-900 sm:text-2xl">Envie sua mensagem</h2>
                        <p class="mt-1.5 text-sm text-slate-500">Preencha o formulário e retornamos em até 24h úteis.</p>

                        @if($enviado)
                            <div class="mt-6 rounded-xl bg-emerald-50 p-6 text-center ring-1 ring-emerald-200">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100">
                                    <svg class="h-7 w-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <h3 class="mt-3 text-lg font-black text-emerald-800">Mensagem enviada!</h3>
                                <p class="mt-1.5 text-sm text-emerald-600">Recebemos sua mensagem e entraremos em contato em breve.</p>
                                <button wire:click="$set('enviado', false)" class="mt-4 inline-flex items-center gap-2 rounded-xl border border-emerald-300 bg-white px-5 py-2.5 text-sm font-bold text-emerald-700 transition hover:bg-emerald-50">
                                    Enviar outra mensagem
                                </button>
                            </div>
                        @else
                            <form wire:submit="enviar" class="mt-6 space-y-4">
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label for="nome" class="mb-1 block text-sm font-semibold text-slate-700">Nome completo <span class="text-rose-500">*</span></label>
                                        <input wire:model="nome" type="text" id="nome" placeholder="Seu nome"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#1f5a7c] focus:ring-2 focus:ring-[#1f5a7c]/15 @error('nome') border-rose-300 ring-2 ring-rose-100 @enderror">
                                        @error('nome') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="email" class="mb-1 block text-sm font-semibold text-slate-700">E-mail <span class="text-rose-500">*</span></label>
                                        <input wire:model="email" type="email" id="email" placeholder="seu@email.com"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#1f5a7c] focus:ring-2 focus:ring-[#1f5a7c]/15 @error('email') border-rose-300 ring-2 ring-rose-100 @enderror">
                                        @error('email') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label for="telefone" class="mb-1 block text-sm font-semibold text-slate-700">Telefone / WhatsApp</label>
                                        <input wire:model="telefone" type="text" id="telefone" placeholder="(00) 00000-0000"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#1f5a7c] focus:ring-2 focus:ring-[#1f5a7c]/15">
                                    </div>
                                    <div>
                                        <label for="assunto" class="mb-1 block text-sm font-semibold text-slate-700">Assunto <span class="text-rose-500">*</span></label>
                                        <select wire:model="assunto" id="assunto"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#1f5a7c] focus:ring-2 focus:ring-[#1f5a7c]/15 @error('assunto') border-rose-300 ring-2 ring-rose-100 @enderror">
                                            <option value="">Selecione o assunto...</option>
                                            <option value="Quero ser lojista parceiro">Quero ser lojista parceiro</option>
                                            <option value="Dúvidas sobre compra">Dúvidas sobre compra</option>
                                            <option value="Suporte técnico">Suporte técnico</option>
                                            <option value="Financeiro / Pagamentos">Financeiro / Pagamentos</option>
                                            <option value="Documentação de veículo">Documentação de veículo</option>
                                            <option value="Parceria comercial">Parceria comercial</option>
                                            <option value="Outro assunto">Outro assunto</option>
                                        </select>
                                        @error('assunto') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="mensagem" class="mb-1 block text-sm font-semibold text-slate-700">Mensagem <span class="text-rose-500">*</span></label>
                                    <textarea wire:model="mensagem" id="mensagem" rows="4" placeholder="Descreva sua necessidade, dúvida ou como podemos ajudar..."
                                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#1f5a7c] focus:ring-2 focus:ring-[#1f5a7c]/15 resize-none @error('mensagem') border-rose-300 ring-2 ring-rose-100 @enderror"></textarea>
                                    @error('mensagem') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                                </div>

                                <div class="flex items-center justify-between pt-1">
                                    <button type="submit"
                                        wire:loading.attr="disabled"
                                        class="inline-flex items-center gap-2 rounded-xl bg-[#0f2f4d] px-7 py-3.5 text-sm font-bold text-white shadow-lg shadow-slate-900/10 transition hover:bg-[#1a4c6e] disabled:opacity-60">
                                        <span wire:loading.remove wire:target="enviar">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                        </span>
                                        <span wire:loading wire:target="enviar">
                                            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                        </span>
                                        <span wire:loading.remove wire:target="enviar">Enviar mensagem</span>
                                        <span wire:loading wire:target="enviar">Enviando...</span>
                                    </button>
                                    <p class="hidden text-xs text-slate-400 sm:block">* obrigatórios</p>
                                </div>
                            </form>
                        @endif
                    </div>

                    {{-- MAPA INLINE (dentro da coluna do form) --}}
                    @if($hasMap)
                    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200/80 shadow-sm">
                        <iframe
                            src="https://www.google.com/maps/embed/v1/place?key={{ $mapsApiKey }}&q={{ $settings->contact_lat }},{{ $settings->contact_lng }}&zoom=15"
                            width="100%" height="280" style="border:0;" allowfullscreen loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade" class="w-full">
                        </iframe>
                    </div>
                    @endif
                </div>

                {{-- ════ SIDEBAR DIREITA ════ --}}
                <div class="lg:col-span-2 space-y-5">

                    {{-- Endereço completo --}}
                    @if(filled($settings->contact_address))
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                                <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Endereço</p>
                                <p class="mt-1 text-sm leading-relaxed text-slate-700">{{ $settings->contact_address }}</p>
                                @if($settings->contact_city || $settings->contact_state)
                                    <p class="text-sm text-slate-700">{{ $settings->contact_city }}{{ $settings->contact_state ? ' - ' . $settings->contact_state : '' }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Horário de Atendimento --}}
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Horário de Atendimento</p>
                        <div class="mt-3 space-y-1.5">
                            <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3.5 py-2">
                                <span class="text-sm text-slate-600">Segunda a Sexta</span>
                                <span class="text-sm font-bold text-[#0f2f4d]">8h – 18h</span>
                            </div>
                            <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3.5 py-2">
                                <span class="text-sm text-slate-600">Sábado</span>
                                <span class="text-sm font-bold text-[#0f2f4d]">8h – 12h</span>
                            </div>
                            <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3.5 py-2">
                                <span class="text-sm text-slate-600">Domingo</span>
                                <span class="text-sm font-medium text-slate-400">Fechado</span>
                            </div>
                        </div>
                    </div>

                    {{-- CTA WhatsApp destaque --}}
                    @if(filled($settings->whatsapp_number))
                    <a href="https://wa.me/{{ $settings->whatsapp_number }}?text={{ urlencode('Olá! Vim pelo site da Elite Repasse e gostaria de mais informações.') }}" target="_blank" rel="noopener"
                       class="group block rounded-2xl bg-gradient-to-br from-[#25D366] to-[#128C7E] p-5 shadow-lg shadow-[#25D366]/15 transition hover:shadow-xl">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-white/20 text-white">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-black text-white">Fale pelo WhatsApp</p>
                                <p class="text-xs text-white/70">Atendimento rápido e personalizado</p>
                            </div>
                            <svg class="ml-auto h-5 w-5 text-white/50 transition group-hover:text-white group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </a>
                    @endif

                    {{-- Redes sociais --}}
                    @if(filled($settings->social_instagram) || filled($settings->social_facebook) || filled($settings->social_youtube))
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Redes Sociais</p>
                        <div class="mt-3 flex gap-2.5">
                            @if(filled($settings->social_instagram))
                                <a href="{{ $settings->social_instagram }}" target="_blank" rel="noopener" class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500 via-pink-500 to-orange-400 text-white transition hover:scale-105" aria-label="Instagram">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                </a>
                            @endif
                            @if(filled($settings->social_facebook))
                                <a href="{{ $settings->social_facebook }}" target="_blank" rel="noopener" class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#1877F2] text-white transition hover:scale-105" aria-label="Facebook">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                </a>
                            @endif
                            @if(filled($settings->social_youtube))
                                <a href="{{ $settings->social_youtube }}" target="_blank" rel="noopener" class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-600 text-white transition hover:scale-105" aria-label="YouTube">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Mini CTA --}}
                    <div class="rounded-2xl bg-gradient-to-br from-[#0f2f4d] to-[#1a4c6e] p-5 text-center">
                        <p class="text-sm font-bold text-blue-100">Quer começar agora?</p>
                        <p class="mt-1 text-xs text-blue-200/60">Cadastre-se gratuitamente e acesse o portal.</p>
                        <a href="{{ route('register') }}" class="mt-3 inline-block rounded-lg bg-orange-500 px-6 py-2.5 text-sm font-bold text-white shadow transition hover:bg-orange-600">Cadastre-se</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════ FOOTER COMPACTO ═══════════ --}}
    <footer class="border-t border-slate-200 bg-[#0b1b2a] py-10 text-slate-400">
        <div class="mx-auto max-w-5xl px-4 sm:px-6">
            <div class="flex flex-col items-center gap-6 sm:flex-row sm:justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ $logoUrl }}" class="h-8 opacity-70" alt="Elite Repasse" onerror="this.src='https://placehold.co/180x50/0b1b2a/cbd5e1?text=Elite+Repasse'">
                </div>
                <div class="flex flex-wrap justify-center gap-x-5 gap-y-1 text-sm">
                    @foreach($footerLinks as $link)
                        <a href="{{ $link['url'] }}" class="transition hover:text-white">{{ $link['label'] }}</a>
                    @endforeach
                    <a href="{{ route('contato') }}" class="transition hover:text-white">Contato</a>
                </div>
            </div>
            <div class="mt-6 border-t border-slate-700/50 pt-5 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} Elite Repasse. Todos os direitos reservados.
            </div>
        </div>
    </footer>

    {{-- WhatsApp Flutuante --}}
    @if(filled($settings->whatsapp_number))
    <a href="https://wa.me/{{ $settings->whatsapp_number }}?text={{ urlencode('Olá! Vim pelo site da Elite Repasse e gostaria de mais informações.') }}" target="_blank"
       class="fixed bottom-6 right-6 z-50 inline-flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-2xl transition hover:scale-110" aria-label="WhatsApp">
        <svg class="h-7 w-7" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884"/>
        </svg>
    </a>
    @endif
</div>
