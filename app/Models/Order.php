<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'valor_compra'  => 'decimal:2',
        'valor_fipe'    => 'decimal:2',
        'total_amount'  => 'decimal:2',
        'confirmado_em' => 'datetime',
    ];

    // ─── Status ───────────────────────────────────────────────────────
    const STATUS_PENDENTE    = 'pendente';
    const STATUS_CONFIRMADO  = 'confirmado';
    const STATUS_CANCELADO   = 'cancelado';
    const STATUS_FATURADO    = 'faturado';
    const STATUS_AGUARD      = 'aguardando_pgto';

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDENTE   => '⏳ Pendente',
            self::STATUS_AGUARD     => '💳 Aguardando Pagamento',
            self::STATUS_CONFIRMADO => '✅ Confirmado',
            self::STATUS_FATURADO   => '📄 Faturado',
            self::STATUS_CANCELADO  => '❌ Cancelado',
        ];
    }

    public static function statusColors(): array
    {
        return [
            self::STATUS_PENDENTE   => 'gray',
            self::STATUS_AGUARD     => 'warning',
            self::STATUS_CONFIRMADO => 'success',
            self::STATUS_FATURADO   => 'info',
            self::STATUS_CANCELADO  => 'danger',
        ];
    }

    // ─── Relações ────────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function paymentData()
    {
        return $this->morphOne(PaymentData::class, 'payable');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Legacy
    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class);
    }

    public function financial()
    {
        return $this->hasOne(Financial::class);
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────
    public function getNumeroAttribute(): string
    {
        return 'ORD-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }
}
