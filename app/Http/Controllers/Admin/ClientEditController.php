<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ClientEditController extends Controller
{
    public function __invoke(User $client): View|Response
    {
        abort_if($client->is_admin, 404);

        return view('admin.clients.edit', [
            'client' => $client,
            'statusOptions' => [
                'pendente'  => '⏳ Pendente',
                'ativo'     => '✅ Ativo',
                'bloqueado' => '🚫 Bloqueado',
            ],
            'estadoOptions' => ClientCreateController::estadoOptions(),
        ]);
    }
}
