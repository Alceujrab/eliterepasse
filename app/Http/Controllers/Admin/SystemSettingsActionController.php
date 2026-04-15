<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SystemSettingsActionController extends Controller
{
    private const BOOLEAN_FIELDS = [
        'aprovacao_automatica',
        'google_recaptcha_ativo',
        'mail_smtp_ativo',
    ];

    private const STRING_FIELDS = [
        'site_nome',
        'google_recaptcha_site_key',
        'google_recaptcha_secret_key',
        'google_recaptcha_score_minimo',
        'google_maps_api_key',
        'google_oauth_client_id',
        'google_oauth_client_secret',
        'gemini_api_key',
        'mail_smtp_host',
        'mail_smtp_port',
        'mail_smtp_username',
        'mail_smtp_password',
        'mail_smtp_encryption',
        'mail_from_address',
        'mail_from_name',
    ];

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        $payload = $this->buildPayload($request, $validated);

        foreach (self::STRING_FIELDS as $field) {
            SystemSetting::set($field, $payload[$field], 'string');
        }

        foreach (self::BOOLEAN_FIELDS as $field) {
            SystemSetting::set($field, $payload[$field] ? '1' : '0', 'boolean');
        }

        $this->applyMailConfig($payload);

        return redirect()->route('admin.v2.settings.index')
            ->with('admin_success', 'Configuracoes gerais atualizadas com sucesso.');
    }

    public function testEmail(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        $payload = $this->buildPayload($request, $validated);

        if (! $payload['mail_smtp_ativo'] || blank($payload['mail_smtp_host'])) {
            return redirect()->route('admin.v2.settings.index')
                ->withInput()
                ->with('admin_error', 'Ative o SMTP e informe o host antes de testar o envio.');
        }

        if (blank($payload['mail_from_address'])) {
            return redirect()->route('admin.v2.settings.index')
                ->withInput()
                ->with('admin_error', 'Informe o e-mail do remetente para receber o teste SMTP.');
        }

        $this->applyMailConfig($payload);

        try {
            Mail::raw(
                'Este e-mail confirma que a configuracao SMTP do Admin v2 esta funcionando corretamente.',
                function ($message) use ($payload) {
                    $message->to($payload['mail_from_address'])
                        ->subject('Teste SMTP - Elite Repasse');
                }
            );

            return redirect()->route('admin.v2.settings.index')
                ->withInput()
                ->with('admin_success', 'E-mail de teste enviado. Verifique a caixa de entrada do remetente configurado.');
        } catch (\Throwable $exception) {
            return redirect()->route('admin.v2.settings.index')
                ->withInput()
                ->with('admin_error', Str::limit($exception->getMessage(), 240));
        }
    }

    private function rules(): array
    {
        return [
            'site_nome' => ['nullable', 'string', 'max:255'],
            'aprovacao_automatica' => ['nullable', 'boolean'],
            'google_recaptcha_site_key' => ['nullable', 'string', 'max:255'],
            'google_recaptcha_secret_key' => ['nullable', 'string', 'max:255'],
            'google_recaptcha_ativo' => ['nullable', 'boolean'],
            'google_recaptcha_score_minimo' => ['nullable', 'numeric', 'between:0,1'],
            'google_maps_api_key' => ['nullable', 'string', 'max:255'],
            'google_oauth_client_id' => ['nullable', 'string', 'max:255'],
            'google_oauth_client_secret' => ['nullable', 'string', 'max:255'],
            'gemini_api_key' => ['nullable', 'string', 'max:255'],
            'mail_smtp_host' => ['nullable', 'string', 'max:255'],
            'mail_smtp_port' => ['nullable', 'string', 'max:20'],
            'mail_smtp_username' => ['nullable', 'string', 'max:255'],
            'mail_smtp_password' => ['nullable', 'string', 'max:255'],
            'mail_smtp_encryption' => ['nullable', 'in:ssl,tls,'],
            'mail_from_address' => ['nullable', 'email:rfc', 'max:255'],
            'mail_from_name' => ['nullable', 'string', 'max:255'],
            'mail_smtp_ativo' => ['nullable', 'boolean'],
        ];
    }

    private function buildPayload(Request $request, array $validated): array
    {
        $payload = [];

        foreach (self::STRING_FIELDS as $field) {
            $value = $validated[$field] ?? '';
            $payload[$field] = is_string($value) ? trim($value) : (string) $value;
        }

        foreach (self::BOOLEAN_FIELDS as $field) {
            $payload[$field] = $request->boolean($field);
        }

        if ($payload['google_recaptcha_score_minimo'] === '') {
            $payload['google_recaptcha_score_minimo'] = '0.5';
        }

        if ($payload['mail_smtp_port'] === '') {
            $payload['mail_smtp_port'] = '465';
        }

        if ($payload['mail_smtp_encryption'] === '') {
            $payload['mail_smtp_encryption'] = 'ssl';
        }

        if ($payload['site_nome'] === '') {
            $payload['site_nome'] = config('app.name', 'Elite Repasse');
        }

        if ($payload['mail_from_name'] === '') {
            $payload['mail_from_name'] = $payload['site_nome'];
        }

        return $payload;
    }

    private function applyMailConfig(array $payload): void
    {
        if ($payload['mail_smtp_ativo'] && $payload['mail_smtp_host'] !== '') {
            $scheme = match ($payload['mail_smtp_encryption']) {
                'ssl' => 'smtps',
                'tls' => 'smtp',
                default => 'smtp',
            };

            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp.transport', 'smtp');
            Config::set('mail.mailers.smtp.host', $payload['mail_smtp_host']);
            Config::set('mail.mailers.smtp.port', (int) $payload['mail_smtp_port']);
            Config::set('mail.mailers.smtp.username', $payload['mail_smtp_username']);
            Config::set('mail.mailers.smtp.password', $payload['mail_smtp_password']);
            Config::set('mail.mailers.smtp.encryption', null);
            Config::set('mail.mailers.smtp.scheme', $scheme);
        }

        if ($payload['mail_from_address'] !== '') {
            Config::set('mail.from.address', $payload['mail_from_address']);
            Config::set('mail.from.name', $payload['mail_from_name']);
        }

        Mail::purge('smtp');
    }
}