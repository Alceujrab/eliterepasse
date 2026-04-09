<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

/**
 * Evolution GO — Uma instância por servidor.
 * Documentação: https://github.com/EvolutionAPI/evolution-go
 *
 * Endpoints principais:
 *   POST   {url_base}/send/text          — Enviar texto
 *   GET    {url_base}/instance/status    — Estado da conexão
 *   GET    {url_base}/instance/qr        — QR Code (base64)
 *   POST   {url_base}/instance/connect   — Conectar / gerar QR
 *   POST   {url_base}/instance/pair      — Código numérico de pareamento
 *   DELETE {url_base}/instance/logout    — Desconectar
 *
 * Autenticação: Header  apikey: {token}
 * O token é o campo `api_key` cadastrado no painel.
 */
class EvolutionInstance extends Model
{
    protected $fillable = [
        'nome', 'instancia', 'url_base', 'api_key',
        'ativo', 'padrao', 'status_conexao', 'verificado_em',
    ];

    protected $casts = [
        'ativo'         => 'boolean',
        'padrao'        => 'boolean',
        'verificado_em' => 'datetime',
    ];

    // ─── Helpers ─────────────────────────────────────────────────────

    public static function getPadrao(): ?self
    {
        return static::where('padrao', true)->where('ativo', true)->first()
            ?? static::where('ativo', true)->first();
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status_conexao) {
            'open'       => '🟢 Conectado',
            'connecting' => '🟡 Conectando',
            'close'      => '🔴 Desconectado',
            default      => '⚪ Não verificado',
        };
    }

    public function getStatusCorAttribute(): string
    {
        return match($this->status_conexao) {
            'open'  => 'success',
            'close' => 'danger',
            default => 'warning',
        };
    }

    // ─── HTTP Client ──────────────────────────────────────────────────

    private function http(int $timeout = 10)
    {
        return Http::timeout($timeout)->withHeaders([
            'apikey'       => $this->api_key,
            'Content-Type' => 'application/json',
        ]);
    }

    private function baseUrl(): string
    {
        return rtrim($this->url_base, '/');
    }

    // ─── Envio de Texto (POST /send/text) ────────────────────────────

    /**
     * @param string $phone  Número com DDD — o +55 é adicionado automaticamente.
     * @param string $text   Suporta formatação WhatsApp (*bold*, _italic_, etc.)
     */
    public function sendText(string $phone, string $text): array
    {
        // Normalizar: apenas dígitos, DDI brasileiro obrigatório
        $phone = preg_replace('/\D/', '', $phone);
        if (! str_starts_with($phone, '55')) {
            $phone = '55' . $phone;
        }

        try {
            $response = $this->http()->post($this->baseUrl() . '/send/text', [
                'number' => $phone,
                'text'   => $text,
                'delay'  => 1200,
            ]);

            $json = $response->json();

            \Log::info('Evolution Go sendText response', [
                'instance' => $this->nome,
                'phone'    => $phone,
                'http'     => $response->status(),
                'body'     => $json,
            ]);

            if ($response->successful()) {
                return ['success' => true, 'status' => $response->status(), 'body' => $json];
            }

            $error = $json['error'] ?? $json['message'] ?? $json['data']['message'] ?? 'HTTP ' . $response->status();

            return ['success' => false, 'error' => $error, 'status' => $response->status(), 'body' => $json];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ─── Status da Instância (GET /instance/status) ──────────────────

    /**
     * Chama a API e atualiza status_conexao + verificado_em na BD.
     * Retorna true se a instância estiver conectada (connected + loggedIn).
     *
     * Evolution Go retorna:
     *   { "data": { "connected": bool, "loggedIn": bool, "name": "...", "myJid": "..." } }
     */
    public function testarConexao(): bool
    {
        try {
            $response = $this->http(8)->get($this->baseUrl() . '/instance/status');

            \Log::info('Evolution Go status response', [
                'instance' => $this->nome,
                'http'     => $response->status(),
                'body'     => $response->json(),
            ]);

            $data = $response->json('data', []);
            // Normalizar chaves para minúsculo — Evolution Go retorna PascalCase:
            // { "Connected": true, "LoggedIn": true, "Name": "..." }
            $data = array_change_key_case($data, CASE_LOWER);

            // Evolution Go pode retornar dois formatos:
            // Formato 1: { "data": { "Connected": true, "LoggedIn": true } }  (real, PascalCase)
            // Formato 2: { "data": { "status": "open" } }
            if (isset($data['status'])) {
                $state = in_array($data['status'], ['open', 'connecting', 'close', 'created'])
                    ? $data['status']
                    : 'close';
                if ($state === 'created') $state = 'close';
            } else {
                $connected = $data['connected'] ?? false;
                $loggedin  = $data['loggedin'] ?? false;

                $state = match (true) {
                    $connected && $loggedin  => 'open',
                    $connected && !$loggedin => 'connecting',
                    default                  => 'close',
                };
            }

            $this->update([
                'status_conexao' => $state,
                'verificado_em'  => now(),
            ]);

            return $state === 'open';
        } catch (\Exception $e) {
            \Log::error('Evolution Go status exception', [
                'instance' => $this->nome,
                'error'    => $e->getMessage(),
            ]);
            $this->update(['status_conexao' => 'close', 'verificado_em' => now()]);
            return false;
        }
    }

    // ─── QR Code (GET /instance/qr) ──────────────────────────────────

    /**
     * Retorna o QR Code em base64 para exibição no painel.
     * Funciona apenas quando a instância está desconectada.
     * Se já estiver conectada, retorna null.
     *
     * Evolution Go retorna:
     *   { "data": { "qrcode": "2@abcd...", "code": "data:image/png;base64,..." } }
     */
    public function getQrCode(): ?string
    {
        try {
            // 1) Iniciar conexão — gera QR internamente no servidor
            //    POST /instance/connect NÃO retorna QR no body, apenas inicia o processo.
            $connectResult = $this->conectar();

            if (! ($connectResult['success'] ?? false)) {
                $error = $connectResult['body']['error'] ?? $connectResult['error'] ?? 'connect falhou';
                \Log::warning('Evolution Go connect antes do QR falhou', ['error' => $error]);
                // Mesmo com falha (ex: "session already logged in"), tenta buscar QR
            }

            // 2) Buscar QR pelo endpoint dedicado: GET /instance/qr
            //    Resposta: { "data": { "qrcode": "2@abc...", "code": "data:image/png;base64,..." } }
            $response = $this->http(12)->get($this->baseUrl() . '/instance/qr');

            $json = $response->json();
            \Log::info('Evolution Go QR response', [
                'http'   => $response->status(),
                'json'   => $json,
            ]);

            if (! $response->successful()) {
                return null;
            }

            // "code" contém a imagem base64, "qrcode" contém o texto do QR
            return $json['data']['code']
                ?? $json['data']['qrcode']
                ?? $json['code']
                ?? $json['qrcode']
                ?? null;
        } catch (\Exception $e) {
            \Log::error('Evolution Go QR exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    // ─── Conectar / Iniciar sessão (POST /instance/connect) ──────────

    /**
     * Inicia conexão via QR Code.
     * O body aceita apenas webhookUrl e subscribe (ambos opcionais).
     * A instância é identificada pelo header apikey (token da instância).
     */
    public function conectar(?string $webhookUrl = null, array $subscribe = []): array
    {
        try {
            $payload = new \stdClass(); // body vazio = {} em JSON
            if ($webhookUrl) { $payload = (object) ['webhookUrl' => $webhookUrl]; }
            if ($subscribe)  { $payload->subscribe = $subscribe; }

            $response = $this->http(15)->post($this->baseUrl() . '/instance/connect', (array) $payload);

            \Log::info('Evolution Go connect response', [
                'instance' => $this->nome,
                'http'     => $response->status(),
                'body'     => $response->json(),
            ]);

            return [
                'success' => $response->successful(),
                'status'  => $response->status(),
                'body'    => $response->json(),
            ];
        } catch (\Exception $e) {
            \Log::error('Evolution Go connect exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ─── Logout (DELETE /instance/logout) ────────────────────────────

    public function logout(): bool
    {
        try {
            // DELETE /instance/logout requer Content-Type: application/json + body {}
            $response = $this->http()->send('DELETE', $this->baseUrl() . '/instance/logout', [
                'json' => new \stdClass(), // envia {} no body
            ]);

            \Log::info('Evolution Go logout response', [
                'instance' => $this->nome,
                'http'     => $response->status(),
                'body'     => $response->json(),
            ]);

            if ($response->successful()) {
                $this->update(['status_conexao' => 'close', 'verificado_em' => now()]);
            }

            return $response->successful();
        } catch (\Exception $e) {
            \Log::error('Evolution Go logout exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    // ─── Código de Pareamento (POST /instance/pair) ──────────────────

    /**
     * Alternativa ao QR Code — gera um código numérico de 8 dígitos.
     * O usuário digita esse código no WhatsApp em vez de escanear o QR.
     */
    public function getPairingCode(string $phone): ?string
    {
        try {
            $phone = preg_replace('/\D/', '', $phone);
            if (! str_starts_with($phone, '55')) {
                $phone = '55' . $phone;
            }

            $response = $this->http(12)->post($this->baseUrl() . '/instance/pair', [
                'phone' => $phone,
            ]);

            return $response->json('data.code');
        } catch (\Exception $e) {
            return null;
        }
    }
}
