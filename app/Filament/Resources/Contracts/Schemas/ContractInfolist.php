<?php

namespace App\Filament\Resources\Contracts\Schemas;

use App\Models\Contract;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ContractInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('numero')
                    ->label('Nº Contrato'),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Contract::statusLabels()[$state] ?? $state)
                    ->color(fn ($state) => Contract::statusColors()[$state] ?? 'gray'),
                TextEntry::make('user.razao_social')
                    ->label('Comprador'),
                TextEntry::make('vehicle.brand')
                    ->label('Veículo')
                    ->formatStateUsing(fn ($state, $record) => $record->vehicle ? "{$state} {$record->vehicle->model} {$record->vehicle->model_year} — {$record->vehicle->plate}" : '—'),
                TextEntry::make('valor_contrato')
                    ->label('Valor do Contrato')
                    ->formatStateUsing(fn ($state) => 'R$ ' . number_format((float) $state, 2, ',', '.'))
                    ->placeholder('—'),
                TextEntry::make('forma_pagamento')
                    ->label('Forma de Pagamento')
                    ->placeholder('—'),
                TextEntry::make('template')
                    ->label('Template'),
                TextEntry::make('enviado_em')
                    ->label('Enviado em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Não enviado'),
                TextEntry::make('assinado_em')
                    ->label('Assinado em (Cliente)')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Não assinado'),
                TextEntry::make('assinado_admin_em')
                    ->label('Assinado em (Admin)')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Não assinado'),
                TextEntry::make('endereco_assinatura')
                    ->label('Local da Assinatura')
                    ->placeholder('—')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i'),
            ]);
    }
}
