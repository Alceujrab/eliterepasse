<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $userId = \Illuminate\Support\Facades\DB::table('users')->insertGetId([
            'name' => 'Alceu Lojista',
            'email' => 'alceujr.ab@gmail.com',
            'cpf' => '12345678900',
            'phone' => '11999999999',
            'is_admin' => true,
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'created_at' => \Illuminate\Support\Carbon::now(),
            'updated_at' => \Illuminate\Support\Carbon::now(),
        ]);

        $companyId = \Illuminate\Support\Facades\DB::table('companies')->insertGetId([
            'razao_social' => 'Elite Repasse Automóveis',
            'cnpj' => '00.000.000/0001-91',
            'whatsapp' => '11999999999',
            'address' => 'Rua das Flores, 123',
            'city' => 'São Paulo',
            'state' => 'SP',
            'created_at' => \Illuminate\Support\Carbon::now(),
            'updated_at' => \Illuminate\Support\Carbon::now(),
        ]);

        \Illuminate\Support\Facades\DB::table('company_user')->insert([
            'user_id' => $userId,
            'company_id' => $companyId,
            'created_at' => \Illuminate\Support\Carbon::now(),
            'updated_at' => \Illuminate\Support\Carbon::now(),
        ]);
    }
}
