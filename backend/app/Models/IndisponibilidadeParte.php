<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IndisponibilidadeParte extends Model
{
    protected $table = 'indisponibilidade_parte';

    const CREATED_AT = 'data_cadastro';
    const UPDATED_AT = null;

    protected $fillable = [
        'indisponibilidade_id',
        'cpf_cnpj',
        'nome_razao',
    ];

    public function indisponibilidade(): BelongsTo
    {
        return $this->belongsTo(Indisponibilidade::class, 'indisponibilidade_id');
    }

    public function matriculas(): HasMany
    {
        return $this->hasMany(IndisponibilidadeMatricula::class, 'indisponibilidade_parte_id');
    }

    public function indicadorPessoal(): BelongsTo
    {
        return $this->belongsTo(IndicadorPessoal::class, 'cpf_cnpj', 'cpf_cnpj')
                    ->where('is_atual', true);
    }
}
