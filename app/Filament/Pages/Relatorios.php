<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vehicle;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Relatorios extends Page
{
    protected string $view = 'filament.pages.relatorios';

    protected static string|\UnitEnum|null $navigationGroup = 'Configurações';

    protected static ?string $navigationLabel = 'Relatórios';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $title = 'Relatórios e Análises';

    protected static ?int $navigationSort = 10;

    // ─── Filtros ─────────────────────────────────────────────────────
    public string $periodo = '30'; // dias

    // ─── Dados calculados ─────────────────────────────────────────────
    public array $dadosGrafico     = [];
    public array $resumoEstoque    = [];
    public array $resumoFinanceiro = [];
    public array $topVendas        = [];
    public array $clientesNovos    = [];

    public function mount(): void
    {
        $this->calcularDados();
    }

    public function filtrar(): void
    {
        $this->calcularDados();
    }

    private function calcularDados(): void
    {
        $dias      = (int) $this->periodo;
        $inicio    = now()->subDays($dias)->startOfDay();
        $fim       = now()->endOfDay();

        // ─── Resumo de Estoque ────────────────────────────────────────
        $this->resumoEstoque = [
            'total'       => Vehicle::count(),
            'disponivel'  => Vehicle::where('status', 'available')->count(),
            'reservado'   => Vehicle::where('status', 'reserved')->count(),
            'vendido'     => Vehicle::where('status', 'sold')->count(),
            'valor_total' => (float) Vehicle::where('status', 'available')->sum('sale_price'),
            'valor_medio' => (float) Vehicle::where('status', 'available')->avg('sale_price'),
            'abaixo_fipe' => Vehicle::where('status', 'available')
                ->whereColumn('sale_price', '<', 'fipe_price')
                ->count(),
        ];

        // ─── Resumo Financeiro ────────────────────────────────────────
        $pedidosPeriodo = Order::whereBetween('created_at', [$inicio, $fim]);

        $this->resumoFinanceiro = [
            'pedidos_total'      => $pedidosPeriodo->count(),
            'pedidos_confirmados'=> (clone $pedidosPeriodo)->where('status', 'confirmado')->count(),
            'pedidos_faturados'  => (clone $pedidosPeriodo)->where('status', 'faturado')->count(),
            'pedidos_cancelados' => (clone $pedidosPeriodo)->where('status', 'cancelado')->count(),
            'receita_total'      => (float) (clone $pedidosPeriodo)->whereIn('status', ['confirmado','faturado'])->sum('valor_compra'),
            'ticket_medio'       => (float) (clone $pedidosPeriodo)->whereIn('status', ['confirmado','faturado'])->avg('valor_compra'),
        ];

        // ─── Gráfico: Receita por dia ──────────────────────────────────
        $this->dadosGrafico = Order::whereIn('status', ['confirmado', 'faturado'])
            ->whereBetween('created_at', [$inicio, $fim])
            ->select(
                DB::raw('DATE(created_at) as data'),
                DB::raw('SUM(valor_compra) as total'),
                DB::raw('COUNT(*) as qtd')
            )
            ->groupBy('data')
            ->orderBy('data')
            ->get()
            ->map(fn ($r) => [
                'data'  => Carbon::parse($r->data)->format('d/m'),
                'total' => (float) $r->total,
                'qtd'   => (int) $r->qtd,
            ])
            ->toArray();

        // ─── Top Veículos mais vendidos ───────────────────────────────
        $this->topVendas = Order::whereIn('status', ['confirmado', 'faturado'])
            ->whereBetween('created_at', [$inicio, $fim])
            ->with('vehicle:id,brand,model,model_year,plate,sale_price')
            ->select('vehicle_id', DB::raw('COUNT(*) as qtd'), DB::raw('SUM(valor_compra) as total'))
            ->groupBy('vehicle_id')
            ->orderByDesc('qtd')
            ->limit(10)
            ->get()
            ->map(fn ($r) => [
                'veiculo' => $r->vehicle
                    ? "{$r->vehicle->brand} {$r->vehicle->model} {$r->vehicle->model_year}"
                    : 'Desconhecido',
                'placa'   => $r->vehicle?->plate ?? '—',
                'qtd'     => $r->qtd,
                'total'   => (float) $r->total,
            ])
            ->toArray();

        // ─── Clientes novos por dia ───────────────────────────────────
        $this->clientesNovos = User::where('is_admin', false)
            ->whereBetween('created_at', [$inicio, $fim])
            ->select(DB::raw('DATE(created_at) as data'), DB::raw('COUNT(*) as qtd'))
            ->groupBy('data')
            ->orderBy('data')
            ->get()
            ->map(fn ($r) => [
                'data' => Carbon::parse($r->data)->format('d/m'),
                'qtd'  => (int) $r->qtd,
            ])
            ->toArray();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportar_csv')
                ->label('Exportar CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(fn () => $this->exportarCsv()),

            Action::make('exportar_pdf')
                ->label('Exportar PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(fn () => $this->exportarPdf()),
        ];
    }

    public function exportarCsv()
    {
        $dias   = (int) $this->periodo;
        $inicio = now()->subDays($dias)->startOfDay();

        $orders = Order::whereIn('status', ['confirmado', 'faturado'])
            ->where('created_at', '>=', $inicio)
            ->with(['user', 'vehicle', 'paymentMethod'])
            ->get();

        $csv = "Nº Pedido;Cliente;CNPJ;Veículo;Placa;Valor;Pagamento;Status;Data\n";
        foreach ($orders as $o) {
            $csv .= implode(';', [
                'ORD-' . str_pad($o->id, 6, '0', STR_PAD_LEFT),
                $o->user?->razao_social ?? $o->user?->name ?? '',
                $o->user?->cnpj ?? '',
                ($o->vehicle ? "{$o->vehicle->brand} {$o->vehicle->model} {$o->vehicle->model_year}" : ''),
                $o->vehicle?->plate ?? '',
                number_format((float) $o->valor_compra, 2, ',', '.'),
                $o->paymentMethod?->nome ?? '',
                $o->status,
                $o->created_at->format('d/m/Y'),
            ]) . "\n";
        }

        return response()->streamDownload(
            fn () => print($csv),
            'relatorio-vendas-' . now()->format('Ymd') . '.csv',
            ['Content-Type' => 'text/csv; charset=UTF-8']
        );
    }

    public function exportarPdf()
    {
        // Usará a view blade para geração de PDF via barryvdh/laravel-dompdf se instalado
        // Fallback: redirecionar para página de impressão
        return redirect()->to('/admin/relatorios/pdf?periodo=' . $this->periodo);
    }
}
