<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permissao extends Model
{
    use HasFactory;

    protected $table = 'permissao';

    const CREATED_AT = 'data_cadastro';
    const UPDATED_AT = 'data_alteracao';

    protected $fillable = [
        'nome',
        'descricao',
        'modulo',
    ];

    public function grupos(): BelongsToMany
    {
        return $this->belongsToMany(Grupo::class, 'grupo_permissao')
            ->withTimestamps('created_at', 'updated_at');
    }

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'usuario_permissao', 'permissao_id', 'usuario_id')
            ->withPivot('tipo')
            ->withTimestamps('created_at', 'updated_at');
    }

    public function scopePorModulo($query, string $modulo)
    {
        return $query->where('modulo', $modulo);
    }
}
