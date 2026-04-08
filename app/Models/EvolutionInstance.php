<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class EvolutionInstance extends Model
{
    protected $fillable = [
        'nome', 'instancia', 'url_base', 'api_key',
        'ativo', 'padrao', 'status_conexao', 'verificado_em'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'padrao' => 'boolean',
        'verificado_em' => 'datetime',
    ];

    /**
     * Retorna a instância padrão do sistema
     */
    public static function getPadrao(): ?self
    {
        return static::where('padrao', true)->where('ativo', true)->first()
            ?? static::where('ativo', true)->first();
    }

    /**
     * Envia mensagem de texto via Evolution GO
     * POST {url_base}/{instancia}/message/sendText
     */
    public function sendText(string $phone, string $message): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type'  => 'application/json',
            ])->post("{$this->url_base}/{$this->instancia}/message/sendText", [
                'number' => $phone,
                'text'   => $message,
                'delay'  => 1000,
            ]);

            return [
                'success' => $response->successful(),
                'status'  => $response->status(),
                'body'    => $response->json(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Testa a conexão com a instância
     */
    public function testarConexao(): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->api_key,
            ])->get("{$this->url_base}/{$this->instancia}/instance/connectionState");

            $connected = $response->successful();
            
            $this->update([
                'status_conexao' => $connected ? 1 : 2,
                'verificado_em'  => now(),
            ]);

            return $connected;
        } catch (\Exception $e) {
            $this->update(['status_conexao' => 2, 'verificado_em' => now()]);
            return false;
        }
    }
}
