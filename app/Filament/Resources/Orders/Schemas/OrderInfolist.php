<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Order;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('numero')
                    ->label('Nº Pedido'),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Order::statusLabels()[$state] ?? $state)
                    ->color(fn ($state) => Order::statusColors()[$state] ?? 'gray'),
                TextEntry::make('user.name')
                    ->label('Cliente'),
                TextEntry::make('vehicle.brand')
                    ->label('Veículo')
                    ->formatStateUsing(fn ($state, $record) => $record->vehicle ? "{$state} {$record->vehicle->model} {$record->vehicle->model_year} — {$record->vehicle->plate}" : '—'),
                TextEntry::make('valor_compra')
                    ->label('Valor de Compra')
                    ->formatStateUsing(fn ($state) => 'R$ ' . number_format((float) $state, 2, ',', '.'))
                    ->placeholder('—'),
                TextEntry::make('valor_fipe')
                    ->label('Valor FIPE')
                    ->formatStateUsing(fn ($state) => $state ? 'R$ ' . number_format((float) $state, 2, ',', '.') : null)
                    ->placeholder('—'),
                TextEntry::make('paymentMethod.nome')
                    ->label('Forma de Pagamento')
                    ->placeholder('—'),
                TextEntry::make('observacoes')
                    ->label('Observações')
                    ->placeholder('—')
                    ->columnSpanFull(),
                TextEntry::make('confirmado_em')
                    ->label('Confirmado em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—'),
                TextEntry::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i'),
            ]);
    }
}
