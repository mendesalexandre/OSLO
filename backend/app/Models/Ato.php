<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ato extends Model
{
    use SoftDeletes;

    protected $table = 'ato';

    const CREATED_AT = 'data_cadastro';
    const UPDATED_AT = 'data_alteracao';
    const DELETED_AT = 'data_exclusao';

    protected $fillable = [
        'codigo',
        'nome',
        'is_ativo',
    ];

    protected $casts = [
        'is_ativo' => 'boolean',
    ];

    public function calcularValor(?float $baseCalculo = null): float
    {
        // Stub — será implementado na fase de tabela de custas
        return 0.0;
    }
}
