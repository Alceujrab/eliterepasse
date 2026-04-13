<?php

namespace App\Filament\Resources\EmailTemplates\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmailTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome')
                    ->label('Template')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->fontFamily('mono')
                    ->searchable(),

                TextColumn::make('assunto')
                    ->label('Assunto')
                    ->limit(50),

                IconColumn::make('ativo')
                    ->label('Ativo')
                    ->boolean(),

                TextColumn::make('updated_at')
                    ->label('Atualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('nome')
            ->actions([
                EditAction::make(),
            ]);
    }
}
