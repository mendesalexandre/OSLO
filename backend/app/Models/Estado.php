<?php

namespace App\Models;

use App\Traits\AuxiliarModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    use HasFactory, AuxiliarModel;

    protected $table = 'estado';

    const CREATED_AT = 'data_cadastro';
    const UPDATED_AT = 'data_alteracao';
    const DELETED_AT = 'data_exclusao';

    protected $fillable = [
        'is_ativo',
        'nome',
        'sigla',
        'ibge_codigo',
    ];

    public function scopeBuscarPorNome($query, string $nome)
    {
        return $query->where('nome', 'ilike', "%{$nome}%");
    }
}
