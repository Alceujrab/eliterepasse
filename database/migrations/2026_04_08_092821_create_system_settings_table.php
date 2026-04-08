<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('geral'); // geral, google, captcha, email, pagamento
            $table->string('tipo')->default('text'); // text, boolean, password, json
            $table->string('label')->nullable();
            $table->timestamps();
        });

        // Seeds padrão
        DB::table('system_settings')->insert([
            // Configurações Gerais
            ['key' => 'site_nome', 'value' => 'Elite Repasse', 'group' => 'geral', 'tipo' => 'text', 'label' => 'Nome do Portal'],
            ['key' => 'site_moeda', 'value' => 'BRL', 'group' => 'geral', 'tipo' => 'text', 'label' => 'Moeda Padrão'],
            ['key' => 'aprovacao_automatica', 'value' => '0', 'group' => 'geral', 'tipo' => 'boolean', 'label' => 'Aprovação Automática de Clientes'],
            // Google APIs
            ['key' => 'google_recaptcha_site_key', 'value' => null, 'group' => 'google', 'tipo' => 'text', 'label' => 'reCAPTCHA v3 — Site Key'],
            ['key' => 'google_recaptcha_secret_key', 'value' => null, 'group' => 'google', 'tipo' => 'password', 'label' => 'reCAPTCHA v3 — Secret Key'],
            ['key' => 'google_recaptcha_ativo', 'value' => '0', 'group' => 'google', 'tipo' => 'boolean', 'label' => 'Ativar reCAPTCHA no Login'],
            ['key' => 'google_recaptcha_score_minimo', 'value' => '0.5', 'group' => 'google', 'tipo' => 'text', 'label' => 'Score mínimo (0.0 a 1.0)'],
            ['key' => 'google_maps_api_key', 'value' => null, 'group' => 'google', 'tipo' => 'password', 'label' => 'Google Maps Geocoding API Key'],
            ['key' => 'google_oauth_client_id', 'value' => null, 'group' => 'google', 'tipo' => 'text', 'label' => 'Google OAuth Client ID (Login Social)'],
            ['key' => 'google_oauth_client_secret', 'value' => null, 'group' => 'google', 'tipo' => 'password', 'label' => 'Google OAuth Client Secret'],
            // E-mail (SMTP)
            ['key' => 'mail_from_address', 'value' => 'noreply@eliterepasse.com.br', 'group' => 'email', 'tipo' => 'text', 'label' => 'E-mail de Envio'],
            ['key' => 'mail_from_name', 'value' => 'Elite Repasse', 'group' => 'email', 'tipo' => 'text', 'label' => 'Nome do Remetente'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
