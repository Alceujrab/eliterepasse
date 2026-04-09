<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Document;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Notifications\ClienteAprovado;
use App\Notifications\ContratoAssinado;
use App\Notifications\ContratoAssinadoAdmin;
use App\Notifications\ContratoParaAssinar;
use App\Notifications\DocumentoVerificado;
use App\Notifications\NovoCadastroAdmin;
use App\Notifications\NovoPedidoAdmin;
use App\Notifications\PedidoConfirmado;
use App\Notifications\TicketAtualizado;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    private EvolutionService $whatsapp;

    public function __construct(EvolutionService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    // ─── Pedido Confirmado ────────────────────────────────────────────

    public function pedidoConfirmado(Order $order): void
    {
        try {
            $user = $order->user;
            if (! $user) return;

            // 1. Notificação database + email
            $user->notify(new PedidoConfirmado($order));

            // 2. WhatsApp
            if ($user->phone) {
                $veiculo = $order->vehicle
                    ? "{$order->vehicle->brand} {$order->vehicle->model} {$order->vehicle->model_year}"
                    : 'Veículo';
                $valor = number_format((float) $order->valor_compra, 2, ',', '.');

                $this->whatsapp->pedidoConfirmado(
                    $user->phone,
                    $user->razao_social ?? $user->name,
                    $order->numero,
                    $veiculo,
                    $valor
                );
            }
        } catch (\Exception $e) {
            Log::error('NotificationService::pedidoConfirmado', ['error' => $e->getMessage()]);
        }
    }

    // ─── Contrato Para Assinar ────────────────────────────────────────

    public function contratoParaAssinar(Contract $contract, string $linkAssinatura): void
    {
        try {
            $user = $contract->user;
            if (! $user) return;

            $user->notify(new ContratoParaAssinar($contract, $linkAssinatura));

            if ($user->phone) {
                $this->whatsapp->contratoParaAssinar(
                    $user->phone,
                    $user->razao_social ?? $user->name,
                    $contract->numero,
                    $linkAssinatura
                );
            }
        } catch (\Exception $e) {
            Log::error('NotificationService::contratoParaAssinar', ['error' => $e->getMessage()]);
        }
    }

    // ─── Ticket Respondido ────────────────────────────────────────────

    public function ticketAtualizado(Ticket $ticket, TicketMessage $message): void
    {
        try {
            $user = $ticket->user;
            if (! $user || $message->is_internal) return;

            $user->notify(new TicketAtualizado($ticket, $message));

            if ($user->phone) {
                $this->whatsapp->ticketRespondido(
                    $user->phone,
                    $user->razao_social ?? $user->name,
                    $ticket->numero,
                    mb_strimwidth($message->mensagem, 0, 120, '...')
                );
            }
        } catch (\Exception $e) {
            Log::error('NotificationService::ticketAtualizado', ['error' => $e->getMessage()]);
        }
    }

    // ─── Cliente Aprovado ─────────────────────────────────────────────

    public function clienteAprovado(User $user): void
    {
        try {
            $user->notify(new ClienteAprovado());

            if ($user->phone) {
                $this->whatsapp->clienteAprovado(
                    $user->phone,
                    $user->razao_social ?? $user->nome_fantasia ?? $user->name,
                    $user->email
                );
            }
        } catch (\Exception $e) {
            Log::error('NotificationService::clienteAprovado', ['error' => $e->getMessage()]);
        }
    }

    // ─── Novo Cadastro → Admin ────────────────────────────────────────

    public function novoCadastroParaAdmin(User $cliente): void
    {
        try {
            $admins = User::where('is_admin', true)->get();
            $nome = $cliente->razao_social ?? $cliente->nome_fantasia ?? $cliente->name;
            $cnpj = $cliente->cnpj ?? 'Não informado';
            $cidade = $cliente->cidade
                ? "{$cliente->cidade}/{$cliente->estado}"
                : 'Não informado';

            foreach ($admins as $admin) {
                // Notificação database + email
                $admin->notify(new NovoCadastroAdmin($cliente));

                // WhatsApp
                if ($admin->phone) {
                    $this->whatsapp->novoCadastroAdmin(
                        $admin->phone,
                        $admin->name,
                        $nome,
                        $cnpj,
                        $cidade
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error('NotificationService::novoCadastroParaAdmin', ['error' => $e->getMessage()]);
        }
    }

    // ─── Documento Verificado ─────────────────────────────────────────

    public function documentoVerificado(Document $document): void
    {
        try {
            $user = $document->user;
            if (! $user) return;

            $user->notify(new DocumentoVerificado($document));

            if ($user->phone) {
                $tipo = Document::tipoLabels()[$document->tipo] ?? $document->tipo;
                $this->whatsapp->documentoVerificado(
                    $user->phone,
                    $user->razao_social ?? $user->name,
                    $tipo,
                    $document->status,
                    $document->motivo_rejeicao ?? null
                );
            }
        } catch (\Exception $e) {
            Log::error('NotificationService::documentoVerificado', ['error' => $e->getMessage()]);
        }
    }

    // ─── Novo Pedido → Admin ──────────────────────────────────────────

    public function novoPedidoParaAdmin(Order $order): void
    {
        try {
            $admins = User::where('is_admin', true)->get();
            $cliente = $order->user;
            $vehicle = $order->vehicle;
            $nome = $cliente?->razao_social ?? $cliente?->nome_fantasia ?? $cliente?->name ?? 'Cliente';
            $veiculo = $vehicle ? "{$vehicle->brand} {$vehicle->model} {$vehicle->model_year}" : 'Veículo';
            $valor = number_format((float) $order->valor_compra, 2, ',', '.');

            foreach ($admins as $admin) {
                $admin->notify(new NovoPedidoAdmin($order));

                if ($admin->phone) {
                    $this->whatsapp->novoPedidoAdmin(
                        $admin->phone,
                        $admin->name,
                        $order->numero,
                        $nome,
                        $veiculo,
                        $valor
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error('NotificationService::novoPedidoParaAdmin', ['error' => $e->getMessage()]);
        }
    }

    // ─── Contrato Assinado (cliente + admin) ──────────────────────────

    public function contratoAssinado(Contract $contract): void
    {
        try {
            $user = $contract->user;
            $veiculo = implode(' ', array_filter([
                $contract->dados_veiculo['brand'] ?? '',
                $contract->dados_veiculo['model'] ?? '',
                $contract->dados_veiculo['model_year'] ?? '',
            ]));

            // 1. Notificar cliente
            if ($user) {
                $user->notify(new ContratoAssinado($contract));

                if ($user->phone) {
                    $this->whatsapp->contratoAssinado(
                        $user->phone,
                        $user->razao_social ?? $user->name,
                        $contract->numero,
                        $veiculo
                    );
                }
            }

            // 2. Notificar admins
            $admins = User::where('is_admin', true)->get();
            $nomeCliente = $contract->dados_comprador['razao_social']
                ?? $contract->dados_comprador['name']
                ?? 'Cliente';

            foreach ($admins as $admin) {
                $admin->notify(new ContratoAssinadoAdmin($contract));

                if ($admin->phone) {
                    $this->whatsapp->contratoAssinadoAdmin(
                        $admin->phone,
                        $admin->name,
                        $contract->numero,
                        $nomeCliente,
                        $veiculo
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error('NotificationService::contratoAssinado', ['error' => $e->getMessage()]);
        }
    }
}
