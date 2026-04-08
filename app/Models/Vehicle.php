<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicle extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'accessories'          => 'array',
        'media'                => 'array',
        'location'             => 'array',
        'has_report'           => 'boolean',
        'has_factory_warranty' => 'boolean',
        'is_on_sale'           => 'boolean',
        'is_just_arrived'      => 'boolean',
        'sale_price'           => 'decimal:2',
        'fipe_price'           => 'decimal:2',
        'profit_margin'        => 'decimal:2',
    ];

    // ─── Relações ────────────────────────────────────────────────────
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    // ─── Helpers FIPE ────────────────────────────────────────────────
    /** Percentual atual abaixo/acima da FIPE */
    public function getDescontoFipeAttribute(): ?float
    {
        if (! $this->fipe_price || ! $this->sale_price) return null;
        return round((1 - ($this->sale_price / $this->fipe_price)) * 100, 1);
    }

    /** Margem de lucro calculada */
    public function getMargemCalculadaAttribute(): ?float
    {
        if (! $this->fipe_price || ! $this->sale_price) return null;
        return round((($this->sale_price - $this->fipe_price) / $this->fipe_price) * 100, 1);
    }

    /** Status em português */
    public static function statusLabels(): array
    {
        return [
            'available' => '✅ Disponível',
            'reserved'  => '⏳ Reservado',
            'sold'      => '🔴 Vendido',
        ];
    }

    public static function statusColors(): array
    {
        return [
            'available' => 'success',
            'reserved'  => 'warning',
            'sold'      => 'danger',
        ];
    }

    /** Nome completo do veículo */
    public function getNomeCompletoAttribute(): string
    {
        return implode(' ', array_filter([
            $this->brand,
            $this->model,
            $this->version,
            $this->model_year,
        ]));
    }
}
