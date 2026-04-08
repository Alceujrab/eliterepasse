<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'alceujr.ab@gmail.com')->first();
        if (! $user) return;

        $tickets = [
            [
                'titulo'     => 'Dúvida sobre documentação do Compass',
                'categoria'  => 'veiculo',
                'prioridade' => 'media',
                'status'     => 'em_atendimento',
                'msgs'       => [
                    ['msg' => 'Olá, gostaria de saber se o CRV do Jeep Compass já está disponível para transferência. O cliente já está perguntando.', 'admin' => false, 'h' => 48],
                    ['msg' => 'Bom dia! O documento já está com nosso despachante central. Enviaremos via Sedex amanhã pela manhã.', 'admin' => true, 'h' => 44],
                    ['msg' => 'Perfeito, obrigado! Posso confirmar ao cliente então?', 'admin' => false, 'h' => 40],
                ],
            ],
            [
                'titulo'     => 'Problema no boleto - valor errado',
                'categoria'  => 'financeiro',
                'prioridade' => 'alta',
                'status'     => 'aberto',
                'msgs'       => [
                    ['msg' => 'O boleto do pedido PED-2026-000001 veio com valor divergente. O combinado era R$ 115.000 e o boleto está R$ 117.500.', 'admin' => false, 'h' => 6],
                ],
            ],
            [
                'titulo'     => 'Garantia do motor - T-Cross',
                'categoria'  => 'veiculo',
                'prioridade' => 'urgente',
                'status'     => 'resolvido',
                'msgs'       => [
                    ['msg' => 'O T-Cross que comprei está com barulho no motor. Está na garantia?', 'admin' => false, 'h' => 72],
                    ['msg' => 'Sim! A garantia de fábrica cobre por mais 2 anos. Agendamos a concessionária VW mais próxima para você.', 'admin' => true, 'h' => 68],
                    ['msg' => 'Agendamento confirmado para terça-feira, dia 15/04 às 9h na Volkswagen Interlagos.', 'admin' => true, 'h' => 66],
                    ['msg' => 'Perfeito, muito obrigado pelo suporte rápido!', 'admin' => false, 'h' => 60],
                ],
            ],
            [
                'titulo'     => 'Solicitar laudo cautelar',
                'categoria'  => 'duvida',
                'prioridade' => 'baixa',
                'status'     => 'aguardando_cliente',
                'msgs'       => [
                    ['msg' => 'Gostaria de solicitar o laudo cautelar do veículo Honda Civic.', 'admin' => false, 'h' => 120],
                    ['msg' => 'Por favor, confirme a placa completa para emitirmos o laudo.', 'admin' => true, 'h' => 96],
                ],
            ],
        ];

        foreach ($tickets as $t) {
            $ticket = Ticket::create([
                'user_id'        => $user->id,
                'titulo'         => $t['titulo'],
                'categoria'      => $t['categoria'],
                'prioridade'     => $t['prioridade'],
                'status'         => $t['status'],
                'prazo_resposta' => now()->addHours(Ticket::slaPorPrioridade($t['prioridade'])),
                'resolvido_em'   => $t['status'] === 'resolvido' ? now()->subHours(60) : null,
                'created_at'     => now()->subHours($t['msgs'][0]['h'] ?? 24),
                'updated_at'     => now(),
            ]);

            $ticket->update(['numero' => $ticket->gerarNumero()]);

            foreach ($t['msgs'] as $m) {
                TicketMessage::create([
                    'ticket_id' => $ticket->id,
                    'user_id'   => $user->id,
                    'mensagem'  => $m['msg'],
                    'is_admin'  => $m['admin'],
                    'created_at'=> now()->subHours($m['h']),
                ]);
            }
        }
    }
}
