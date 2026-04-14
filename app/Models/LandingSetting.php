<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingSetting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'features' => 'array',
        'faq' => 'array',
    ];

    public static function defaults(): array
    {
        return [
            'hero_title' => 'Acelere sua venda de carros com a Elite Repasse',
            'hero_subtitle' => 'Compre seminovos com as melhores condicoes do mercado para o seu negocio de forma 100% online.',
            'whatsapp_number' => '5511999999999',
            'features' => [
                [
                    'title' => 'Diversidade de estoque',
                    'description' => 'Acesso a veiculos de repasse e frota em todo o Brasil.',
                    'icon' => 'truck',
                ],
                [
                    'title' => 'Gestao simplificada',
                    'description' => 'Fluxo de compra, documentos e suporte no mesmo portal.',
                    'icon' => 'check-circle',
                ],
                [
                    'title' => 'Atendimento consultivo',
                    'description' => 'Equipe pronta para orientar o lojista no processo.',
                    'icon' => 'message-circle',
                ],
            ],
            'faq' => [
                [
                    'question' => 'Como funciona o portal?',
                    'answer' => 'O lojista se cadastra, aprova o acesso e compra veiculos pelo ambiente B2B.',
                ],
                [
                    'question' => 'Posso tirar duvidas antes de comprar?',
                    'answer' => 'Sim. Nossa equipe comercial e operacional presta suporte durante toda a jornada.',
                ],
            ],
        ];
    }
}
