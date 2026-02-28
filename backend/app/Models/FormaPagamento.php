<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormaPagamento extends Model
{
    use SoftDeletes;

    protected $table = 'forma_pagamento';

    const CREATED_AT = 'data_cadastro';
    const UPDATED_AT = 'data_alteracao';
    const DELETED_AT = 'data_exclusao';

    protected $fillable = ['nome', 'is_ativo'];

    protected $casts = ['is_ativo' => 'boolean'];

    public function meiosPagamento(): HasMany
    {
        return $this->hasMany(MeioPagamento::class);
    }
}
