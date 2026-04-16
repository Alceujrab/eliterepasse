@extends('admin.layouts.app')

@php
    $pageTitle = 'Banners da Landing';
    $pageSubtitle = 'Gerencie as imagens do carrossel principal do site.';
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
            <p class="admin-metric-label">Total de banners</p>
            <p class="admin-metric-value">{{ $banners->count() }}</p>
            <p class="admin-metric-footnote">Cadastrados no sistema</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Ativos</p>
            <p class="admin-metric-value">{{ $banners->where('is_active', true)->count() }}</p>
            <p class="admin-metric-footnote">Exibidos no site</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Inativos</p>
            <p class="admin-metric-value">{{ $banners->where('is_active', false)->count() }}</p>
            <p class="admin-metric-footnote">Ocultos do carrossel</p>
        </article>
    </section>

    {{-- Adicionar banner --}}
    <section class="admin-card mt-6 mb-4">
        <div class="admin-toolbar">
            <div class="admin-toolbar-main">
                <span class="admin-tag admin-tag-new">novo</span>
                <h2 class="mt-3 admin-section-title">Adicionar Banner</h2>
                <p class="admin-section-note">Envie uma imagem para o carrossel da landing page. Recomendado: 1920x600px.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.v2.landing-banners.store') }}" enctype="multipart/form-data" class="mt-5 grid gap-4 xl:grid-cols-3">
            @csrf
            <div class="admin-info-card">
                <label class="admin-detail-label">Imagem *</label>
                <input type="file" name="image" accept="image/*" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">
                <p class="mt-1 text-xs text-slate-500">JPG ou PNG. Max 4 MB.</p>
            </div>
            <div class="admin-info-card">
                <label class="admin-detail-label">Titulo (opcional)</label>
                <input type="text" name="title" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Texto sobreposto a imagem">
            </div>
            <div class="admin-info-card">
                <label class="admin-detail-label">Subtitulo (opcional)</label>
                <input type="text" name="subtitle" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Descricao secundaria">
            </div>
            <div class="xl:col-span-3">
                <button type="submit" class="admin-btn-primary">Enviar Banner</button>
            </div>
        </form>
    </section>

    {{-- Lista de banners --}}
    <section class="admin-card mb-4">
        <h2 class="admin-section-title">Banners Cadastrados</h2>
        <p class="admin-section-note mt-1">Clique em "Salvar" apos editar titulo, subtitulo ou status. Para excluir, use o botao vermelho.</p>

        @forelse($banners as $banner)
            <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-start">
                    {{-- Preview --}}
                    <div class="shrink-0">
                        <img src="{{ asset('storage/' . $banner->image_path) }}" alt="{{ $banner->title ?? 'Banner' }}" class="h-28 w-48 rounded-2xl object-cover border border-slate-200">
                    </div>

                    {{-- Formulario de edicao --}}
                    <form method="POST" action="{{ route('admin.v2.landing-banners.update', $banner) }}" enctype="multipart/form-data" class="flex-1 grid gap-3 xl:grid-cols-2">
                        @csrf
                        <div>
                            <label class="text-xs font-semibold text-slate-500">Titulo</label>
                            <input type="text" name="title" value="{{ $banner->title }}" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium outline-none transition focus:border-blue-400">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500">Subtitulo</label>
                            <input type="text" name="subtitle" value="{{ $banner->subtitle }}" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium outline-none transition focus:border-blue-400">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500">Trocar imagem (opcional)</label>
                            <input type="file" name="image" accept="image/*" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium outline-none transition focus:border-blue-400">
                        </div>
                        <div class="flex items-end gap-3">
                            <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                                <input type="checkbox" name="is_active" value="1" {{ $banner->is_active ? 'checked' : '' }} class="rounded border-slate-300">
                                Ativo
                            </label>
                            <span class="text-xs text-slate-400">Ordem: {{ $banner->order }}</span>
                        </div>
                        <div class="xl:col-span-2 flex gap-2">
                            <button type="submit" class="admin-btn-primary text-xs">Salvar</button>
                        </div>
                    </form>

                    {{-- Excluir --}}
                    <form method="POST" action="{{ route('admin.v2.landing-banners.destroy', $banner) }}" onsubmit="return confirm('Tem certeza que deseja excluir este banner?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-xl bg-rose-100 px-4 py-2 text-xs font-bold text-rose-700 hover:bg-rose-200 transition">Excluir</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="mt-4 admin-empty-state">Nenhum banner cadastrado ainda. Use o formulario acima para adicionar.</div>
        @endforelse
    </section>

    <div class="mb-6">
        <a href="{{ route('admin.v2.landing-settings.index') }}" class="admin-btn-primary text-xs">Voltar para Configuracoes da Landing</a>
    </div>
@endsection
