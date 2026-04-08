<?php

namespace App\Filament\Resources\Vehicles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;

class VehiclesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // ─── Foto ──────────────────────────────────────────────────
                ImageColumn::make('media')
                    ->label('Foto')
                    ->circular()
                    ->getStateUsing(fn ($record) =>
                        is_array($record->media) ? ($record->media[0] ?? null) : null
                    ),

                // ─── Identificação ─────────────────────────────────────────
                TextColumn::make('plate')
                    ->label('Placa')
                    ->searchable()
                    ->fontFamily('mono')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('brand')
                    ->label('Marca')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('model')
                    ->label('Modelo')
                    ->searchable()
                    ->description(fn ($record) => $record->version ? mb_strimwidth($record->version, 0, 35, '…') : null),

                TextColumn::make('model_year')
                    ->label('Ano')
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) => "{$record->manufacture_year}/{$record->model_year}"),

                // ─── Preços ────────────────────────────────────────────────
                TextColumn::make('sale_price')
                    ->label('Preço Venda')
                    ->money('BRL')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),

                TextColumn::make('fipe_price')
                    ->label('FIPE')
                    ->money('BRL')
                    ->sortable()
                    ->color('gray')
                    ->description(fn ($record) => $record->fipe_price && $record->sale_price
                        ? '↓ ' . number_format((1 - $record->sale_price / $record->fipe_price) * 100, 1) . '% abaixo'
                        : null
                    ),

                // ─── Km ────────────────────────────────────────────────────
                TextColumn::make('mileage')
                    ->label('Km')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.') . ' km'),

                // ─── Status ────────────────────────────────────────────────
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'available' => '✅ Disponível',
                        'reserved'  => '⏳ Reservado',
                        'sold'      => '🔴 Vendido',
                        default     => $state,
                    })
                    ->color(fn ($state) => match($state) {
                        'available' => 'success',
                        'reserved'  => 'warning',
                        'sold'      => 'danger',
                        default     => 'gray',
                    }),

                // ─── Badges ────────────────────────────────────────────────
                IconColumn::make('is_on_sale')
                    ->label('Oferta')
                    ->boolean()
                    ->trueIcon('heroicon-o-tag')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                IconColumn::make('has_report')
                    ->label('Laudo')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('has_factory_warranty')
                    ->label('Garantia')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('category')
                    ->label('Categoria')
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('color')
                    ->label('Cor')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('fuel_type')
                    ->label('Combustível')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('transmission')
                    ->label('Câmbio')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Cadastrado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->defaultSort('created_at', 'desc')

            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'available' => '✅ Disponível',
                        'reserved'  => '⏳ Reservado',
                        'sold'      => '🔴 Vendido',
                    ]),

                SelectFilter::make('category')
                    ->label('Categoria')
                    ->options([
                        'SUV'    => 'SUV',
                        'Sedan'  => 'Sedan',
                        'Hatch'  => 'Hatch',
                        'Pickup' => 'Pickup',
                    ]),

                SelectFilter::make('brand')
                    ->label('Marca')
                    ->options(fn () =>
                        \App\Models\Vehicle::distinct()->orderBy('brand')->pluck('brand', 'brand')->toArray()
                    ),

                TernaryFilter::make('is_on_sale')
                    ->label('Em Oferta'),

                TernaryFilter::make('has_report')
                    ->label('Com Laudo'),
            ])

            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('marcar_disponivel')
                    ->label('Disponibilizar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'available')
                    ->action(fn ($record) => $record->update(['status' => 'available']))
                    ->requiresConfirmation(),
                Action::make('marcar_vendido')
                    ->label('Marcar Vendido')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'available')
                    ->action(fn ($record) => $record->update(['status' => 'sold']))
                    ->requiresConfirmation(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    \Filament\Actions\BulkAction::make('disponibilizar_todos')
                        ->label('Marcar Disponíveis')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['status' => 'available']))
                        ->requiresConfirmation(),
                    \Filament\Actions\BulkAction::make('marcar_vendidos')
                        ->label('Marcar Vendidos')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['status' => 'sold']))
                        ->requiresConfirmation(),
                ]),
            ]);
    }
}
