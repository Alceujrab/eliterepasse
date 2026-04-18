<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\View\View;

class VehicleCreateController extends Controller
{
    public static function formOptions(): array
    {
        return [
            'categoryOptions' => [
                'SUV'              => 'SUV',
                'Crossover'        => 'Crossover',
                'Sedan'            => 'Sedan',
                'Hatch'            => 'Hatch',
                'Pickup'           => 'Pickup',
                'Minivan'          => 'Minivan',
                'Perua/SW'         => 'Perua / SW',
                'Coupé'            => 'Coupé',
                'Conversível'      => 'Conversível',
                'Van'              => 'Van',
                'Utilitário'       => 'Utilitário',
                'Outro'            => 'Outro',
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
            'steeringOptions' => [
                'Elétrica'          => 'Elétrica',
                'Hidráulica'        => 'Hidráulica',
                'Eletro-hidráulica' => 'Eletro-hidráulica',
                'Mecânica'          => 'Mecânica',
            ],
            'statusOptions' => Vehicle::statusLabels(),
            'ufOptions' => [
                'AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS',
                'MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC',
                'SP','SE','TO',
            ],
        ];
    }

    public function __invoke(): View
    {
        return view('admin.vehicles.create', self::formOptions());
    }
}
