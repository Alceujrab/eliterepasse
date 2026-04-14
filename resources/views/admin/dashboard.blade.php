@extends('admin.layouts.app')

@php
    $pageTitle = 'Dashboard Executivo';
    $pageSubtitle = 'Visão consolidada do negócio, com foco em gargalos operacionais.';
@endphp

@section('content')
    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <article class="admin-kpi">
            <p class="admin-kpi-label">Clientes</p>
            <p class="admin-kpi-value">{{ number_format($metrics['clientes']) }}</p>
        </article>

        <article class="admin-kpi">
            <p class="admin-kpi-label">Pendentes de aprovação</p>
            <p class="admin-kpi-value">{{ number_format($metrics['clientesPendentes']) }}</p>
        </article>

        <article class="admin-kpi">
            <p class="admin-kpi-label">Veículos disponíveis</p>
            <p class="admin-kpi-value">{{ number_format($metrics['veiculosDisponiveis']) }}</p>
        </article>

        <article class="admin-kpi">
            <p class="admin-kpi-label">Pedidos pendentes</p>
            <p class="admin-kpi-value">{{ number_format($metrics['pedidosPendentes']) }}</p>
        </article>

        <article class="admin-kpi">
            <p class="admin-kpi-label">Contratos aguardando</p>
            <p class="admin-kpi-value">{{ number_format($metrics['contratosAguardando']) }}</p>
        </article>

        <article class="admin-kpi">
            <p class="admin-kpi-label">Tickets urgentes</p>
            <p class="admin-kpi-value">{{ number_format($metrics['ticketsUrgentes']) }}</p>
        </article>

        <article class="admin-kpi md:col-span-2">
            <p class="admin-kpi-label">Documentos pendentes</p>
            <p class="admin-kpi-value">{{ number_format($metrics['documentosPendentes']) }}</p>
        </article>
    </section>

    <section class="mt-6 admin-card">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-black">Acesso Rápido por Módulo</h2>
                <p class="text-sm text-slate-500">Navegação prioritária para as áreas mais usadas no dia a dia.</p>
            </div>
            <a href="/admin" class="admin-btn-soft">Abrir admin legado</a>
        </div>

        <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
            @foreach($quickModules as $moduleEntry)
                @php($module = $moduleEntry['data'])
                <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h3 class="font-bold text-slate-800">{{ $module['label'] }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $module['description'] }}</p>
                        </div>
                        <span class="admin-tag {{ $module['status'] === 'novo' ? 'admin-tag-new' : 'admin-tag-migration' }}">
                            {{ $module['status'] === 'novo' ? 'novo' : 'migração' }}
                        </span>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ $module['v2_path'] ?? route('admin.v2.module', $moduleEntry['key']) }}" class="admin-btn-primary">Entrar</a>
                        <a href="{{ $module['legacy_path'] }}" class="admin-btn-soft">Legado</a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endsection
