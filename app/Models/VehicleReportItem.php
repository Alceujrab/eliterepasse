<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleReportItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'fotos' => 'array',
    ];

    public function report()
    {
        return $this->belongsTo(VehicleReport::class, 'vehicle_report_id');
    }

    public static function resultadoColors(): array
    {
        return [
            'ok'          => 'success',
            'atencao'     => 'warning',
            'reprovado'   => 'danger',
            'nao_avaliado'=> 'gray',
        ];
    }

    public static function resultadoIcons(): array
    {
        return [
            'ok'          => '✅',
            'atencao'     => '⚠️',
            'reprovado'   => '❌',
            'nao_avaliado'=> '—',
        ];
    }
}
