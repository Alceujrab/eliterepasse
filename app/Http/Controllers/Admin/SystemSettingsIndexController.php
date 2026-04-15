<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\View\View;

class SystemSettingsIndexController extends Controller
{
    public function __invoke(): View
    {
        $settings = [
            'site_nome' => (string) SystemSetting::get('site_nome', config('app.name', 'Elite Repasse')),
            'aprovacao_automatica' => (bool) SystemSetting::get('aprovacao_automatica', false),
            'google_recaptcha_site_key' => (string) SystemSetting::get('google_recaptcha_site_key', ''),
            'google_recaptcha_secret_key' => (string) SystemSetting::get('google_recaptcha_secret_key', ''),
            'google_recaptcha_ativo' => (bool) SystemSetting::get('google_recaptcha_ativo', false),
            'google_recaptcha_score_minimo' => (string) SystemSetting::get('google_recaptcha_score_minimo', '0.5'),
            'google_maps_api_key' => (string) SystemSetting::get('google_maps_api_key', ''),
            'google_oauth_client_id' => (string) SystemSetting::get('google_oauth_client_id', ''),
            'google_oauth_client_secret' => (string) SystemSetting::get('google_oauth_client_secret', ''),
            'gemini_api_key' => (string) SystemSetting::get('gemini_api_key', ''),
            'mail_smtp_host' => (string) SystemSetting::get('mail_smtp_host', ''),
            'mail_smtp_port' => (string) SystemSetting::get('mail_smtp_port', '465'),
            'mail_smtp_username' => (string) SystemSetting::get('mail_smtp_username', ''),
            'mail_smtp_password' => (string) SystemSetting::get('mail_smtp_password', ''),
            'mail_smtp_encryption' => (string) SystemSetting::get('mail_smtp_encryption', 'ssl'),
            'mail_from_address' => (string) SystemSetting::get('mail_from_address', ''),
            'mail_from_name' => (string) SystemSetting::get('mail_from_name', config('app.name', 'Elite Repasse')),
            'mail_smtp_ativo' => (bool) SystemSetting::get('mail_smtp_ativo', false),
        ];

        return view('admin.settings.index', [
            'settings' => $settings,
        ]);
    }
}