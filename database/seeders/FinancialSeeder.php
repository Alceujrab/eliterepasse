<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class FinancialSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::whereIn('status', ['confirmado', 'faturado', 'aguardando_pgto'])->get();

        foreach ($orders as $i => $order) {
            $isPago = $order->status === 'faturado';
            $vencimento = now()->subDays(rand(-15, 30));

            DB::table('financials')->insert([
                'order_id'           => $order->id,
                'numero_fatura'      => 'FAT-' . now()->year . '-' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'valor'              => $order->valor_compra,
                'data_vencimento'    => $vencimento->toDateString(),
                'data_pagamento'     => $isPago ? $vencimento->copy()->subDays(2)->toDateString() : null,
                'forma_pagamento'    => $isPago ? 'Transferência Bancária' : null,
                'nota_fiscal_numero' => $isPago ? 'NF-' . rand(10000, 99999) : null,
                'observacoes'        => $i === 0 ? 'Pagamento antecipado com desconto de 2%.' : null,
                'invoice_url'        => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
                'boleto_url'         => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
                'digitable_line'     => '34191.09008 63321.43088 12345.67890 1 ' . rand(10000000000000, 99999999999999),
                'status'             => $isPago ? 'pago' : ($vencimento->isPast() ? 'vencido' : 'em_aberto'),
                'created_at'         => Carbon::now(),
                'updated_at'         => Carbon::now(),
            ]);
        }
    }
}
