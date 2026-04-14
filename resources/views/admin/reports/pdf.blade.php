<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatorio Admin v2</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #0f172a; font-size: 12px; }
        h1, h2 { margin: 0 0 8px; }
        .muted { color: #475569; }
        .grid { width: 100%; margin: 18px 0; }
        .card { border: 1px solid #cbd5e1; border-radius: 10px; padding: 12px; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px; text-align: left; }
        th { background: #e2e8f0; }
    </style>
</head>
<body>
    <h1>Relatorio Admin v2</h1>
    <p class="muted">Periodo analisado: {{ $start->format('d/m/Y') }} ate {{ $end->format('d/m/Y') }}</p>

    <div class="grid">
        <div class="card">
            <h2>Receita</h2>
            <p>Receita no periodo: R$ {{ number_format($financeSummary['grossRevenue'], 2, ',', '.') }}</p>
            <p>Ticket medio: R$ {{ number_format($financeSummary['averageTicket'], 2, ',', '.') }}</p>
            <p>Conversao: {{ number_format($commercialSummary['conversionRate'], 1, ',', '.') }}%</p>
        </div>
        <div class="card">
            <h2>Estoque</h2>
            <p>Total: {{ number_format($inventorySummary['total']) }}</p>
            <p>Disponiveis: {{ number_format($inventorySummary['available']) }}</p>
            <p>Vendidos: {{ number_format($inventorySummary['sold']) }}</p>
        </div>
        <div class="card">
            <h2>Financeiro</h2>
            <p>Em aberto: {{ number_format($financeSummary['openFinancials']) }}</p>
            <p>Vencidos: {{ number_format($financeSummary['overdueFinancials']) }}</p>
            <p>Tickets urgentes: {{ number_format($commercialSummary['urgentTickets']) }}</p>
        </div>
    </div>

    <h2>Top vendas</h2>
    <table>
        <thead>
            <tr>
                <th>Veiculo</th>
                <th>Placa</th>
                <th>Qtd</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topSales as $sale)
                <tr>
                    <td>{{ $sale['vehicle'] }}</td>
                    <td>{{ $sale['plate'] }}</td>
                    <td>{{ $sale['quantity'] }}</td>
                    <td>R$ {{ number_format($sale['total'], 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Pedidos usados na exportacao</h2>
    <table>
        <thead>
            <tr>
                <th>Pedido</th>
                <th>Cliente</th>
                <th>Veiculo</th>
                <th>Valor</th>
                <th>Status</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders->take(30) as $order)
                <tr>
                    <td>{{ $order->numero }}</td>
                    <td>{{ $order->user?->razao_social ?? $order->user?->name }}</td>
                    <td>{{ $order->vehicle ? trim($order->vehicle->brand . ' ' . $order->vehicle->model . ' ' . $order->vehicle->model_year) : 'Sem veiculo' }}</td>
                    <td>R$ {{ number_format((float) $order->valor_compra, 2, ',', '.') }}</td>
                    <td>{{ $order->status }}</td>
                    <td>{{ $order->created_at?->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>