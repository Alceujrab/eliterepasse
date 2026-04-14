<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ClientShowController extends Controller
{
    public function __invoke(User $client): View|Response
    {
        abort_if($client->is_admin, 404);

        $client->load(['approvedBy:id,name']);

        $recentOrders = Order::query()
            ->with(['vehicle', 'financial'])
            ->where('user_id', $client->id)
            ->latest()
            ->limit(6)
            ->get();

        $recentDocuments = Document::query()
            ->with(['vehicle', 'verificadoPor'])
            ->where('user_id', $client->id)
            ->latest()
            ->limit(6)
            ->get();

        $recentTickets = Ticket::query()
            ->with(['order', 'vehicle', 'atribuidoA'])
            ->where('user_id', $client->id)
            ->latest()
            ->limit(6)
            ->get();

        $summary = [
            'ordersTotal' => Order::query()->where('user_id', $client->id)->count(),
            'paidOrders' => Order::query()->where('user_id', $client->id)->where('status', Order::STATUS_PAGO)->count(),
            'openTickets' => Ticket::query()->where('user_id', $client->id)->whereNotIn('status', ['resolvido', 'fechado'])->count(),
            'pendingDocuments' => Document::query()->where('user_id', $client->id)->where('status', 'pendente')->count(),
            'visibleDocuments' => Document::query()->where('user_id', $client->id)->where('visivel_cliente', true)->count(),
        ];

        return view('admin.clients.show', [
            'client' => $client,
            'summary' => $summary,
            'recentOrders' => $recentOrders,
            'recentDocuments' => $recentDocuments,
            'recentTickets' => $recentTickets,
            'orderStatusOptions' => Order::statusLabels(),
            'ticketStatusOptions' => Ticket::statusLabels(),
            'documentStatusOptions' => Document::statusLabels(),
            'documentTypeOptions' => Document::tipoLabels(),
        ]);
    }
}