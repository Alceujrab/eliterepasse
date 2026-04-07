<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Order;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Pega a primeira empresa e veiculos.
        $company = DB::table('companies')->first();
        if (!$company) return;

        $vehicles = DB::table('vehicles')->take(2)->get();

        if ($vehicles->count() == 0) return;

        // Cria o pedido 1 vinculando ao Jeep e Onix (ficticio)
        $orderId = DB::table('orders')->insertGetId([
            'company_id' => $company->id,
            'status' => 'faturado',
            'total_amount' => $vehicles->sum('sale_price'),
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subDays(2),
        ]);

        foreach ($vehicles as $vehicle) {
            DB::table('order_vehicle')->insert([
                'order_id' => $orderId,
                'vehicle_id' => $vehicle->id,
            ]);
        }

        // Outro pedido com status diferente
        $vehicle2 = DB::table('vehicles')->skip(2)->first();
        if ($vehicle2) {
            $order2Id = DB::table('orders')->insertGetId([
                'company_id' => $company->id,
                'status' => 'aguardando_pgto',
                'total_amount' => $vehicle2->sale_price,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            DB::table('order_vehicle')->insert([
                'order_id' => $order2Id,
                'vehicle_id' => $vehicle2->id,
            ]);
        }
    }
}
