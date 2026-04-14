@extends('admin.layouts.app')

@php
    $pageTitle = $emailTemplate->nome . ' · Template';
    $pageSubtitle = 'Edicao operacional com preview de variaveis e geracao assistida do corpo do e-mail.';
@endphp

@section('content')
    @if(session('admin_success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">{{ session('admin_success') }}</div>
    @endif

    @if(session('admin_warning'))
        <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700">{{ session('admin_warning') }}</div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ $errors->first() }}</div>
    @endif

    <section class="admin-summary-grid">
        <article class="admin-metric-card">
            <p class="admin-metric-label">Slug</p>
            <p class="admin-metric-value text-[1.35rem] font-mono">{{ $emailTemplate->slug }}</p>
            <p class="admin-metric-footnote">{{ $emailTemplate->isSystemTemplate() ? 'Slug de sistema protegido' : 'Slug customizado' }}</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Status</p>
            <p class="admin-metric-value">{{ $emailTemplate->ativo ? 'Ativo' : 'Inativo' }}</p>
            <p class="admin-metric-footnote">{{ $emailTemplate->ativo ? 'Sera usado nas notificacoes' : 'Codigo fara fallback' }}</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Variaveis</p>
            <p class="admin-metric-value">{{ number_format(count($emailTemplate->variaveis_disponiveis ?? [])) }}</p>
            <p class="admin-metric-footnote">Marcadores disponiveis no preview e na geracao com IA</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Atualizado</p>
            <p class="admin-metric-value">{{ $emailTemplate->updated_at?->format('d/m/Y') }}</p>
            <p class="admin-metric-footnote">{{ $emailTemplate->updated_at?->format('H:i') }}</p>
        </article>
    </section>

    <section class="mt-6 admin-detail-grid">
        <div class="admin-stack">
            <section class="admin-card">
                <div class="admin-toolbar">
                    <div class="admin-toolbar-main">
                        <span class="admin-tag admin-tag-new">editor v2</span>
                        <h2 class="mt-3 admin-section-title">Conteudo do template</h2>
                        <p class="admin-section-note">Edite assunto, corpo, CTA e variaveis mantendo os slugs de sistema congelados.</p>
                    </div>
                    <div class="admin-toolbar-actions">
                        <a href="{{ route('admin.v2.email-templates.index') }}" class="admin-btn-soft">Voltar para lista</a>
                        <a href="/admin/email-templates/{{ $emailTemplate->id }}/edit" class="admin-btn-soft">Abrir legado</a>
                    </div>
                </div>

                @include('admin.email-templates.partials.form', [
                    'action' => route('admin.v2.email-templates.update', $emailTemplate),
                    'template' => $emailTemplate,
                    'sampleVariables' => $sampleVariables,
                    'preview' => $preview,
                ])
            </section>
        </div>

        <aside class="admin-stack">
            <section class="admin-card">
                <span class="admin-tag admin-tag-new">geracao assistida</span>
                <h2 class="mt-3 admin-section-title">Gemini</h2>
                <p class="admin-section-note">Gera um novo corpo com base na descricao funcional e nas variaveis do template.</p>

                <form method="POST" action="{{ route('admin.v2.email-templates.generate-ai', $emailTemplate) }}" class="mt-4 admin-stack">
                    @csrf
                    <textarea name="descricao" rows="5" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Descreva o email que deseja gerar."></textarea>
                    <button type="submit" class="admin-btn-primary">Gerar com IA</button>
                </form>
            </section>

            <section class="admin-card">
                <h2 class="admin-section-title">Preview renderizado</h2>
                <p class="admin-section-note">Simulacao usando valores de exemplo gerados a partir das variaveis disponiveis.</p>

                <div class="mt-4 admin-stack text-sm text-slate-600">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="admin-detail-label">Assunto</div>
                        <div class="admin-detail-value">{{ $preview['assunto'] ?: 'Sem assunto' }}</div>
                    </div>
                    @if($preview['saudacao'])
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">{{ $preview['saudacao'] }}</div>
                    @endif
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 whitespace-pre-line">{{ $preview['corpo'] }}</div>
                    @if($preview['texto_acao'] || $preview['url_acao'])
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="admin-detail-label">CTA</div>
                            <div class="admin-detail-value">{{ $preview['texto_acao'] ?: 'Sem texto de acao' }}</div>
                            <div class="admin-row-meta break-all">{{ $preview['url_acao'] ?: 'Sem URL' }}</div>
                        </div>
                    @endif
                    @if($preview['texto_rodape'])
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 whitespace-pre-line">{{ $preview['texto_rodape'] }}</div>
                    @endif
                </div>
            </section>
        </aside>
    </section>
@endsection