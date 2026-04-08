<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use App\Services\ContractService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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
                        $contract = app(ContractService::class)->gerarDeOrdem($record);
                        Notification::make()
                            ->title("Contrato {$contract->numero} gerado!")
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
