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

    public function clienteAprovado(string $phone, string $nome, string $email): bool
    {
        $url = url('/');
        $msg = "🎉 *Cadastro Aprovado!*\n\n"
            . "Olá, *{$nome}*!\n\n"
            . "Seu cadastro no *Portal B2B Elite Repasse* foi aprovado!\n\n"
            . "Agora você tem acesso à nossa vitrine com descontos exclusivos.\n\n"
            . "📋 *Seus dados de acesso:*\n"
            . "📧 E-mail: {$email}\n"
            . "🔑 Senha: a mesma que você definiu no cadastro\n"
            . "🌐 Portal: {$url}\n\n"
            . "👉 Acesse agora: {$url}\n\n"
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

    public function novoCadastroAdmin(string $phone, string $nomeAdmin, string $nomeCliente, string $cnpj, string $cidade): bool
    {
        $msg = "🆕 *Novo Cadastro de Lojista*\n\n"
            . "Olá, *{$nomeAdmin}*!\n\n"
            . "Um novo lojista se cadastrou no portal:\n"
            . "🏢 *Empresa:* {$nomeCliente}\n"
            . "📋 *CNPJ:* {$cnpj}\n"
            . "📍 *Cidade:* {$cidade}\n\n"
            . "👉 Acesse o admin para analisar:\n"
            . url('/admin/clients') . "\n\n"
            . "_Elite Repasse — Admin_";

        return $this->enviar($phone, $msg);
    }

    public function novoPedidoAdmin(string $phone, string $nomeAdmin, string $numeroPedido, string $cliente, string $veiculo, string $valor): bool
    {
        $msg = "🛒 *Novo Pedido de Compra*\n\n"
            . "Olá, *{$nomeAdmin}*!\n\n"
            . "Um novo pedido foi registrado:\n"
            . "📋 *Pedido:* {$numeroPedido}\n"
            . "👤 *Cliente:* {$cliente}\n"
            . "🚗 *Veículo:* {$veiculo}\n"
            . "💰 *Valor:* R$ {$valor}\n\n"
            . "👉 Acesse o admin para confirmar:\n"
            . url('/admin/orders') . "\n\n"
            . "_Elite Repasse — Admin_";

        return $this->enviar($phone, $msg);
    }

    public function contratoAssinado(string $phone, string $nome, string $numeroContrato, string $veiculo): bool
    {
        $msg = "✅ *Contrato Assinado!*\n\n"
            . "Olá, *{$nome}*!\n\n"
            . "Seu contrato *{$numeroContrato}* foi assinado com sucesso!\n\n"
            . "🚗 *Veículo:* {$veiculo}\n\n"
            . "O contrato assinado já está disponível na área *Meus Documentos* do portal.\n\n"
            . "👉 Acesse: " . url('/meus-documentos') . "\n\n"
            . "_Elite Repasse — Portal B2B_";

        return $this->enviar($phone, $msg);
    }

    public function contratoAssinadoAdmin(string $phone, string $nomeAdmin, string $numeroContrato, string $cliente, string $veiculo): bool
    {
        $msg = "✍️ *Contrato Assinado pelo Cliente*\n\n"
            . "Olá, *{$nomeAdmin}*!\n\n"
            . "O contrato *{$numeroContrato}* foi assinado:\n"
            . "👤 *Cliente:* {$cliente}\n"
            . "🚗 *Veículo:* {$veiculo}\n\n"
            . "👉 Acesse o admin para revisar:\n"
            . url('/admin/contracts') . "\n\n"
            . "_Elite Repasse — Admin_";

        return $this->enviar($phone, $msg);
    }

    public function faturaGerada(string $phone, string $nome, string $numeroFatura, string $valor, string $vencimento, string $formaPagamento): bool
    {
        $msg = "💰 *Nova Fatura Gerada*\n\n"
            . "Olá, *{$nome}*!\n\n"
            . "Uma fatura foi gerada para o seu pedido:\n"
            . "📋 *Fatura:* {$numeroFatura}\n"
            . "💵 *Valor:* R$ {$valor}\n"
            . "📅 *Vencimento:* {$vencimento}\n"
            . "💳 *Pagamento:* {$formaPagamento}\n\n"
            . "👉 Acesse o portal para mais detalhes:\n"
            . url('/financeiro') . "\n\n"
            . "_Elite Repasse — Portal B2B_";

        return $this->enviar($phone, $msg);
    }

    public function pagamentoConfirmado(string $phone, string $nome, string $numeroFatura, string $valor): bool
    {
        $msg = "💚 *Pagamento Confirmado!*\n\n"
            . "Olá, *{$nome}*!\n\n"
            . "O pagamento da fatura *{$numeroFatura}* foi confirmado!\n\n"
            . "💵 *Valor:* R$ {$valor}\n"
            . "📅 *Data:* " . now()->format('d/m/Y') . "\n\n"
            . "Obrigado pela confiança! 🤝\n\n"
            . "👉 Acompanhe no portal: " . url('/financeiro') . "\n\n"
            . "_Elite Repasse — Portal B2B_";

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
