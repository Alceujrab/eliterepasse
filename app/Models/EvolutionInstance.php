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
 * Autenticação: Header  Authorization: Bearer {token}
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
            'Authorization' => 'Bearer ' . $this->api_key,
            'Content-Type'  => 'application/json',
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

            return [
                'success' => $response->successful(),
                'status'  => $response->status(),
                'body'    => $response->json(),
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ─── Status da Instância (GET /instance/status) ──────────────────

    /**
     * Chama a API e atualiza status_conexao + verificado_em na BD.
     * Retorna true se a instância estiver conectada (state === 'open').
     */
    public function testarConexao(): bool
    {
        try {
            $response = $this->http(8)->get($this->baseUrl() . '/instance/status');

            // A API retorna: { "state": "open" | "connecting" | "close" }
            $state = $response->json('state') ?? 'close';

            $this->update([
                'status_conexao' => $state,
                'verificado_em'  => now(),
            ]);

            return $state === 'open';
        } catch (\Exception $e) {
            $this->update(['status_conexao' => 'close', 'verificado_em' => now()]);
            return false;
        }
    }

    // ─── QR Code (GET /instance/qr) ──────────────────────────────────

    /**
     * Retorna o QR Code em base64 para exibição no painel.
     * Funciona apenas quando a instância está desconectada.
     * Se já estiver conectada, retorna null.
     */
    public function getQrCode(): ?string
    {
        try {
            $response = $this->http(12)->get($this->baseUrl() . '/instance/qr');

            if (! $response->successful()) {
                return null;
            }

            // A API retorna: { "qr": "data:image/png;base64,..." }
            return $response->json('qr');
        } catch (\Exception $e) {
            return null;
        }
    }

    // ─── Conectar / Iniciar sessão (POST /instance/connect) ──────────

    /**
     * Inicia conexão. Pode retornar QR Code se não tiver phone.
     * @param string|null $phone Para pareamento por código numérico (opcional).
     * @param string|null $webhookUrl URL para receber eventos (opcional).
     */
    public function conectar(?string $phone = null, ?string $webhookUrl = null): array
    {
        try {
            $payload = ['immediate' => true];
            if ($phone) $payload['phone'] = $phone;
            if ($webhookUrl) $payload['webhookUrl'] = $webhookUrl;

            $response = $this->http(15)->post($this->baseUrl() . '/instance/connect', $payload);

            return [
                'success' => $response->successful(),
                'body'    => $response->json(),
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ─── Logout (DELETE /instance/logout) ────────────────────────────

    public function logout(): bool
    {
        try {
            $response = $this->http()->delete($this->baseUrl() . '/instance/logout');

            if ($response->successful()) {
                $this->update(['status_conexao' => 'close', 'verificado_em' => now()]);
            }

            return $response->successful();
        } catch (\Exception $e) {
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

            return $response->json('code');
        } catch (\Exception $e) {
            return null;
        }
    }
}
