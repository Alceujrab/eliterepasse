<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'tipo', 'label'];

    /**
     * Obtém um valor de configuração pelo key
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) return $default;
        
        if ($setting->tipo === 'boolean') {
            return (bool) $setting->value;
        }
        
        return $setting->value ?? $default;
    }

    /**
     * Define um valor de configuração
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
