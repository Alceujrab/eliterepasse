<?php

namespace App\Filament\Resources\EvolutionInstances\Tables;

use App\Models\EvolutionInstance;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EvolutionInstancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('instancia')
                    ->label('Instância Técnica')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Copiado!')
                    ->fontFamily('mono'),

                TextColumn::make('url_base')
                    ->label('Servidor')
                    ->url(fn ($record) => $record->url_base)
                    ->openUrlInNewTab(),

                IconColumn::make('ativo')
                    ->label('Ativo')
                    ->boolean(),

                IconColumn::make('padrao')
                    ->label('Padrão')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->trueColor('warning'),

                TextColumn::make('status_conexao')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match((int) $state) {
                        1 => 'Conectado',
                        2 => 'Desconectado',
                        default => 'Não verificado',
                    })
                    ->color(fn ($state) => match((int) $state) {
                        1 => 'success',
                        2 => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('verificado_em')
                    ->label('Última Verificação')
                    ->dateTime('d/m/Y H:i')
                    ->since()
                    ->placeholder('Nunca verificado'),
            ])
            ->filters([])
            ->recordActions([
                Action::make('testar')
                    ->label('Testar Conexão')
                    ->icon('heroicon-o-signal')
                    ->color('info')
                    ->action(function (EvolutionInstance $record) {
                        $conectado = $record->testarConexao();
                        Notification::make()
                            ->title($conectado ? 'Instância conectada!' : 'Falha na conexão')
                            ->status($conectado ? 'success' : 'danger')
                            ->send();
                    }),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('padrao', 'desc');
    }
}
