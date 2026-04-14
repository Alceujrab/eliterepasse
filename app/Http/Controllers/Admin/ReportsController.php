<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Financial;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vehicle;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    public function index(Request $request): View
    {
        $report = $this->buildReport((int) $request->integer('periodo', 30));

        return view('admin.reports.index', $report);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $report = $this->buildReport((int) $request->integer('periodo', 30));

        $csv = "Numero Pedido;Cliente;CNPJ;Veiculo;Placa;Valor;Pagamento;Status;Data\n";

        foreach ($report['orders'] as $order) {
            $csv .= implode(';', [
                $order->numero,
                $order->user?->razao_social ?? $order->user?->name ?? '',
                $order->user?->cnpj ?? '',
                $order->vehicle ? trim($order->vehicle->brand . ' ' . $order->vehicle->model . ' ' . $order->vehicle->model_year) : '',
                $order->vehicle?->plate ?? '',
                number_format((float) $order->valor_compra, 2, ',', '.'),
                $order->paymentMethod?->nome ?? '',
                $order->status,
                $order->created_at?->format('d/m/Y') ?? '',
            ]) . "\n";
        }

        return response()->streamDownload(
            fn () => print($csv),
            'relatorio-v2-' . now()->format('Ymd_His') . '.csv',
            ['Content-Type' => 'text/csv; charset=UTF-8']
        );
    }

    public function exportPdf(Request $request): Response
    {
        $report = $this->buildReport((int) $request->integer('periodo', 30));

        $pdf = Pdf::loadView('admin.reports.pdf', $report)->setPaper('a4', 'portrait');

        return $pdf->download('relatorio-v2-' . now()->format('Ymd_His') . '.pdf');
    }

    private function buildReport(int $periodDays): array
    {
        $periodDays = in_array($periodDays, [7, 15, 30, 60, 90], true) ? $periodDays : 30;
        $start = now()->subDays($periodDays)->startOfDay();
        $end = now()->endOfDay();

        $ordersInPeriod = Order::query()->whereBetween('created_at', [$start, $end]);
        $confirmedStatuses = ['confirmado', 'faturado', 'pago'];

        $inventorySummary = [
            'total' => Vehicle::count(),
            'available' => Vehicle::where('status', 'available')->count(),
            'reserved' => Vehicle::where('status', 'reserved')->count(),
            'sold' => Vehicle::where('status', 'sold')->count(),
            'availableValue' => (float) Vehicle::where('status', 'available')->sum('sale_price'),
            'averageValue' => (float) Vehicle::where('status', 'available')->avg('sale_price'),
            'belowFipe' => Vehicle::where('status', 'available')->whereColumn('sale_price', '<', 'fipe_price')->count(),
        ];

        $financeSummary = [
            'ordersTotal' => (clone $ordersInPeriod)->count(),
            'confirmed' => (clone $ordersInPeriod)->where('status', 'confirmado')->count(),
            'invoiced' => (clone $ordersInPeriod)->where('status', 'faturado')->count(),
            'paid' => (clone $ordersInPeriod)->where('status', 'pago')->count(),
            'cancelled' => (clone $ordersInPeriod)->where('status', 'cancelado')->count(),
            'grossRevenue' => (float) (clone $ordersInPeriod)->whereIn('status', $confirmedStatuses)->sum('valor_compra'),
            'averageTicket' => (float) (clone $ordersInPeriod)->whereIn('status', $confirmedStatuses)->avg('valor_compra'),
            'openFinancials' => Financial::where('status', 'em_aberto')->count(),
            'overdueFinancials' => Financial::where('status', 'em_aberto')->whereDate('data_vencimento', '<', now()->toDateString())->count(),
        ];

        $commercialSummary = [
            'newClients' => User::where('is_admin', false)->whereBetween('created_at', [$start, $end])->count(),
            'urgentTickets' => Ticket::where('prioridade', 'alta')->whereNotIn('status', ['resolvido', 'fechado'])->count(),
            'conversionRate' => $financeSummary['ordersTotal'] > 0
                ? round((($financeSummary['confirmed'] + $financeSummary['invoiced'] + $financeSummary['paid']) / $financeSummary['ordersTotal']) * 100, 1)
                : 0,
        ];

        $revenueSeries = Order::query()
            ->whereIn('status', $confirmedStatuses)
            ->whereBetween('created_at', [$start, $end])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(valor_compra) as total'), DB::raw('COUNT(*) as quantity'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'label' => Carbon::parse($row->date)->format('d/m'),
                'total' => (float) $row->total,
                'quantity' => (int) $row->quantity,
            ])
            ->values();

        $topSales = Order::query()
            ->whereIn('status', $confirmedStatuses)
            ->whereBetween('created_at', [$start, $end])
            ->with('vehicle:id,brand,model,model_year,plate')
            ->select('vehicle_id', DB::raw('COUNT(*) as quantity'), DB::raw('SUM(valor_compra) as total'))
            ->groupBy('vehicle_id')
            ->orderByDesc('quantity')
            ->limit(8)
            ->get()
            ->map(fn ($row) => [
                'vehicle' => $row->vehicle ? trim($row->vehicle->brand . ' ' . $row->vehicle->model . ' ' . $row->vehicle->model_year) : 'Desconhecido',
                'plate' => $row->vehicle?->plate ?? 'Sem placa',
                'quantity' => (int) $row->quantity,
                'total' => (float) $row->total,
            ])
            ->values();

        $newClientsSeries = User::query()
            ->where('is_admin', false)
            ->whereBetween('created_at', [$start, $end])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as quantity'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'label' => Carbon::parse($row->date)->format('d/m'),
                'quantity' => (int) $row->quantity,
            ])
            ->values();

        $orders = Order::query()
            ->whereIn('status', $confirmedStatuses)
            ->whereBetween('created_at', [$start, $end])
            ->with(['user', 'vehicle', 'paymentMethod'])
            ->latest()
            ->limit(200)
            ->get();

        return [
            'periodDays' => $periodDays,
            'start' => $start,
            'end' => $end,
            'inventorySummary' => $inventorySummary,
            'financeSummary' => $financeSummary,
            'commercialSummary' => $commercialSummary,
            'revenueSeries' => $revenueSeries,
            'topSales' => $topSales,
            'newClientsSeries' => $newClientsSeries,
            'orders' => $orders,
        ];
    }
}