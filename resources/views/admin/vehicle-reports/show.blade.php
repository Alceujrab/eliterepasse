@extends('admin.layouts.app')

@php
    $pageTitle = "Laudo {$report->numero}";
    $pageSubtitle = ($tipoLabels[$report->tipo] ?? $report->tipo) . ' — ' . ($report->vehicle?->model ?? '') . ' ' . ($report->vehicle?->plate ?? '');

    $statusColors = [
        'rascunho' => 'bg-gray-100 text-gray-600',
        'em_revisao' => 'bg-amber-100 text-amber-700',
        'aprovado' => 'bg-emerald-100 text-emerald-700',
        'reprovado' => 'bg-rose-100 text-rose-700',
    ];

    $resultadoColors = [
        'ok' => 'bg-emerald-100 text-emerald-700',
        'atencao' => 'bg-amber-100 text-amber-700',
        'reprovado' => 'bg-rose-100 text-rose-700',
        'nao_avaliado' => 'bg-gray-100 text-gray-400',
    ];

    $resultadoIcons = [
        'ok' => '✅',
        'atencao' => '⚠️',
        'reprovado' => '❌',
        'nao_avaliado' => '⏳',
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

    {{-- Header com dados do laudo e ações --}}
    <section class="admin-card mb-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <span class="font-mono text-lg font-bold text-gray-800">{{ $report->numero }}</span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $statusColors[$report->status] ?? 'bg-gray-100' }}">
                        {{ $statusLabels[$report->status] ?? $report->status }}
                    </span>
                </div>
                <div class="mt-2 text-sm text-gray-500 space-y-1">
                    <p><strong>Veículo:</strong> {{ $report->vehicle?->model ?? '—' }} — {{ $report->vehicle?->plate ?? '' }}</p>
                    <p><strong>Tipo:</strong> {{ $tipoLabels[$report->tipo] ?? $report->tipo }}</p>
                    <p><strong>Nota geral:</strong> {{ $report->nota_geral ?? '—' }}/10</p>
                    <p><strong>Criado por:</strong> {{ $report->criadoPor?->name ?? '—' }} em {{ $report->created_at?->format('d/m/Y H:i') }}</p>
                    @if($report->aprovadoPor)
                        <p><strong>{{ $report->status === 'aprovado' ? 'Aprovado' : 'Avaliado' }} por:</strong> {{ $report->aprovadoPor->name }} em {{ $report->aprovado_em?->format('d/m/Y H:i') }}</p>
                    @endif
                </div>
                @if($report->conclusao)
                    <div class="mt-3 p-3 bg-blue-50 rounded-xl text-sm text-gray-700">
                        <strong>Conclusão:</strong> {{ $report->conclusao }}
                    </div>
                @endif
                @if($report->recomendacoes)
                    <div class="mt-2 p-3 bg-orange-50 rounded-xl text-sm text-gray-700">
                        <strong>Recomendações:</strong> {{ $report->recomendacoes }}
                    </div>
                @endif
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.v2.vehicle-reports.index') }}" class="admin-btn-soft text-sm">← Voltar</a>
                @if($report->status === 'rascunho')
                    <form method="POST" action="{{ route('admin.v2.vehicle-reports.enviar-revisao', $report) }}">
                        @csrf
                        <button type="submit" class="admin-btn-primary text-sm">📤 Enviar p/ revisão</button>
                    </form>
                @endif
                @if(in_array($report->status, ['em_revisao', 'rascunho']))
                    <form method="POST" action="{{ route('admin.v2.vehicle-reports.aprovar', $report) }}">
                        @csrf
                        <button type="submit" class="admin-btn-soft text-sm text-emerald-600 border-emerald-200 hover:bg-emerald-50">✅ Aprovar</button>
                    </form>
                    <form method="POST" action="{{ route('admin.v2.vehicle-reports.reprovar', $report) }}">
                        @csrf
                        <button type="submit" class="admin-btn-soft text-sm text-rose-600 border-rose-200 hover:bg-rose-50">❌ Reprovar</button>
                    </form>
                @endif
            </div>
        </div>
    </section>

    {{-- Checklist por grupo --}}
    <form method="POST" action="{{ route('admin.v2.vehicle-reports.update-items', $report) }}">
        @csrf

        <div class="grid gap-4">
            @foreach($grupos as $grupo => $items)
                <section class="admin-card">
                    <h3 class="admin-section-title text-base flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        {{ $grupo }}
                        <span class="text-xs font-normal text-gray-400">({{ $items->count() }} itens)</span>
                    </h3>

                    <div class="mt-3 space-y-2">
                        @foreach($items as $item)
                            <div class="flex flex-wrap items-center gap-3 p-3 rounded-xl bg-gray-50/50 hover:bg-gray-50 transition">
                                <input type="hidden" name="items[{{ $loop->parent->index }}_{{ $loop->index }}][id]" value="{{ $item->id }}">

                                <span class="flex-1 min-w-[150px] text-sm font-semibold text-gray-700">{{ $item->item }}</span>

                                <select name="items[{{ $loop->parent->index }}_{{ $loop->index }}][resultado]"
                                    class="rounded-lg border border-gray-200 text-xs px-3 py-1.5 focus:ring-2 focus:ring-blue-500
                                        {{ $resultadoColors[$item->resultado] ?? '' }}">
                                    <option value="nao_avaliado" @selected($item->resultado === 'nao_avaliado')>⏳ Não avaliado</option>
                                    <option value="ok" @selected($item->resultado === 'ok')>✅ OK</option>
                                    <option value="atencao" @selected($item->resultado === 'atencao')>⚠️ Atenção</option>
                                    <option value="reprovado" @selected($item->resultado === 'reprovado')>❌ Reprovado</option>
                                </select>

                                <input type="text" name="items[{{ $loop->parent->index }}_{{ $loop->index }}][observacao]"
                                    value="{{ $item->observacao }}" placeholder="Obs..."
                                    class="flex-1 min-w-[120px] rounded-lg border border-gray-200 text-xs px-3 py-1.5 focus:ring-2 focus:ring-blue-500">
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>

        {{-- Campos gerais --}}
        <section class="admin-card mt-4">
            <h3 class="admin-section-title text-base">📝 Avaliação geral</h3>
            <div class="mt-3 grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nota geral (0-10)</label>
                    <input type="number" name="nota_geral" min="0" max="10" value="{{ $report->nota_geral }}"
                        class="w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div></div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Conclusão</label>
                    <textarea name="conclusao" rows="3" class="w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">{{ $report->conclusao }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Recomendações</label>
                    <textarea name="recomendacoes" rows="3" class="w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">{{ $report->recomendacoes }}</textarea>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="admin-btn-primary py-2.5 px-6">💾 Salvar checklist</button>
            </div>
        </section>
    </form>
@endsection
