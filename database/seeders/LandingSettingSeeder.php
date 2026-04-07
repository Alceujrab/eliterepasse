<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class LandingSettingSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('landing_settings')->count() === 0) {
            DB::table('landing_settings')->insert([
                'hero_title' => 'Acelere sua venda de carros com a Elite Repasse',
                'hero_subtitle' => 'Compre seminovos com as melhores condições do mercado para o seu negócio de forma 100% online.',
                'whatsapp_number' => '5511999999999',
                'features' => json_encode([
                    [
                        'title' => 'Diversidade de estoque',
                        'description' => 'Acesso a milhares de veículos de repasse e frota em todo o Brasil.',
                        'icon' => 'truck',
                    ],
                    [
                        'title' => 'Gestão simplificada',
                        'description' => 'Acompanhe seus pedidos, faturas e boletos em um único dashboard.',
                        'icon' => 'chart-bar',
                    ],
                    [
                        'title' => 'Documentação garantida',
                        'description' => 'Equipe de despachantes pronta para enviar o CRV direto para sua loja.',
                        'icon' => 'document-check',
                    ]
                ]),
                'faq' => json_encode([
                    [
                        'question' => 'Sou pessoa física, posso comprar?',
                        'answer' => 'No momento, o Portal do Lojista da Elite Repasse atende exclusivamente CNPJs dos segmentos de lojas e concessionárias.',
                    ],
                    [
                        'question' => 'Quais as formas de pagamento?',
                        'answer' => 'Aceitamos pagamentos via Boleto Bancário faturado e PIX, com aprovação sujeita a análise de crédito.',
                    ],
                    [
                        'question' => 'Como funciona o transporte do veículo?',
                        'answer' => 'A logística pode ser feita pelo nosso parceiro ou você pode enviar sua transportadora própria para retirar no pátio.',
                    ]
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
