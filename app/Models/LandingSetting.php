<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingSetting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'features' => 'array',
        'faq' => 'array',
        'menu_items' => 'array',
        'footer_links' => 'array',
    ];

    public function banners()
    {
        return $this->hasMany(LandingBanner::class)->orderBy('order');
    }

    public static function defaults(): array
    {
        return [
            'hero_title' => 'Acelere sua venda de carros com a Elite Repasse',
            'hero_subtitle' => 'Compre seminovos com as melhores condicoes do mercado para o seu negocio de forma 100% online.',
            'whatsapp_number' => '5511999999999',
            'logo_path' => null,
            'menu_items' => [
                ['label' => 'Modelos', 'url' => '#modelos'],
                ['label' => 'Vantagens', 'url' => '#vantagens'],
                ['label' => 'Como Funciona', 'url' => '#como-funciona'],
                ['label' => 'Sobre Nós', 'url' => '#sobre'],
                ['label' => 'Contato', 'url' => '#contato'],
                ['label' => 'FAQ', 'url' => '#faq'],
            ],
            'about_title' => 'Sobre a Elite Repasse',
            'about_text' => 'Somos uma plataforma digital B2B focada em conectar lojistas a oportunidades de compra de seminovos com segurança, agilidade e transparência.',
            'about_image' => null,
            'contact_phone' => '',
            'contact_email' => '',
            'contact_address' => '',
            'contact_city' => 'Belo Horizonte',
            'contact_state' => 'MG',
            'contact_lat' => '',
            'contact_lng' => '',
            'footer_text' => 'Plataforma digital B2B para compra de seminovos com eficiência operacional para lojistas.',
            'footer_links' => [
                ['label' => 'Perguntas frequentes', 'url' => '#faq'],
                ['label' => 'Entrar', 'url' => '/login'],
                ['label' => 'Cadastre-se', 'url' => '/register'],
            ],
            'social_instagram' => '',
            'social_facebook' => '',
            'social_youtube' => '',
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
