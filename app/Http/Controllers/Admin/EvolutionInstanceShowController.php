<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvolutionInstance;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EvolutionInstanceShowController extends Controller
{
    public function __invoke(Request $request, EvolutionInstance $evolutionInstance): View
    {
        $qrCode = null;

        if ($request->boolean('show_qr')) {
            $rawQr = $evolutionInstance->getQrCode();
            $qrCode = $rawQr
                ? (str_starts_with($rawQr, 'data:') ? $rawQr : 'data:image/png;base64,' . $rawQr)
                : null;
        }

        return view('admin.whatsapp-instancias.show', [
            'instance' => $evolutionInstance,
            'qrCode' => $qrCode,
            'statusOptions' => [
                'open' => 'Conectado',
                'connecting' => 'Conectando',
                'close' => 'Desconectado',
            ],
        ]);
    }
}