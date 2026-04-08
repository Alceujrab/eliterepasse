<x-filament-panels::page>

    {{-- ─── KPI Cards ──────────────────────────────────────────────── --}}
    @php $resumo = $this->getResumo(); @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @php
            $kpis = [
                ['label' => 'Enviadas Hoje', 'value' => $resumo['total_hoje'], 'icone' => '📨', 'color' => 'blue'],
                ['label' => 'Últimos 7 Dias', 'value' => $resumo['total_semana'], 'icone' => '📊', 'color' => 'indigo'],
                ['label' => 'Não Lidas (Total)', 'value' => $resumo['nao_lidas_total'], 'icone' => '🔴', 'color' => 'red'],
                ['label' => 'Clientes com Pendentes', 'value' => $resumo['clientes_com_notif'], 'icone' => '👥', 'color' => 'amber'],
            ];
        @endphp
        @foreach($kpis as $kpi)
            <div class="kpi-card">
                <div class="text-2xl mb-1">{{ $kpi['icone'] }}</div>
                <div class="kpi-value">{{ number_format($kpi['value']) }}</div>
                <div class="kpi-label mt-0.5">{{ $kpi['label'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- ─── Filtros + Ação ─────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <div class="flex gap-2 flex-wrap">
            {{-- Tipo --}}
            @foreach(['todas' => 'Todas', 'pedido_confirmado' => '✅ Pedidos', 'contrato_para_assinar' => '✍️ Contratos', 'ticket_atualizado' => '💬 Chamados', 'cliente_aprovado' => '🎉 Aprovações', 'documento_verificado' => '📄 Documentos', 'manual' => '👤 Manual', 'broadcast' => '📢 Broadcast'] as $val => $label)
                <button wire:click="$set('filtroTipo', '{{ $val }}')"
                    class="px-4 py-2 text-sm font-semibold rounded-lg transition
                        {{ $filtroTipo === $val
                            ? 'bg-primary text-white shadow'
                            : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700 hover:border-primary' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div class="flex items-center gap-2">
            {{-- Período --}}
            <select wire:model.live="filtroPeriodo"
                class="text-base rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 px-4 py-2">
                <option value="1">Hoje</option>
                <option value="7" selected>7 dias</option>
                <option value="30">30 dias</option>
                <option value="90">90 dias</option>
            </select>

            @if($resumo['nao_lidas_total'] > 0)
                <button wire:click="marcarTodasLidas"
                    class="text-sm bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-4 py-2 rounded-lg font-semibold hover:bg-green-200 transition">
                    ✓ Marcar todas lidas
                </button>
            @endif
        </div>
    </div>

    {{-- ─── Tabela de Notificações ──────────────────────────────────── --}}
    @php $notificacoes = $this->getNotificacoes(); @endphp

    <div class="elite-card overflow-hidden">

        {{-- Header --}}
        <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <span class="text-base font-bold text-gray-700 dark:text-gray-300">
                {{ number_format($notificacoes->total()) }} notificações encontradas
            </span>
            <span class="text-sm text-gray-400">Atualizando automaticamente</span>
        </div>

        {{-- Lista --}}
        <div class="divide-y divide-gray-50 dark:divide-gray-700">
            @forelse($notificacoes as $notif)
                @php
                    $dados    = $notif->data;
                    $lida     = ! is_null($notif->read_at);
                    $icone    = $dados['icone'] ?? '🔔';
                    $titulo   = $dados['titulo'] ?? '—';
                    $mensagem = $dados['mensagem'] ?? '';
                    $tipo     = $dados['tipo'] ?? 'outro';

                    // Buscar nome do destinatário
                    $destinatario = \App\Models\User::find($notif->notifiable_id);
                @endphp

                <div class="flex items-start gap-4 px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition {{ $lida ? 'opacity-75' : '' }}">

                    {{-- Ícone --}}
                    <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 shadow-sm flex items-center justify-center text-xl leading-none">
                        {{ $icone }}
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5 flex-wrap">
                            <span class="text-base font-semibold text-gray-800 dark:text-gray-200">{{ $titulo }}</span>
                            <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 px-2 py-0.5 rounded-full font-mono">{{ $tipo }}</span>
                            @if(! $lida)
                                <span class="w-2 h-2 rounded-full bg-orange-500 flex-shrink-0 animate-pulse"></span>
                            @endif
                        </div>
                        @if($mensagem)
                            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-1">{{ $mensagem }}</p>
                        @endif
                    </div>

                    {{-- Destinatário --}}
                    <div class="flex-shrink-0 text-right hidden md:block">
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                            {{ $destinatario?->razao_social ?? $destinatario?->name ?? 'Desconhecido' }}
                        </p>
                        <p class="text-[11px] text-gray-400">{{ $destinatario?->email }}</p>
                    </div>

                    {{-- Data --}}
                    <div class="flex-shrink-0 text-right">
                        <p class="text-xs text-gray-400">{{ $notif->created_at->format('d/m H:i') }}</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <div class="py-16 text-center">
                    <div class="text-5xl mb-3">🔕</div>
                    <p class="text-gray-400 dark:text-gray-500 font-semibold">Nenhuma notificação no período.</p>
                </div>
            @endforelse
        </div>

        {{-- Paginação --}}
        @if($notificacoes->hasPages())
            <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $notificacoes->links() }}
            </div>
        @endif
    </div>

    {{-- Tipos de Notificação Disponíveis --}}
    <div class="mt-6 elite-card p-5">
        <h3 class="section-title text-base mb-4">📋 Notificações Automáticas do Sistema</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @php
                $automaticas = [
                    ['icone' => '✅', 'titulo' => 'Pedido Confirmado', 'desc' => 'Disparada quando o admin confirma um pedido de compra.', 'canais' => 'DB + E-mail'],
                    ['icone' => '✍️', 'titulo' => 'Contrato para Assinar', 'desc' => 'Enviada ao gerar o contrato de compra e venda.', 'canais' => 'DB + E-mail'],
                    ['icone' => '💬', 'titulo' => 'Ticket Atualizado', 'desc' => 'Disparada quando o admin responde um chamado.', 'canais' => 'DB + E-mail'],
                    ['icone' => '🎉', 'titulo' => 'Cadastro Aprovado', 'desc' => 'Enviada quando o cliente é aprovado pelo admin.', 'canais' => 'DB + E-mail'],
                    ['icone' => '📄', 'titulo' => 'Documento Verificado/Rejeitado', 'desc' => 'Enviada após análise de documento enviado.', 'canais' => 'DB + E-mail'],
                ];
            @endphp
            @foreach($automaticas as $item)
                <div class="flex items-start gap-3 p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                    <span class="text-2xl leading-none flex-shrink-0">{{ $item['icone'] }}</span>
                    <div class="min-w-0">
                        <p class="text-base font-semibold text-gray-800 dark:text-gray-200">{{ $item['titulo'] }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $item['desc'] }}</p>
                        <span class="inline-block mt-1 text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 font-bold px-2 py-0.5 rounded-full">
                            {{ $item['canais'] }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</x-filament-panels::page>
