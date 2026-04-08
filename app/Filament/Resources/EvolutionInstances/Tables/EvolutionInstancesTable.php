<?php

namespace App\Filament\Resources\EvolutionInstances\Tables;

use App\Models\EvolutionInstance;
use App\Services\EvolutionService;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
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
                    ->searchable(),
                TextColumn::make('instancia')
                    ->searchable(),
                TextColumn::make('url_base')
                    ->searchable(),
                IconColumn::make('ativo')
                    ->boolean(),
                IconColumn::make('padrao')
                    ->boolean(),
                TextColumn::make('status_conexao')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('verificado_em')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('conectar')
                    ->label('QR Code / Conectar')
                    ->icon('heroicon-o-qr-code')
                    ->color('success')
                    ->modalHeading('Conectar Instância do WhatsApp')
                    ->modalSubmitAction(false)
                    ->modalContent(function (EvolutionInstance $record) {
                        $service = app(EvolutionService::class)->withInstance($record);
                        $qrCode  = $service->getQrCode();
                        
                        return view('filament.components.evolution-qrcode', ['qrCode' => $qrCode]);
                    }),
                    
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
