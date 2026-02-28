<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProtocoloItem extends Model
{
    use SoftDeletes;

    protected $table = 'protocolo_item';

    const CREATED_AT = 'data_cadastro';
    const UPDATED_AT = 'data_alteracao';
    const DELETED_AT = 'data_exclusao';

    protected $fillable = [
        'protocolo_id',
        'ato_id',
        'descricao',
        'quantidade',
        'base_calculo',
        'valor_unitario',
        'valor_total',
        'observacao',
    ];

    protected $casts = [
        'quantidade'     => 'integer',
        'base_calculo'   => 'decimal:2',
        'valor_unitario' => 'decimal:2',
        'valor_total'    => 'decimal:2',
        'data_cadastro'  => 'datetime',
        'data_alteracao' => 'datetime',
        'data_exclusao'  => 'datetime',
    ];

    public function protocolo(): BelongsTo
    {
        return $this->belongsTo(Protocolo::class);
    }

    public function ato(): BelongsTo
    {
        return $this->belongsTo(Ato::class);
    }
}
