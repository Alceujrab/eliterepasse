<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingSetting;
use Illuminate\View\View;

class LandingSettingsIndexController extends Controller
{
    public function __invoke(): View
    {
        $setting = LandingSetting::query()->latest('id')->first() ?? new LandingSetting([
            'hero_title' => 'Acelere sua venda de carros com a Elite Repasse',
            'hero_subtitle' => 'Compre seminovos com as melhores condicoes do mercado para o seu negocio de forma 100% online.',
            'whatsapp_number' => '5511999999999',
            'features' => [
                ['title' => 'Diversidade de estoque', 'description' => 'Acesso a veiculos de repasse e frota em todo o Brasil.', 'icon' => 'truck'],
                ['title' => 'Gestao simplificada', 'description' => 'Fluxo de compra, documentos e suporte no mesmo portal.', 'icon' => 'check-circle'],
                ['title' => 'Atendimento consultivo', 'description' => 'Equipe pronta para orientar o lojista no processo.', 'icon' => 'message-circle'],
            ],
            'faq' => [
                ['question' => 'Como funciona o portal?', 'answer' => 'O lojista se cadastra, aprova o acesso e compra veiculos pelo ambiente B2B.'],
                ['question' => 'Posso tirar duvidas antes de comprar?', 'answer' => 'Sim. Nossa equipe comercial e operacional presta suporte durante toda a jornada.'],
            ],
        ]);

        $features = collect(old('features', $setting->features ?? []))->take(6)->values()->all();
        $faq = collect(old('faq', $setting->faq ?? []))->take(6)->values()->all();

        while (count($features) < 6) {
            $features[] = ['title' => '', 'description' => '', 'icon' => ''];
        }

        while (count($faq) < 6) {
            $faq[] = ['question' => '', 'answer' => ''];
        }

        return view('admin.landing-settings.index', [
            'setting' => $setting,
            'featuresRows' => $features,
            'faqRows' => $faq,
        ]);
    }
}