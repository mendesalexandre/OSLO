<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'usuario';

    const CREATED_AT = 'data_cadastro';
    const UPDATED_AT = 'data_alteracao';
    const DELETED_AT = 'data_exclusao';

    protected $fillable = [
        'nome',
        'email',
        'senha',
        'is_ativo',
    ];

    protected $hidden = [
        'senha',
    ];

    protected function casts(): array
    {
        return [
            'is_ativo' => 'boolean',
            'senha'    => 'hashed',
        ];
    }

    /**
     * Mapeia a coluna `senha` para o campo de autenticação do Laravel.
     */
    public function getAuthPassword(): string
    {
        return $this->senha;
    }

    // ---- Relacionamentos RBAC ----

    public function grupos(): BelongsToMany
    {
        return $this->belongsToMany(Grupo::class, 'usuario_grupo', 'usuario_id', 'grupo_id')
            ->withTimestamps('created_at', 'updated_at');
    }

    public function permissoesIndividuais(): BelongsToMany
    {
        return $this->belongsToMany(Permissao::class, 'usuario_permissao', 'usuario_id', 'permissao_id')
            ->withPivot('tipo')
            ->withTimestamps('created_at', 'updated_at');
    }

    // ---- Métodos RBAC ----

    public function isAdmin(): bool
    {
        return $this->grupos->contains('nome', 'Administrador');
    }

    /**
     * Retorna array com todos os nomes de permissão efetivas do usuário.
     * Regra: permissão individual sobrepõe permissão de grupo.
     */
    public function obterPermissoes(): array
    {
        if ($this->isAdmin()) {
            return Permissao::pluck('nome')->toArray();
        }

        // Permissões herdadas dos grupos
        $deGrupos = $this->grupos
            ->flatMap(fn($g) => $g->permissoes->pluck('nome'))
            ->unique()
            ->values();

        // Permissões individuais
        $individuais = $this->permissoesIndividuais;

        $negar   = $individuais->where('pivot.tipo', 'negar')->pluck('nome');
        $permitir = $individuais->where('pivot.tipo', 'permitir')->pluck('nome');

        return $deGrupos
            ->merge($permitir)
            ->diff($negar)
            ->unique()
            ->values()
            ->toArray();
    }

    public function temPermissao(string $permissao): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return in_array($permissao, $this->obterPermissoes(), true);
    }
}
