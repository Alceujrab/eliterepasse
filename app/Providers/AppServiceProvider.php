<?php

namespace App\Providers;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureMailFromDatabase();
    }

    private function configureMailFromDatabase(): void
    {
        try {
            if (! Schema::hasTable('system_settings')) {
                return;
            }

            $smtpAtivo = SystemSetting::get('mail_smtp_ativo', false);

            if ($smtpAtivo && $smtpAtivo !== '0') {
                $host = SystemSetting::get('mail_smtp_host');
                if ($host) {
                    Config::set('mail.default', 'smtp');
                    Config::set('mail.mailers.smtp.host', $host);
                    Config::set('mail.mailers.smtp.port', (int) SystemSetting::get('mail_smtp_port', 465));
                    Config::set('mail.mailers.smtp.username', SystemSetting::get('mail_smtp_username'));
                    Config::set('mail.mailers.smtp.password', SystemSetting::get('mail_smtp_password'));
                    Config::set('mail.mailers.smtp.encryption', SystemSetting::get('mail_smtp_encryption', 'ssl'));
                }
            }

            $fromAddress = SystemSetting::get('mail_from_address');
            if ($fromAddress) {
                Config::set('mail.from.address', $fromAddress);
                Config::set('mail.from.name', SystemSetting::get('mail_from_name', config('app.name')));
            }
        } catch (\Exception $e) {
            // DB não disponível durante migrations/install — ignora silenciosamente
        }
    }
}
