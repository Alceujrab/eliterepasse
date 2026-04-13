<?php

namespace App\Filament\Resources\EmailTemplates\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmailTemplatesTable
{
    /**
     * Slugs dos templates padrão do sistema — não podem ser excluídos.
     */
    private const SYSTEM_SLUGS = [
        'cliente_aprovado', 'contrato_assinado', 'contrato_assinado_admin',
        'contrato_para_assinar', 'documento_verificado', 'fatura_gerada',
        'novo_cadastro_admin', 'novo_pedido_admin', 'pagamento_confirmado',
        'pedido_confirmado', 'ticket_atualizado', 'usuario_aprovado',
        'usuario_bloqueado',
    ];

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome')
                    ->label('Template')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->fontFamily('mono')
                    ->searchable(),

                TextColumn::make('assunto')
                    ->label('Assunto')
                    ->limit(50),

                IconColumn::make('ativo')
                    ->label('Ativo')
                    ->boolean(),

                TextColumn::make('updated_at')
                    ->label('Atualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('nome')
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn ($record) => !in_array($record->slug, self::SYSTEM_SLUGS)),
            ]);
    }
}
