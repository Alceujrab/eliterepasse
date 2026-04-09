<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class VehicleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('plate')
                    ->label('Placa'),
                TextEntry::make('brand')
                    ->label('Marca'),
                TextEntry::make('model')
                    ->label('Modelo'),
                TextEntry::make('version')
                    ->label('Versão'),
                TextEntry::make('manufacture_year')
                    ->label('Ano Fabricação')
                    ->formatStateUsing(fn ($state) => $state),
                TextEntry::make('model_year')
                    ->label('Ano Modelo')
                    ->formatStateUsing(fn ($state) => $state),
                TextEntry::make('mileage')
                    ->label('Quilometragem')
                    ->numeric()
                    ->suffix(' km'),
                TextEntry::make('fuel_type')
                    ->label('Combustível')
                    ->placeholder('-'),
                TextEntry::make('transmission')
                    ->label('Câmbio')
                    ->placeholder('-'),
                TextEntry::make('engine')
                    ->label('Motor')
                    ->placeholder('-'),
                TextEntry::make('color')
                    ->label('Cor')
                    ->placeholder('-'),
                TextEntry::make('doors')
                    ->label('Portas')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('category')
                    ->label('Carroceria')
                    ->placeholder('-'),
                TextEntry::make('sale_price')
                    ->label('Preço de Venda')
                    ->money('BRL')
                    ->placeholder('-'),
                TextEntry::make('fipe_code')
                    ->label('Código FIPE')
                    ->placeholder('-'),
                TextEntry::make('fipe_price')
                    ->label('Tabela FIPE')
                    ->money('BRL')
                    ->placeholder('-'),
                TextEntry::make('profit_margin')
                    ->label('% abaixo da FIPE')
                    ->numeric()
                    ->suffix('%')
                    ->placeholder('-'),
                TextEntry::make('accessories')
                    ->label('Acessórios')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('media')
                    ->label('Fotos')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('location')
                    ->label('Localização')
                    ->formatStateUsing(fn ($record) => collect([$record->yard_name, $record->city, $record->state])->filter()->implode(', ') ?: '-')
                    ->columnSpanFull(),
                TextEntry::make('status')
                    ->label('Disponibilidade')
                    ->badge(),
                IconEntry::make('has_report')
                    ->label('Possui Laudo')
                    ->boolean(),
                IconEntry::make('has_factory_warranty')
                    ->label('Garantia de Fábrica')
                    ->boolean(),
                IconEntry::make('is_on_sale')
                    ->label('Em Oferta')
                    ->boolean(),
                IconEntry::make('is_just_arrived')
                    ->label('Recém Chegado')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-'),
            ]);
    }
}
