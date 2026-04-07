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
                TextEntry::make('plate'),
                TextEntry::make('brand'),
                TextEntry::make('model'),
                TextEntry::make('version'),
                TextEntry::make('manufacture_year')
                    ->numeric(),
                TextEntry::make('model_year')
                    ->numeric(),
                TextEntry::make('mileage')
                    ->numeric(),
                TextEntry::make('fuel_type')
                    ->placeholder('-'),
                TextEntry::make('transmission')
                    ->placeholder('-'),
                TextEntry::make('engine')
                    ->placeholder('-'),
                TextEntry::make('color')
                    ->placeholder('-'),
                TextEntry::make('doors')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('category')
                    ->placeholder('-'),
                TextEntry::make('sale_price')
                    ->money()
                    ->placeholder('-'),
                TextEntry::make('fipe_code')
                    ->placeholder('-'),
                TextEntry::make('fipe_price')
                    ->money()
                    ->placeholder('-'),
                TextEntry::make('profit_margin')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('accessories')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('media')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('location')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('status')
                    ->badge(),
                IconEntry::make('has_report')
                    ->boolean(),
                IconEntry::make('has_factory_warranty')
                    ->boolean(),
                IconEntry::make('is_on_sale')
                    ->boolean(),
                IconEntry::make('is_just_arrived')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
