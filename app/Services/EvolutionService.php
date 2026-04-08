<?php

namespace App\Services;

use App\Models\EvolutionInstance;
use Illuminate\Support\Facades\Log;

class EvolutionService
{
    private ?EvolutionInstance $instance;

    public function __construct()
    {
        $this->instance = EvolutionInstance::getPadrao();
    }

    public function withInstance(EvolutionInstance $instance): static
    {
        $this->instance = $instance;
        return $this;
    }

    // ─── Envio de Mensagens ───────────────────────────────────────────

    /**
     * Envia qualquer mensagem de texto WhatsApp.
     */
    public function enviar(string $phone, string $mensagem): bool
    {
        if (! $this->instance) {
            Log::warning('EvolutionService: nenhuma instância ativa configurada.');
            return false;
        }

        $result = $this->instance->sendText($phone, $mensagem);

        if (! $result['success']) {
            Log::error('EvolutionService::enviar falhou', [
                'phone'    => $phone,
                'instance' => $this->instance->instancia,
                'error'    => $result['error'] ?? $result['body'] ?? null,
            ]);
        }

        return $result['success'];
    }

    // ─── Templates de Mensagens ───────────────────────────────────────

    public function pedidoConfirmado(string $phone, string $nome, string $numeroPedido, string $veiculo, string $valor): bool
    {
        $msg = "✅ *Pedido Confirmado!*\n\n"
            . "Olá, *{$nome}*!\n\n"
            . "Seu pedido *{$numeroPedido}* foi confirmado com sucesso.\n"
            . "🚗 *Veículo:* {$veiculo}\n"
            . "💰 *Valor:* R$ {$valor}\n\n"
            . "Em breve você receberá o contrato para assinatura.\n\n"
            . "_Elite Repasse — Portal B2B_";

        return $this->enviar($phone, $msg);
    }

    public function contratoParaAssinar(string $phone, string $nome, string $numeroContrato, string $link): bool
    {
        $msg = "✍️ *Contrato para Assinar*\n\n"
            . "Olá, *{$nome}*!\n\n"
            . "Seu contrato *{$numeroContrato}* está pronto.\n\n"
            . "📄 *Acesse o link para assinar:*\n"
            . "{$link}\n\n"
            . "⚠️ Este link expira em *72 horas*.\n\n"
            . "_Elite Repasse — Portal B2B_";

        return $this->enviar($phone, $msg);
    }

    public function clienteAprovado(string $phone, string $nome): bool
    {
        $msg = "🎉 *Cadastro Aprovado!*\n\n"
            . "Olá, *{$nome}*!\n\n"
            . "Seu cadastro no *Portal B2B Elite Repasse* foi aprovado!\n\n"
            . "Agora você tem acesso à nossa vitrine com descontos exclusivos.\n\n"
            . "👉 Acesse: " . url('/') . "\n\n"
            . "_Elite Repasse — Portal B2B_";

        return $this->enviar($phone, $msg);
    }

    public function ticketRespondido(string $phone, string $nome, string $numeroTicket, string $resumo): bool
    {
        $msg = "💬 *Chamado Respondido*\n\n"
            . "Olá, *{$nome}*!\n\n"
            . "Seu chamado *{$numeroTicket}* recebeu uma nova resposta.\n\n"
            . "📝 *Resposta:* {$resumo}\n\n"
            . "👉 Acesse o portal para responder ou ver o status completo.\n\n"
            . "_Elite Repasse — Suporte_";

        return $this->enviar($phone, $msg);
    }

    public function documentoVerificado(string $phone, string $nome, string $tipoDoc, string $status, ?string $motivo = null): bool
    {
        $emoji = $status === 'verificado' ? '✅' : '❌';
        $msg   = "{$emoji} *Documento {$status}*\n\n"
            . "Olá, *{$nome}*!\n\n"
            . "Seu documento *{$tipoDoc}* foi {$status}.";

        if ($status === 'rejeitado' && $motivo) {
            $msg .= "\n\n*Motivo:* {$motivo}\n"
                . "Por favor, envie o documento correto pelo portal.";
        }

        $msg .= "\n\n_Elite Repasse — Portal B2B_";

        return $this->enviar($phone, $msg);
    }

    public function novoTicketAdmin(string $phone, string $nomeAdmin, string $numeroTicket, string $cliente, string $titulo): bool
    {
        $msg = "🎫 *Novo Chamado de Suporte*\n\n"
            . "Olá, *{$nomeAdmin}*!\n\n"
            . "Um novo chamado foi aberto:\n"
            . "📋 *Número:* {$numeroTicket}\n"
            . "👤 *Cliente:* {$cliente}\n"
            . "📌 *Assunto:* {$titulo}\n\n"
            . "👉 Acesse o admin para responder:\n"
            . url('/admin/tickets') . "\n\n"
            . "_Elite Repasse — Admin_";

        return $this->enviar($phone, $msg);
    }

    // ─── Status da Instância ──────────────────────────────────────────

    public function testarConexao(?EvolutionInstance $instance = null): bool
    {
        $inst = $instance ?? $this->instance;
        if (! $inst) return false;
        return $inst->testarConexao();
    }

    public function getQrCode(?EvolutionInstance $instance = null): ?string
    {
        $inst = $instance ?? $this->instance;
        if (! $inst) return null;
        return $inst->getQrCode();
    }

    public function instanciaAtiva(): ?EvolutionInstance
    {
        return $this->instance;
    }
}
