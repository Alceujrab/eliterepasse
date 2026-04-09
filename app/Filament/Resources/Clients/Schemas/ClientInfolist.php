<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ClientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Nome'),
                TextEntry::make('email')
                    ->label('E-mail'),
                TextEntry::make('phone')
                    ->label('Telefone')
                    ->placeholder('—'),
                TextEntry::make('cpf')
                    ->label('CPF')
                    ->placeholder('—'),
                TextEntry::make('razao_social')
                    ->label('Razão Social')
                    ->placeholder('—'),
                TextEntry::make('nome_fantasia')
                    ->label('Nome Fantasia')
                    ->placeholder('—'),
                TextEntry::make('cnpj')
                    ->label('CNPJ')
                    ->placeholder('—'),
                TextEntry::make('inscricao_estadual')
                    ->label('Inscrição Estadual')
                    ->placeholder('—'),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'ativo' => 'success',
                        'pendente' => 'warning',
                        'bloqueado' => 'danger',
                        default => 'gray',
                    }),
                TextEntry::make('logradouro')
                    ->label('Endereço')
                    ->formatStateUsing(fn ($record) => collect([
                        $record->logradouro,
                        $record->numero ? "nº {$record->numero}" : null,
                        $record->complemento,
                        $record->bairro,
                        $record->cidade ? "{$record->cidade}/{$record->estado}" : null,
                        $record->cep ? "CEP {$record->cep}" : null,
                    ])->filter()->implode(', '))
                    ->placeholder('—')
                    ->columnSpanFull(),
                TextEntry::make('observacoes')
                    ->label('Observações')
                    ->placeholder('—')
                    ->columnSpanFull(),
                TextEntry::make('aprovado_em')
                    ->label('Aprovado em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—'),
                TextEntry::make('created_at')
                    ->label('Cadastrado em')
                    ->dateTime('d/m/Y H:i'),
            ]);
    }
}
