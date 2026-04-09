<?php

namespace App\Livewire;

use App\Models\Favorite;
use App\Models\Order;
use App\Models\Vehicle;
use App\Services\NotificationService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class VehicleDetails extends Component
{
    public Vehicle $vehicle;
    public bool    $isFavorited = false;
    public int     $fotoAtual   = 0;
    public bool    $showProposta = false;
    public string  $observacoes = '';

    public function mount(int $id): void
    {
        $this->vehicle = Vehicle::findOrFail($id);
        $this->checkFavorite();
    }

    public function checkFavorite(): void
    {
        if (auth()->check()) {
            $this->isFavorited = Favorite::where('user_id', auth()->id())
                ->where('vehicle_id', $this->vehicle->id)
                ->exists();
        }
    }

    public function toggleFavorite(): void
    {
        if (! auth()->check()) {
            redirect()->route('login');
            return;
        }

        if ($this->isFavorited) {
            Favorite::where('user_id', auth()->id())->where('vehicle_id', $this->vehicle->id)->delete();
            $this->isFavorited = false;
        } else {
            Favorite::create(['user_id' => auth()->id(), 'vehicle_id' => $this->vehicle->id]);
            $this->isFavorited = true;
        }
    }

    public function fotoAnterior(): void
    {
        $media = $this->getMedia();
        $this->fotoAtual = $this->fotoAtual > 0 ? $this->fotoAtual - 1 : count($media) - 1;
    }

    public function fotoProxima(): void
    {
        $media = $this->getMedia();
        $this->fotoAtual = $this->fotoAtual < count($media) - 1 ? $this->fotoAtual + 1 : 0;
    }

    public function solicitarProposta(): void
    {
        if (! auth()->check()) {
            redirect()->route('login');
            return;
        }

        // Cria ou atualiza pedido pendente
        $order = Order::firstOrCreate(
            [
                'user_id'    => auth()->id(),
                'vehicle_id' => $this->vehicle->id,
                'status'     => 'pendente',
            ],
            [
                'valor_compra' => $this->vehicle->sale_price,
                'valor_fipe'   => $this->vehicle->fipe_price,
                'observacoes'  => $this->observacoes,
            ]
        );

        // Notificar admins sobre novo pedido (email + WhatsApp)
        if ($order->wasRecentlyCreated) {
            \App\Models\OrderHistory::registrar($order->id, 'pedido_criado', null, 'pendente');
            app(NotificationService::class)->novoPedidoParaAdmin($order->load(['user', 'vehicle']));
        }

        session()->flash('message', "✅ Proposta #{$order->numero} enviada! Nossa equipe entrará em contato em breve.");
        $this->showProposta = false;
        $this->observacoes  = '';
    }

    // ─── Veículos Similares ────────────────────────────────────────────

    public function getSimilaresProperty()
    {
        return Vehicle::where('id', '!=', $this->vehicle->id)
            ->where('status', 'available')
            ->where(fn ($q) =>
                $q->where('brand', $this->vehicle->brand)
                  ->orWhere('category', $this->vehicle->category)
            )
            ->inRandomOrder()
            ->limit(4)
            ->get();
    }

    // ─── Helpers ─────────────────────────────────────────────────────

    public function getMedia(): array
    {
        $raw = $this->vehicle->media;
        $media = is_string($raw) ? json_decode($raw, true) : $raw;
        return is_array($media) && count($media) > 0 ? $media : [];
    }

    public function render()
    {
        return view('livewire.vehicle-details');
    }
}

