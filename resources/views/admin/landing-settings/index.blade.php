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
                        <a href="/admin/landing-settings" class="admin-btn-soft">Abrir legado</a>
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