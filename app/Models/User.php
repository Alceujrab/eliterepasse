<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

#[Fillable([
    'name', 'email', 'password', 'phone', 'cpf', 'foto',
    'razao_social', 'nome_fantasia', 'cnpj', 'inscricao_estadual',
    'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'estado',
    'social_id', 'social_provider', 'avatar_url',
    'status', 'observacoes', 'aprovado_em', 'aprovado_por',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->is_admin;
    }

    /** O cliente pode acessar o portal */
    public function isActive(): bool
    {
        return $this->status === 'ativo';
    }

    public function isPending(): bool
    {
        return $this->status === 'pendente';
    }

    public function isBlocked(): bool
    {
        return $this->status === 'bloqueado';
    }

    /** Nome completo ou razão social */
    public function getNomeExibicaoAttribute(): string
    {
        return $this->razao_social ?? $this->nome_fantasia ?? $this->name;
    }

    /** CNPJ ou CPF formatado */
    public function getDocumentoAttribute(): ?string
    {
        return $this->cnpj ?? $this->cpf;
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(self::class, 'aprovado_por');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'aprovado_em'       => 'datetime',
            'is_admin'          => 'boolean',
        ];
    }
}
