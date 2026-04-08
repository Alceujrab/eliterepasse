<?php

namespace App\Filament\Resources\VehicleReports\Tables;

use App\Models\VehicleReport;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VehicleReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero')
                    ->label('Nº Laudo')
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn ($state) => VehicleReport::tipoLabels()[$state] ?? $state)
                    ->color('info'),

                TextColumn::make('vehicle.brand')
                    ->label('Veículo')
                    ->description(fn (VehicleReport $r) => $r->vehicle
                        ? "{$r->vehicle->model} {$r->vehicle->model_year} — {$r->vehicle->plate}"
                        : '—')
                    ->searchable(),

                TextColumn::make('nota_geral')
                    ->label('Nota')
                    ->formatStateUsing(fn ($state) => $state !== null ? "{$state}/10" : '—')
                    ->badge()
                    ->color(fn (VehicleReport $r) => $r->nota_cor),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => VehicleReport::statusLabels()[$state] ?? $state)
                    ->color(fn ($state) => VehicleReport::statusColors()[$state] ?? 'gray'),

                TextColumn::make('criadoPor.name')
                    ->label('Vistoriador')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options(VehicleReport::tipoLabels()),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options(VehicleReport::statusLabels()),
            ])

            ->recordActions([
                // Aprovar laudo
                Action::make('aprovar')
                    ->label('Aprovar')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (VehicleReport $r) => in_array($r->status, ['rascunho', 'em_revisao']))
                    ->action(function (VehicleReport $record) {
                        $record->update([
                            'status'       => 'aprovado',
                            'aprovado_por' => auth()->id(),
                            'aprovado_em'  => now(),
                        ]);
                        // Atualizar has_report no veículo
                        $record->vehicle?->update(['has_report' => true]);

                        Notification::make()->title('Laudo aprovado!')->success()->send();
                    }),

                // Reprovar laudo
                Action::make('reprovar')
                    ->label('Reprovar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (VehicleReport $r) => in_array($r->status, ['em_revisao', 'aprovado']))
                    ->action(function (VehicleReport $record) {
                        $record->update(['status' => 'reprovado']);
                        Notification::make()->title('Laudo reprovado.')->danger()->send();
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
