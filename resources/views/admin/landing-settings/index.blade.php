@extends('admin.layouts.app')

@php
    $pageTitle = 'Configurações da Landing';
    $pageSubtitle = 'Edite todas as seções do site público organizado por abas.';
@endphp

@section('content')
    @if(session('admin_success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">{{ session('admin_success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">{{ $errors->first() }}</div>
    @endif

    {{-- ═══════════ ABAS DE NAVEGAÇÃO ═══════════ --}}
    <div x-data="{ tab: window.location.hash ? window.location.hash.substring(1) : 'topo' }" x-init="$watch('tab', t => window.location.hash = t)">

        <nav class="mb-6 flex flex-wrap gap-1 rounded-2xl border border-slate-200 bg-white p-1.5">
            <template x-for="item in [
                {id:'topo',   label:'Topo / Menu'},
                {id:'hero',   label:'Hero'},
                {id:'banners',label:'Banners'},
                {id:'beneficios', label:'Benefícios'},
                {id:'sobre',  label:'Sobre Nós'},
                {id:'contato',label:'Contato'},
                {id:'faq',    label:'FAQ'},
                {id:'rodape', label:'Rodapé'},
            ]">
                <button type="button"
                    @click="tab = item.id"
                    :class="tab === item.id
                        ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-md'
                        : 'text-slate-600 hover:bg-slate-100'"
                    class="rounded-xl px-4 py-2 text-sm font-semibold transition-all duration-200"
                    x-text="item.label">
                </button>
            </template>
        </nav>

        <form method="POST" action="{{ route('admin.v2.landing-settings.upsert') }}" enctype="multipart/form-data">
            @csrf

            {{-- ═══════════ ABA: TOPO / MENU ═══════════ --}}
            <div x-show="tab === 'topo'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <section class="admin-card mb-4">
                    <h2 class="admin-section-title">Logomarca</h2>
                    <p class="admin-section-note">Logo exibida no topo e no rodapé do site.</p>
                    <div class="mt-4 grid gap-4 xl:grid-cols-2">
                        <div class="admin-info-card">
                            <label class="admin-detail-label">Logo atual</label>
                            @if($setting->logo_path)
                                <div class="mt-2 mb-2">
                                    <img src="{{ asset($setting->logo_path) }}" alt="Logo" class="h-12 rounded-lg bg-slate-100 p-2">
                                </div>
                            @else
                                <p class="mt-2 text-xs text-slate-400 italic">Nenhuma logo enviada. O site usa um placeholder.</p>
                            @endif
                            <input type="file" name="logo" accept="image/*" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">
                            <p class="mt-1 text-xs text-slate-500">PNG ou SVG, fundo transparente. Máx 2 MB.</p>
                        </div>
                    </div>
                </section>

                <section class="admin-card mb-4">
                    <h2 class="admin-section-title">Itens do Menu Superior</h2>
                    <p class="admin-section-note">Links de navegação que aparecem no cabeçalho do site. Os botões "Entrar" e "Cadastre-se" são fixos.</p>
                    <div class="mt-4 grid gap-3">
                        @foreach($menuItemsRows as $index => $item)
                            <div class="grid gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3 xl:grid-cols-[40px_1fr_1fr] items-center">
                                <span class="text-center text-xs font-bold text-slate-400">{{ $index + 1 }}</span>
                                <input type="text" name="menu_items[{{ $index }}][label]" value="{{ $item['label'] ?? '' }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Texto (ex: Modelos)">
                                <input type="text" name="menu_items[{{ $index }}][url]" value="{{ $item['url'] ?? '' }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Link (ex: #modelos)">
                            </div>
                        @endforeach
                    </div>
                </section>

                <div class="flex flex-wrap gap-2 mb-6">
                    <button type="submit" class="admin-btn-primary">Salvar alterações</button>
                </div>
            </div>

            {{-- ═══════════ ABA: HERO ═══════════ --}}
            <div x-show="tab === 'hero'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <section class="admin-card mb-4">
                    <h2 class="admin-section-title">Seção Hero (Topo da Landing)</h2>
                    <p class="admin-section-note">Título principal, subtítulo e número do WhatsApp exibidos na área de destaque.</p>
                    <div class="mt-4 grid gap-4">
                        <div class="admin-info-card">
                            <label class="admin-detail-label">Título Principal</label>
                            <input type="text" name="hero_title" value="{{ old('hero_title', $setting->hero_title) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">
                            <p class="mt-1 text-xs text-slate-500">Aparece em destaque no topo da página. Ex: "Acelere sua venda de carros com a Elite Repasse"</p>
                        </div>
                        <div class="admin-info-card">
                            <label class="admin-detail-label">Subtítulo</label>
                            <textarea name="hero_subtitle" rows="2" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">{{ old('hero_subtitle', $setting->hero_subtitle) }}</textarea>
                            <p class="mt-1 text-xs text-slate-500">Texto descritivo logo abaixo do título.</p>
                        </div>
                        <div class="admin-info-card xl:w-1/2">
                            <label class="admin-detail-label">WhatsApp (botão flutuante)</label>
                            <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $setting->whatsapp_number) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="5566992184925">
                            <p class="mt-1 text-xs text-slate-500">Número com código do país + DDD, sem espaços ou traços.</p>
                        </div>
                    </div>
                </section>

                {{-- Preview do hero --}}
                <section class="admin-card mb-4">
                    <h2 class="admin-section-title">Pré-visualização do Hero</h2>
                    <div class="mt-4 rounded-[28px] bg-gradient-to-br from-slate-950 via-blue-900 to-slate-900 p-6 text-white shadow-xl">
                        <p class="text-xs font-extrabold uppercase tracking-[0.24em] text-blue-200">Plataforma B2B para lojistas</p>
                        <h3 class="mt-3 text-2xl font-black leading-tight">{{ old('hero_title', $setting->hero_title) }}</h3>
                        <p class="mt-3 text-sm font-medium leading-6 text-blue-100">{{ old('hero_subtitle', $setting->hero_subtitle) }}</p>
                        <div class="mt-4 flex gap-3">
                            <span class="rounded-full bg-blue-500 px-4 py-2 text-sm font-bold text-white">Cadastre-se agora</span>
                            <span class="rounded-full bg-white/10 px-4 py-2 text-sm font-bold text-blue-100">Já tenho conta</span>
                        </div>
                    </div>
                </section>

                <div class="flex flex-wrap gap-2 mb-6">
                    <button type="submit" class="admin-btn-primary">Salvar alterações</button>
                </div>
            </div>

            {{-- ═══════════ ABA: BANNERS ═══════════ --}}
            <div x-show="tab === 'banners'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <section class="admin-card mb-4">
                    <h2 class="admin-section-title">Imagens do Carrossel</h2>
                    <p class="admin-section-note">Fotos de veículos e destaques exibidas no slider da seção hero.</p>

                    <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                        @forelse($banners as $banner)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 overflow-hidden">
                                <img src="{{ asset('storage/' . $banner->image_path) }}" alt="{{ $banner->title ?? 'Banner' }}" class="h-36 w-full object-cover">
                                <div class="p-3">
                                    <p class="text-sm font-semibold text-slate-700">{{ $banner->title ?: 'Sem título' }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $banner->subtitle ?: '' }}</p>
                                    <div class="mt-2 flex items-center gap-2">
                                        <span class="text-xs font-semibold {{ $banner->is_active ? 'text-emerald-600' : 'text-slate-400' }}">{{ $banner->is_active ? 'Ativo' : 'Inativo' }}</span>
                                        <span class="text-xs text-slate-400">Ordem: {{ $banner->order }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="sm:col-span-2 xl:col-span-3 admin-empty-state">Nenhum banner cadastrado ainda.</div>
                        @endforelse
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('admin.v2.landing-banners.index') }}" class="admin-btn-primary">Gerenciar Banners (adicionar, editar, excluir)</a>
                    </div>
                </section>
            </div>

            {{-- ═══════════ ABA: BENEFÍCIOS ═══════════ --}}
            <div x-show="tab === 'beneficios'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <section class="admin-card mb-4">
                    <h2 class="admin-section-title">Cards de Benefícios</h2>
                    <p class="admin-section-note">Até 6 cards exibidos na seção "Por que escolher o Portal da Elite?".</p>
                    <div class="mt-4 grid gap-3">
                        @foreach($featuresRows as $index => $feature)
                            <div class="grid gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 xl:grid-cols-[40px_1fr_2fr_180px] items-start">
                                <span class="text-center text-xs font-bold text-slate-400 pt-3">{{ $index + 1 }}</span>
                                <div>
                                    <label class="text-xs font-semibold text-slate-500">Título</label>
                                    <input type="text" name="features[{{ $index }}][title]" value="{{ $feature['title'] ?? '' }}" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Ex: Diversidade de estoque">
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-slate-500">Descrição</label>
                                    <textarea name="features[{{ $index }}][description]" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" rows="2" placeholder="Ex: Acesso a veículos de repasse e frota em todo o Brasil.">{{ $feature['description'] ?? '' }}</textarea>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-slate-500">Ícone</label>
                                    <input type="text" name="features[{{ $index }}][icon]" value="{{ $feature['icon'] ?? '' }}" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Ex: truck">
                                    <p class="mt-0.5 text-[10px] text-slate-400">Lucide icons</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <div class="flex flex-wrap gap-2 mb-6">
                    <button type="submit" class="admin-btn-primary">Salvar alterações</button>
                </div>
            </div>

            {{-- ═══════════ ABA: SOBRE NÓS ═══════════ --}}
            <div x-show="tab === 'sobre'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <section class="admin-card mb-4">
                    <h2 class="admin-section-title">Seção "Sobre Nós"</h2>
                    <p class="admin-section-note">Conteúdo institucional da empresa exibido na landing.</p>
                    <div class="mt-4 grid gap-4 xl:grid-cols-2">
                        <div class="admin-info-card xl:col-span-2">
                            <label class="admin-detail-label">Título da Seção</label>
                            <input type="text" name="about_title" value="{{ old('about_title', $setting->about_title) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Sobre a Elite Repasse">
                        </div>
                        <div class="admin-info-card xl:col-span-2">
                            <label class="admin-detail-label">Texto Descritivo</label>
                            <textarea name="about_text" rows="5" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Descreva a empresa...">{{ old('about_text', $setting->about_text) }}</textarea>
                        </div>
                        <div class="admin-info-card">
                            <label class="admin-detail-label">Imagem da Seção</label>
                            @if($setting->about_image)
                                <div class="mt-2 mb-2">
                                    <img src="{{ asset($setting->about_image) }}" alt="Sobre Nós" class="h-24 rounded-lg object-cover">
                                </div>
                            @else
                                <p class="mt-2 text-xs text-slate-400 italic">Nenhuma imagem enviada.</p>
                            @endif
                            <input type="file" name="about_image" accept="image/*" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">
                        </div>
                    </div>
                </section>

                <div class="flex flex-wrap gap-2 mb-6">
                    <button type="submit" class="admin-btn-primary">Salvar alterações</button>
                </div>
            </div>

            {{-- ═══════════ ABA: CONTATO ═══════════ --}}
            <div x-show="tab === 'contato'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <section class="admin-card mb-4">
                    <h2 class="admin-section-title">Informações de Contato</h2>
                    <p class="admin-section-note">Dados exibidos na seção "Fale com a equipe Elite Repasse".</p>
                    <div class="mt-4 grid gap-4 xl:grid-cols-2">
                        <div class="admin-info-card">
                            <label class="admin-detail-label">Telefone</label>
                            <input type="text" name="contact_phone" value="{{ old('contact_phone', $setting->contact_phone) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="(66) 99218-4925">
                        </div>
                        <div class="admin-info-card">
                            <label class="admin-detail-label">E-mail</label>
                            <input type="text" name="contact_email" value="{{ old('contact_email', $setting->contact_email) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="contato@eliterepasse.com.br">
                        </div>
                        <div class="admin-info-card xl:col-span-2">
                            <label class="admin-detail-label">Endereço</label>
                            <input type="text" name="contact_address" value="{{ old('contact_address', $setting->contact_address) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Rua Exemplo, 123 - Bairro">
                        </div>
                        <div class="admin-info-card">
                            <label class="admin-detail-label">Cidade</label>
                            <input type="text" name="contact_city" value="{{ old('contact_city', $setting->contact_city) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Belo Horizonte">
                        </div>
                        <div class="admin-info-card">
                            <label class="admin-detail-label">Estado (UF)</label>
                            <input type="text" name="contact_state" value="{{ old('contact_state', $setting->contact_state) }}" maxlength="2" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="MG">
                        </div>
                    </div>
                </section>

                <section class="admin-card mb-4">
                    <h2 class="admin-section-title">Localização no Mapa</h2>
                    <p class="admin-section-note">Coordenadas para o Google Maps na landing. A chave da API fica em "Configurações Gerais".</p>
                    <div class="mt-4 grid gap-4 xl:grid-cols-2">
                        <div class="admin-info-card">
                            <label class="admin-detail-label">Latitude</label>
                            <input type="text" name="contact_lat" value="{{ old('contact_lat', $setting->contact_lat) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="-19.9167">
                        </div>
                        <div class="admin-info-card">
                            <label class="admin-detail-label">Longitude</label>
                            <input type="text" name="contact_lng" value="{{ old('contact_lng', $setting->contact_lng) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="-43.9345">
                        </div>
                    </div>

                    @if($mapsApiKey && old('contact_lat', $setting->contact_lat) && old('contact_lng', $setting->contact_lng))
                        <div class="mt-4 rounded-2xl overflow-hidden border border-slate-200">
                            <iframe width="100%" height="250" style="border:0" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                                src="https://www.google.com/maps/embed/v1/place?key={{ $mapsApiKey }}&q={{ old('contact_lat', $setting->contact_lat) }},{{ old('contact_lng', $setting->contact_lng) }}&zoom=15">
                            </iframe>
                        </div>
                    @endif
                </section>

                <div class="flex flex-wrap gap-2 mb-6">
                    <button type="submit" class="admin-btn-primary">Salvar alterações</button>
                </div>
            </div>

            {{-- ═══════════ ABA: FAQ ═══════════ --}}
            <div x-show="tab === 'faq'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <section class="admin-card mb-4">
                    <h2 class="admin-section-title">Perguntas Frequentes (FAQ)</h2>
                    <p class="admin-section-note">Até 6 perguntas e respostas na seção "Dúvidas frequentes" da landing.</p>
                    <div class="mt-4 grid gap-3">
                        @foreach($faqRows as $index => $faq)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700">{{ $index + 1 }}</span>
                                    <span class="text-xs font-semibold text-slate-500">Pergunta e Resposta #{{ $index + 1 }}</span>
                                </div>
                                <div class="grid gap-3">
                                    <input type="text" name="faq[{{ $index }}][question]" value="{{ $faq['question'] ?? '' }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Ex: Como funciona o portal?">
                                    <textarea name="faq[{{ $index }}][answer]" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" rows="3" placeholder="Ex: O lojista se cadastra, aprova o acesso e compra veículos pelo ambiente B2B.">{{ $faq['answer'] ?? '' }}</textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <div class="flex flex-wrap gap-2 mb-6">
                    <button type="submit" class="admin-btn-primary">Salvar alterações</button>
                </div>
            </div>

            {{-- ═══════════ ABA: RODAPÉ ═══════════ --}}
            <div x-show="tab === 'rodape'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <section class="admin-card mb-4">
                    <h2 class="admin-section-title">Texto do Rodapé</h2>
                    <p class="admin-section-note">Descrição institucional exibida no footer do site.</p>
                    <div class="mt-4">
                        <div class="admin-info-card">
                            <label class="admin-detail-label">Texto</label>
                            <textarea name="footer_text" rows="3" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Plataforma digital B2B para compra de seminovos...">{{ old('footer_text', $setting->footer_text) }}</textarea>
                        </div>
                    </div>
                </section>

                <section class="admin-card mb-4">
                    <h2 class="admin-section-title">Redes Sociais</h2>
                    <p class="admin-section-note">URLs dos perfis exibidos com ícones no rodapé.</p>
                    <div class="mt-4 grid gap-4 xl:grid-cols-3">
                        <div class="admin-info-card">
                            <label class="admin-detail-label">Instagram</label>
                            <input type="url" name="social_instagram" value="{{ old('social_instagram', $setting->social_instagram) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="https://instagram.com/eliterepasse">
                        </div>
                        <div class="admin-info-card">
                            <label class="admin-detail-label">Facebook</label>
                            <input type="url" name="social_facebook" value="{{ old('social_facebook', $setting->social_facebook) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="https://facebook.com/eliterepasse">
                        </div>
                        <div class="admin-info-card">
                            <label class="admin-detail-label">YouTube</label>
                            <input type="url" name="social_youtube" value="{{ old('social_youtube', $setting->social_youtube) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="https://youtube.com/@eliterepasse">
                        </div>
                    </div>
                </section>

                <section class="admin-card mb-4">
                    <h2 class="admin-section-title">Links do Rodapé</h2>
                    <p class="admin-section-note">Links rápidos exibidos na coluna "Links" do footer.</p>
                    <div class="mt-4 grid gap-3">
                        @foreach($footerLinksRows as $index => $link)
                            <div class="grid gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3 xl:grid-cols-[40px_1fr_1fr] items-center">
                                <span class="text-center text-xs font-bold text-slate-400">{{ $index + 1 }}</span>
                                <input type="text" name="footer_links[{{ $index }}][label]" value="{{ $link['label'] ?? '' }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Texto (ex: Cadastre-se)">
                                <input type="text" name="footer_links[{{ $index }}][url]" value="{{ $link['url'] ?? '' }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="URL (ex: /register)">
                            </div>
                        @endforeach
                    </div>
                </section>

                <div class="flex flex-wrap gap-2 mb-6">
                    <button type="submit" class="admin-btn-primary">Salvar alterações</button>
                </div>
            </div>

        </form>
    </div>
