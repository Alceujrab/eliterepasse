<?php

namespace App\Filament\Resources\Clients\Tables;

use App\Models\User;
use App\Notifications\UserBlockedNotification;
use App\Services\NotificationService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('razao_social')
                    ->label('Empresa')
                    ->searchable()
                    ->description(fn (User $r) => $r->nome_fantasia)
                    ->weight('bold'),

                TextColumn::make('cnpj')
                    ->label('CNPJ')
                    ->fontFamily('mono')
                    ->placeholder('Não informado'),

                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('phone')
                    ->label('WhatsApp')
                    ->placeholder('—'),

                TextColumn::make('cidade')
                    ->label('Cidade/UF')
                    ->formatStateUsing(fn ($state, User $r) => $state ? "{$state}/{$r->estado}" : '—'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pendente'  => '⏳ Pendente',
                        'ativo'     => '✅ Ativo',
                        'bloqueado' => '🚫 Bloqueado',
                        default     => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'pendente'  => 'warning',
                        'ativo'     => 'success',
                        'bloqueado' => 'danger',
                        default     => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Cadastro')
                    ->dateTime('d/m/Y')
                    ->since()
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('status')
                    ->label('Filtrar por Status')
                    ->options([
                        'pendente'  => '⏳ Pendentes',
                        'ativo'     => '✅ Ativos',
                        'bloqueado' => '🚫 Bloqueados',
                    ]),
            ])

            ->recordActions([
                Action::make('aprovar')
                    ->label('Aprovar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Aprovar acesso do lojista?')
                    ->modalDescription('O cliente receberá um e-mail e WhatsApp de boas-vindas.')
                    ->visible(fn (User $r) => $r->status !== 'ativo')
                    ->action(function (User $record) {
                        $record->update([
                            'status'      => 'ativo',
                            'aprovado_em' => now(),
                            'aprovado_por'=> auth()->id(),
                        ]);
                        // Notificação multicanal: database + email + WhatsApp com dados de acesso
                        app(NotificationService::class)->clienteAprovado($record);

                        Notification::make()
                            ->title('Lojista aprovado com sucesso!')
                            ->success()->send();
                    }),

                Action::make('bloquear')
                    ->label('Bloquear')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Bloquear acesso do lojista?')
                    ->visible(fn (User $r) => $r->status === 'ativo')
                    ->action(function (User $record) {
                        $record->update(['status' => 'bloqueado']);
                        $record->notify(new UserBlockedNotification());

                        Notification::make()
                            ->title('Acesso do lojista bloqueado.')
                            ->danger()->send();
                    }),

                ViewAction::make(),
                EditAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])

            ->defaultSort('created_at', 'desc')
            ->poll('30s');
    }
}
