@extends('admin.layouts.app')

@php
    $pageTitle = 'Central de Notificações';
    $pageSubtitle = 'Monitore, filtre e envie notificações para clientes.';
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
            <p class="admin-metric-label">Hoje</p>
            <p class="admin-metric-value">{{ number_format($summary['hoje']) }}</p>
            <p class="admin-metric-footnote">Notificações geradas hoje</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Última semana</p>
            <p class="admin-metric-value">{{ number_format($summary['semana']) }}</p>
            <p class="admin-metric-footnote">Nos últimos 7 dias</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Não lidas</p>
            <p class="admin-metric-value text-amber-600">{{ number_format($summary['nao_lidas']) }}</p>
            <p class="admin-metric-footnote">Pendentes de leitura</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Clientes pendentes</p>
            <p class="admin-metric-value text-orange-600">{{ number_format($summary['clientes_pendentes']) }}</p>
            <p class="admin-metric-footnote">Aguardando aprovação</p>
        </article>
    </section>

    <section class="mt-6 admin-split-grid">
        <div class="admin-stack">
            <section class="admin-card">
                <div class="admin-toolbar">
                    <div class="admin-toolbar-main">
                        <span class="admin-tag admin-tag-new">notificações</span>
                        <h2 class="mt-3 admin-section-title">Histórico de notificações</h2>
                        <p class="admin-section-note">Todas as notificações do sistema, manuais e automáticas.</p>
                    </div>
                    <div class="admin-toolbar-actions">
                        <form method="POST" action="{{ route('admin.v2.notificacoes.marcar-lidas') }}" class="inline">
                            @csrf
                            <button type="submit" class="admin-btn-soft text-sm" onclick="return confirm('Marcar TODAS como lidas?')">✅ Marcar todas lidas</button>
                        </form>
                    </div>
                </div>

                {{-- Filtros --}}
                <form method="GET" action="{{ route('admin.v2.notificacoes.index') }}" class="mt-4 flex flex-wrap items-end gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Tipo</label>
                        <select name="tipo" class="rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                            @foreach($tipoFiltros as $key => $label)
                                <option value="{{ $key }}" @selected($tipo === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Período</label>
                        <select name="periodo" class="rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos</option>
                            <option value="hoje" @selected($periodo === 'hoje')>Hoje</option>
                            <option value="semana" @selected($periodo === 'semana')>Última semana</option>
                            <option value="30dias" @selected($periodo === '30dias')>Últimos 30 dias</option>
                        </select>
                    </div>
                    <button type="submit" class="admin-btn-primary">Filtrar</button>
                    @if($tipo !== '' || $periodo !== '')
                        <a href="{{ route('admin.v2.notificacoes.index') }}" class="admin-btn-soft">Limpar</a>
                    @endif
                </form>

                {{-- Lista de notificações --}}
                <div class="mt-5 space-y-2">
                    @forelse($notifications as $n)
                        @php
                            $data = $n->data ?? [];
                            $titulo = $data['titulo'] ?? $data['title'] ?? class_basename($n->type);
                            $mensagem = $data['mensagem'] ?? $data['message'] ?? $data['body'] ?? '—';
                            $tipoTag = $data['tipo'] ?? 'sistema';
                            $isRead = $n->read_at !== null;

                            $tipoColors = [
                                'manual' => 'bg-purple-100 text-purple-700',
                                'broadcast' => 'bg-blue-100 text-blue-700',
                                'pedido' => 'bg-indigo-100 text-indigo-700',
                                'contrato' => 'bg-teal-100 text-teal-700',
                                'chamado' => 'bg-orange-100 text-orange-700',
                                'documento' => 'bg-cyan-100 text-cyan-700',
                                'aprovacao' => 'bg-emerald-100 text-emerald-700',
                            ];
                        @endphp
                        <div class="flex items-start gap-3 p-3 rounded-xl {{ $isRead ? 'bg-gray-50/30' : 'bg-blue-50/40 border border-blue-100' }} transition">
                            <div class="flex-shrink-0 mt-1">
                                @if(!$isRead)
                                    <span class="block w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                                @else
                                    <span class="block w-2.5 h-2.5 rounded-full bg-gray-300"></span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-bold text-sm text-gray-800">{{ $titulo }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $tipoColors[$tipoTag] ?? 'bg-gray-100 text-gray-500' }}">
                                        {{ $tipoTag }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ $mensagem }}</p>
                                <div class="flex items-center gap-3 mt-1 text-[10px] text-gray-400">
                                    @if($n->notifiable)
                                        <span>👤 {{ $n->notifiable?->name ?? 'ID ' . $n->notifiable_id }}</span>
                                    @endif
                                    <span>{{ $n->created_at?->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 text-gray-400">
                            <div class="text-4xl mb-2">🔔</div>
                            <p class="font-semibold">Nenhuma notificação encontrada.</p>
                        </div>
                    @endforelse
                </div>

                @if($notifications->hasPages())
                    <div class="mt-5">{{ $notifications->links() }}</div>
                @endif
            </section>
        </div>

        {{-- Sidebar: Enviar notificação --}}
        <aside class="admin-stack">
            {{-- Envio manual --}}
            <section class="admin-card">
                <h3 class="admin-section-title text-base">📨 Enviar manual</h3>
                <p class="admin-section-note mt-1">Para um cliente específico.</p>

                <form method="POST" action="{{ route('admin.v2.notificacoes.enviar-manual') }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Destinatário *</label>
                        <select name="user_id" required class="w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">Selecione...</option>
                            @foreach(\App\Models\User::where('is_admin', false)->orderBy('name')->get(['id', 'name', 'email']) as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Título *</label>
                        <input type="text" name="titulo" required placeholder="Ex: Atualização importante"
                            class="w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Mensagem *</label>
                        <textarea name="mensagem" rows="3" required placeholder="Corpo da notificação..."
                            class="w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    <button type="submit" class="w-full admin-btn-primary py-2.5">Enviar notificação</button>
                </form>
            </section>

            {{-- Broadcast --}}
            <section class="admin-card">
                <h3 class="admin-section-title text-base">📢 Broadcast</h3>
                <p class="admin-section-note mt-1">Enviar para todos os clientes ativos.</p>

                <form method="POST" action="{{ route('admin.v2.notificacoes.enviar-broadcast') }}" class="mt-4 space-y-4"
                    onsubmit="return confirm('Enviar notificação para TODOS os clientes ativos?')">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Título *</label>
                        <input type="text" name="titulo" required placeholder="Ex: Novidade Elite Repasse"
                            class="w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Mensagem *</label>
                        <textarea name="mensagem" rows="3" required placeholder="Mensagem para todos..."
                            class="w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    <button type="submit" class="w-full admin-btn-primary py-2.5">📢 Enviar broadcast</button>
                </form>
            </section>
        </aside>
    </section>
@endsection
