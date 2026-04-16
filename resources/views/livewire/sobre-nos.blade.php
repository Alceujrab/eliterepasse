{{-- ═══════════════════════════════════════════════════════════
     PÁGINA SOBRE NÓS — Elite Repasse
     Layout: hero → missão/visão/valores → história → números
              → equipe → depoimentos → galeria → vídeo → CTA
═══════════════════════════════════════════════════════════ --}}
<div x-data="{ menuOpen: false, lightbox: false, lightboxSrc: '' }" class="relative min-h-screen overflow-x-hidden bg-slate-50 text-slate-900">

    {{-- ═══════════ HEADER ═══════════ --}}
    <header class="fixed inset-x-0 top-0 z-50 border-b border-white/20 bg-[#0f2f4d]/95 backdrop-blur-xl">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ $logoUrl }}" class="h-9" alt="Elite Repasse" onerror="this.src='https://placehold.co/220x60/1f5a7c/ffffff?text=Elite+Repasse'">
                    <span class="hidden text-xs font-bold uppercase tracking-[0.2em] text-blue-200 md:inline">Portal do Lojista</span>
                </a>
                <nav class="hidden items-center gap-6 lg:flex">
                    @foreach($menuItems as $item)
                        <a href="{{ url('/') }}{{ $item['url'] }}" class="text-[13px] font-bold uppercase tracking-wide text-blue-200/80 transition hover:text-white">{{ $item['label'] }}</a>
                    @endforeach
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
                <div class="flex gap-2 pt-2">
                    <a href="{{ route('login') }}" class="flex-1 rounded-lg border border-white/30 py-2.5 text-center text-sm font-bold text-white">Entrar</a>
                    <a href="{{ route('register') }}" class="flex-1 rounded-lg bg-orange-500 py-2.5 text-center text-sm font-black text-white">Cadastre-se</a>
                </div>
            </div>
        </div>
    </header>

    {{-- ═══════════ HERO ═══════════ --}}
    <section class="relative bg-gradient-to-br from-[#0b2240] via-[#0f2f4d] to-[#1a4c6e] pt-28 pb-28 sm:pt-36 sm:pb-20">
        <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.07) 1px, transparent 0); background-size: 32px 32px;"></div>
        <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-slate-50 to-transparent"></div>
        <div class="relative mx-auto max-w-3xl px-4 text-center">
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-orange-400">Quem somos</p>
            <h1 class="mt-3 text-3xl font-black tracking-tight text-white sm:text-4xl sm:text-5xl">{{ $settings->about_page_hero_title ?: $defaults['about_page_hero_title'] }}</h1>
            <p class="mx-auto mt-3 max-w-xl text-base text-blue-200/70">{{ $settings->about_page_hero_subtitle ?: $defaults['about_page_hero_subtitle'] }}</p>
        </div>
    </section>

    {{-- ═══════════ MISSÃO / VISÃO / VALORES ═══════════ --}}
    @php
        $mission = $settings->about_page_mission ?: $defaults['about_page_mission'];
        $vision  = $settings->about_page_vision  ?: $defaults['about_page_vision'];
        $values  = $settings->about_page_values   ?: $defaults['about_page_values'];
    @endphp
    @if(filled($mission) || filled($vision) || filled($values))
    <section class="relative z-10 -mt-14 mb-12">
        <div class="mx-auto max-w-5xl px-4 sm:px-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                {{-- Missão --}}
                @if(filled($mission))
                <div class="rounded-2xl border border-white bg-white p-6 shadow-lg shadow-slate-900/5">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#0f2f4d] text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="mt-4 text-lg font-black text-slate-900">Nossa Missão</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $mission }}</p>
                </div>
                @endif

                {{-- Visão --}}
                @if(filled($vision))
                <div class="rounded-2xl border border-white bg-white p-6 shadow-lg shadow-slate-900/5">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-500 text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                    <h3 class="mt-4 text-lg font-black text-slate-900">Nossa Visão</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $vision }}</p>
                </div>
                @endif

                {{-- Valores --}}
                @if(filled($values))
                <div class="rounded-2xl border border-white bg-white p-6 shadow-lg shadow-slate-900/5">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#1a4c6e] text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="mt-4 text-lg font-black text-slate-900">Nossos Valores</h3>
                    <div class="mt-2 text-sm leading-relaxed text-slate-600">{!! nl2br(e($values)) !!}</div>
                </div>
                @endif
            </div>
        </div>
    </section>
    @endif

    {{-- ═══════════ NOSSA HISTÓRIA ═══════════ --}}
    @php
        $history = $settings->about_page_history ?: $defaults['about_page_history'];
        $historyImage = $settings->about_page_history_image;
    @endphp
    @if(filled($history))
    <section class="py-16">
        <div class="mx-auto max-w-5xl px-4 sm:px-6">
            <div class="grid gap-10 lg:grid-cols-2 items-center">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.25em] text-orange-500">Nossa Trajetória</p>
                    <h2 class="mt-2 text-2xl font-black text-slate-900 sm:text-3xl">Nossa História</h2>
                    <div class="mt-4 space-y-3 text-sm leading-relaxed text-slate-600">{!! nl2br(e($history)) !!}</div>
                </div>
                @if(filled($historyImage))
                <div class="overflow-hidden rounded-2xl shadow-xl">
                    <img src="{{ asset($historyImage) }}" alt="Nossa História" class="h-auto w-full object-cover" onerror="this.parentElement.style.display='none'">
                </div>
                @endif
            </div>
        </div>
    </section>
    @endif

    {{-- ═══════════ NÚMEROS / STATS ═══════════ --}}
    @if($stats->isNotEmpty())
    <section class="bg-gradient-to-r from-[#0b2240] to-[#1a4c6e] py-16">
        <div class="mx-auto max-w-5xl px-4 sm:px-6">
            <div class="text-center">
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-orange-400">Nossos Números</p>
                <h2 class="mt-2 text-2xl font-black text-white sm:text-3xl">Resultados que falam por si</h2>
            </div>
            <div class="mt-10 grid grid-cols-2 gap-6 sm:grid-cols-4">
                @foreach($stats as $stat)
                <div class="text-center">
                    <p class="text-3xl font-black text-white sm:text-4xl">{{ $stat['value'] }}</p>
                    <p class="mt-1 text-sm font-semibold text-blue-200/70">{{ $stat['label'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ═══════════ EQUIPE ═══════════ --}}
    @if($team->isNotEmpty())
    <section class="py-16">
        <div class="mx-auto max-w-5xl px-4 sm:px-6">
            <div class="text-center">
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-orange-500">Nosso Time</p>
                <h2 class="mt-2 text-2xl font-black text-slate-900 sm:text-3xl">Quem faz acontecer</h2>
            </div>
            <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($team as $member)
                <div class="group rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm transition hover:shadow-lg">
                    @if(filled($member['photo'] ?? null))
                    <img src="{{ asset($member['photo']) }}" alt="{{ $member['name'] }}"
                         class="mx-auto h-28 w-28 rounded-full object-cover ring-4 ring-slate-100 transition group-hover:ring-orange-200"
                         onerror="this.src='https://placehold.co/200x200/e2e8f0/94a3b8?text={{ urlencode(mb_substr($member['name'],0,2)) }}'">
                    @else
                    <div class="mx-auto flex h-28 w-28 items-center justify-center rounded-full bg-gradient-to-br from-[#0f2f4d] to-[#1a4c6e] text-3xl font-black text-white ring-4 ring-slate-100">
                        {{ mb_strtoupper(mb_substr($member['name'], 0, 2)) }}
                    </div>
                    @endif
                    <div class="mt-4 text-center">
                        <h3 class="text-lg font-black text-slate-900">{{ $member['name'] }}</h3>
                        @if(filled($member['role'] ?? null))
                        <p class="mt-0.5 text-sm font-semibold text-orange-500">{{ $member['role'] }}</p>
                        @endif
                        @if(filled($member['bio'] ?? null))
                        <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ $member['bio'] }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ═══════════ DEPOIMENTOS ═══════════ --}}
    @if($testimonials->isNotEmpty())
    <section class="bg-slate-100 py-16">
        <div class="mx-auto max-w-5xl px-4 sm:px-6">
            <div class="text-center">
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-orange-500">Depoimentos</p>
                <h2 class="mt-2 text-2xl font-black text-slate-900 sm:text-3xl">O que nossos parceiros dizem</h2>
            </div>
            <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($testimonials as $testimonial)
                <div class="flex flex-col rounded-2xl border border-white bg-white p-6 shadow-sm">
                    {{-- Estrelas --}}
                    @if(filled($testimonial['rating'] ?? null))
                    <div class="flex gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="h-4 w-4 {{ $i <= (int) $testimonial['rating'] ? 'text-orange-400' : 'text-slate-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    @endif

                    {{-- Texto --}}
                    <p class="mt-3 flex-1 text-sm leading-relaxed text-slate-600 italic">"{{ $testimonial['text'] }}"</p>

                    {{-- Vídeo --}}
                    @if(filled($testimonial['video_url'] ?? null))
                    @php
                        $videoId = null;
                        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $testimonial['video_url'], $m)) {
                            $videoId = $m[1];
                        }
                    @endphp
                        @if($videoId)
                        <div class="mt-3 overflow-hidden rounded-xl">
                            <iframe src="https://www.youtube.com/embed/{{ $videoId }}" width="100%" height="180" frameborder="0" allowfullscreen loading="lazy" class="w-full rounded-xl"></iframe>
                        </div>
                        @endif
                    @endif

                    {{-- Autor --}}
                    <div class="mt-4 flex items-center gap-3 border-t border-slate-100 pt-4">
                        @if(filled($testimonial['photo'] ?? null))
                        <img src="{{ asset($testimonial['photo']) }}" alt="{{ $testimonial['name'] }}" class="h-10 w-10 rounded-full object-cover"
                             onerror="this.src='https://placehold.co/80x80/e2e8f0/94a3b8?text={{ urlencode(mb_substr($testimonial['name'],0,1)) }}'">
                        @else
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#0f2f4d] text-sm font-bold text-white">
                            {{ mb_strtoupper(mb_substr($testimonial['name'], 0, 1)) }}
                        </div>
                        @endif
                        <div>
                            <p class="text-sm font-bold text-slate-900">{{ $testimonial['name'] }}</p>
                            @if(filled($testimonial['company'] ?? null) || filled($testimonial['role'] ?? null))
                            <p class="text-xs text-slate-500">
                                {{ $testimonial['role'] ?? '' }}{{ filled($testimonial['role'] ?? null) && filled($testimonial['company'] ?? null) ? ' — ' : '' }}{{ $testimonial['company'] ?? '' }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ═══════════ GALERIA DE FOTOS ═══════════ --}}
    @if($gallery->isNotEmpty())
    <section class="py-16">
        <div class="mx-auto max-w-5xl px-4 sm:px-6">
            <div class="text-center">
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-orange-500">Galeria</p>
                <h2 class="mt-2 text-2xl font-black text-slate-900 sm:text-3xl">Nossas Instalações</h2>
            </div>
            <div class="mt-10 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                @foreach($gallery as $photo)
                <button @click="lightbox = true; lightboxSrc = '{{ asset($photo) }}'"
                        class="group relative overflow-hidden rounded-xl aspect-square cursor-pointer">
                    <img src="{{ asset($photo) }}" alt="Galeria" class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                         onerror="this.parentElement.style.display='none'">
                    <div class="absolute inset-0 flex items-center justify-center bg-black/0 transition group-hover:bg-black/30">
                        <svg class="h-8 w-8 text-white opacity-0 transition group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                    </div>
                </button>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Lightbox --}}
    <div x-show="lightbox" x-transition.opacity class="fixed inset-0 z-[60] flex items-center justify-center bg-black/80 p-4" @click.self="lightbox = false" @keydown.escape.window="lightbox = false" style="display: none;">
        <button @click="lightbox = false" class="absolute top-4 right-4 text-white/80 hover:text-white">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <img :src="lightboxSrc" alt="Foto ampliada" class="max-h-[85vh] max-w-full rounded-lg shadow-2xl">
    </div>
    @endif

    {{-- ═══════════ VÍDEO INSTITUCIONAL ═══════════ --}}
    @php
        $videoUrl = $settings->about_page_video_url;
        $mainVideoId = null;
        if (filled($videoUrl) && preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $videoUrl, $mv)) {
            $mainVideoId = $mv[1];
        }
    @endphp
    @if($mainVideoId)
    <section class="bg-gradient-to-br from-[#0b2240] via-[#0f2f4d] to-[#1a4c6e] py-16">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 text-center">
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-orange-400">Vídeo Institucional</p>
            <h2 class="mt-2 text-2xl font-black text-white sm:text-3xl">Conheça mais sobre nós</h2>
            <div class="mt-8 overflow-hidden rounded-2xl shadow-2xl shadow-black/30">
                <div class="relative w-full" style="padding-bottom: 56.25%;">
                    <iframe src="https://www.youtube.com/embed/{{ $mainVideoId }}" class="absolute inset-0 h-full w-full"
                            frameborder="0" allowfullscreen loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ═══════════ CTA CADASTRO ═══════════ --}}
    <section class="py-16">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 text-center">
            <div class="rounded-2xl bg-gradient-to-br from-[#0f2f4d] to-[#1a4c6e] p-10 shadow-xl">
                <h2 class="text-2xl font-black text-white sm:text-3xl">Pronto para fazer parte?</h2>
                <p class="mx-auto mt-3 max-w-lg text-sm text-blue-200/70">Cadastre-se gratuitamente e tenha acesso ao nosso portfólio exclusivo de seminovos para lojistas.</p>
                <div class="mt-6 flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-xl bg-orange-500 px-8 py-3.5 text-sm font-black text-white shadow-lg shadow-orange-700/30 transition hover:bg-orange-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        Cadastre-se Agora
                    </a>
                    @if(filled($settings->whatsapp_number))
                    <a href="https://wa.me/{{ $settings->whatsapp_number }}?text={{ urlencode('Olá! Vim pelo site da Elite Repasse e gostaria de mais informações.') }}" target="_blank"
                       class="inline-flex items-center gap-2 rounded-xl border border-white/30 bg-white/10 px-8 py-3.5 text-sm font-bold text-white transition hover:bg-white/20">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/></svg>
                        Falar pelo WhatsApp
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════ FOOTER ═══════════ --}}
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
