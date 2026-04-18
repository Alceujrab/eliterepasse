@extends('admin.layouts.app')

@php
    $pageTitle = 'Novo veículo';
    $pageSubtitle = 'Cadastre um novo veiculo no estoque da Elite Repasse.';
@endphp

@section('content')
    @if($errors->any())
        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="admin-card mb-6">
        <div class="admin-toolbar">
            <div class="admin-toolbar-main">
                <span class="admin-tag admin-tag-new">novo veiculo</span>
                <h2 class="mt-3 admin-section-title">Cadastrar veículo</h2>
                <p class="admin-section-note">Preencha os dados do veiculo para adiciona-lo ao estoque e exibi-lo no portal do lojista.</p>
            </div>
            <div class="admin-toolbar-actions">
                <a href="{{ route('admin.v2.vehicles.index') }}" class="admin-btn-soft">Voltar para lista</a>
            </div>
        </div>
    </section>

    <form method="POST" action="{{ route('admin.v2.vehicles.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.vehicles.partials.form')
    </form>
@endsection
