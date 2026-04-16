@php
    $vantagens = collect($settings->features ?? [])->filter(fn ($item) => filled($item['title'] ?? null))->values();

    if ($vantagens->isEmpty()) {
        $vantagens = collect([
            [
                'title' => 'Mix de cores e modelos',
                'description' => 'Estoque com alta rotatividade para reposicao de patio com mais margem.',
            ],
            [
                'title' => 'Gestao de compras',
                'description' => 'Fluxo de compra com acompanhamento de pedido, documentos e pagamentos.',
            ],
            [
                'title' => 'Contratos digitais',
                'description' => 'Assinatura online e acompanhamento de cada etapa sem burocracia.',
            ],
        ]);
    }

    $faqItems = collect($settings->faq ?? [])->filter(fn ($item) => filled($item['question'] ?? null))->values();

    if ($faqItems->isEmpty()) {
        $faqItems = collect([
            [
                'question' => 'Quais empresas podem comprar no portal?',
                'answer' => 'O acesso e destinado a revendas de veiculos com CNPJ ativo e analise de cadastro.',
            ],
            [
                'question' => 'Consigo fazer toda a compra online?',
                'answer' => 'Sim. Da selecao do veiculo ate documentos e financeiro, o processo e 100% digital.',
            ],
            [
                'question' => 'Como acompanho meus pedidos?',
                'answer' => 'Depois do login voce visualiza status, documentos, contratos e detalhes de entrega em um painel unico.',
            ],
            [
                'question' => 'Pessoa fisica pode comprar no portal?',
                'answer' => 'Nao. O portal e focado no mercado B2B para lojistas e revendedores.',
            ],
        ]);
    }

    $modelos = [
        ['nome' => 'Fiat Uno', 'tipo' => 'Hatch', 'destaque' => null, 'imagem' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?q=80&w=1600&auto=format&fit=crop'],
        ['nome' => 'Ford Ka', 'tipo' => 'Hatch', 'destaque' => 'Hatch popular', 'imagem' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=1600&auto=format&fit=crop'],
        ['nome' => 'Chevrolet Onix', 'tipo' => 'Hatch', 'destaque' => 'Alta liquidez', 'imagem' => 'https://images.unsplash.com/photo-1553440569-bcc63803a83d?q=80&w=1600&auto=format&fit=crop'],
        ['nome' => 'Volkswagen T-Cross', 'tipo' => 'SUV', 'destaque' => null, 'imagem' => 'https://images.unsplash.com/photo-1519641471654-76ce0107ad1b?q=80&w=1600&auto=format&fit=crop'],
        ['nome' => 'Jeep Renegade', 'tipo' => 'SUV', 'destaque' => 'SUV em alta', 'imagem' => 'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?q=80&w=1600&auto=format&fit=crop'],
        ['nome' => 'Fiat Toro', 'tipo' => 'Picape', 'destaque' => null, 'imagem' => 'https://images.unsplash.com/photo-1590362891991-f776e747a588?q=80&w=1600&auto=format&fit=crop'],
    ];

    $etapas = [
        ['titulo' => 'Faca seu cadastro', 'descricao' => 'Cadastre sua empresa e representantes para analise de acesso.'],
        ['titulo' => 'Acesse o portal', 'descricao' => 'Entre na plataforma para visualizar ofertas e condicoes comerciais.'],
        ['titulo' => 'Escolha seus carros', 'descricao' => 'Use filtros por marca, faixa de preco, ano, combustivel e mais.'],
        ['titulo' => 'Compre online', 'descricao' => 'Feche negocio com seguranca e acompanhe pedidos em tempo real.'],
    ];

    $modulosGestao = [
        [
            'titulo' => 'Acompanhe seus pedidos',
            'descricao' => 'Linha do tempo de status, contratos e documentos em cada compra.',
            'imagem' => 'https://images.unsplash.com/photo-1556745757-8d76bdb6984b?q=80&w=1600&auto=format&fit=crop',
        ],
        [
            'titulo' => 'Consulte dados do veiculo',
            'descricao' => 'Acesse laudos, historico e informacoes essenciais antes da decisao final.',
            'imagem' => 'https://images.unsplash.com/photo-1580273916550-e323be2ae537?q=80&w=1600&auto=format&fit=crop',
        ],
        [
            'titulo' => 'Financeiro sem friccao',
            'descricao' => 'Visualize cobrancas, segunda via e detalhes de pagamento no mesmo painel.',
            'imagem' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?q=80&w=1600&auto=format&fit=crop',
        ],
    ];
@endphp

<div x-data="{ menuOpen: false, faqOpen: 0 }" class="relative min-h-screen overflow-x-hidden bg-white text-slate-900">
    <div class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(circle_at_85%_10%,rgba(249,115,22,0.14),transparent_35%),radial-gradient(circle_at_15%_20%,rgba(31,90,124,0.18),transparent_40%)]"></div>

    <header class="fixed inset-x-0 top-0 z-50 border-b border-white/20 bg-[#0f2f4d]/90 backdrop-blur-xl">
        <div class="page-container">
            <div class="flex h-20 items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ asset('build/assets/logo.png') }}" class="h-10" alt="Elite Repasse" onerror="this.src='https://placehold.co/220x60/1f5a7c/ffffff?text=Elite+Repasse'">
                    <span class="hidden text-sm font-bold uppercase tracking-[0.2em] text-blue-100 md:inline">Portal do Lojista</span>
                </a>

                <nav class="hidden items-center gap-7 lg:flex">
                    <a href="#modelos" class="text-sm font-bold uppercase tracking-wide text-blue-100 transition hover:text-white">Modelos</a>
                    <a href="#vantagens" class="text-sm font-bold uppercase tracking-wide text-blue-100 transition hover:text-white">Vantagens</a>
                    <a href="#como-funciona" class="text-sm font-bold uppercase tracking-wide text-blue-100 transition hover:text-white">Como Funciona</a>
                    <a href="#faq" class="text-sm font-bold uppercase tracking-wide text-blue-100 transition hover:text-white">FAQ</a>
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
                    <a @click="menuOpen=false" href="#modelos" class="rounded-xl bg-white/5 px-4 py-3 text-sm font-semibold text-white">Modelos</a>
                    <a @click="menuOpen=false" href="#vantagens" class="rounded-xl bg-white/5 px-4 py-3 text-sm font-semibold text-white">Vantagens</a>
                    <a @click="menuOpen=false" href="#como-funciona" class="rounded-xl bg-white/5 px-4 py-3 text-sm font-semibold text-white">Como Funciona</a>
                    <a @click="menuOpen=false" href="#faq" class="rounded-xl bg-white/5 px-4 py-3 text-sm font-semibold text-white">FAQ</a>
                    <a href="{{ route('login') }}" class="rounded-xl border border-white/30 px-4 py-3 text-center text-sm font-bold text-white">Entrar</a>
                    <a href="{{ route('register') }}" class="rounded-xl bg-orange-500 px-4 py-3 text-center text-sm font-black text-white">Cadastre-se</a>
                </div>
            </div>
        </div>
    </header>

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
                        {{ $settings->hero_title ?? 'Acelere o giro da sua loja com um portal de repasse inteligente' }}
                    </h1>
                    <p class="mt-5 max-w-2xl text-lg font-medium leading-relaxed text-blue-100 sm:text-xl">
                        {{ $settings->hero_subtitle ?? 'Compre seminovos com seguranca, compare oportunidades e acompanhe cada etapa da compra em um unico ambiente digital.' }}
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-2xl bg-orange-500 px-7 py-4 text-base font-black text-white shadow-lg shadow-orange-900/40 transition hover:translate-y-[-1px] hover:bg-orange-600">
                            Cadastre-se agora
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/40 bg-white/10 px-7 py-4 text-base font-bold text-white transition hover:bg-white/20">
                            Ja tenho conta
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

                <div class="relative">
                    <div class="overflow-hidden rounded-[28px] border border-white/20 bg-white/10 p-3 shadow-2xl backdrop-blur">
                        <div class="overflow-hidden rounded-[22px] bg-slate-100">
                            <img src="https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?q=80&w=1800&auto=format&fit=crop" alt="Veiculos em exposicao" class="h-[420px] w-full object-cover">
                        </div>
                    </div>
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

    <section id="modelos" class="py-20 sm:py-24">
        <div class="page-container">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-[#1f5a7c]">Estoque em destaque</p>
                <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Diversos modelos para acelerar o seu negocio</h2>
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

    <section id="vantagens" class="border-y border-slate-200 bg-slate-50 py-16">
        <div class="page-container">
            <div class="grid gap-5 md:grid-cols-3">
                <article class="rounded-2xl border border-slate-200 bg-white p-6">
                    <h3 class="text-xl font-black text-slate-900">Laudo disponivel</h3>
                    <p class="mt-3 text-slate-600">Documentacao e informacoes tecnicas para consulta antes da compra.</p>
                </article>
                <article class="rounded-2xl border border-slate-200 bg-white p-6">
                    <h3 class="text-xl font-black text-slate-900">Quilometragem real</h3>
                    <p class="mt-3 text-slate-600">Transparencia nos dados para tomada de decisao com mais seguranca.</p>
                </article>
                <article class="rounded-2xl border border-slate-200 bg-white p-6">
                    <h3 class="text-xl font-black text-slate-900">Preco abaixo da FIPE</h3>
                    <p class="mt-3 text-slate-600">Melhor oportunidade de compra com margem potencial para revenda.</p>
                </article>
            </div>
        </div>
    </section>

    <section id="como-funciona" class="py-20 sm:py-24">
        <div class="page-container">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-[#1f5a7c]">Como funciona</p>
                <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Fluxo rapido para voce comprar melhor</h2>
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
                <a href="{{ route('register') }}" class="btn-cta-md">Comecar agora</a>
            </div>
        </div>
    </section>

    <section class="bg-[#0f2f4d] py-20 text-white sm:py-24">
        <div class="page-container">
            <div class="mb-10 text-center">
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-blue-200">Gestao completa</p>
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
                <p class="mt-2 text-orange-100">Suporte comercial e operacional para garantir uma jornada simples do inicio ao fim.</p>
            </article>
        </div>
    </section>

    <section id="faq" class="py-20 sm:py-24">
        <div class="page-container">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-[#1f5a7c]">Duvidas frequentes</p>
                <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Respostas rapidas para comecar com seguranca</h2>
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

            <article class="mx-auto mt-12 max-w-4xl rounded-2xl border border-slate-200 bg-slate-50 p-6 text-center sm:p-8">
                <h3 class="text-xl font-black text-slate-900 sm:text-2xl">Nao possui CNPJ e busca um seminovo?</h3>
                <p class="mt-3 text-slate-600">Este portal e exclusivo para empresas revendedoras. Para compra de varejo, acesse nosso estoque publico.</p>
                <a href="https://seminovos.localiza.com/" target="_blank" class="mt-5 inline-flex rounded-xl border border-[#1f5a7c] px-5 py-3 text-sm font-black uppercase tracking-wide text-[#1f5a7c] transition hover:bg-[#1f5a7c] hover:text-white">Ver estoque para pessoa fisica</a>
            </article>
        </div>
    </section>

    <section class="bg-gradient-to-r from-[#1f5a7c] to-[#10395f] py-16 text-white sm:py-20">
        <div class="page-container text-center">
            <h2 class="text-3xl font-black tracking-tight sm:text-4xl">Pronto para ampliar seu catalogo com mais margem?</h2>
            <p class="mx-auto mt-4 max-w-2xl text-blue-100">Cadastre sua empresa e ganhe acesso ao portal com oportunidades para acelerar seu giro.</p>
            <div class="mt-7 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ route('register') }}" class="rounded-2xl bg-orange-500 px-8 py-4 text-base font-black text-white shadow-lg shadow-orange-900/35 transition hover:bg-orange-600">Cadastre-se agora</a>
                <a href="{{ route('login') }}" class="rounded-2xl border border-white/40 bg-white/10 px-8 py-4 text-base font-bold text-white transition hover:bg-white/20">Entrar no portal</a>
            </div>
        </div>
    </section>

    <footer class="bg-[#0b1b2a] py-12 text-slate-300">
        <div class="page-container">
            <div class="grid gap-8 md:grid-cols-3">
                <div>
                    <img src="{{ asset('build/assets/logo.png') }}" class="h-9 opacity-80" alt="Elite Repasse" onerror="this.src='https://placehold.co/220x60/0b1b2a/cbd5e1?text=Elite+Repasse'">
                    <p class="mt-4 max-w-sm text-sm text-slate-400">Plataforma digital B2B para compra de seminovos com eficiencia operacional para lojistas.</p>
                </div>
                <div>
                    <h3 class="text-sm font-black uppercase tracking-[0.14em] text-slate-200">Contato</h3>
                    <p class="mt-3 text-sm">Suporte comercial para revendas de veiculos</p>
                    <p class="mt-1 text-sm">Belo Horizonte - MG</p>
                </div>
                <div>
                    <h3 class="text-sm font-black uppercase tracking-[0.14em] text-slate-200">Links</h3>
                    <div class="mt-3 flex flex-col gap-2 text-sm">
                        <a href="#faq" class="transition hover:text-white">Perguntas frequentes</a>
                        <a href="{{ route('login') }}" class="transition hover:text-white">Entrar</a>
                        <a href="{{ route('register') }}" class="transition hover:text-white">Cadastre-se</a>
                    </div>
                </div>
            </div>
            <div class="mt-8 border-t border-slate-700 pt-6 text-sm text-slate-500">
                © {{ date('Y') }} Elite Repasse. Todos os direitos reservados.
            </div>
        </div>
    </footer>

    <a href="https://wa.me/{{ $settings->whatsapp_number ?? '5511999999999' }}" target="_blank" class="fixed bottom-6 right-6 z-50 inline-flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-2xl transition hover:scale-105" aria-label="WhatsApp">
        <svg class="h-7 w-7" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884"/>
        </svg>
    </a>
</div>
