<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $now = Carbon::now();

        // ─── Admin / Lojista Demo ─────────────────────────────────────
        $userId = DB::table('users')->insertGetId([
            'name'       => 'Alceu Lojista',
            'email'      => 'alceujr.ab@gmail.com',
            'cpf'        => '12345678900',
            'phone'      => '11999999999',
            'is_admin'   => true,
            'status'     => 'ativo',
            'password'   => Hash::make('password'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // ─── Empresa demo ─────────────────────────────────────────────
        $companyId = DB::table('companies')->insertGetId([
            'razao_social' => 'Elite Repasse Automóveis Ltda',
            'cnpj'         => '00.000.000/0001-91',
            'whatsapp'     => '11999999999',
            'address'      => 'Rua das Flores, 123',
            'city'         => 'São Paulo',
            'state'        => 'SP',
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        DB::table('company_user')->insert([
            'user_id'    => $userId,
            'company_id' => $companyId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // ─── Seeders encadeados ───────────────────────────────────────
        $this->call([
            VehicleSeeder::class,
            OrderSeeder::class,
            TicketSeeder::class,
            FinancialSeeder::class,
        ]);
    }
}
