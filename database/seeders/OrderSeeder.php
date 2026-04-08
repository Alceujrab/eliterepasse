<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'alceujr.ab@gmail.com')->first();
        if (! $user) return;

        $vehicles = Vehicle::where('status', 'disponivel')->take(6)->get();
        if ($vehicles->isEmpty()) return;

        $statuses = ['confirmado', 'pendente', 'faturado', 'aguardando_pgto', 'pendente', 'cancelado'];

        foreach ($vehicles as $i => $vehicle) {
            $status = $statuses[$i] ?? 'pendente';
            $daysAgo = rand(1, 60);

            $order = Order::create([
                'user_id'        => $user->id,
                'vehicle_id'     => $vehicle->id,
                'valor_compra'   => $vehicle->sale_price,
                'valor_fipe'     => $vehicle->fipe_price,
                'status'         => $status,
                'observacoes'    => $i === 0 ? 'Pagamento via transferência bancária. Retirada em SP.' : null,
                'confirmado_em'  => in_array($status, ['confirmado', 'faturado']) ? now()->subDays($daysAgo - 1) : null,
                'confirmado_por' => in_array($status, ['confirmado', 'faturado']) ? $user->id : null,
                'created_at'     => now()->subDays($daysAgo),
                'updated_at'     => now()->subDays(max(0, $daysAgo - 2)),
            ]);

            // Gera número
            $order->update(['numero' => 'PED-' . now()->year . '-' . str_pad($order->id, 6, '0', STR_PAD_LEFT)]);
        }
    }
}
