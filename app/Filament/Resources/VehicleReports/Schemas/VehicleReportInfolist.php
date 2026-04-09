<?php

namespace App\Filament\Resources\VehicleReports\Schemas;

use App\Models\VehicleReport;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class VehicleReportInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('numero')
                    ->label('Nº Laudo'),
                TextEntry::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn ($state) => VehicleReport::tipoLabels()[$state] ?? $state),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => VehicleReport::statusLabels()[$state] ?? $state)
                    ->color(fn ($state) => VehicleReport::statusColors()[$state] ?? 'gray'),
                TextEntry::make('vehicle.plate')
                    ->label('Veículo')
                    ->description(fn ($record) => $record->vehicle ? "{$record->vehicle->brand} {$record->vehicle->model} {$record->vehicle->model_year}" : '—'),
                TextEntry::make('nota_geral')
                    ->label('Nota Geral')
                    ->placeholder('—')
                    ->badge()
                    ->color(fn ($record) => $record?->notaColor ?? 'gray'),
                TextEntry::make('criadoPor.name')
                    ->label('Criado por')
                    ->placeholder('—'),
                TextEntry::make('aprovadoPor.name')
                    ->label('Aprovado por')
                    ->placeholder('—'),
                TextEntry::make('aprovado_em')
                    ->label('Aprovado em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—'),
                TextEntry::make('conclusao')
                    ->label('Conclusão')
                    ->placeholder('—')
                    ->columnSpanFull(),
                TextEntry::make('recomendacoes')
                    ->label('Recomendações')
                    ->placeholder('—')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i'),
            ]);
    }
}
