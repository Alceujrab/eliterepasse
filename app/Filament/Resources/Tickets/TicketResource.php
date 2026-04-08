<?php

namespace App\Filament\Resources\Tickets;

use App\Filament\Resources\Tickets\Pages\CreateTicket;
use App\Filament\Resources\Tickets\Pages\EditTicket;
use App\Filament\Resources\Tickets\Pages\ListTickets;
use App\Filament\Resources\Tickets\Pages\ViewTicket;
use App\Filament\Resources\Tickets\Schemas\TicketForm;
use App\Filament\Resources\Tickets\Schemas\TicketInfolist;
use App\Filament\Resources\Tickets\Tables\TicketsTable;
use App\Models\Ticket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static string|\UnitEnum|null $navigationGroup = 'Clientes';

    protected static ?string $navigationLabel = 'Chamados de Suporte';

    protected static ?string $modelLabel = 'Chamado';

    protected static ?string $pluralModelLabel = 'Chamados';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'titulo';

    /** Badge com tickets abertos */
    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()
            ->whereIn('status', ['aberto', 'em_atendimento'])->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $urgentes = static::getEloquentQuery()
            ->where('prioridade', 'urgente')->where('status', 'aberto')->count();
        return $urgentes > 0 ? 'danger' : 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return TicketForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TicketInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TicketsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTickets::route('/'),
            'create' => CreateTicket::route('/create'),
            'view'   => ViewTicket::route('/{record}'),
            'edit'   => EditTicket::route('/{record}/edit'),
        ];
    }
}
