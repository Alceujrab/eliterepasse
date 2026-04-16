{{-- ═══════════════════════════════════════════════════════════
     PÁGINA DE CONTATO — Elite Repasse
     Profissional, responsiva, formulário com Livewire + mapa
═══════════════════════════════════════════════════════════ --}}
<div x-data="{ menuOpen: false }" class="relative min-h-screen overflow-x-hidden bg-white text-slate-900">
    <div class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(circle_at_85%_10%,rgba(249,115,22,0.14),transparent_35%),radial-gradient(circle_at_15%_20%,rgba(31,90,124,0.18),transparent_40%)]"></div>

    {{-- ═══════════ HEADER ═══════════ --}}
    <header class="fixed inset-x-0 top-0 z-50 border-b border-white/20 bg-[#0f2f4d]/90 backdrop-blur-xl">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-20 items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ $logoUrl }}" class="h-10" alt="Elite Repasse" onerror="this.src='https://placehold.co/220x60/1f5a7c/ffffff?text=Elite+Repasse'">
                    <span class="hidden text-sm font-bold uppercase tracking-[0.2em] text-blue-100 md:inline">Portal do Lojista</span>
                </a>
                <nav class="hidden items-center gap-7 lg:flex">
                    @foreach($menuItems as $item)
                        <a href="{{ url('/') }}{{ $item['url'] }}" class="text-sm font-bold uppercase tracking-wide text-blue-100 transition hover:text-white">{{ $item['label'] }}</a>
                    @endforeach
                    <a href="{{ route('contato') }}" class="text-sm font-bold uppercase tracking-wide text-white border-b-2 border-orange-400 pb-0.5">Contato</a>
                </nav>
                <div class="hidden items-center gap-3 sm:flex">
                    <a href="{{ route('login') }}" class="rounded-full border border-white/40 px-5 py-2.5 text-sm font-bold text-white transition hover:border-white hover:bg-white/10">Entrar</a>
                    <a href="{{ route('register') }}" class="rounded-full bg-orange-500 px-5 py-2.5 text-sm font-black text-white shadow-lg shadow-orange-700/30 transition hover:bg-orange-600">Cadastre-se</a>
                </div>
                <button @click="menuOpen = !menuOpen" class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-white/30 text-white lg:hidden">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
        {{-- Mobile nav --}}
        <div x-show="menuOpen" x-transition class="border-t border-white/20 bg-[#10395f] lg:hidden">
            <div class="mx-auto max-w-7xl px-4 py-4 space-y-2">
                @foreach($menuItems as $item)
                    <a href="{{ url('/') }}{{ $item['url'] }}" class="block rounded-xl px-4 py-3 text-sm font-bold text-blue-100 hover:bg-white/10">{{ $item['label'] }}</a>
                @endforeach
                <a href="{{ route('contato') }}" class="block rounded-xl px-4 py-3 text-sm font-bold text-orange-300 bg-white/5">Contato</a>
                <div class="flex gap-2 pt-2">
                    <a href="{{ route('login') }}" class="flex-1 rounded-xl border border-white/30 py-3 text-center text-sm font-bold text-white">Entrar</a>
                    <a href="{{ route('register') }}" class="flex-1 rounded-xl bg-orange-500 py-3 text-center text-sm font-black text-white">Cadastre-se</a>
                </div>
            </div>
        </div>
    </header>

    {{-- ═══════════ HERO BANNER ═══════════ --}}
    <section class="relative bg-gradient-to-br from-[#0f2f4d] via-[#1f5a7c] to-[#10395f] pt-32 pb-16 sm:pt-36 sm:pb-20">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImciIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PGNpcmNsZSBjeD0iMzAiIGN5PSIzMCIgcj0iMSIgZmlsbD0icmdiYSgyNTUsMjU1LDI1NSwwLjA1KSIvPjwvcGF0dGVybj48L2RlZnM+PHJlY3QgZmlsbD0idXJsKCNnKSIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIvPjwvc3ZnPg==')] opacity-60"></div>
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-[0.2em] text-blue-200 backdrop-blur">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Fale conosco
            </div>
            <h1 class="mt-4 text-4xl font-black tracking-tight text-white sm:text-5xl lg:text-6xl">Entre em contato</h1>
            <p class="mx-auto mt-4 max-w-2xl text-lg font-medium text-blue-100/80">Estamos prontos para ajudar sua loja a crescer. Fale com nossa equipe comercial ou envie sua mensagem.</p>
        </div>
    </section>

    {{-- ═══════════ CONTEÚDO PRINCIPAL ═══════════ --}}
    <section class="relative -mt-8 pb-20 sm:pb-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="grid gap-8 lg:grid-cols-5">
                {{-- ════ COLUNA ESQUERDA: CARDS DE CONTATO ════ --}}
                <div class="lg:col-span-2 space-y-5">

                    {{-- Card: Endereço --}}
                    @if(filled($settings->contact_address))
                    <div class="group rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-lg hover:border-[#1f5a7c]/30">
                        <div class="flex items-start gap-4">
                            <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-[#1f5a7c] to-[#10395f] text-white shadow-lg shadow-[#1f5a7c]/20">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-black text-slate-900">Endereço</h3>
                                <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ $settings->contact_address }}</p>
                                @if($settings->contact_city || $settings->contact_state)
                                    <p class="text-sm text-slate-600">{{ $settings->contact_city }}{{ $settings->contact_state ? ' - ' . $settings->contact_state : '' }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Card: Telefone --}}
                    @if(filled($settings->contact_phone))
                    <div class="group rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-lg hover:border-[#1f5a7c]/30">
                        <div class="flex items-start gap-4">
                            <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-[#1f5a7c] to-[#10395f] text-white shadow-lg shadow-[#1f5a7c]/20">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-black text-slate-900">Telefone</h3>
                                <a href="tel:{{ preg_replace('/\D/', '', $settings->contact_phone) }}" class="mt-1 inline-block text-sm font-semibold text-[#1f5a7c] hover:underline">{{ $settings->contact_phone }}</a>
                                <p class="mt-0.5 text-xs text-slate-400">Seg a Sex, 8h às 18h</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Card: E-mail --}}
                    @if(filled($settings->contact_email))
                    <div class="group rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-lg hover:border-[#1f5a7c]/30">
                        <div class="flex items-start gap-4">
                            <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-[#1f5a7c] to-[#10395f] text-white shadow-lg shadow-[#1f5a7c]/20">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-black text-slate-900">E-mail</h3>
                                <a href="mailto:{{ $settings->contact_email }}" class="mt-1 inline-block text-sm font-semibold text-[#1f5a7c] hover:underline">{{ $settings->contact_email }}</a>
                                <p class="mt-0.5 text-xs text-slate-400">Respondemos em até 24h úteis</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Card: WhatsApp --}}
                    @if(filled($settings->whatsapp_number))
                    <a href="https://wa.me/{{ $settings->whatsapp_number }}?text={{ urlencode('Olá! Vim pelo site da Elite Repasse e gostaria de mais informações.') }}" target="_blank" rel="noopener"
                       class="group flex items-start gap-4 rounded-3xl border-2 border-[#25D366]/30 bg-gradient-to-br from-[#25D366]/5 to-white p-6 shadow-sm transition hover:shadow-lg hover:border-[#25D366]/60">
                        <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-[#25D366] to-[#128C7E] text-white shadow-lg shadow-[#25D366]/25">
                            <svg class="h-7 w-7" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-slate-900">WhatsApp</h3>
                            <p class="mt-1 text-sm font-semibold text-[#25D366]">Fale agora com um consultor</p>
                            <p class="mt-0.5 text-xs text-slate-400">Atendimento rápido e personalizado</p>
                        </div>
                        <svg class="ml-auto h-5 w-5 flex-shrink-0 self-center text-[#25D366] opacity-0 transition group-hover:opacity-100 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    @endif

                    {{-- Card: Redes Sociais --}}
                    @if(filled($settings->social_instagram) || filled($settings->social_facebook) || filled($settings->social_youtube))
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-base font-black text-slate-900">Siga-nos nas redes</h3>
                        <div class="mt-4 flex gap-3">
                            @if(filled($settings->social_instagram))
                                <a href="{{ $settings->social_instagram }}" target="_blank" rel="noopener" class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-purple-500 via-pink-500 to-orange-400 text-white shadow-md transition hover:scale-105 hover:shadow-lg" aria-label="Instagram">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                </a>
                            @endif
                            @if(filled($settings->social_facebook))
                                <a href="{{ $settings->social_facebook }}" target="_blank" rel="noopener" class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#1877F2] text-white shadow-md transition hover:scale-105 hover:shadow-lg" aria-label="Facebook">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                </a>
                            @endif
                            @if(filled($settings->social_youtube))
                                <a href="{{ $settings->social_youtube }}" target="_blank" rel="noopener" class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-600 text-white shadow-md transition hover:scale-105 hover:shadow-lg" aria-label="YouTube">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Card: Horário de Funcionamento --}}
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-start gap-4">
                            <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 text-white shadow-lg shadow-orange-400/20">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-base font-black text-slate-900">Horário de Atendimento</h3>
                                <div class="mt-3 space-y-2">
                                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-2.5">
                                        <span class="text-sm font-semibold text-slate-700">Segunda a Sexta</span>
                                        <span class="text-sm font-black text-[#1f5a7c]">8h às 18h</span>
                                    </div>
                                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-2.5">
                                        <span class="text-sm font-semibold text-slate-700">Sábado</span>
                                        <span class="text-sm font-black text-[#1f5a7c]">8h às 12h</span>
                                    </div>
                                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-2.5">
                                        <span class="text-sm font-semibold text-slate-700">Domingo</span>
                                        <span class="text-sm font-bold text-slate-400">Fechado</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ════ COLUNA DIREITA: FORMULÁRIO ════ --}}
                <div class="lg:col-span-3">
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl sm:p-8">
                        <div class="mb-6">
                            <h2 class="text-2xl font-black text-slate-900 sm:text-3xl">Envie sua mensagem</h2>
                            <p class="mt-2 text-sm text-slate-500">Preencha o formulário e retornamos o mais breve possível.</p>
                        </div>

                        @if($enviado)
                            <div class="rounded-2xl border-2 border-emerald-200 bg-emerald-50 p-8 text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100">
                                    <svg class="h-8 w-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <h3 class="mt-4 text-xl font-black text-emerald-800">Mensagem enviada!</h3>
                                <p class="mt-2 text-sm text-emerald-600">Recebemos sua mensagem e entraremos em contato em breve.</p>
                                <button wire:click="$set('enviado', false)" class="mt-5 inline-flex items-center gap-2 rounded-2xl border border-emerald-300 bg-white px-6 py-3 text-sm font-bold text-emerald-700 transition hover:bg-emerald-50">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Enviar outra mensagem
                                </button>
                            </div>
                        @else
                            <form wire:submit="enviar" class="space-y-5">
                                <div class="grid gap-5 sm:grid-cols-2">
                                    {{-- Nome --}}
                                    <div>
                                        <label for="nome" class="mb-1.5 block text-sm font-bold text-slate-700">Nome completo <span class="text-rose-500">*</span></label>
                                        <input wire:model="nome" type="text" id="nome" placeholder="Seu nome"
                                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#1f5a7c] focus:bg-white focus:ring-2 focus:ring-[#1f5a7c]/20 @error('nome') border-rose-300 bg-rose-50 @enderror">
                                        @error('nome') <p class="mt-1 text-xs font-semibold text-rose-500">{{ $message }}</p> @enderror
                                    </div>

                                    {{-- E-mail --}}
                                    <div>
                                        <label for="email" class="mb-1.5 block text-sm font-bold text-slate-700">E-mail <span class="text-rose-500">*</span></label>
                                        <input wire:model="email" type="email" id="email" placeholder="seu@email.com"
                                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#1f5a7c] focus:bg-white focus:ring-2 focus:ring-[#1f5a7c]/20 @error('email') border-rose-300 bg-rose-50 @enderror">
                                        @error('email') <p class="mt-1 text-xs font-semibold text-rose-500">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="grid gap-5 sm:grid-cols-2">
                                    {{-- Telefone --}}
                                    <div>
                                        <label for="telefone" class="mb-1.5 block text-sm font-bold text-slate-700">Telefone / WhatsApp</label>
                                        <input wire:model="telefone" type="text" id="telefone" placeholder="(00) 00000-0000"
                                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#1f5a7c] focus:bg-white focus:ring-2 focus:ring-[#1f5a7c]/20">
                                    </div>

                                    {{-- Assunto --}}
                                    <div>
                                        <label for="assunto" class="mb-1.5 block text-sm font-bold text-slate-700">Assunto <span class="text-rose-500">*</span></label>
                                        <select wire:model="assunto" id="assunto"
                                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 outline-none transition focus:border-[#1f5a7c] focus:bg-white focus:ring-2 focus:ring-[#1f5a7c]/20 @error('assunto') border-rose-300 bg-rose-50 @enderror">
                                            <option value="">Selecione o assunto...</option>
                                            <option value="Quero ser lojista parceiro">Quero ser lojista parceiro</option>
                                            <option value="Dúvidas sobre compra">Dúvidas sobre compra</option>
                                            <option value="Suporte técnico">Suporte técnico</option>
                                            <option value="Financeiro / Pagamentos">Financeiro / Pagamentos</option>
                                            <option value="Documentação de veículo">Documentação de veículo</option>
                                            <option value="Parceria comercial">Parceria comercial</option>
                                            <option value="Outro assunto">Outro assunto</option>
                                        </select>
                                        @error('assunto') <p class="mt-1 text-xs font-semibold text-rose-500">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                {{-- Mensagem --}}
                                <div>
                                    <label for="mensagem" class="mb-1.5 block text-sm font-bold text-slate-700">Mensagem <span class="text-rose-500">*</span></label>
                                    <textarea wire:model="mensagem" id="mensagem" rows="5" placeholder="Descreva sua necessidade, dúvida ou como podemos ajudar..."
                                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#1f5a7c] focus:bg-white focus:ring-2 focus:ring-[#1f5a7c]/20 resize-none @error('mensagem') border-rose-300 bg-rose-50 @enderror"></textarea>
                                    @error('mensagem') <p class="mt-1 text-xs font-semibold text-rose-500">{{ $message }}</p> @enderror
                                </div>

                                {{-- Submit --}}
                                <div class="flex items-center gap-4">
                                    <button type="submit"
                                        wire:loading.attr="disabled"
                                        class="inline-flex items-center gap-2.5 rounded-2xl bg-gradient-to-r from-[#1f5a7c] to-[#10395f] px-8 py-4 text-base font-black text-white shadow-xl shadow-[#1f5a7c]/25 transition hover:shadow-2xl hover:shadow-[#1f5a7c]/30 disabled:opacity-60">
                                        <span wire:loading.remove wire:target="enviar">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                        </span>
                                        <span wire:loading wire:target="enviar">
                                            <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                        </span>
                                        <span wire:loading.remove wire:target="enviar">Enviar mensagem</span>
                                        <span wire:loading wire:target="enviar">Enviando...</span>
                                    </button>
                                    <p class="text-xs text-slate-400">* campos obrigatórios</p>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════ MAPA GOOGLE MAPS (FULLWIDTH) ═══════════ --}}
    @if($hasMap)
    <section class="border-t border-slate-200">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center mb-8">
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-[#1f5a7c]">Localização</p>
                <h2 class="mt-2 text-2xl font-black text-slate-900 sm:text-3xl">Onde estamos</h2>
                @if(filled($settings->contact_address))
                    <p class="mt-2 text-sm text-slate-500">{{ $settings->contact_address }}{{ $settings->contact_city ? ', ' . $settings->contact_city : '' }}{{ $settings->contact_state ? ' - ' . $settings->contact_state : '' }}</p>
                @endif
            </div>
            <div class="overflow-hidden rounded-3xl border border-slate-200 shadow-2xl">
                <iframe
                    src="https://www.google.com/maps/embed/v1/place?key={{ $mapsApiKey }}&q={{ $settings->contact_lat }},{{ $settings->contact_lng }}&zoom=15"
                    width="100%"
                    height="450"
                    style="border:0;"
                    allowfullscreen
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    class="w-full">
                </iframe>
            </div>
        </div>
    </section>
    @endif

    {{-- ═══════════ CTA FINAL ═══════════ --}}
    <section class="bg-gradient-to-r from-[#1f5a7c] to-[#10395f] py-16 text-white sm:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-black tracking-tight sm:text-4xl">Pronto para fazer parte da rede Elite Repasse?</h2>
            <p class="mx-auto mt-4 max-w-2xl text-blue-100">Cadastre sua empresa e ganhe acesso ao portal com oportunidades exclusivas para lojistas.</p>
            <div class="mt-7 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ route('register') }}" class="rounded-2xl bg-orange-500 px-8 py-4 text-base font-black text-white shadow-lg shadow-orange-900/35 transition hover:bg-orange-600">Cadastre-se agora</a>
                <a href="{{ route('login') }}" class="rounded-2xl border border-white/40 bg-white/10 px-8 py-4 text-base font-bold text-white transition hover:bg-white/20">Entrar no portal</a>
            </div>
        </div>
    </section>

    {{-- ═══════════ FOOTER ═══════════ --}}
    <footer class="bg-[#0b1b2a] py-12 text-slate-300">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-8 md:grid-cols-3">
                <div>
                    <img src="{{ $logoUrl }}" class="h-9 opacity-80" alt="Elite Repasse" onerror="this.src='https://placehold.co/220x60/0b1b2a/cbd5e1?text=Elite+Repasse'">
                    <p class="mt-4 max-w-sm text-sm text-slate-400">{{ $settings->footer_text ?? $defaults['footer_text'] }}</p>

                    @if(filled($settings->social_instagram) || filled($settings->social_facebook) || filled($settings->social_youtube))
                        <div class="mt-4 flex gap-3">
                            @if(filled($settings->social_instagram))
                                <a href="{{ $settings->social_instagram }}" target="_blank" rel="noopener" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-slate-300 transition hover:bg-white/20 hover:text-white" aria-label="Instagram">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                </a>
                            @endif
                            @if(filled($settings->social_facebook))
                                <a href="{{ $settings->social_facebook }}" target="_blank" rel="noopener" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-slate-300 transition hover:bg-white/20 hover:text-white" aria-label="Facebook">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                </a>
                            @endif
                            @if(filled($settings->social_youtube))
                                <a href="{{ $settings->social_youtube }}" target="_blank" rel="noopener" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-slate-300 transition hover:bg-white/20 hover:text-white" aria-label="YouTube">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
                <div>
                    <h3 class="text-sm font-black uppercase tracking-[0.14em] text-slate-200">Contato</h3>
                    @if($settings->contact_phone)
                        <p class="mt-3 text-sm">{{ $settings->contact_phone }}</p>
                    @endif
                    @if($settings->contact_email)
                        <p class="mt-1 text-sm">{{ $settings->contact_email }}</p>
                    @endif
                    <p class="mt-1 text-sm">{{ $settings->contact_city ?? 'Belo Horizonte' }} - {{ $settings->contact_state ?? 'MG' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-black uppercase tracking-[0.14em] text-slate-200">Links</h3>
                    <div class="mt-3 flex flex-col gap-2 text-sm">
                        @foreach($footerLinks as $link)
                            <a href="{{ $link['url'] }}" class="transition hover:text-white">{{ $link['label'] }}</a>
                        @endforeach
                        <a href="{{ route('contato') }}" class="transition hover:text-white">Contato</a>
                    </div>
                </div>
            </div>
            <div class="mt-8 border-t border-slate-700 pt-6 text-sm text-slate-500">
                &copy; {{ date('Y') }} Elite Repasse. Todos os direitos reservados.
            </div>
        </div>
    </footer>

    {{-- WhatsApp Flutuante --}}
    @if(filled($settings->whatsapp_number))
    <a href="https://wa.me/{{ $settings->whatsapp_number }}?text={{ urlencode('Olá! Vim pelo site da Elite Repasse e gostaria de mais informações.') }}" target="_blank"
       class="fixed bottom-6 right-6 z-50 inline-flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-2xl transition hover:scale-105" aria-label="WhatsApp">
        <svg class="h-7 w-7" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884"/>
        </svg>
    </a>
    @endif
</div>
