<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $order = DB::table('orders')->first();
        $user = DB::table('users')->where('email', 'alceujr.ab@gmail.com')->first();
        
        $admin = DB::table('users')->where('email', 'suporte@eliterepasse.com.br')->first();
        if (!$admin) {
            $adminId = DB::table('users')->insertGetId([
                'name' => 'Elite Repasse (Suporte)',
                'email' => 'suporte@eliterepasse.com.br',
                'password' => bcrypt('password'),
                'is_admin' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        } else {
            $adminId = $admin->id;
        }

        if ($order && $user) {
            $ticketId = DB::table('tickets')->insertGetId([
                'user_id' => $user->id,
                'type' => 'Dúvida sobre documentação',
                'status' => 'andamento',
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ]);

            DB::table('ticket_messages')->insert([
                [
                    'ticket_id' => $ticketId,
                    'user_id' => $user->id,
                    'message' => 'Olá, gostaria de saber se o CRV já está disponível para transferência. O cliente já está perguntando.',
                    'created_at' => Carbon::now()->subHours(24),
                    'updated_at' => Carbon::now()->subHours(24),
                ],
                [
                    'ticket_id' => $ticketId,
                    'user_id' => $adminId,
                    'message' => 'Olá! Sim, o documento já está no nosso despachante central. Enviaremos as custas via Sedex amanhã pela manhã.',
                    'created_at' => Carbon::now()->subHours(20),
                    'updated_at' => Carbon::now()->subHours(20),
                ]
            ]);
        }
    }
}
