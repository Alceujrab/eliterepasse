<?php

namespace App\Filament\Resources\Documents\Tables;

use App\Models\Document;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Document::tipoLabels()[$state] ?? $state)
                    ->color('info'),

                TextColumn::make('titulo')
                    ->label('Título')
                    ->description(fn (Document $r) => $r->nome_original)
                    ->searchable(),

                TextColumn::make('vehicle.brand')
                    ->label('Veículo')
                    ->description(fn (Document $r) => $r->vehicle
                        ? "{$r->vehicle->model} {$r->vehicle->model_year} — {$r->vehicle->plate}"
                        : '—')
                    ->searchable(),

                TextColumn::make('user.razao_social')
                    ->label('Enviado por')
                    ->description(fn (Document $r) => $r->user?->email)
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Document::statusLabels()[$state] ?? $state)
                    ->color(fn ($state) => Document::statusColors()[$state] ?? 'gray'),

                TextColumn::make('validade')
                    ->label('Validade')
                    ->date('d/m/Y')
                    ->color(fn (Document $r) => $r->estaVencido() ? 'danger' : 'gray')
                    ->description(fn (Document $r) => $r->estaVencido() ? '⚠️ Vencido' : null)
                    ->placeholder('—'),

                TextColumn::make('tamanho_formatado')
                    ->label('Tamanho')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Enviado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options(Document::tipoLabels()),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options(Document::statusLabels()),
            ])

            ->recordActions([
                // Visualizar arquivo
                Action::make('visualizar')
                    ->label('Visualizar')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (Document $r) => $r->url)
                    ->openUrlInNewTab(),

                // Verificar documento
                Action::make('verificar')
                    ->label('Verificar')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Document $r) => $r->status === 'pendente')
                    ->action(function (Document $record) {
                        $record->update([
                            'status'       => 'verificado',
                            'verificado_por' => auth()->id(),
                            'verificado_em'  => now(),
                        ]);
                        Notification::make()->title('Documento verificado!')->success()->send();
                    }),

                // Rejeitar documento
                Action::make('rejeitar')
                    ->label('Rejeitar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Textarea::make('motivo_rejeicao')
                            ->label('Motivo da rejeição')
                            ->required()
                            ->rows(3),
                    ])
                    ->visible(fn (Document $r) => in_array($r->status, ['pendente', 'verificado']))
                    ->action(function (Document $record, array $data) {
                        $record->update([
                            'status'          => 'rejeitado',
                            'motivo_rejeicao' => $data['motivo_rejeicao'],
                            'verificado_por'  => auth()->id(),
                            'verificado_em'   => now(),
                        ]);
                        Notification::make()->title('Documento rejeitado.')->warning()->send();
                    }),

                ViewAction::make(),
                EditAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])

            ->defaultSort('created_at', 'desc');
    }
}
