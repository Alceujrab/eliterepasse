@extends('admin.layouts.app')

@php
    $pageTitle = 'Laudos e Vistorias';
    $pageSubtitle = 'Checklist técnico de veículos com aprovação operacional.';

    $statusColors = [
        'rascunho' => 'bg-gray-100 text-gray-600',
        'em_revisao' => 'bg-amber-100 text-amber-700',
        'aprovado' => 'bg-emerald-100 text-emerald-700',
        'reprovado' => 'bg-rose-100 text-rose-700',
    ];
@endphp

@section('content')
    @if(session('admin_success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            {{ session('admin_success') }}
        </div>
    @endif

    @if(session('admin_warning'))
        <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700">
            {{ session('admin_warning') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- KPIs --}}
    <section class="admin-metrics-grid">
        <article class="admin-metric-card">
            <p class="admin-metric-label">Total</p>
            <p class="admin-metric-value">{{ number_format($summary['total']) }}</p>
            <p class="admin-metric-footnote">Laudos registrados</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Rascunho</p>
            <p class="admin-metric-value">{{ number_format($summary['rascunho']) }}</p>
            <p class="admin-metric-footnote">Ainda em preenchimento</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Em revisão</p>
            <p class="admin-metric-value text-amber-600">{{ number_format($summary['em_revisao']) }}</p>
            <p class="admin-metric-footnote">Aguardando aprovação</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Aprovados</p>
            <p class="admin-metric-value text-emerald-600">{{ number_format($summary['aprovado']) }}</p>
            <p class="admin-metric-footnote">Concluídos com sucesso</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Reprovados</p>
            <p class="admin-metric-value text-rose-600">{{ number_format($summary['reprovado']) }}</p>
            <p class="admin-metric-footnote">Necessitam ação</p>
        </article>
    </section>

    <section class="mt-6 admin-split-grid">
        <div class="admin-stack">
            <section class="admin-card">
                <div class="admin-toolbar">
                    <div class="admin-toolbar-main">
                        <span class="admin-tag admin-tag-new">laudos</span>
                        <h2 class="mt-3 admin-section-title">Central de laudos</h2>
                        <p class="admin-section-note">Laudos técnicos de vistoria para veículos no estoque.</p>
                    </div>
                    <div class="admin-toolbar-actions">
                        <a href="{{ route('admin.v2.vehicle-reports.index') }}" class="admin-btn-soft">Atualizar</a>
                    </div>
                </div>

                {{-- Filtros --}}
                <form method="GET" action="{{ route('admin.v2.vehicle-reports.index') }}" class="mt-4 flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Buscar</label>
                        <input type="text" name="q" value="{{ $search }}" placeholder="Nº, placa ou modelo..."
                            class="w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Tipo</label>
                        <select name="tipo" class="rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos</option>
                            @foreach($tipoLabels as $key => $label)
                                <option value="{{ $key }}" @selected($tipo === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Status</label>
                        <select name="status" class="rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos</option>
                            @foreach($statusLabels as $key => $label)
                                <option value="{{ $key }}" @selected($status === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="admin-btn-primary">Filtrar</button>
                    @if($hasActiveFilters)
                        <a href="{{ route('admin.v2.vehicle-reports.index') }}" class="admin-btn-soft">Limpar</a>
                    @endif
                </form>

                {{-- Tabela desktop --}}
                <div class="mt-5 hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-left py-3 px-3 text-xs font-bold text-gray-400 uppercase">Número</th>
                                <th class="text-left py-3 px-3 text-xs font-bold text-gray-400 uppercase">Veículo</th>
                                <th class="text-left py-3 px-3 text-xs font-bold text-gray-400 uppercase">Tipo</th>
                                <th class="text-center py-3 px-3 text-xs font-bold text-gray-400 uppercase">Nota</th>
                                <th class="text-center py-3 px-3 text-xs font-bold text-gray-400 uppercase">Status</th>
                                <th class="text-left py-3 px-3 text-xs font-bold text-gray-400 uppercase">Criado por</th>
                                <th class="text-left py-3 px-3 text-xs font-bold text-gray-400 uppercase">Data</th>
                                <th class="text-right py-3 px-3 text-xs font-bold text-gray-400 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($reports as $report)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="py-3 px-3 font-mono font-bold text-gray-800">{{ $report->numero }}</td>
                                    <td class="py-3 px-3">
                                        @if($report->vehicle)
                                            <span class="font-semibold text-gray-700">{{ $report->vehicle->model ?? '—' }}</span>
                                            <br><span class="text-xs text-gray-400">{{ $report->vehicle->plate ?? '' }}</span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-gray-600">{{ $tipoLabels[$report->tipo] ?? $report->tipo }}</td>
                                    <td class="py-3 px-3 text-center font-bold {{ ($report->nota_geral ?? 0) >= 7 ? 'text-emerald-600' : (($report->nota_geral ?? 0) >= 4 ? 'text-amber-600' : 'text-rose-600') }}">
                                        {{ $report->nota_geral ?? '—' }}
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold {{ $statusColors[$report->status] ?? 'bg-gray-100 text-gray-500' }}">
                                            {{ $statusLabels[$report->status] ?? $report->status }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-gray-500 text-xs">{{ $report->criadoPor?->name ?? '—' }}</td>
                                    <td class="py-3 px-3 text-gray-400 text-xs">{{ $report->created_at?->format('d/m/Y') }}</td>
                                    <td class="py-3 px-3 text-right">
                                        <div class="inline-flex gap-1">
                                            <a href="{{ route('admin.v2.vehicle-reports.show', $report) }}" class="admin-btn-soft text-xs px-2 py-1">👁 Ver</a>
                                            @if(in_array($report->status, ['em_revisao', 'rascunho']))
                                                <form method="POST" action="{{ route('admin.v2.vehicle-reports.aprovar', $report) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="admin-btn-soft text-xs px-2 py-1 text-emerald-600 hover:bg-emerald-50">✅</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.v2.vehicle-reports.reprovar', $report) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="admin-btn-soft text-xs px-2 py-1 text-rose-600 hover:bg-rose-50">❌</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-gray-400">
                                        <div class="text-4xl mb-2">📋</div>
                                        <p class="font-semibold">Nenhum laudo encontrado.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Lista mobile --}}
                <div class="mt-5 md:hidden space-y-3">
                    @forelse($reports as $report)
                        <a href="{{ route('admin.v2.vehicle-reports.show', $report) }}" class="block admin-card p-4 hover:shadow-md transition">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="font-mono font-bold text-gray-800">{{ $report->numero }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $report->vehicle?->model }} — {{ $report->vehicle?->plate }}</p>
                                    <p class="text-xs text-gray-400">{{ $tipoLabels[$report->tipo] ?? $report->tipo }}</p>
                                </div>
                                <span class="flex-shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $statusColors[$report->status] ?? 'bg-gray-100 text-gray-500' }}">
                                    {{ $statusLabels[$report->status] ?? $report->status }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-8 text-gray-400">
                            <div class="text-4xl mb-2">📋</div>
                            <p class="font-semibold">Nenhum laudo encontrado.</p>
                        </div>
                    @endforelse
                </div>

                @if($reports->hasPages())
                    <div class="mt-5">{{ $reports->links() }}</div>
                @endif
            </section>
        </div>

        {{-- Sidebar: Formulário novo laudo --}}
        <aside class="admin-stack">
            <section class="admin-card">
                <h3 class="admin-section-title text-base">📋 Novo laudo</h3>
                <p class="admin-section-note mt-1">Crie um laudo com checklist padrão preenchível.</p>

                <form method="POST" action="{{ route('admin.v2.vehicle-reports.store') }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Veículo *</label>
                        <select name="vehicle_id" required class="w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">Selecione...</option>
                            @foreach(\App\Models\Vehicle::select('id', 'model', 'plate')->orderBy('model')->get() as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->model }} — {{ $vehicle->plate }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tipo *</label>
                        <select name="tipo" required class="w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                            @foreach($tipoLabels as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nota geral (0-10)</label>
                        <input type="number" name="nota_geral" min="0" max="10" placeholder="Ex: 8"
                            class="w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Conclusão</label>
                        <textarea name="conclusao" rows="2" placeholder="Resumo do laudo..."
                            class="w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Recomendações</label>
                        <textarea name="recomendacoes" rows="2" placeholder="Reparos sugeridos..."
                            class="w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    <button type="submit" class="w-full admin-btn-primary py-2.5">Criar laudo + checklist</button>
                </form>
            </section>
        </aside>
    </section>
@endsection