@endsection
@extends('admin.layouts.app')

@php
    $pageTitle = 'Configuracoes da Landing';
    $pageSubtitle = 'Edite todas as secoes do site: topo, menu, banners, sobre nos, contato, FAQ e rodape.';

    $featuresPreview = collect(old('features', $setting->features ?? []))
        ->filter(fn ($item) => filled($item['title'] ?? null) || filled($item['description'] ?? null))
        ->values();

    $faqPreview = collect(old('faq', $setting->faq ?? []))
        ->filter(fn ($item) => filled($item['question'] ?? null) || filled($item['answer'] ?? null))
        ->values();

    $menuPreview = collect(old('menu_items', $setting->menu_items ?? []))
        ->filter(fn ($item) => filled($item['label'] ?? null))
        ->values();

    $footerLinksPreview = collect(old('footer_links', $setting->footer_links ?? []))
        ->filter(fn ($item) => filled($item['label'] ?? null))
        ->values();
@endphp

@section('content')
    @if(session('admin_success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">{{ session('admin_success') }}</div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">{{ $errors->first() }}</div>
    @endif

    {{-- Metricas --}}
    <section class="admin-metrics-grid">
        <article class="admin-metric-card">
            <p class="admin-metric-label">Hero ativo</p>
            <p class="admin-metric-value text-[1.35rem]">{{ filled(old('hero_title', $setting->hero_title)) ? '1' : '0' }}</p>
            <p class="admin-metric-footnote">Titulo principal configurado</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Banners</p>
            <p class="admin-metric-value">{{ $banners->count() }}</p>
            <p class="admin-metric-footnote"><a href="{{ route('admin.v2.landing-banners.index') }}" class="text-blue-600 underline">Gerenciar banners</a></p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Menu</p>
            <p class="admin-metric-value">{{ $menuPreview->count() }}</p>
            <p class="admin-metric-footnote">Links no topo do site</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">FAQ</p>
            <p class="admin-metric-value">{{ $faqPreview->count() }}</p>
            <p class="admin-metric-footnote">Perguntas e respostas</p>
        </article>
    </section>

    <form method="POST" action="{{ route('admin.v2.landing-settings.upsert') }}" enctype="multipart/form-data" class="mt-6">
        @csrf

        {{-- TOPO / LOGO / MENU --}}
        <section class="admin-card mb-4">
            <div class="admin-toolbar">
                <div class="admin-toolbar-main">
                    <span class="admin-tag admin-tag-new">topo</span>
                    <h2 class="mt-3 admin-section-title">Logomarca & Menu Superior</h2>
                    <p class="admin-section-note">Logomarca exibida no topo do site e links do menu de navegacao.</p>
                </div>
            </div>

            <div class="mt-5 grid gap-4 xl:grid-cols-2">
                <div class="admin-info-card">
                    <label class="admin-detail-label">Logo do Site</label>
                    @if($setting->logo_path)
                        <div class="mt-2 mb-2">
                            <img src="{{ asset($setting->logo_path) }}" alt="Logo atual" class="h-12 rounded-lg bg-slate-100 p-2">
                        </div>
                    @endif
                    <input type="file" name="logo" accept="image/*" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">
                    <p class="mt-1 text-xs text-slate-500">PNG ou SVG, fundo transparente. Max 2 MB.</p>
                </div>
                <div></div>

                <div class="admin-info-card xl:col-span-2">
                    <label class="admin-detail-label">Itens do Menu (ate 8)</label>
                    <p class="mb-2 text-xs text-slate-500">Links de navegacao que aparecem no topo do site.</p>
                    <div class="grid gap-3">
                        @foreach($menuItemsRows as $index => $item)
                            <div class="grid gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3 xl:grid-cols-[1fr_1fr]">
                                <input type="text" name="menu_items[{{ $index }}][label]" value="{{ $item['label'] ?? '' }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Texto do link (ex: Modelos)">
                                <input type="text" name="menu_items[{{ $index }}][url]" value="{{ $item['url'] ?? '' }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="URL (ex: #modelos ou /pagina)">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- HERO / BANNER --}}
        <section class="admin-card mb-4">
            <div class="admin-toolbar">
                <div class="admin-toolbar-main">
                    <span class="admin-tag admin-tag-new">hero</span>
                    <h2 class="mt-3 admin-section-title">Hero / Secao Principal</h2>
                    <p class="admin-section-note">Titulo, subtitulo e WhatsApp exibidos na parte superior da landing.</p>
                </div>
                <div class="admin-toolbar-actions">
                    <a href="/" target="_blank" class="admin-btn-primary">Ver landing publica</a>
                </div>
            </div>

            <div class="mt-5 grid gap-4 xl:grid-cols-2">
                <div class="admin-info-card xl:col-span-2">
                    <label class="admin-detail-label">Hero title</label>
                    <input type="text" name="hero_title" value="{{ old('hero_title', $setting->hero_title) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">
                </div>
                <div class="admin-info-card xl:col-span-2">
                    <label class="admin-detail-label">Hero subtitle</label>
                    <input type="text" name="hero_subtitle" value="{{ old('hero_subtitle', $setting->hero_subtitle) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">
                </div>
                <div class="admin-info-card xl:col-span-2">
                    <label class="admin-detail-label">WhatsApp number</label>
                    <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $setting->whatsapp_number) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="5511999999999">
                </div>
            </div>

            <div class="mt-4 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3">
                <p class="text-sm font-semibold text-blue-800">Banners / Imagens do Carrossel</p>
                <p class="mt-1 text-sm text-blue-700">Os banners de imagens sao gerenciados em uma pagina separada. Voce tem <strong>{{ $banners->count() }}</strong> banner(s) cadastrado(s).</p>
                <a href="{{ route('admin.v2.landing-banners.index') }}" class="mt-2 inline-block admin-btn-primary text-xs">Gerenciar Banners</a>
            </div>
        </section>

        {{-- BENEFICIOS --}}
        <section class="admin-card mb-4">
            <span class="admin-tag admin-tag-new">vantagens</span>
            <h2 class="mt-3 admin-section-title">Beneficios</h2>
            <p class="admin-section-note">Use ate 6 cards para o bloco "Por que escolher o Portal da Elite?".</p>
            <div class="mt-4 grid gap-3">
                @foreach($featuresRows as $index => $feature)
                    <div class="grid gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 xl:grid-cols-[1fr_2fr_180px]">
                        <input type="text" name="features[{{ $index }}][title]" value="{{ $feature['title'] ?? '' }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Titulo do beneficio">
                        <textarea name="features[{{ $index }}][description]" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" rows="2" placeholder="Descricao do beneficio">{{ $feature['description'] ?? '' }}</textarea>
                        <input type="text" name="features[{{ $index }}][icon]" value="{{ $feature['icon'] ?? '' }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Icone (ex: truck)">
                    </div>
                @endforeach
            </div>
        </section>

        {{-- SOBRE NOS --}}
        <section class="admin-card mb-4">
            <span class="admin-tag admin-tag-new">sobre</span>
            <h2 class="mt-3 admin-section-title">Sobre Nos</h2>
            <p class="admin-section-note">Secao institucional sobre a empresa.</p>
            <div class="mt-4 grid gap-4 xl:grid-cols-2">
                <div class="admin-info-card xl:col-span-2">
                    <label class="admin-detail-label">Titulo</label>
                    <input type="text" name="about_title" value="{{ old('about_title', $setting->about_title) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Sobre a Elite Repasse">
                </div>
                <div class="admin-info-card xl:col-span-2">
                    <label class="admin-detail-label">Texto</label>
                    <textarea name="about_text" rows="4" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Descricao da empresa...">{{ old('about_text', $setting->about_text) }}</textarea>
                </div>
                <div class="admin-info-card">
                    <label class="admin-detail-label">Imagem (Sobre Nos)</label>
                    @if($setting->about_image)
                        <div class="mt-2 mb-2">
                            <img src="{{ asset($setting->about_image) }}" alt="Sobre Nos" class="h-20 rounded-lg">
                        </div>
                    @endif
                    <input type="file" name="about_image" accept="image/*" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">
                </div>
            </div>
        </section>

        {{-- CONTATO / MAPA --}}
        <section class="admin-card mb-4">
            <span class="admin-tag admin-tag-new">contato</span>
            <h2 class="mt-3 admin-section-title">Contato & Localizacao</h2>
            <p class="admin-section-note">Informacoes de contato e localizacao da loja exibidas na landing. A chave do Google Maps esta em Configuracoes Gerais.</p>
            <div class="mt-4 grid gap-4 xl:grid-cols-2">
                <div class="admin-info-card">
                    <label class="admin-detail-label">Telefone</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $setting->contact_phone) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="(66) 99218-4925">
                </div>
                <div class="admin-info-card">
                    <label class="admin-detail-label">E-mail</label>
                    <input type="text" name="contact_email" value="{{ old('contact_email', $setting->contact_email) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="contato@eliterepasse.com.br">
                </div>
                <div class="admin-info-card xl:col-span-2">
                    <label class="admin-detail-label">Endereco</label>
                    <input type="text" name="contact_address" value="{{ old('contact_address', $setting->contact_address) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Rua Exemplo, 123 - Bairro">
                </div>
                <div class="admin-info-card">
                    <label class="admin-detail-label">Cidade</label>
                    <input type="text" name="contact_city" value="{{ old('contact_city', $setting->contact_city) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Belo Horizonte">
                </div>
                <div class="admin-info-card">
                    <label class="admin-detail-label">Estado (UF)</label>
                    <input type="text" name="contact_state" value="{{ old('contact_state', $setting->contact_state) }}" maxlength="2" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="MG">
                </div>
                <div class="admin-info-card">
                    <label class="admin-detail-label">Latitude</label>
                    <input type="text" name="contact_lat" value="{{ old('contact_lat', $setting->contact_lat) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="-19.9167">
                </div>
                <div class="admin-info-card">
                    <label class="admin-detail-label">Longitude</label>
                    <input type="text" name="contact_lng" value="{{ old('contact_lng', $setting->contact_lng) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="-43.9345">
                </div>
            </div>

            @if($mapsApiKey && old('contact_lat', $setting->contact_lat) && old('contact_lng', $setting->contact_lng))
                <div class="mt-4 rounded-2xl overflow-hidden border border-slate-200">
                    <iframe
                        width="100%" height="250" style="border:0"
                        loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                        src="https://www.google.com/maps/embed/v1/place?key={{ $mapsApiKey }}&q={{ old('contact_lat', $setting->contact_lat) }},{{ old('contact_lng', $setting->contact_lng) }}&zoom=15">
                    </iframe>
                </div>
            @endif
        </section>

        {{-- FAQ --}}
        <section class="admin-card mb-4">
            <span class="admin-tag admin-tag-new">faq</span>
            <h2 class="mt-3 admin-section-title">FAQ</h2>
            <p class="admin-section-note">Use ate 6 perguntas frequentes na landing.</p>
            <div class="mt-4 grid gap-3">
                @foreach($faqRows as $index => $faq)
                    <div class="grid gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <input type="text" name="faq[{{ $index }}][question]" value="{{ $faq['question'] ?? '' }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Pergunta">
                        <textarea name="faq[{{ $index }}][answer]" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" rows="3" placeholder="Resposta">{{ $faq['answer'] ?? '' }}</textarea>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- RODAPE --}}
        <section class="admin-card mb-4">
            <span class="admin-tag admin-tag-new">rodape</span>
            <h2 class="mt-3 admin-section-title">Rodape</h2>
            <p class="admin-section-note">Texto, links e redes sociais exibidos no rodape do site.</p>
            <div class="mt-4 grid gap-4 xl:grid-cols-2">
                <div class="admin-info-card xl:col-span-2">
                    <label class="admin-detail-label">Texto do Rodape</label>
                    <textarea name="footer_text" rows="2" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Plataforma digital B2B para compra de seminovos...">{{ old('footer_text', $setting->footer_text) }}</textarea>
                </div>

                <div class="admin-info-card">
                    <label class="admin-detail-label">Instagram (URL)</label>
                    <input type="text" name="social_instagram" value="{{ old('social_instagram', $setting->social_instagram) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="https://instagram.com/eliterepasse">
                </div>
                <div class="admin-info-card">
                    <label class="admin-detail-label">Facebook (URL)</label>
                    <input type="text" name="social_facebook" value="{{ old('social_facebook', $setting->social_facebook) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="https://facebook.com/eliterepasse">
                </div>
                <div class="admin-info-card xl:col-span-2">
                    <label class="admin-detail-label">YouTube (URL)</label>
                    <input type="text" name="social_youtube" value="{{ old('social_youtube', $setting->social_youtube) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="https://youtube.com/@eliterepasse">
                </div>

                <div class="admin-info-card xl:col-span-2">
                    <label class="admin-detail-label">Links do Rodape (ate 6)</label>
                    <div class="mt-2 grid gap-3">
                        @foreach($footerLinksRows as $index => $link)
                            <div class="grid gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3 xl:grid-cols-[1fr_1fr]">
                                <input type="text" name="footer_links[{{ $index }}][label]" value="{{ $link['label'] ?? '' }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Texto do link">
                                <input type="text" name="footer_links[{{ $index }}][url]" value="{{ $link['url'] ?? '' }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="URL do link">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <div class="flex flex-wrap gap-2 mb-6">
            <button type="submit" class="admin-btn-primary">Salvar todas as configuracoes</button>
        </div>
    </form>
@endsection
@extends('admin.layouts.app')

@php
    $pageTitle = 'Configuracoes da Landing';
    $pageSubtitle = 'Edite o hero, beneficios, FAQ e o WhatsApp usado pela home publica.';

    $featuresPreview = collect(old('features', $setting->features ?? []))
        ->filter(fn ($item) => filled($item['title'] ?? null) || filled($item['description'] ?? null))
        ->values();

    $faqPreview = collect(old('faq', $setting->faq ?? []))
        ->filter(fn ($item) => filled($item['question'] ?? null) || filled($item['answer'] ?? null))
        ->values();
@endphp

@section('content')
    @if(session('admin_success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">{{ session('admin_success') }}</div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">{{ $errors->first() }}</div>
    @endif

    <section class="admin-metrics-grid">
        <article class="admin-metric-card">
            <p class="admin-metric-label">Hero ativo</p>
            <p class="admin-metric-value text-[1.35rem]">{{ filled(old('hero_title', $setting->hero_title)) ? '1' : '0' }}</p>
            <p class="admin-metric-footnote">Titulo principal configurado</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Beneficios</p>
            <p class="admin-metric-value">{{ number_format($featuresPreview->count()) }}</p>
            <p class="admin-metric-footnote">Cards informativos da landing</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">FAQ</p>
            <p class="admin-metric-value">{{ number_format($faqPreview->count()) }}</p>
            <p class="admin-metric-footnote">Perguntas e respostas publicas</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">WhatsApp</p>
            <p class="admin-metric-value text-[1.1rem]">{{ old('whatsapp_number', $setting->whatsapp_number) ?: 'Nao definido' }}</p>
            <p class="admin-metric-footnote">Botao flutuante da landing</p>
        </article>
    </section>

    <section class="mt-6 admin-detail-grid">
        <div class="admin-stack">
            <section class="admin-card">
                <div class="admin-toolbar">
                    <div class="admin-toolbar-main">
                        <span class="admin-tag admin-tag-new">conteudo v2</span>
                        <h2 class="mt-3 admin-section-title">Edicao da landing</h2>
                        <p class="admin-section-note">Ajuste os textos e blocos que aparecem na home publica antes do cadastro do lojista.</p>
                    </div>
                    <div class="admin-toolbar-actions">
                        <a href="/" target="_blank" class="admin-btn-primary">Ver landing publica</a>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.v2.landing-settings.upsert') }}" class="mt-5 admin-stack">
                    @csrf

                    <div class="grid gap-4 xl:grid-cols-2">
                        <div class="admin-info-card xl:col-span-2">
                            <label class="admin-detail-label">Hero title</label>
                            <input type="text" name="hero_title" value="{{ old('hero_title', $setting->hero_title) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">
                        </div>
                        <div class="admin-info-card xl:col-span-2">
                            <label class="admin-detail-label">Hero subtitle</label>
                            <input type="text" name="hero_subtitle" value="{{ old('hero_subtitle', $setting->hero_subtitle) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">
                        </div>
                        <div class="admin-info-card xl:col-span-2">
                            <label class="admin-detail-label">WhatsApp number</label>
                            <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $setting->whatsapp_number) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="5511999999999">
                        </div>
                    </div>

                    <section class="admin-card !p-4">
                        <h3 class="admin-section-title">Beneficios</h3>
                        <p class="admin-section-note">Use ate 6 cards para o bloco “Por que escolher o Portal da Elite?”.</p>
                        <div class="mt-4 admin-stack">
                            @foreach($featuresRows as $index => $feature)
                                <div class="grid gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 xl:grid-cols-[1fr_2fr_180px]">
                                    <input type="text" name="features[{{ $index }}][title]" value="{{ $feature['title'] ?? '' }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Titulo do beneficio">
                                    <textarea name="features[{{ $index }}][description]" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" rows="2" placeholder="Descricao do beneficio">{{ $feature['description'] ?? '' }}</textarea>
                                    <input type="text" name="features[{{ $index }}][icon]" value="{{ $feature['icon'] ?? '' }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Icone">
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <section class="admin-card !p-4">
                        <h3 class="admin-section-title">FAQ</h3>
                        <p class="admin-section-note">Use ate 6 perguntas frequentes na landing.</p>
                        <div class="mt-4 admin-stack">
                            @foreach($faqRows as $index => $faq)
                                <div class="grid gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <input type="text" name="faq[{{ $index }}][question]" value="{{ $faq['question'] ?? '' }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Pergunta">
                                    <textarea name="faq[{{ $index }}][answer]" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" rows="3" placeholder="Resposta">{{ $faq['answer'] ?? '' }}</textarea>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <div class="flex flex-wrap gap-2">
                        <button type="submit" class="admin-btn-primary">Salvar configuracoes</button>
                    </div>
                </form>
            </section>
        </div>

        <aside class="admin-stack">
            <section class="admin-card">
                <span class="admin-tag admin-tag-new">preview</span>
                <h2 class="mt-3 admin-section-title">Hero publico</h2>
                <div class="mt-4 rounded-[28px] bg-gradient-to-br from-slate-950 via-blue-900 to-slate-900 p-6 text-white shadow-xl">
                    <p class="text-xs font-extrabold uppercase tracking-[0.24em] text-blue-200">Elite Repasse B2B</p>
                    <h3 class="mt-3 text-2xl font-black leading-tight">{{ old('hero_title', $setting->hero_title) }}</h3>
                    <p class="mt-3 text-sm font-medium leading-6 text-blue-100">{{ old('hero_subtitle', $setting->hero_subtitle) }}</p>
                    <div class="mt-5 inline-flex rounded-full bg-white/10 px-4 py-2 text-sm font-bold text-emerald-200">WA {{ old('whatsapp_number', $setting->whatsapp_number) }}</div>
                </div>
            </section>

            <section class="admin-card">
                <h2 class="admin-section-title">Beneficios em destaque</h2>
                <div class="mt-4 admin-stack">
                    @forelse($featuresPreview as $feature)
                        <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="admin-row-title">{{ $feature['title'] ?? 'Sem titulo' }}</div>
                            <div class="admin-row-meta mt-1">Icone: {{ $feature['icon'] ?? 'padrao' }}</div>
                            <p class="mt-2 text-sm font-medium text-slate-600">{{ $feature['description'] ?? '' }}</p>
                        </article>
                    @empty
                        <div class="admin-empty-state">Nenhum beneficio preenchido ainda.</div>
                    @endforelse
                </div>
            </section>

            <section class="admin-card">
                <h2 class="admin-section-title">FAQ atual</h2>
                <div class="mt-4 admin-stack">
                    @forelse($faqPreview as $faq)
                        <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="admin-row-title">{{ $faq['question'] ?? 'Sem pergunta' }}</div>
                            <p class="mt-2 text-sm font-medium text-slate-600">{{ $faq['answer'] ?? '' }}</p>
                        </article>
                    @empty
                        <div class="admin-empty-state">Nenhuma pergunta frequente preenchida ainda.</div>
                    @endforelse
                </div>
            </section>
        </aside>
    </section>
@endsection