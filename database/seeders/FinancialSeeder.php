<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class FinancialSeeder extends Seeder
{
    public function run(): void
    {
        // Encontra um pedido "aguardando_pgto" ou cria um para testes de boleto
        $order = DB::table('orders')->where('status', 'aguardando_pgto')->first();

        if ($order) {
            DB::table('financials')->insert([
                'order_id' => $order->id,
                'invoice_url' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
                'boleto_url' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
                'digitable_line' => '34191.09008 63321.43088 12345.67890 1 12345678901234',
                'status' => 'em_aberto',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
