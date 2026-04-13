<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;

class GeminiService
{
    public static function gerarCorpoEmail(string $descricao, ?array $variaveis = []): string
    {
        $apiKey = SystemSetting::get('gemini_api_key');

        if (!$apiKey) {
            throw new \RuntimeException('Chave da API Gemini não configurada. Vá em Configurações > Configurações Gerais.');
        }

        $varsTexto = '';
        if (!empty($variaveis)) {
            $varsTexto = "\n\nVariáveis disponíveis para usar no template (use no formato {{variavel}}): " . implode(', ', array_map(fn ($v) => '{{' . $v . '}}', $variaveis));
        }

        $prompt = <<<PROMPT
Você é um especialista em copywriting de e-mails transacionais para empresas B2B do setor automotivo.

Gere o CORPO de um e-mail para o seguinte assunto:
"{$descricao}"

Contexto: Portal Elite Repasse — plataforma B2B de repasse de veículos entre concessionárias e lojistas.
{$varsTexto}

Regras:
- Escreva em português do Brasil, tom profissional e cordial
- Use **negrito** (Markdown) para destacar dados importantes
- Cada parágrafo deve ser uma linha separada (será convertido em parágrafos no e-mail)
- NÃO inclua saudação (Olá, nome) — isso é outro campo
- NÃO inclua assinatura/rodapé — isso é outro campo
- NÃO inclua assunto/subject — isso é outro campo
- Gere APENAS o corpo do e-mail (os parágrafos centrais)
- Seja conciso, entre 3 a 8 linhas
- Se houver variáveis disponíveis, use-as naturalmente no texto
PROMPT;

        $response = Http::timeout(30)->post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}",
            [
                'contents' => [
                    ['parts' => [['text' => $prompt]]],
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 500,
                ],
            ]
        );

        if (!$response->successful()) {
            $error = $response->json('error.message') ?? $response->body();
            throw new \RuntimeException("Erro na API Gemini: {$error}");
        }

        $text = $response->json('candidates.0.content.parts.0.text');

        if (!$text) {
            throw new \RuntimeException('A API Gemini não retornou conteúdo.');
        }

        return trim($text);
    }
}
