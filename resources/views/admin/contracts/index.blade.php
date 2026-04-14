@extends('admin.layouts.app')

@php
    $pageTitle = 'Contratos (Admin v2)';
    $pageSubtitle = 'Gestão de assinaturas e rastreio de contratos no novo painel.';
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

    <section class="admin-card">
        <form method="GET" action="{{ route('admin.v2.contracts.index') }}" class="grid gap-3 md:grid-cols-[1fr_220px_auto] md:items-end">
            <div>
                <label for="contracts-q" class="mb-1 block text-xs font-extrabold uppercase tracking-[0.12em] text-slate-500">Busca</label>
                <input id="contracts-q" name="q" value="{{ $search }}" placeholder="Número, cliente, placa..."
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium outline-none transition focus:border-blue-400">
            </div>

            <div>
                <label for="contracts-status" class="mb-1 block text-xs font-extrabold uppercase tracking-[0.12em] text-slate-500">Status</label>
                <select id="contracts-status" name="status"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium outline-none transition focus:border-blue-400">
                    <option value="">Todos</option>
                    @foreach($statusOptions as $statusKey => $statusLabel)
                        <option value="{{ $statusKey }}" @selected($status === $statusKey)>{{ $statusLabel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="admin-btn-primary">Filtrar</button>
                <a href="{{ route('admin.v2.contracts.index') }}" class="admin-btn-soft">Limpar</a>
            </div>
        </form>
    </section>

    <section class="mt-4 admin-card overflow-x-auto">
        <table class="min-w-full border-separate border-spacing-y-2">
            <thead>
            <tr class="text-left text-xs font-extrabold uppercase tracking-[0.12em] text-slate-500">
                <th class="px-3 py-2">Contrato</th>
                <th class="px-3 py-2">Comprador</th>
                <th class="px-3 py-2">Veículo</th>
                <th class="px-3 py-2">Valor</th>
                <th class="px-3 py-2">Status</th>
                <th class="px-3 py-2">Assinado em</th>
                <th class="px-3 py-2">Ações</th>
            </tr>
            </thead>
            <tbody>
            @forelse($contracts as $contract)
                <tr class="rounded-xl border border-slate-200 bg-slate-50 align-top">
                    <td class="px-3 py-3 text-sm font-black text-slate-800">{{ $contract->numero }}</td>
                    <td class="px-3 py-3 text-sm font-semibold text-slate-700">
                        {{ $contract->user?->razao_social ?? $contract->user?->name ?? '—' }}
                        <div class="text-xs font-medium text-slate-500">{{ $contract->user?->cnpj ?? $contract->user?->email }}</div>
                    </td>
                    <td class="px-3 py-3 text-sm font-semibold text-slate-700">
                        @if($contract->vehicle)
                            {{ $contract->vehicle->brand }} {{ $contract->vehicle->model }} {{ $contract->vehicle->model_year }}
                            <div class="text-xs font-medium text-slate-500">{{ $contract->vehicle->plate }}</div>
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-3 py-3 text-sm font-bold text-emerald-700">R$ {{ number_format((float) $contract->valor_contrato, 2, ',', '.') }}</td>
                    <td class="px-3 py-3 text-xs font-bold text-slate-700">{{ $statusOptions[$contract->status] ?? $contract->status }}</td>
                    <td class="px-3 py-3 text-xs font-semibold text-slate-500">{{ $contract->assinado_em?->format('d/m/Y H:i') ?? '—' }}</td>
                    <td class="px-3 py-3">
                        <div class="flex flex-wrap gap-2">
                            @if(in_array($contract->status, ['rascunho', 'aguardando'], true))
                                <form method="POST" action="{{ route('admin.v2.contracts.send-to-sign', $contract) }}">
                                    @csrf
                                    <button type="submit" class="admin-btn-soft">Enviar p/ Assinar</button>
                                </form>
                            @endif

                            @if($contract->assinaturaComprador)
                                <form method="POST" action="{{ route('admin.v2.contracts.copy-link', $contract) }}">
                                    @csrf
                                    <button type="submit" class="admin-btn-soft">Copiar Link</button>
                                </form>
                            @endif

                            <a href="/admin/contracts/{{ $contract->id }}" class="admin-btn-soft">Ver legado</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-3 py-6 text-center text-sm font-semibold text-slate-500">Nenhum contrato encontrado para o filtro atual.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $contracts->links() }}
        </div>
    </section>
@endsection
