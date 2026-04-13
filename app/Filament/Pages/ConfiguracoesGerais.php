<?php

namespace App\Filament\Pages;

use App\Models\SystemSetting;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ConfiguracoesGerais extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.configuracoes-gerais';

    protected static string|\UnitEnum|null $navigationGroup = 'Configurações';
    protected static ?string $navigationLabel = 'Configurações Gerais';
    protected static ?string $title = 'Configurações do Sistema';

    // Configurações Gerais
    public string $site_nome = '';
    public bool $aprovacao_automatica = false;

    // Google reCAPTCHA
    public string $google_recaptcha_site_key = '';
    public string $google_recaptcha_secret_key = '';
    public bool $google_recaptcha_ativo = false;
    public string $google_recaptcha_score_minimo = '0.5';

    // Google Maps e OAuth
    public string $google_maps_api_key = '';
    public string $google_oauth_client_id = '';
    public string $google_oauth_client_secret = '';

    // E-mail SMTP
    public string $mail_smtp_host = '';
    public string $mail_smtp_port = '465';
    public string $mail_smtp_username = '';
    public string $mail_smtp_password = '';
    public string $mail_smtp_encryption = 'ssl';
    public string $mail_from_address = '';
    public string $mail_from_name = '';
    public bool $mail_smtp_ativo = false;

    public function mount(): void
    {
        $settings = SystemSetting::pluck('value', 'key')->toArray();

        foreach ($settings as $key => $value) {
            if (property_exists($this, $key)) {
                $tipo = SystemSetting::where('key', $key)->value('tipo');
                $this->$key = $tipo === 'boolean' ? (bool) $value : ($value ?? '');
            }
        }
    }

    public function save(): void
    {
        $fields = [
            'site_nome', 'aprovacao_automatica',
            'google_recaptcha_site_key', 'google_recaptcha_secret_key',
            'google_recaptcha_ativo', 'google_recaptcha_score_minimo',
            'google_maps_api_key', 'google_oauth_client_id',
            'google_oauth_client_secret',
            'mail_smtp_host', 'mail_smtp_port', 'mail_smtp_username',
            'mail_smtp_password', 'mail_smtp_encryption',
            'mail_from_address', 'mail_from_name', 'mail_smtp_ativo',
        ];

        foreach ($fields as $field) {
            $value = $this->$field;
            SystemSetting::set($field, is_bool($value) ? ($value ? '1' : '0') : $value);
        }

        // Recarregar config de mail em runtime
        $this->aplicarConfigMail();

        Notification::make()
            ->title('Configurações salvas com sucesso!')
            ->success()
            ->send();
    }

    public function testarEmail(): void
    {
        $this->aplicarConfigMail();

        try {
            \Illuminate\Support\Facades\Mail::raw(
                'Este é um e-mail de teste do Portal Elite Repasse. Se você recebeu, a configuração SMTP está funcionando!',
                function ($message) {
                    $message->to($this->mail_from_address)
                        ->subject('✅ Teste SMTP - Elite Repasse');
                }
            );

            Notification::make()
                ->title('E-mail de teste enviado!')
                ->body("Verifique a caixa de entrada de {$this->mail_from_address}")
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Erro ao enviar e-mail')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function aplicarConfigMail(): void
    {
        if ($this->mail_smtp_ativo && $this->mail_smtp_host) {
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.host' => $this->mail_smtp_host,
                'mail.mailers.smtp.port' => (int) $this->mail_smtp_port,
                'mail.mailers.smtp.username' => $this->mail_smtp_username,
                'mail.mailers.smtp.password' => $this->mail_smtp_password,
                'mail.mailers.smtp.encryption' => $this->mail_smtp_encryption,
            ]);
        }

        if ($this->mail_from_address) {
            config([
                'mail.from.address' => $this->mail_from_address,
                'mail.from.name' => $this->mail_from_name ?: config('app.name'),
            ]);
        }
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('salvar')
                ->label('Salvar Configurações')
                ->submit('save')
                ->color('primary'),
        ];
    }
}
