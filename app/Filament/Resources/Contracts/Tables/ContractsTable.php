<?php

namespace App\Filament\Resources\Contracts\Tables;

use App\Models\Contract;
use App\Services\ContractService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContractsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero')
                    ->label('Nº Contrato')
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->copyable()
                    ->searchable(),

                TextColumn::make('user.razao_social')
                    ->label('Comprador')
                    ->description(fn (Contract $r) => $r->user?->cnpj)
                    ->searchable(),

                TextColumn::make('vehicle.brand')
                    ->label('Veículo')
                    ->description(fn (Contract $r) => $r->vehicle
                        ? "{$r->vehicle->model} {$r->vehicle->model_year} — {$r->vehicle->plate}"
                        : '—'),

                TextColumn::make('valor_contrato')
                    ->label('Valor')
                    ->formatStateUsing(fn ($state) => 'R$ ' . number_format((float) $state, 2, ',', '.'))
                    ->color('success')
                    ->weight('semibold'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Contract::statusLabels()[$state] ?? $state)
                    ->color(fn ($state) => Contract::statusColors()[$state] ?? 'gray'),

                TextColumn::make('assinado_em')
                    ->label('Assinado em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Não assinado')
                    ->sortable(),

                TextColumn::make('endereco_assinatura')
                    ->label('Local da Assinatura')
                    ->placeholder('—')
                    ->limit(40)
                    ->tooltip(fn (Contract $r) => $r->endereco_assinatura),
            ])

            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(Contract::statusLabels()),
            ])

            ->recordActions([
                // Gerar e enviar link de assinatura via WhatsApp
                Action::make('enviarWhatsApp')
                    ->label('Enviar p/ Assinar')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Enviar contrato para assinatura?')
                    ->modalDescription('O cliente receberá um link via WhatsApp com 72h de validade.')
                    ->visible(fn (Contract $r) => in_array($r->status, ['rascunho', 'aguardando']))
                    ->action(function (Contract $record) {
                        $service = app(ContractService::class);
                        $ok = $service->enviarLinkAssinatura($record);

                        Notification::make()
                            ->title($ok ? 'Link enviado via WhatsApp!' : 'Falha ao enviar WhatsApp')
                            ->status($ok ? 'success' : 'danger')
                            ->send();
                    }),

                // Copiar link de assinatura
                Action::make('copiarLink')
                    ->label('Copiar Link')
                    ->icon('heroicon-o-link')
                    ->color('gray')
                    ->visible(fn (Contract $r) => $r->assinaturaComprador !== null)
                    ->action(function (Contract $record) {
                        $token = $record->assinaturaComprador?->token_assinatura;
                        Notification::make()
                            ->title('Link: ' . url("/contrato/assinar/{$token}"))
                            ->info()->send();
                    }),

                ViewAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])

            ->defaultSort('created_at', 'desc');
    }
}
