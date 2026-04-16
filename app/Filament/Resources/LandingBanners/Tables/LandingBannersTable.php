<?php

namespace App\Filament\Resources\LandingBanners\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LandingBannersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Imagem')
                    ->disk('public')
                    ->height(60),

                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('subtitle')
                    ->label('Subtítulo')
                    ->limit(40)
                    ->toggleable(),

                TextColumn::make('order')
                    ->label('Ordem')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean(),

                TextColumn::make('updated_at')
                    ->label('Atualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('order', 'asc')
            ->reorderable('order')
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
