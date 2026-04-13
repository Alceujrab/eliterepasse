<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\OrderHistory;
use App\Models\OrderShipment;
use App\Services\NotificationService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),

            // ─── Disponibilizar Documento ────────────────────────────
            Action::make('disponibilizar_documento')
                ->label('Disponibilizar Documento')
                ->icon('heroicon-o-document-arrow-up')
                ->color('info')
                ->visible(fn () => in_array($this->record->status, ['confirmado', 'faturado', 'pago']))
                ->form([
                    Select::make('tipo_documento')
                        ->label('Tipo de Documento')
                        ->options(OrderShipment::tipoDocumentoLabels())
                        ->required(),
                    TextInput::make('titulo')
                        ->label('Título / Descrição')
                        ->placeholder('Ex: ATPV do veículo HB20 - Placa ABC1234'),
                    FileUpload::make('arquivo')
                        ->label('Arquivo do Documento')
                        ->disk('public')
                        ->directory('shipments/documentos')
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                        ->maxSize(10240)
                        ->required(),
                    Textarea::make('observacoes')
                        ->label('Observações')
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $shipment = OrderShipment::create([
                        'order_id'        => $this->record->id,
                        'user_id'         => auth()->id(),
                        'tipo_documento'  => $data['tipo_documento'],
                        'titulo'          => $data['titulo'],
                        'file_path'       => $data['arquivo'],
                        'nome_original'   => $data['titulo'] ?? $data['tipo_documento'],
                        'status'          => 'disponivel',
                        'observacoes'     => $data['observacoes'],
                    ]);

                    $tipo = OrderShipment::tipoDocumentoLabels()[$data['tipo_documento']] ?? $data['tipo_documento'];

                    OrderHistory::registrar(
                        $this->record->id,
                        'documento_disponivel',
                        $this->record->status,
                        $this->record->status,
                        "Documento {$tipo} disponibilizado para o cliente",
                        auth()->id(),
                        ['shipment_id' => $shipment->id, 'tipo' => $data['tipo_documento']]
                    );

                    app(NotificationService::class)->documentoDisponivel($shipment);

                    Notification::make()
                        ->title('Documento disponibilizado!')
                        ->body("O cliente foi notificado via e-mail e WhatsApp.")
                        ->success()
                        ->send();
                }),

            // ─── Registrar Despacho ──────────────────────────────────
            Action::make('registrar_despacho')
                ->label('Registrar Despacho')
                ->icon('heroicon-o-truck')
                ->color('warning')
                ->visible(fn () => $this->record->shipments()->where('status', 'disponivel')->exists())
                ->form([
                    Select::make('shipment_id')
                        ->label('Documento a Despachar')
                        ->options(fn () => $this->record->shipments()
                            ->where('status', 'disponivel')
                            ->get()
                            ->mapWithKeys(fn ($s) => [
                                $s->id => (OrderShipment::tipoDocumentoLabels()[$s->tipo_documento] ?? $s->tipo_documento) . ($s->titulo ? " — {$s->titulo}" : ''),
                            ])
                        )
                        ->required(),
                    Select::make('metodo_envio')
                        ->label('Método de Envio')
                        ->options(OrderShipment::metodoEnvioLabels())
                        ->required()
                        ->live(),
                    TextInput::make('metodo_envio_detalhe')
                        ->label('Detalhes do Envio')
                        ->placeholder('Nome da transportadora, etc.')
                        ->visible(fn ($get) => in_array($get('metodo_envio'), ['transportadora', 'outro'])),
                    TextInput::make('codigo_rastreio')
                        ->label('Código de Rastreio')
                        ->placeholder('Ex: BR123456789BR')
                        ->visible(fn ($get) => in_array($get('metodo_envio'), ['correios', 'transportadora'])),
                    FileUpload::make('comprovante_despacho')
                        ->label('Comprovante de Despacho')
                        ->helperText('Recibo dos Correios, AR, comprovante da transportadora, etc.')
                        ->disk('public')
                        ->directory('shipments/comprovantes')
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                        ->maxSize(10240),
                    DateTimePicker::make('despachado_em')
                        ->label('Data/Hora do Despacho')
                        ->default(now())
                        ->required(),
                    Textarea::make('observacoes')
                        ->label('Observações do Envio')
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $shipment = OrderShipment::findOrFail($data['shipment_id']);
                    $shipment->update([
                        'metodo_envio'              => $data['metodo_envio'],
                        'metodo_envio_detalhe'      => $data['metodo_envio_detalhe'] ?? null,
                        'codigo_rastreio'           => $data['codigo_rastreio'] ?? null,
                        'comprovante_despacho_path' => $data['comprovante_despacho'] ?? null,
                        'despachado_em'             => $data['despachado_em'],
                        'status'                    => 'despachado',
                        'observacoes'               => $data['observacoes'] ?? $shipment->observacoes,
                    ]);

                    $tipo = OrderShipment::tipoDocumentoLabels()[$shipment->tipo_documento] ?? $shipment->tipo_documento;
                    $metodo = OrderShipment::metodoEnvioLabels()[$data['metodo_envio']] ?? $data['metodo_envio'];

                    OrderHistory::registrar(
                        $this->record->id,
                        'documento_despachado',
                        $this->record->status,
                        $this->record->status,
                        "Documento {$tipo} despachado via {$metodo}" . ($data['codigo_rastreio'] ?? false ? " — Rastreio: {$data['codigo_rastreio']}" : ''),
                        auth()->id(),
                        [
                            'shipment_id'     => $shipment->id,
                            'metodo_envio'    => $data['metodo_envio'],
                            'codigo_rastreio' => $data['codigo_rastreio'] ?? null,
                        ]
                    );

                    app(NotificationService::class)->documentoDespachado($shipment);

                    Notification::make()
                        ->title('Despacho registrado!')
                        ->body("O cliente foi notificado via e-mail e WhatsApp.")
                        ->success()
                        ->send();
                }),

            // ─── Marcar Entregue ─────────────────────────────────────
            Action::make('marcar_entregue')
                ->label('Marcar como Entregue')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->record->shipments()->where('status', 'despachado')->exists())
                ->form([
                    Select::make('shipment_id')
                        ->label('Documento Entregue')
                        ->options(fn () => $this->record->shipments()
                            ->where('status', 'despachado')
                            ->get()
                            ->mapWithKeys(fn ($s) => [
                                $s->id => (OrderShipment::tipoDocumentoLabels()[$s->tipo_documento] ?? $s->tipo_documento) . " — Rastreio: " . ($s->codigo_rastreio ?? 'N/A'),
                            ])
                        )
                        ->required(),
                ])
                ->action(function (array $data) {
                    $shipment = OrderShipment::findOrFail($data['shipment_id']);
                    $shipment->update(['status' => 'entregue']);

                    $tipo = OrderShipment::tipoDocumentoLabels()[$shipment->tipo_documento] ?? $shipment->tipo_documento;

                    OrderHistory::registrar(
                        $this->record->id,
                        'documento_entregue',
                        $this->record->status,
                        $this->record->status,
                        "Documento {$tipo} entregue ao cliente",
                        auth()->id(),
                        ['shipment_id' => $shipment->id]
                    );

                    Notification::make()
                        ->title('Documento marcado como entregue!')
                        ->success()
                        ->send();
                }),
        ];
    }
}
