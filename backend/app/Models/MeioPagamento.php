<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeioPagamento extends Model
{
    use SoftDeletes;

    protected $table = 'meio_pagamento';

    const CREATED_AT = 'data_cadastro';
    const UPDATED_AT = 'data_alteracao';
    const DELETED_AT = 'data_exclusao';

    protected $fillable = [
        'forma_pagamento_id', 'nome', 'descricao', 'identificador',
        'taxa_percentual', 'taxa_fixa', 'prazo_compensacao', 'is_ativo',
    ];

    protected $casts = [
        'is_ativo'          => 'boolean',
        'taxa_percentual'   => 'decimal:2',
        'taxa_fixa'         => 'decimal:2',
        'prazo_compensacao' => 'integer',
    ];

    public function scopeAtivo($query)
    {
        return $query->where('is_ativo', true);
    }

    public function formaPagamento(): BelongsTo
    {
        return $this->belongsTo(FormaPagamento::class);
    }
}
