<?php

namespace App\Livewire;

use App\Models\LandingSetting;
use App\Models\SystemSetting;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.landing')]
class Contato extends Component
{
    public string $nome = '';
    public string $email = '';
    public string $telefone = '';
    public string $assunto = '';
    public string $mensagem = '';

    public bool $enviado = false;

    protected function rules(): array
    {
        return [
            'nome'     => 'required|string|min:3|max:100',
            'email'    => 'required|email|max:150',
            'telefone' => 'nullable|string|max:20',
            'assunto'  => 'required|string|max:150',
            'mensagem' => 'required|string|min:10|max:2000',
        ];
    }

    protected function messages(): array
    {
        return [
            'nome.required'     => 'Informe seu nome completo.',
            'nome.min'          => 'O nome deve ter pelo menos 3 caracteres.',
            'email.required'    => 'Informe seu e-mail.',
            'email.email'       => 'Informe um e-mail válido.',
            'assunto.required'  => 'Selecione ou informe o assunto.',
            'mensagem.required' => 'Escreva sua mensagem.',
            'mensagem.min'      => 'A mensagem deve ter pelo menos 10 caracteres.',
        ];
    }

    public function enviar(): void
    {
        $this->validate();

        $settings = LandingSetting::first() ?? new LandingSetting(LandingSetting::defaults());
        $destinatario = $settings->contact_email ?: config('mail.from.address', 'contato@eliterepasse.com.br');

        \Illuminate\Support\Facades\Mail::raw(
            "Nome: {$this->nome}\nE-mail: {$this->email}\nTelefone: {$this->telefone}\nAssunto: {$this->assunto}\n\nMensagem:\n{$this->mensagem}",
            function ($mail) use ($destinatario) {
                $mail->to($destinatario)
                     ->replyTo($this->email, $this->nome)
                     ->subject("[Elite Repasse] Contato: {$this->assunto}");
            }
        );

        $this->reset(['nome', 'email', 'telefone', 'assunto', 'mensagem']);
        $this->enviado = true;
    }

    public function render()
    {
        $settings = LandingSetting::first() ?? new LandingSetting(LandingSetting::defaults());
        $defaults = LandingSetting::defaults();
        $mapsApiKey = SystemSetting::get('google_maps_api_key', '');

        $menuItems = collect($settings->menu_items ?? [])->filter(fn ($i) => filled($i['label'] ?? null))->values();
        if ($menuItems->isEmpty()) {
            $menuItems = collect($defaults['menu_items']);
        }
        $menuItems = $menuItems->map(function ($item) {
            if (($item['url'] ?? '') === '#contato') return array_merge($item, ['url' => '/contato']);
            if (($item['url'] ?? '') === '#sobre') return array_merge($item, ['url' => '/sobre-nos']);
            return $item;
        });

        $footerLinks = collect($settings->footer_links ?? [])->filter(fn ($i) => filled($i['label'] ?? null))->values();
        if ($footerLinks->isEmpty()) {
            $footerLinks = collect($defaults['footer_links']);
        }

        $logoUrl = $settings->logo_path
            ? asset($settings->logo_path)
            : asset('build/assets/logo.png');

        $hasMap = filled($settings->contact_lat) && filled($settings->contact_lng) && filled($mapsApiKey);

        return view('livewire.contato', [
            'settings'    => $settings,
            'defaults'    => $defaults,
            'mapsApiKey'  => $mapsApiKey,
            'menuItems'   => $menuItems,
            'footerLinks' => $footerLinks,
            'logoUrl'     => $logoUrl,
            'hasMap'      => $hasMap,
        ]);
    }
}
