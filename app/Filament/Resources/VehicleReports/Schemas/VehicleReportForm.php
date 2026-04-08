<?php

namespace App\Filament\Resources\VehicleReports\Schemas;

use App\Models\Vehicle;
use App\Models\VehicleReport;
use App\Models\VehicleReportItem;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VehicleReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // ─── Dados Gerais ─────────────────────────────────────────
            Section::make('Dados do Laudo')
                ->schema([
                    Select::make('vehicle_id')
                        ->label('Veículo')
                        ->options(
                            Vehicle::get()->mapWithKeys(fn ($v) => [
                                $v->id => "[{$v->plate}] {$v->brand} {$v->model} {$v->model_year}"
                            ])
                        )
                        ->searchable()
                        ->required(),

                    Select::make('tipo')
                        ->label('Tipo de Laudo')
                        ->options(VehicleReport::tipoLabels())
                        ->required()
                        ->live()
                        ->default('vistoria_entrada'),

                    Select::make('status')
                        ->label('Status')
                        ->options(VehicleReport::statusLabels())
                        ->default('rascunho')
                        ->required(),

                    TextInput::make('nota_geral')
                        ->label('Nota Geral (0–10)')
                        ->numeric()
                        ->minValue(0)->maxValue(10)
                        ->placeholder('0 a 10')
                        ->suffix('/10'),
                ])->columns(2),

            // ─── Conclusão ────────────────────────────────────────────
            Section::make('Parecer Técnico')
                ->schema([
                    Textarea::make('conclusao')
                        ->label('Conclusão')
                        ->rows(3)
                        ->placeholder('Descreva a conclusão geral do laudo...'),

                    Textarea::make('recomendacoes')
                        ->label('Recomendações')
                        ->rows(3)
                        ->placeholder('Liste as recomendações para o comprador...'),
                ])->columns(2),

            // ─── Checklist de Itens ───────────────────────────────────
            Section::make('Checklist de Vistoria')
                ->description('Avalie cada item da vistoria.')
                ->schema([
                    Repeater::make('items')
                        ->relationship()
                        ->schema([
                            TextInput::make('grupo')
                                ->label('Grupo')
                                ->required()
                                ->columnSpan(1),

                            TextInput::make('item')
                                ->label('Item')
                                ->required()
                                ->columnSpan(2),

                            Select::make('resultado')
                                ->label('Resultado')
                                ->options([
                                    'ok'          => '✅ OK',
                                    'atencao'     => '⚠️ Atenção',
                                    'reprovado'   => '❌ Reprovado',
                                    'nao_avaliado'=> '— N/A',
                                ])
                                ->default('nao_avaliado')
                                ->required()
                                ->columnSpan(1),

                            Textarea::make('observacao')
                                ->label('Observação')
                                ->rows(1)
                                ->columnSpan(4),
                        ])
                        ->columns(4)
                        ->defaultItems(0)
                        ->addActionLabel('+ Adicionar item')
                        ->reorderable()
                        ->collapsible()
                        ->cloneable()
                        ->itemLabel(fn (array $state) => ($state['grupo'] ?? '') . ' — ' . ($state['item'] ?? '')),
                ]),
        ]);
    }
}
