@extends('admin.layouts.app')

@php
    $pageTitle = $module['label'];
    $pageSubtitle = $module['description'];
@endphp

@section('content')
    <section class="admin-card">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="text-lg font-black text-slate-900">{{ $module['label'] }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $module['description'] }}</p>
            </div>
            <span class="admin-tag {{ $module['status'] === 'novo' ? 'admin-tag-new' : 'admin-tag-migration' }}">
                {{ $module['status'] === 'novo' ? 'novo' : 'em migração' }}
            </span>
        </div>

        <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4">
            <h3 class="text-sm font-extrabold uppercase tracking-[0.12em] text-slate-500">Ações mapeadas</h3>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($module['actions'] as $action)
                    <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-700 ring-1 ring-slate-200">
                        {{ str_replace('-', ' ', $action) }}
                    </span>
                @endforeach
            </div>
        </div>

        <div class="mt-5 flex flex-wrap gap-2">
            <a href="{{ route('admin.v2.dashboard') }}" class="admin-btn-soft">Voltar ao dashboard</a>
            @if(! empty($module['v2_path']))
                <a href="{{ $module['v2_path'] }}" class="admin-btn-primary">Abrir módulo v2</a>
            @endif
            <a href="{{ $module['legacy_path'] }}" class="admin-btn-primary">Abrir módulo legado</a>
        </div>
    </section>
@endsection
