<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

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

    /** Instância padrão ativa */
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
            'close', '0' => '🔴 Desconectado',
            default      => '⚪ Desconhecido',
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

    // ─── API Calls ────────────────────────────────────────────────────

    /** Envia mensagem de texto (Evolution GO API) */
    public function sendText(string $phone, string $text): array
    {
        // Normalizar telefone: apenas dígitos, com DDI 55
        $phone = preg_replace('/\D/', '', $phone);
        if (! str_starts_with($phone, '55')) {
            $phone = '55' . $phone;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'apikey'       => $this->api_key,
                    'Content-Type' => 'application/json',
                ])
                ->post(rtrim($this->url_base, '/') . "/message/sendText/{$this->instancia}", [
                    'number'  => $phone,
                    'text'    => $text,
                    'delay'   => 1200,
                    'options' => ['delay' => 1200, 'presence' => 'composing'],
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

    /** Verifica estado de conexão da instância */
    public function testarConexao(): bool
    {
        try {
            $response = Http::timeout(8)
                ->withHeaders(['apikey' => $this->api_key])
                ->get(rtrim($this->url_base, '/') . "/instance/connectionState/{$this->instancia}");

            $state = $response->json('instance.state') ?? 'close';

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

    /** Obtém QR Code para conectar a instância */
    public function getQrCode(): ?string
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['apikey' => $this->api_key])
                ->get(rtrim($this->url_base, '/') . "/instance/connect/{$this->instancia}");

            return $response->json('qrcode.base64') ?? $response->json('base64');
        } catch (\Exception $e) {
            return null;
        }
    }
}
