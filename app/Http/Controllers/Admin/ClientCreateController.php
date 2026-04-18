<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ClientCreateController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.clients.create', [
            'statusOptions' => [
                'pendente'  => '⏳ Pendente',
                'ativo'     => '✅ Ativo',
                'bloqueado' => '🚫 Bloqueado',
            ],
            'estadoOptions' => self::estadoOptions(),
        ]);
    }

    public static function estadoOptions(): array
    {
        return [
            'AC' => 'Acre', 'AL' => 'Alagoas', 'AP' => 'Amapá', 'AM' => 'Amazonas',
            'BA' => 'Bahia', 'CE' => 'Ceará', 'DF' => 'Distrito Federal',
            'ES' => 'Espírito Santo', 'GO' => 'Goiás', 'MA' => 'Maranhão',
            'MT' => 'Mato Grosso', 'MS' => 'Mato Grosso do Sul', 'MG' => 'Minas Gerais',
            'PA' => 'Pará', 'PB' => 'Paraíba', 'PR' => 'Paraná', 'PE' => 'Pernambuco',
            'PI' => 'Piauí', 'RJ' => 'Rio de Janeiro', 'RN' => 'Rio Grande do Norte',
            'RS' => 'Rio Grande do Sul', 'RO' => 'Rondônia', 'RR' => 'Roraima',
            'SC' => 'Santa Catarina', 'SP' => 'São Paulo', 'SE' => 'Sergipe', 'TO' => 'Tocantins',
        ];
    }
}
