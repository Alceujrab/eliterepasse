<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\View\View;

class VehicleCreateController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.vehicles.create', [
            'categoryOptions' => [
                'SUV'         => 'SUV',
                'Sedan'       => 'Sedan',
                'Hatch'       => 'Hatch',
                'Pickup'      => 'Pickup',
                'Minivan'     => 'Minivan',
                'Conversível' => 'Conversível',
                'Outro'       => 'Outro',
            ],
            'fuelOptions' => [
                'Flex'      => 'Flex',
                'Gasolina'  => 'Gasolina',
                'Diesel'    => 'Diesel',
                'Elétrico'  => 'Elétrico',
                'Híbrido'   => 'Híbrido',
                'GNV'       => 'GNV',
                'Etanol'    => 'Etanol',
            ],
            'transmissionOptions' => [
                'Manual'           => 'Manual',
                'Automático'       => 'Automático',
                'CVT'              => 'CVT',
                'Automatizado'     => 'Automatizado',
                'Automático (8AT)' => 'Automático (8AT)',
                'Automático (9AT)' => 'Automático (9AT)',
                'Automático (7DCT)'=> 'Automático (7DCT)',
            ],
            'statusOptions' => Vehicle::statusLabels(),
        ]);
    }
}
