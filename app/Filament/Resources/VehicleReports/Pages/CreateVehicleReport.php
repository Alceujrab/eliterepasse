<?php

namespace App\Filament\Resources\VehicleReports\Pages;

use App\Filament\Resources\VehicleReports\VehicleReportResource;
use App\Models\VehicleReport;
use Filament\Resources\Pages\CreateRecord;

class CreateVehicleReport extends CreateRecord
{
    protected static string $resource = VehicleReportResource::class;

    /**
     * Pré-popula o checklist padrão com os 36 itens ao criar um novo laudo.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $tipo    = $data['tipo'] ?? 'vistoria_entrada';
        $items   = VehicleReport::checklistPadrao($tipo);

        $data['items'] = collect($items)->map(fn ($item, $i) => [
            'grupo'    => $item['grupo'],
            'item'     => $item['item'],
            'resultado'=> 'nao_avaliado',
            'observacao'=> null,
            'ordem'    => $i,
        ])->values()->toArray();

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;

        // Gerar número se não tiver
        if (! $record->numero) {
            $record->update(['numero' => $record->gerarNumero()]);
        }

        // Salvar criado_por
        $record->update(['criado_por' => auth()->id()]);
    }
}
