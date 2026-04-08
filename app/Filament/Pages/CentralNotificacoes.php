<?php

namespace App\Filament\Pages;

use App\Models\Document;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use App\Services\NotificationService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;

class CentralNotificacoes extends Page
{
    protected string $view = 'filament.pages.central-notificacoes';

    protected static string|\UnitEnum|null $navigationGroup = 'Comunicação';

    protected static ?string $navigationLabel = 'Central de Notificações';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBell;

    protected static ?string $title = 'Central de Notificações';

    protected static ?int $navigationSort = 1;

    public string $filtroTipo  = 'todas';
    public string $filtroPeriodo = '7';

    /** Badge com total de notificações enviadas hoje */
    public static function getNavigationBadge(): ?string
    {
        $count = DatabaseNotification::whereDate('created_at', today())->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public function mount(): void {}

    protected function getHeaderActions(): array
    {
        return [
            // Enviar notificação manual para cliente
            Action::make('enviar_manual')
                ->label('Enviar Notificação')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->modalHeading('Enviar Notificação Manual')
                ->modalWidth('xl')
                ->form([
                    Select::make('user_id')
                        ->label('Cliente')
                        ->options(User::where('is_admin', false)->pluck('name', 'id'))
                        ->searchable()
                        ->required(),

                    Select::make('tipo')
                        ->label('Tipo')
                        ->options([
                            'info'    => '🔵 Informação',
                            'sucesso' => '✅ Sucesso',
                            'alerta'  => '⚠️ Alerta',
                        ])
                        ->default('info')
                        ->required(),

                    Textarea::make('mensagem')
                        ->label('Mensagem')
                        ->required()
                        ->rows(4)
                        ->placeholder('Digite a mensagem que o cliente receberá...'),
                ])
                ->action(function (array $data) {
                    $user = User::find($data['user_id']);
                    if (! $user) return;

                    $icone = match($data['tipo']) {
                        'sucesso' => '✅',
                        'alerta'  => '⚠️',
                        default   => 'ℹ️',
                    };

                    $user->notify(new class($data['mensagem'], $icone) extends \Illuminate\Notifications\Notification {
                        public function __construct(
                            private string $mensagem,
                            private string $icone
                        ) {}

                        public function via($n): array { return ['database']; }

                        public function toDatabase($n): array {
                            return [
                                'tipo'     => 'manual',
                                'icone'    => $this->icone,
                                'titulo'   => 'Mensagem da Elite Repasse',
                                'mensagem' => $this->mensagem,
                                'url'      => '/',
                                'dados'    => [],
                            ];
                        }
                    });

                    FilamentNotification::make()
                        ->title("Notificação enviada para {$user->name}!")
                        ->success()->send();
                }),

            // Notificar todos os clientes ativos
            Action::make('enviar_broadcast')
                ->label('Broadcast')
                ->icon('heroicon-o-megaphone')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Enviar para TODOS os clientes ativos')
                ->modalDescription('Esta ação enviará a notificação para todos os clientes aprovados.')
                ->form([
                    Textarea::make('mensagem')
                        ->label('Mensagem')
                        ->required()
                        ->rows(3)
                        ->placeholder('Novidade, promoção ou aviso importante...'),
                ])
                ->action(function (array $data) {
                    $clientes = User::where('is_admin', false)
                        ->where('aprovado', true)
                        ->get();

                    foreach ($clientes as $user) {
                        $user->notify(new class($data['mensagem']) extends \Illuminate\Notifications\Notification {
                            public function __construct(private string $mensagem) {}
                            public function via($n): array { return ['database']; }
                            public function toDatabase($n): array {
                                return [
                                    'tipo'     => 'broadcast',
                                    'icone'    => '📢',
                                    'titulo'   => 'Novidade — Elite Repasse',
                                    'mensagem' => $this->mensagem,
                                    'url'      => '/',
                                    'dados'    => [],
                                ];
                            }
                        });
                    }

                    FilamentNotification::make()
                        ->title("Broadcast enviado para {$clientes->count()} clientes!")
                        ->success()->send();
                }),
        ];
    }

    public function getNotificacoes(): \Illuminate\Pagination\LengthAwarePaginator
    {
        $dias   = (int) $this->filtroPeriodo;
        $inicio = now()->subDays($dias)->startOfDay();

        $query = DatabaseNotification::with([])
            ->where('created_at', '>=', $inicio)
            ->orderByDesc('created_at');

        if ($this->filtroTipo !== 'todas') {
            $query->whereJsonContains('data->tipo', $this->filtroTipo);
        }

        return $query->paginate(25);
    }

    public function getResumo(): array
    {
        return [
            'total_hoje'       => DatabaseNotification::whereDate('created_at', today())->count(),
            'total_semana'     => DatabaseNotification::where('created_at', '>=', now()->subDays(7))->count(),
            'nao_lidas_total'  => DatabaseNotification::whereNull('read_at')->count(),
            'clientes_com_notif' => DatabaseNotification::whereNull('read_at')
                ->select('notifiable_id')
                ->distinct()
                ->count(),
        ];
    }

    public function marcarTodasLidas(): void
    {
        DatabaseNotification::whereNull('read_at')->update(['read_at' => now()]);
        FilamentNotification::make()->title('Todas marcadas como lidas!')->success()->send();
    }
}
