@php
    $defaults = \App\Models\LandingSetting::defaults();

    $vantagens = collect($settings->features ?? [])->filter(fn ($item) => filled($item['title'] ?? null))->values();
    if ($vantagens->isEmpty()) {
        $vantagens = collect($defaults['features']);
    }

    $faqItems = collect($settings->faq ?? [])->filter(fn ($item) => filled($item['question'] ?? null))->values();
    if ($faqItems->isEmpty()) {
        $faqItems = collect($defaults['faq']);
    }

    $menuItems = collect($settings->menu_items ?? [])->filter(fn ($item) => filled($item['label'] ?? null))->values();
    if ($menuItems->isEmpty()) {
        $menuItems = collect($defaults['menu_items']);
    }
    // Substituir âncoras por páginas dedicadas
    $menuItems = $menuItems->map(function ($item) {
        if (($item['url'] ?? '') === '#contato') return array_merge($item, ['url' => '/contato']);
        if (($item['url'] ?? '') === '#sobre') return array_merge($item, ['url' => '/sobre-nos']);
        return $item;
    });

    $footerLinks = collect($settings->footer_links ?? [])->filter(fn ($item) => filled($item['label'] ?? null))->values();
    if ($footerLinks->isEmpty()) {
        $footerLinks = collect($defaults['footer_links']);
    }

    $logoUrl = $settings->logo_path
        ? asset($settings->logo_path)
        : asset('build/assets/logo.png');

    $modelos = [
        ['nome' => 'Fiat Uno', 'tipo' => 'Hatch', 'destaque' => null, 'imagem' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?q=80&w=1600&auto=format&fit=crop'],
        ['nome' => 'Ford Ka', 'tipo' => 'Hatch', 'destaque' => 'Hatch popular', 'imagem' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=1600&auto=format&fit=crop'],
        ['nome' => 'Chevrolet Onix', 'tipo' => 'Hatch', 'destaque' => 'Alta liquidez', 'imagem' => 'https://images.unsplash.com/photo-1553440569-bcc63803a83d?q=80&w=1600&auto=format&fit=crop'],
        ['nome' => 'Volkswagen T-Cross', 'tipo' => 'SUV', 'destaque' => null, 'imagem' => 'https://images.unsplash.com/photo-1519641471654-76ce0107ad1b?q=80&w=1600&auto=format&fit=crop'],
        ['nome' => 'Jeep Renegade', 'tipo' => 'SUV', 'destaque' => 'SUV em alta', 'imagem' => 'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?q=80&w=1600&auto=format&fit=crop'],
        ['nome' => 'Fiat Toro', 'tipo' => 'Picape', 'destaque' => null, 'imagem' => 'https://images.unsplash.com/photo-1590362891991-f776e747a588?q=80&w=1600&auto=format&fit=crop'],
    ];

    $etapas = [
        ['titulo' => 'Faça seu cadastro', 'descricao' => 'Cadastre sua empresa e representantes para análise de acesso.'],
        ['titulo' => 'Acesse o portal', 'descricao' => 'Entre na plataforma para visualizar ofertas e condições comerciais.'],
        ['titulo' => 'Escolha seus carros', 'descricao' => 'Use filtros por marca, faixa de preço, ano, combustível e mais.'],
        ['titulo' => 'Compre online', 'descricao' => 'Feche negócio com segurança e acompanhe pedidos em tempo real.'],
    ];

    $modulosGestao = [
        [
            'titulo' => 'Acompanhe seus pedidos',
            'descricao' => 'Linha do tempo de status, contratos e documentos em cada compra.',
            'imagem' => 'https://images.unsplash.com/photo-1556745757-8d76bdb6984b?q=80&w=1600&auto=format&fit=crop',
        ],
        [
            'titulo' => 'Consulte dados do veículo',
            'descricao' => 'Acesse laudos, histórico e informações essenciais antes da decisão final.',
            'imagem' => 'https://images.unsplash.com/photo-1580273916550-e323be2ae537?q=80&w=1600&auto=format&fit=crop',
        ],
        [
            'titulo' => 'Financeiro sem fricção',
            'descricao' => 'Visualize cobranças, segunda via e detalhes de pagamento no mesmo painel.',
            'imagem' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?q=80&w=1600&auto=format&fit=crop',
        ],
    ];

    $hasContact = filled($settings->contact_address) || filled($settings->contact_phone) || filled($settings->contact_email);
    $hasMap = filled($settings->contact_lat) && filled($settings->contact_lng) && filled($mapsApiKey);
    $hasAbout = filled($settings->about_title) || filled($settings->about_text);
@endphp

<div x-data="{ menuOpen: false, faqOpen: 0, bannerCurrent: 0 }" class="relative min-h-screen overflow-x-hidden bg-white text-slate-900">
    <div class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(circle_at_85%_10%,rgba(249,115,22,0.14),transparent_35%),radial-gradient(circle_at_15%_20%,rgba(31,90,124,0.18),transparent_40%)]"></div>

    {{-- ═══════════════════════════════════════════════════════════
         HEADER / MENU (editável pelo admin)
    ═══════════════════════════════════════════════════════════ --}}
    <header class="fixed inset-x-0 top-0 z-50 border-b border-white/20 bg-[#0f2f4d]/90 backdrop-blur-xl">
        <div class="page-container">
            <div class="flex h-20 items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ $logoUrl }}" class="h-10" alt="Elite Repasse" onerror="this.src='https://placehold.co/220x60/1f5a7c/ffffff?text=Elite+Repasse'">
                    <span class="hidden text-sm font-bold uppercase tracking-[0.2em] text-blue-100 md:inline">Portal do Lojista</span>
                </a>

                <nav class="hidden items-center gap-7 lg:flex">
                    @foreach($menuItems as $item)
                        <a href="{{ $item['url'] }}" class="text-sm font-bold uppercase tracking-wide text-blue-100 transition hover:text-white">{{ $item['label'] }}</a>
                    @endforeach
                </nav>

                <div class="hidden items-center gap-3 sm:flex">
                    <a href="{{ route('login') }}" class="rounded-full border border-white/40 px-5 py-2.5 text-sm font-bold text-white transition hover:border-white hover:bg-white/10">Entrar</a>
                    <a href="{{ route('register') }}" class="rounded-full bg-orange-500 px-5 py-2.5 text-sm font-black text-white shadow-lg shadow-orange-700/30 transition hover:bg-orange-600">Cadastre-se</a>
                </div>

                <button @click="menuOpen = !menuOpen" class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-white/30 text-white lg:hidden">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        <div x-show="menuOpen" x-transition class="border-t border-white/20 bg-[#10395f] lg:hidden">
            <div class="page-container py-4">
                <div class="flex flex-col gap-3">
                    @foreach($menuItems as $item)
                        <a @click="menuOpen=false" href="{{ $item['url'] }}" class="rounded-xl bg-white/5 px-4 py-3 text-sm font-semibold text-white">{{ $item['label'] }}</a>
                    @endforeach
                    <a href="{{ route('login') }}" class="rounded-xl border border-white/30 px-4 py-3 text-center text-sm font-bold text-white">Entrar</a>
                    <a href="{{ route('register') }}" class="rounded-xl bg-orange-500 px-4 py-3 text-center text-sm font-black text-white">Cadastre-se</a>
                </div>
            </div>
        </div>
    </header>

    {{-- ═══════════════════════════════════════════════════════════
         HERO / BANNERS (carrossel editável)
    ═══════════════════════════════════════════════════════════ --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-[#0f2f4d] via-[#1a4b77] to-[#1f5a7c] pt-32 pb-16 sm:pt-36 sm:pb-20">
        <div class="pointer-events-none absolute -left-24 top-16 h-72 w-72 rounded-full bg-orange-500/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-20 bottom-10 h-80 w-80 rounded-full bg-blue-300/20 blur-3xl"></div>

        <div class="page-container relative z-10">
            <div class="grid items-center gap-10 lg:grid-cols-2">
                <div>
                    <p class="mb-4 inline-flex items-center rounded-full border border-blue-200/40 bg-white/10 px-4 py-1.5 text-xs font-extrabold uppercase tracking-[0.22em] text-blue-100">
                        Plataforma B2B para lojistas
                    </p>
                    <h1 class="text-4xl font-black leading-tight tracking-tight text-white sm:text-5xl lg:text-6xl">
                        {{ $settings->hero_title ?? $defaults['hero_title'] }}
                    </h1>
                    <p class="mt-5 max-w-2xl text-lg font-medium leading-relaxed text-blue-100 sm:text-xl">
                        {{ $settings->hero_subtitle ?? $defaults['hero_subtitle'] }}
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-2xl bg-orange-500 px-7 py-4 text-base font-black text-white shadow-lg shadow-orange-900/40 transition hover:translate-y-[-1px] hover:bg-orange-600">
                            Cadastre-se agora
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/40 bg-white/10 px-7 py-4 text-base font-bold text-white transition hover:bg-white/20">
                            Já tenho conta
                        </a>
                    </div>

                    <div class="mt-8 grid gap-3 sm:grid-cols-3">
                        @foreach($vantagens->take(3) as $vantagem)
                            <article class="rounded-2xl border border-white/20 bg-white/10 px-4 py-4 backdrop-blur">
                                <h3 class="text-sm font-black uppercase tracking-wide text-white">{{ $vantagem['title'] }}</h3>
                                <p class="mt-2 text-sm leading-relaxed text-blue-100">{{ $vantagem['description'] ?? '' }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>

                {{-- Banner / Carrossel --}}
                <div class="relative">
                    @if($banners->count() > 0)
                        <div class="overflow-hidden rounded-[28px] border border-white/20 bg-white/10 p-3 shadow-2xl backdrop-blur">
                            <div class="relative overflow-hidden rounded-[22px] bg-slate-100">
                                @foreach($banners as $index => $banner)
                                    <div x-show="bannerCurrent === {{ $index }}"
                                         x-transition:enter="transition ease-out duration-500"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-300"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         class="w-full">
                                        <img src="{{ asset('storage/' . $banner->image_path) }}" alt="{{ $banner->title ?? 'Banner' }}" class="h-[420px] w-full object-cover">
                                        @if($banner->title || $banner->subtitle)
                                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-6">
                                                @if($banner->title)
                                                    <h3 class="text-xl font-black text-white">{{ $banner->title }}</h3>
                                                @endif
                                                @if($banner->subtitle)
                                                    <p class="mt-1 text-sm text-blue-100">{{ $banner->subtitle }}</p>
                                                @endif
                                                @if($banner->button_text && $banner->button_url)
                                                    <a href="{{ $banner->button_url }}" class="mt-3 inline-flex rounded-xl bg-orange-500 px-5 py-2.5 text-sm font-black text-white transition hover:bg-orange-600">{{ $banner->button_text }}</a>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            @if($banners->count() > 1)
                                <div class="mt-3 flex items-center justify-center gap-2">
                                    @foreach($banners as $index => $banner)
                                        <button @click="bannerCurrent = {{ $index }}" class="h-2.5 rounded-full transition-all duration-300" :class="bannerCurrent === {{ $index }} ? 'w-8 bg-orange-500' : 'w-2.5 bg-white/40'"></button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        @if($banners->count() > 1)
                            <div x-init="setInterval(() => { bannerCurrent = (bannerCurrent + 1) % {{ $banners->count() }} }, 5000)"></div>
                        @endif
                    @else
                        <div class="overflow-hidden rounded-[28px] border border-white/20 bg-white/10 p-3 shadow-2xl backdrop-blur">
                            <div class="overflow-hidden rounded-[22px] bg-slate-100">
                                <img src="https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?q=80&w=1800&auto=format&fit=crop" alt="Veículos em exposição" class="h-[420px] w-full object-cover">
                            </div>
                        </div>
                    @endif

                    <div class="absolute -bottom-5 -left-4 rounded-2xl border border-blue-100/30 bg-[#123a5d] px-5 py-4 text-white shadow-xl sm:-left-8">
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-blue-200">Portal completo</p>
                        <p class="mt-1 text-xl font-black">Pedidos, contratos e financeiro</p>
                    </div>
                    <div class="absolute -right-2 top-6 rounded-2xl border border-orange-200/40 bg-orange-500 px-4 py-3 text-white shadow-xl sm:-right-6">
                        <p class="text-xs font-bold uppercase tracking-wider text-orange-100">Compra online</p>
                        <p class="text-base font-black">100% digital</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════
         MODELOS EM DESTAQUE
    ═══════════════════════════════════════════════════════════ --}}
    <section id="modelos" class="py-20 sm:py-24">
        <div class="page-container">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-[#1f5a7c]">Estoque em destaque</p>
                <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Diversos modelos para acelerar o seu negócio</h2>
                <p class="mt-4 text-base leading-relaxed text-slate-600 sm:text-lg">Selecione os carros com melhor potencial de giro e monte sua carteira com mais previsibilidade.</p>
            </div>

            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($modelos as $modelo)
                    <article class="group overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                        <div class="relative h-52 overflow-hidden">
                            <img src="{{ $modelo['imagem'] }}" alt="{{ $modelo['nome'] }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-900/80 to-transparent px-4 py-3">
                                <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-blue-100">{{ $modelo['tipo'] }}</p>
                                <h3 class="text-lg font-black text-white">{{ $modelo['nome'] }}</h3>
                            </div>
                            @if($modelo['destaque'])
                                <span class="absolute left-3 top-3 rounded-full bg-orange-500 px-3 py-1 text-xs font-black uppercase tracking-wide text-white">{{ $modelo['destaque'] }}</span>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-10 flex justify-center">
                <a href="{{ route('register') }}" class="btn-cta-md">Quero acessar mais carros</a>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════
         VANTAGENS
    ═══════════════════════════════════════════════════════════ --}}
    <section id="vantagens" class="border-y border-slate-200 bg-slate-50 py-16">
        <div class="page-container">
            <div class="grid gap-5 md:grid-cols-3">
                <article class="rounded-2xl border border-slate-200 bg-white p-6">
                    <h3 class="text-xl font-black text-slate-900">Laudo disponível</h3>
                    <p class="mt-3 text-slate-600">Documentação e informações técnicas para consulta antes da compra.</p>
                </article>
                <article class="rounded-2xl border border-slate-200 bg-white p-6">
                    <h3 class="text-xl font-black text-slate-900">Quilometragem real</h3>
                    <p class="mt-3 text-slate-600">Transparência nos dados para tomada de decisão com mais segurança.</p>
                </article>
                <article class="rounded-2xl border border-slate-200 bg-white p-6">
                    <h3 class="text-xl font-black text-slate-900">Preço abaixo da FIPE</h3>
                    <p class="mt-3 text-slate-600">Melhor oportunidade de compra com margem potencial para revenda.</p>
                </article>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════
         COMO FUNCIONA
    ═══════════════════════════════════════════════════════════ --}}
    <section id="como-funciona" class="py-20 sm:py-24">
        <div class="page-container">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-[#1f5a7c]">Como funciona</p>
                <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Fluxo rápido para você comprar melhor</h2>
            </div>

            <div class="mt-10 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                @foreach($etapas as $index => $etapa)
                    <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="mb-4 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#1f5a7c] text-lg font-black text-white">{{ $index + 1 }}</div>
                        <h3 class="text-lg font-black text-slate-900">{{ $etapa['titulo'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $etapa['descricao'] }}</p>
                    </article>
                @endforeach
            </div>

            <div class="mt-8 flex justify-center">
                <a href="{{ route('register') }}" class="btn-cta-md">Começar agora</a>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════
         SOBRE NÓS (editável pelo admin)
    ═══════════════════════════════════════════════════════════ --}}
    @if($hasAbout)
    <section id="sobre" class="border-y border-slate-200 bg-slate-50 py-20 sm:py-24">
        <div class="page-container">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-[#1f5a7c]">Quem somos</p>
                <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">{{ $settings->about_title ?? 'Sobre a Elite Repasse' }}</h2>
            </div>

            <div class="mt-10 grid items-center gap-10 lg:grid-cols-2">
                @if($settings->about_image)
                    <div class="overflow-hidden rounded-3xl border border-slate-200 shadow-lg">
                        <img src="{{ asset($settings->about_image) }}" alt="{{ $settings->about_title ?? 'Sobre nós' }}" class="h-80 w-full object-cover lg:h-96">
                    </div>
                @endif
                <div class="{{ $settings->about_image ? '' : 'lg:col-span-2 mx-auto max-w-3xl' }}">
                    <div class="prose prose-lg max-w-none text-slate-600">
                        {!! nl2br(e($settings->about_text ?? '')) !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ═══════════════════════════════════════════════════════════
         GESTÃO COMPLETA
    ═══════════════════════════════════════════════════════════ --}}
    <section class="bg-[#0f2f4d] py-20 text-white sm:py-24">
        <div class="page-container">
            <div class="mb-10 text-center">
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-blue-200">Gestão completa</p>
                <h2 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">Facilidade para acompanhar todo o ciclo de compra</h2>
            </div>

            <div class="grid gap-5 lg:grid-cols-3">
                @foreach($modulosGestao as $modulo)
                    <article class="overflow-hidden rounded-3xl border border-white/15 bg-white/10 backdrop-blur">
                        <img src="{{ $modulo['imagem'] }}" alt="{{ $modulo['titulo'] }}" class="h-52 w-full object-cover">
                        <div class="p-6">
                            <h3 class="text-xl font-black">{{ $modulo['titulo'] }}</h3>
                            <p class="mt-3 text-sm leading-relaxed text-blue-100">{{ $modulo['descricao'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>

            <article class="mt-8 rounded-2xl border border-orange-300/40 bg-orange-500/90 p-6 text-center shadow-lg shadow-orange-900/30">
                <h3 class="text-2xl font-black">Equipe especializada ao seu lado</h3>
                <p class="mt-2 text-orange-100">Suporte comercial e operacional para garantir uma jornada simples do início ao fim.</p>
            </article>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════
         CONTATO / MAPA (editável pelo admin)
    ═══════════════════════════════════════════════════════════ --}}
    @if($hasContact)
    <section id="contato" class="py-20 sm:py-24">
        <div class="page-container">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-[#1f5a7c]">Contato</p>
                <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Fale com a equipe Elite Repasse</h2>
            </div>

            <div class="mt-10 grid gap-8 {{ $hasMap ? 'lg:grid-cols-2' : '' }}">
                <div class="space-y-6">
                    @if($settings->contact_address)
                        <div class="flex gap-4">
                            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-[#1f5a7c]/10 text-[#1f5a7c]">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-900">Endereço</h3>
                                <p class="mt-1 text-slate-600">{{ $settings->contact_address }}</p>
                                @if($settings->contact_city || $settings->contact_state)
                                    <p class="text-slate-600">{{ $settings->contact_city }}{{ $settings->contact_state ? ' - ' . $settings->contact_state : '' }}</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($settings->contact_phone)
                        <div class="flex gap-4">
                            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-[#1f5a7c]/10 text-[#1f5a7c]">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-900">Telefone</h3>
                                <p class="mt-1 text-slate-600">{{ $settings->contact_phone }}</p>
                            </div>
                        </div>
                    @endif

                    @if($settings->contact_email)
                        <div class="flex gap-4">
                            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-[#1f5a7c]/10 text-[#1f5a7c]">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-900">E-mail</h3>
                                <p class="mt-1 text-slate-600">{{ $settings->contact_email }}</p>
                            </div>
                        </div>
                    @endif

                    @if($settings->whatsapp_number)
                        <div class="flex gap-4">
                            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-[#25D366]/10 text-[#25D366]">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-900">WhatsApp</h3>
                                <a href="https://wa.me/{{ $settings->whatsapp_number }}" target="_blank" class="mt-1 font-bold text-[#25D366] hover:underline">Enviar mensagem</a>
                            </div>
                        </div>
                    @endif
                </div>

                @if($hasMap)
                    <div class="overflow-hidden rounded-3xl border border-slate-200 shadow-lg">
                        <iframe
                            src="https://www.google.com/maps/embed/v1/place?key={{ $mapsApiKey }}&q={{ $settings->contact_lat }},{{ $settings->contact_lng }}&zoom=15"
                            width="100%"
                            height="400"
                            style="border:0;"
                            allowfullscreen
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            class="h-full min-h-[360px] w-full">
                        </iframe>
                    </div>
                @endif
            </div>
        </div>
    </section>
    @endif

    {{-- ═══════════════════════════════════════════════════════════
         FAQ (editável pelo admin)
    ═══════════════════════════════════════════════════════════ --}}
    <section id="faq" class="{{ $hasContact ? 'border-t border-slate-200' : '' }} bg-slate-50 py-20 sm:py-24">
        <div class="page-container">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-[#1f5a7c]">Dúvidas frequentes</p>
                <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Respostas rápidas para começar com segurança</h2>
            </div>

            <div class="mx-auto mt-10 max-w-4xl space-y-3">
                @foreach($faqItems as $index => $faq)
                    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <button @click="faqOpen = faqOpen === {{ $index }} ? null : {{ $index }}" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left sm:px-6 sm:py-5">
                            <span class="text-base font-bold text-slate-800 sm:text-lg">{{ $faq['question'] ?? '' }}</span>
                            <span class="inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full border border-slate-300 text-slate-600 transition" :class="faqOpen === {{ $index }} ? 'bg-[#1f5a7c] text-white border-[#1f5a7c]' : ''">
                                <svg class="h-4 w-4 transition" :class="faqOpen === {{ $index }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </span>
                        </button>
                        <div x-show="faqOpen === {{ $index }}" x-collapse class="border-t border-slate-100 px-5 py-4 sm:px-6 sm:py-5">
                            <p class="leading-relaxed text-slate-600">{{ $faq['answer'] ?? '' }}</p>
                        </div>
                    </article>
                @endforeach
            </div>

            <article class="mx-auto mt-12 max-w-4xl rounded-2xl border border-slate-200 bg-white p-6 text-center sm:p-8">
                <h3 class="text-xl font-black text-slate-900 sm:text-2xl">Não possui CNPJ e busca um seminovo?</h3>
                <p class="mt-3 text-slate-600">Este portal é exclusivo para empresas revendedoras. Para compra de varejo, acesse nosso estoque público.</p>
                <a href="#" class="mt-5 inline-flex rounded-xl border border-[#1f5a7c] px-5 py-3 text-sm font-black uppercase tracking-wide text-[#1f5a7c] transition hover:bg-[#1f5a7c] hover:text-white">Ver estoque para pessoa física</a>
            </article>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════
         CTA FINAL
    ═══════════════════════════════════════════════════════════ --}}
    <section class="bg-gradient-to-r from-[#1f5a7c] to-[#10395f] py-16 text-white sm:py-20">
        <div class="page-container text-center">
            <h2 class="text-3xl font-black tracking-tight sm:text-4xl">Pronto para ampliar seu catálogo com mais margem?</h2>
            <p class="mx-auto mt-4 max-w-2xl text-blue-100">Cadastre sua empresa e ganhe acesso ao portal com oportunidades para acelerar seu giro.</p>
            <div class="mt-7 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ route('register') }}" class="rounded-2xl bg-orange-500 px-8 py-4 text-base font-black text-white shadow-lg shadow-orange-900/35 transition hover:bg-orange-600">Cadastre-se agora</a>
                <a href="{{ route('login') }}" class="rounded-2xl border border-white/40 bg-white/10 px-8 py-4 text-base font-bold text-white transition hover:bg-white/20">Entrar no portal</a>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════
         FOOTER (editável pelo admin)
    ═══════════════════════════════════════════════════════════ --}}
    <footer class="bg-[#0b1b2a] py-12 text-slate-300">
        <div class="page-container">
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
                    @else
                        <p class="mt-3 text-sm">Suporte comercial para revendas de veículos</p>
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
                    </div>
                </div>
            </div>
            <div class="mt-8 border-t border-slate-700 pt-6 text-sm text-slate-500">
                &copy; {{ date('Y') }} Elite Repasse. Todos os direitos reservados.
            </div>
        </div>
    </footer>

    {{-- WhatsApp Flutuante --}}
    <a href="https://wa.me/{{ $settings->whatsapp_number ?? '5511999999999' }}" target="_blank" class="fixed bottom-6 right-6 z-50 inline-flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-2xl transition hover:scale-105" aria-label="WhatsApp">
        <svg class="h-7 w-7" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884"/>
        </svg>
    </a>
</div>
