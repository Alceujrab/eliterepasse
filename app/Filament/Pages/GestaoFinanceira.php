<?php

namespace App\Filament\Pages;

use App\Models\Financial;
use App\Models\Order;
use App\Models\User;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class GestaoFinanceira extends Page
{
    protected string $view = 'filament.pages.gestao-financeira';

    protected static string|\UnitEnum|null $navigationGroup = 'Financeiro';

    protected static ?string $navigationLabel = 'Gestão Financeira';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $title = 'Gestão Financeira';

    protected static ?int $navigationSort = 1;

    // ─── Filtros ──────────────────────────────────────────────────────
    public string $filtroStatus = 'todos';
    public string $buscaCliente = '';

    // ─── Modal de Criar/Editar Cobrança ──────────────────────────────
    public bool   $showForm      = false;
    public ?int   $editingId     = null;
    public ?int   $orderId       = null;
    public string $status        = 'em_aberto';
    public string $numeroFatura  = '';
    public string $valor         = '';
    public string $dataVencimento = '';
    public string $dataPagamento  = '';
    public string $formaPagamento = 'boleto';
    public string $boletoUrl      = '';
    public string $invoiceUrl     = '';
    public string $digitableLine  = '';
    public string $notaFiscalNumero = '';
    public string $observacoes    = '';

    public static function getNavigationBadge(): ?string
    {
        $vencidos = Financial::where('status', 'em_aberto')
            ->where('data_vencimento', '<', now())
            ->count();
        return $vencidos > 0 ? (string) $vencidos : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    // ─── Dados ───────────────────────────────────────────────────────

    public function getFinanciaisProperty()
    {
        return Financial::with(['order.user', 'order.vehicle', 'criadoPor'])
            ->when($this->filtroStatus !== 'todos', fn ($q) => $q->where('status', $this->filtroStatus))
            ->when($this->buscaCliente, function ($q) {
                $q->whereHas('order.user', fn ($u) =>
                    $u->where('razao_social', 'LIKE', "%{$this->buscaCliente}%")
                      ->orWhere('name', 'LIKE', "%{$this->buscaCliente}%")
                      ->orWhere('cnpj', 'LIKE', "%{$this->buscaCliente}%")
                )->orWhereHas('order', fn ($o) =>
                    $o->where('numero', 'LIKE', "%{$this->buscaCliente}%")
                );
            })
            ->latest()
            ->get();
    }

    public function getKpisProperty(): array
    {
        return [
            'total_mes'   => Financial::whereMonth('created_at', now()->month)->sum('valor'),
            'em_aberto'   => Financial::where('status', 'em_aberto')->sum('valor'),
            'vencidos'    => Financial::where('status', 'em_aberto')->where('data_vencimento', '<', now())->count(),
            'pagos_mes'   => Financial::where('status', 'pago')->whereMonth('data_pagamento', now()->month)->sum('valor'),
            'count_total' => Financial::count(),
        ];
    }

    public function getPedidosSemFinancieroProperty()
    {
        return Order::whereDoesntHave('financial')
            ->whereIn('status', ['confirmado', 'faturado', 'aguardando_pgto'])
            ->with('user')
            ->latest()
            ->limit(20)
            ->get();
    }

    // ─── CRUD ─────────────────────────────────────────────────────────

    public function novaCobranca(?int $orderId = null): void
    {
        $this->reset(['editingId', 'status', 'valor', 'dataVencimento', 'dataPagamento',
            'boletoUrl', 'invoiceUrl', 'digitableLine', 'notaFiscalNumero', 'observacoes']);
        $this->formaPagamento = 'boleto';
        $this->status         = 'em_aberto';
        $this->orderId        = $orderId;
        $this->numeroFatura   = 'FAT-' . now()->year . '-' . str_pad(Financial::max('id') + 1, 6, '0', STR_PAD_LEFT);

        // Preencher valor automaticamente se tiver pedido
        if ($orderId) {
            $order = Order::find($orderId);
            $this->valor = $order?->valor_compra ?? '';
        }

        $this->showForm = true;
    }

    public function editarCobranca(int $id): void
    {
        $f = Financial::findOrFail($id);
        $this->editingId        = $id;
        $this->orderId          = $f->order_id;
        $this->status           = $f->status;
        $this->numeroFatura     = $f->numero_fatura ?? '';
        $this->valor            = $f->valor ?? '';
        $this->dataVencimento   = $f->data_vencimento?->format('Y-m-d') ?? '';
        $this->dataPagamento    = $f->data_pagamento?->format('Y-m-d') ?? '';
        $this->formaPagamento   = $f->forma_pagamento ?? 'boleto';
        $this->boletoUrl        = $f->boleto_url ?? '';
        $this->invoiceUrl       = $f->invoice_url ?? '';
        $this->digitableLine    = $f->digitable_line ?? '';
        $this->notaFiscalNumero = $f->nota_fiscal_numero ?? '';
        $this->observacoes      = $f->observacoes ?? '';
        $this->showForm         = true;
    }

    public function salvar(): void
    {
        $this->validate([
            'orderId'       => 'required|exists:orders,id',
            'status'        => 'required',
            'valor'         => 'nullable|numeric|min:0',
            'dataVencimento'=> 'nullable|date',
            'dataPagamento' => 'nullable|date',
            'boletoUrl'     => 'nullable|url',
            'invoiceUrl'    => 'nullable|url',
        ]);

        $dados = [
            'order_id'          => $this->orderId,
            'status'            => $this->status,
            'numero_fatura'     => $this->numeroFatura ?: null,
            'valor'             => $this->valor ?: null,
            'data_vencimento'   => $this->dataVencimento ?: null,
            'data_pagamento'    => $this->dataPagamento ?: null,
            'forma_pagamento'   => $this->formaPagamento ?: null,
            'boleto_url'        => $this->boletoUrl ?: null,
            'invoice_url'       => $this->invoiceUrl ?: null,
            'digitable_line'    => $this->digitableLine ?: null,
            'nota_fiscal_numero'=> $this->notaFiscalNumero ?: null,
            'observacoes'       => $this->observacoes ?: null,
            'criado_por'        => auth()->id(),
        ];

        if ($this->editingId) {
            Financial::findOrFail($this->editingId)->update($dados);
            Notification::make()->title('Cobrança atualizada!')->success()->send();
        } else {
            Financial::create($dados);
            Notification::make()->title('Cobrança criada!')->success()->send();
        }

        $this->showForm = false;
    }

    public function marcarPago(int $id): void
    {
        Financial::findOrFail($id)->update([
            'status'         => 'pago',
            'data_pagamento' => now()->toDateString(),
        ]);
        Notification::make()->title('✅ Cobrança marcada como paga!')->success()->send();
    }

    public function excluir(int $id): void
    {
        Financial::findOrFail($id)->delete();
        Notification::make()->title('Cobrança excluída.')->warning()->send();
    }
}
