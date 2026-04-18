@extends('admin.layouts.app')

@php
    $pageTitle = 'Editar · ' . ($client->razao_social ?? $client->nome_fantasia ?? $client->name);
    $pageSubtitle = 'Atualize os dados cadastrais do lojista.';
@endphp

@section('content')
    @if(session('admin_success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">{{ session('admin_success') }}</div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="admin-card mb-6">
        <div class="admin-toolbar">
            <div class="admin-toolbar-main">
                <span class="admin-tag admin-tag-migration">editar cliente</span>
                <h2 class="mt-3 admin-section-title">{{ $client->razao_social ?? $client->nome_fantasia ?? $client->name }}</h2>
                <p class="admin-section-note">Atualize dados cadastrais, endereco, status e observacoes internas.</p>
            </div>
            <div class="admin-toolbar-actions">
                <a href="{{ route('admin.v2.clients.show', $client) }}" class="admin-btn-soft">Voltar para detalhes</a>
            </div>
        </div>
    </section>

    <form method="POST" action="{{ route('admin.v2.clients.update', $client) }}">
        @csrf
        @method('PUT')
        @include('admin.clients.partials.form')
    </form>
@endsection
