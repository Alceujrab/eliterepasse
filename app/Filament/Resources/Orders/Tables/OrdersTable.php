<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use App\Models\Financial;
use App\Models\OrderHistory;
use App\Services\ContractService;
use App\Services\NotificationService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Nº Compra')
                    ->formatStateUsing(fn ($state) => 'ORD-' . str_pad($state, 6, '0', STR_PAD_LEFT))
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('user.razao_social')
                    ->label('Cliente')
                    ->description(fn (Order $r) => $r->user?->cnpj)
                    ->searchable(),

                TextColumn::make('vehicle.brand')
                    ->label('Veículo')
                    ->description(fn (Order $r) => $r->vehicle
                        ? "{$r->vehicle->model} {$r->vehicle->model_year} — {$r->vehicle->plate}"
                        : '—'),

                TextColumn::make('valor_compra')
                    ->label('Valor')
                    ->formatStateUsing(fn ($state) => 'R$ ' . number_format((float) $state, 2, ',', '.'))
                    ->color('success')
                    ->weight('semibold'),

                TextColumn::make('paymentMethod.nome')
                    ->label('Pagamento')
                    ->badge()
                    ->color('info'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Order::statusLabels()[$state] ?? $state)
                    ->color(fn ($state) => Order::statusColors()[$state] ?? 'gray'),

                TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->since()
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(Order::statusLabels()),

                SelectFilter::make('payment_method_id')
                    ->label('Forma de Pgto')
                    ->relationship('paymentMethod', 'nome'),
            ])

            ->recordActions([
                Action::make('confirmar')
                    ->label('Confirmar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Order $r) => $r->status === 'pendente')
                    ->action(function (Order $record) {
                        $record->update([
                            'status'         => 'confirmado',
                            'confirmado_em'  => now(),
                            'confirmado_por' => auth()->id(),
                        ]);
                        $record->vehicle?->update(['status' => 'reserved']);
                        OrderHistory::registrar($record->id, 'pedido_confirmado', 'pendente', 'confirmado');
                        // Notificar cliente
                        app(NotificationService::class)->pedidoConfirmado($record->fresh());
                        Notification::make()->title('Compra confirmada!')->success()->send();
                    }),

                Action::make('gerar_contrato')
                    ->label('Gerar Contrato')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Gerar contrato de compra e venda?')
                    ->modalDescription('Será gerado um contrato com os dados atuais do pedido.')
                    ->visible(fn (Order $r) => $r->status === 'confirmado')
                    ->action(function (Order $record) {
                        $contract      = app(ContractService::class)->gerarDeOrdem($record);
                        $linkAssinatura = route('contrato.assinar.show', $contract->token_assinatura);
                        OrderHistory::registrar($record->id, 'contrato_gerado', null, null, "Contrato {$contract->numero} gerado");
                        // Notificar cliente para assinar
                        app(NotificationService::class)->contratoParaAssinar($contract, $linkAssinatura);
                        Notification::make()
                            ->title("Contrato {$contract->numero} gerado! Notificação enviada.")
                            ->success()->send();
                    }),

                Action::make('gerar_fatura')
                    ->label('Gerar Fatura')
                    ->icon('heroicon-o-banknotes')
                    ->color('warning')
                    ->modalHeading('Gerar Fatura')
                    ->modalDescription('Preencha os dados da fatura para o cliente.')
                    ->visible(fn (Order $r) => in_array($r->status, ['confirmado', 'faturado']) && ! $r->financial)
                    ->form([
                        TextInput::make('descricao')
                            ->label('Descrição')
                            ->default(fn (Order $r) => "Compra veículo " . ($r->vehicle ? "{$r->vehicle->brand} {$r->vehicle->model}" : ''))
                            ->required(),
                        TextInput::make('valor')
                            ->label('Valor (R$)')
                            ->numeric()
                            ->default(fn (Order $r) => $r->valor_compra)
                            ->required(),
                        Select::make('forma_pagamento')
                            ->label('Forma de Pagamento')
                            ->options(Financial::formasPagamento())
                            ->default(fn (Order $r) => $r->paymentMethod?->slug ?? 'boleto')
                            ->required(),
                        DatePicker::make('data_vencimento')
                            ->label('Vencimento')
                            ->default(now()->addDays(3)->format('Y-m-d'))
                            ->required(),
                        Textarea::make('observacoes')
                            ->label('Observações')
                            ->rows(2),
                    ])
                    ->action(function (Order $record, array $data) {
                        $financial = Financial::create([
                            'order_id'        => $record->id,
                            'numero'          => Financial::gerarNumero(),
                            'descricao'       => $data['descricao'],
                            'valor'           => $data['valor'],
                            'forma_pagamento' => $data['forma_pagamento'],
                            'data_vencimento' => $data['data_vencimento'],
                            'status'          => 'em_aberto',
                            'criado_por'      => auth()->id(),
                            'observacoes'     => $data['observacoes'] ?? null,
                        ]);

                        $record->update(['status' => Order::STATUS_FATURADO]);
                        OrderHistory::registrar($record->id, 'fatura_gerada', 'confirmado', 'faturado', "Fatura {$financial->numero} gerada");

                        app(NotificationService::class)->faturaGerada($financial);

                        Notification::make()
                            ->title("Fatura {$financial->numero} gerada! Cliente notificado.")
                            ->success()->send();
                    }),

                Action::make('confirmar_pagamento')
                    ->label('Confirmar Pagamento')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar recebimento do pagamento?')
                    ->modalDescription('O cliente será notificado por e-mail e WhatsApp.')
                    ->visible(fn (Order $r) => $r->status === 'faturado' && $r->financial && $r->financial->status === 'em_aberto')
                    ->action(function (Order $record) {
                        $financial = $record->financial;
                        $financial->update([
                            'status'         => 'pago',
                            'data_pagamento' => now(),
                        ]);
                        OrderHistory::registrar($record->id, 'pagamento_confirmado', 'faturado', 'faturado', "Pagamento {$financial->numero} confirmado");

                        app(NotificationService::class)->pagamentoConfirmado($financial->fresh());

                        Notification::make()
                            ->title("Pagamento {$financial->numero} confirmado! Cliente notificado.")
                            ->success()->send();
                    }),

                Action::make('cancelar')
                    ->label('Cancelar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Order $r) => in_array($r->status, ['pendente', 'confirmado']))
                    ->action(function (Order $record) {
                        $status_anterior = $record->status;
                        $record->update(['status' => 'cancelado']);
                        OrderHistory::registrar($record->id, 'pedido_cancelado', $status_anterior, 'cancelado');
                        if ($status_anterior === 'confirmado') {
                            $record->vehicle?->update(['status' => 'available']);
                        }
                        Notification::make()->title('Compra cancelada.')->danger()->send();
                    }),

                ViewAction::make(),
                EditAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])

            ->defaultSort('created_at', 'desc')
            ->poll('60s');
    }
}
