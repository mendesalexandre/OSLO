<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndisponibilidadeMatricula extends Model
{
    protected $table = 'indisponibilidade_matricula';

    const CREATED_AT = 'data_cadastro';
    const UPDATED_AT = null;

    protected $fillable = [
        'indisponibilidade_parte_id',
        'matricula',
    ];

    public function parte(): BelongsTo
    {
        return $this->belongsTo(IndisponibilidadeParte::class, 'indisponibilidade_parte_id');
    }
}
