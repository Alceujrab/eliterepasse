@extends('admin.layouts.app')

@php
    $pageTitle = 'Editar · ' . ($vehicle->nome_completo ?: $vehicle->plate);
    $pageSubtitle = 'Altere os dados do veiculo e salve para refletir no estoque e no portal.';
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
                <span class="admin-tag admin-tag-migration">editar veiculo</span>
                <h2 class="mt-3 admin-section-title">{{ $vehicle->nome_completo ?: $vehicle->plate }}</h2>
                <p class="admin-section-note">Atualize dados cadastrais, fotos, precos e destaques comerciais deste veiculo.</p>
            </div>
            <div class="admin-toolbar-actions">
                <a href="{{ route('admin.v2.vehicles.show', $vehicle) }}" class="admin-btn-soft">Voltar para detalhes</a>
            </div>
        </div>
    </section>

    <form method="POST" action="{{ route('admin.v2.vehicles.update', $vehicle) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.vehicles.partials.form')
    </form>

    {{-- Botão de exclusão separado --}}
    @if(! $vehicle->orders()->exists())
        <section class="admin-card mt-6 border-rose-200 bg-rose-50">
            <h2 class="admin-section-title text-rose-700">Zona de perigo</h2>
            <p class="admin-section-note text-rose-600">Excluir este veiculo remove permanentemente o registro e suas fotos. Esta acao nao pode ser desfeita.</p>
            <form method="POST" action="{{ route('admin.v2.vehicles.destroy', $vehicle) }}" class="mt-4" onsubmit="return confirm('Tem certeza que deseja excluir este veículo?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-xl bg-rose-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-rose-700 transition">Excluir veículo</button>
            </form>
        </section>
    @endif
@endsection
