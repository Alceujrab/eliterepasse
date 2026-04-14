@extends('admin.layouts.app')

@php
    $pageTitle = 'Novo template de e-mail';
    $pageSubtitle = 'Crie um template customizado com slug proprio e variaveis disponiveis.';
@endphp

@section('content')
    @if($errors->any())
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="admin-card max-w-5xl">
        <div class="admin-toolbar">
            <div class="admin-toolbar-main">
                <span class="admin-tag admin-tag-new">criacao v2</span>
                <h2 class="mt-3 admin-section-title">Novo template</h2>
                <p class="admin-section-note">Slugs devem ser unicos e usar apenas letras minusculas, numeros e underscore.</p>
            </div>
            <div class="admin-toolbar-actions">
                <a href="{{ route('admin.v2.email-templates.index') }}" class="admin-btn-soft">Voltar para lista</a>
            </div>
        </div>

        @include('admin.email-templates.partials.form', [
            'action' => route('admin.v2.email-templates.store'),
            'template' => null,
            'sampleVariables' => $sampleVariables,
            'preview' => null,
        ])
    </section>
@endsection