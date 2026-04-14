@extends('admin.layouts.app')

@php
    $pageTitle = 'Nova instancia WhatsApp';
    $pageSubtitle = 'Cadastre uma nova conexao Evolution com URL, token e instancia padrao opcional.';
@endphp

@section('content')
    @if($errors->any())
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ $errors->first() }}</div>
    @endif

    <section class="admin-card max-w-5xl">
        <div class="admin-toolbar">
            <div class="admin-toolbar-main">
                <span class="admin-tag admin-tag-new">cadastro v2</span>
                <h2 class="mt-3 admin-section-title">Nova instancia</h2>
                <p class="admin-section-note">Use a URL base da Evolution e o token correto da instancia para operacoes de envio e status.</p>
            </div>
            <div class="admin-toolbar-actions">
                <a href="{{ route('admin.v2.whatsapp-instancias.index') }}" class="admin-btn-soft">Voltar para lista</a>
            </div>
        </div>

        @include('admin.whatsapp-instancias.partials.form', [
            'action' => route('admin.v2.whatsapp-instancias.store'),
            'instance' => null,
            'qrCode' => null,
        ])
    </section>
@endsection