<?php

namespace App\Filament\Resources\Documents\Schemas;

use App\Models\Document;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DocumentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title')
                    ->label('Título'),
                TextEntry::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Document::tipoLabels()[$state] ?? $state),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Document::statusLabels()[$state] ?? $state)
                    ->color(fn ($state) => Document::statusColors()[$state] ?? 'gray'),
                TextEntry::make('user.name')
                    ->label('Cliente')
                    ->placeholder('—'),
                TextEntry::make('vehicle.plate')
                    ->label('Veículo')
                    ->description(fn ($record) => $record->vehicle ? "{$record->vehicle->brand} {$record->vehicle->model}" : null)
                    ->placeholder('—'),
                TextEntry::make('nome_original')
                    ->label('Arquivo Original')
                    ->placeholder('—'),
                TextEntry::make('tamanho')
                    ->label('Tamanho')
                    ->formatStateUsing(fn ($record) => $record->tamanhoFormatado ?? '—')
                    ->placeholder('—'),
                TextEntry::make('mime_type')
                    ->label('Tipo MIME')
                    ->placeholder('—'),
                IconEntry::make('visivel_cliente')
                    ->label('Visível para Cliente')
                    ->boolean(),
                TextEntry::make('validade')
                    ->label('Validade')
                    ->date('d/m/Y')
                    ->placeholder('Sem validade'),
                TextEntry::make('verificadoPor.name')
                    ->label('Verificado por')
                    ->placeholder('—'),
                TextEntry::make('verificado_em')
                    ->label('Verificado em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—'),
                TextEntry::make('motivo_rejeicao')
                    ->label('Motivo da Rejeição')
                    ->placeholder('—')
                    ->columnSpanFull(),
                TextEntry::make('observacoes')
                    ->label('Observações')
                    ->placeholder('—')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i'),
            ]);
    }
}
