@extends('admin.layouts.app')

@php
    $pageTitle = $instance->nome . ' · WhatsApp';
    $pageSubtitle = 'Diagnostico, QR Code, teste de envio e configuracao da instancia Evolution.';
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
            <p class="admin-metric-label">Status</p>
            <p class="admin-metric-value text-[1.35rem]">{{ $instance->status_label }}</p>
            <p class="admin-metric-footnote">{{ $instance->status_conexao ?: 'nao verificado' }}</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Ativacao</p>
            <p class="admin-metric-value">{{ $instance->ativo ? 'Ativa' : 'Inativa' }}</p>
            <p class="admin-metric-footnote">{{ $instance->padrao ? 'Instancia padrao do sistema' : 'Instancia secundaria' }}</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Ultima verificacao</p>
            <p class="admin-metric-value">{{ $instance->verificado_em?->format('d/m/Y') ?? 'Nunca' }}</p>
            <p class="admin-metric-footnote">{{ $instance->verificado_em?->format('H:i') ?? 'Sem historico' }}</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Evolution ID</p>
            <p class="admin-metric-value font-mono text-[1.1rem]">{{ $instance->instancia }}</p>
            <p class="admin-metric-footnote">Identificador cadastrado na API</p>
        </article>
    </section>

    <section class="mt-6 admin-detail-grid">
        <div class="admin-stack">
            <section class="admin-card">
                <div class="admin-toolbar">
                    <div class="admin-toolbar-main">
                        <span class="admin-tag admin-tag-new">edicao v2</span>
                        <h2 class="mt-3 admin-section-title">Configuracao da instancia</h2>
                        <p class="admin-section-note">Ajuste nome, identificador, URL base, token e flags operacionais.</p>
                    </div>
                    <div class="admin-toolbar-actions">
                        <a href="{{ route('admin.v2.whatsapp-instancias.index') }}" class="admin-btn-soft">Voltar para lista</a>
                        <a href="/admin/whatsapp-instancias" class="admin-btn-soft">Abrir legado</a>
                    </div>
                </div>

                @include('admin.whatsapp-instancias.partials.form', [
                    'action' => route('admin.v2.whatsapp-instancias.update', $instance),
                    'instance' => $instance,
                    'qrCode' => $qrCode,
                ])
            </section>
        </div>

        <aside class="admin-stack">
            <section class="admin-card">
                <span class="admin-tag admin-tag-new">operacao</span>
                <h2 class="mt-3 admin-section-title">Diagnostico rapido</h2>
                <div class="mt-4 flex flex-wrap gap-2">
                    <form method="POST" action="{{ route('admin.v2.whatsapp-instancias.test-connection', $instance) }}">@csrf<button type="submit" class="admin-btn-primary">Testar conexao</button></form>
                    <a href="{{ route('admin.v2.whatsapp-instancias.show', [$instance, 'show_qr' => 1]) }}" class="admin-btn-soft">Gerar QR</a>
                    <form method="POST" action="{{ route('admin.v2.whatsapp-instancias.logout', $instance) }}">@csrf<button type="submit" class="admin-btn-soft">Logout</button></form>
                </div>
            </section>

            <section class="admin-card">
                <h2 class="admin-section-title">Teste de envio</h2>
                <p class="admin-section-note">Envia uma mensagem simples para validar token, URL e sessao.</p>
                <form method="POST" action="{{ route('admin.v2.whatsapp-instancias.send-test', $instance) }}" class="mt-4 admin-stack">
                    @csrf
                    <input type="text" name="telefone" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="11987654321">
                    <button type="submit" class="admin-btn-primary">Enviar teste</button>
                </form>
            </section>

            <section class="admin-card">
                <h2 class="admin-section-title">QR Code</h2>
                <p class="admin-section-note">Use para vincular o WhatsApp quando a sessao estiver desconectada.</p>
                <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-center">
                    @if($qrCode)
                        <img src="{{ $qrCode }}" alt="QR Code Evolution" class="mx-auto w-64 max-w-full rounded-xl border border-slate-200 bg-white p-2">
                    @else
                        <p class="text-sm font-semibold text-slate-500">Ainda sem QR carregado. Use o botao Gerar QR.</p>
                    @endif
                </div>
            </section>
        </aside>
    </section>
@endsection