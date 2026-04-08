<?php

namespace App\Filament\Pages;

use App\Models\SystemSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
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

    // E-mail
    public string $mail_from_address = '';
    public string $mail_from_name = '';

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
            'google_oauth_client_secret', 'mail_from_address', 'mail_from_name',
        ];

        foreach ($fields as $field) {
            $value = $this->$field;
            SystemSetting::set($field, is_bool($value) ? ($value ? '1' : '0') : $value);
        }

        Notification::make()
            ->title('Configurações salvas com sucesso!')
            ->success()
            ->send();
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
