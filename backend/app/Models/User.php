<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
}
