<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    public const SYSTEM_SLUGS = [
        'cliente_aprovado',
        'contrato_assinado',
        'contrato_assinado_admin',
        'contrato_para_assinar',
        'documento_verificado',
        'fatura_gerada',
        'novo_cadastro_admin',
        'novo_pedido_admin',
        'pagamento_confirmado',
        'pedido_confirmado',
        'ticket_atualizado',
        'usuario_aprovado',
        'usuario_bloqueado',
        'documento_disponivel',
        'documento_despachado',
    ];

    protected $fillable = [
        'slug',
        'nome',
        'assunto',
        'saudacao',
        'corpo',
        'texto_acao',
        'url_acao',
        'texto_rodape',
        'variaveis_disponiveis',
        'ativo',
    ];

    protected $casts = [
        'variaveis_disponiveis' => 'array',
        'ativo' => 'boolean',
    ];

    /**
     * Busca template pelo slug. Retorna null se inativo ou inexistente.
     */
    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->where('ativo', true)->first();
    }

    /**
     * Substitui variáveis no texto usando array associativo.
     * Ex: renderText('Olá, {{nome}}!', ['nome' => 'João']) => 'Olá, João!'
     */
    public static function renderText(?string $text, array $vars): string
    {
        if (!$text) return '';

        foreach ($vars as $key => $value) {
            $text = str_replace("{{{$key}}}", (string) $value, $text);
        }

        return $text;
    }

    /**
     * Converte o template em MailMessage com variáveis aplicadas.
     */
    public function toMailMessage(array $vars): \Illuminate\Notifications\Messages\MailMessage
    {
        $mail = new \Illuminate\Notifications\Messages\MailMessage();

        $mail->subject(self::renderText($this->assunto, $vars));

        if ($this->saudacao) {
            $mail->greeting(self::renderText($this->saudacao, $vars));
        }

        // Corpo: cada linha separada por \n vira um ->line()
        $linhas = preg_split('/\r?\n/', self::renderText($this->corpo, $vars));
        foreach ($linhas as $linha) {
            if (trim($linha) !== '') {
                $mail->line($linha);
            }
        }

        if ($this->texto_acao && $this->url_acao) {
            $mail->action(
                self::renderText($this->texto_acao, $vars),
                self::renderText($this->url_acao, $vars)
            );
        }

        if ($this->texto_rodape) {
            $mail->line(self::renderText($this->texto_rodape, $vars));
        }

        return $mail;
    }

    public function isSystemTemplate(): bool
    {
        return in_array($this->slug, self::SYSTEM_SLUGS, true);
    }
}
