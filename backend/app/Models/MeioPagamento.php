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

    protected $fillable = ['forma_pagamento_id', 'nome', 'is_ativo'];

    protected $casts = ['is_ativo' => 'boolean'];

    public function formaPagamento(): BelongsTo
    {
        return $this->belongsTo(FormaPagamento::class);
    }
}
