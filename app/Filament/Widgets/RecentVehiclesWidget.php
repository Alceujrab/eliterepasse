<?php

namespace App\Filament\Widgets;

use App\Models\Vehicle;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentVehiclesWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected static ?string $heading = 'Últimos Veículos Cadastrados';
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Vehicle::query()->latest()->limit(8)
            )
            ->columns([
                TextColumn::make('brand')
                    ->label('Marca')
                    ->searchable(),

                TextColumn::make('model')
                    ->label('Modelo')
                    ->searchable(),

                TextColumn::make('model_year')
                    ->label('Ano'),

                TextColumn::make('plate')
                    ->label('Placa')
                    ->fontFamily('mono'),

                TextColumn::make('sale_price')
                    ->label('Preço')
                    ->formatStateUsing(fn ($state) => 'R$ ' . number_format((float) $state, 2, ',', '.'))
                    ->color('success'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'available' => 'Disponível',
                        'reserved' => 'Reservado',
                        'sold' => 'Vendido',
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'available' => 'success',
                        'reserved' => 'warning',
                        'sold' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->paginated(false);
    }
}
