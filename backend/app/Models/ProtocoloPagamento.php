<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProtocoloPagamento extends Model
{
    use SoftDeletes;

    protected $table = 'protocolo_pagamento';

    const CREATED_AT = 'data_cadastro';
    const UPDATED_AT = 'data_alteracao';
    const DELETED_AT = 'data_exclusao';

    protected $fillable = [
        'protocolo_id',
        'forma_pagamento_id',
        'meio_pagamento_id',
        'usuario_id',
        'valor',
        'data_pagamento',
        'comprovante',
        'observacao',
        'status',
    ];

    protected $casts = [
        'valor'          => 'decimal:2',
        'data_pagamento' => 'datetime',
        'data_cadastro'  => 'datetime',
        'data_alteracao' => 'datetime',
        'data_exclusao'  => 'datetime',
    ];

    public function protocolo(): BelongsTo
    {
        return $this->belongsTo(Protocolo::class);
    }

    public function formaPagamento(): BelongsTo
    {
        return $this->belongsTo(FormaPagamento::class);
    }

    public function meioPagamento(): BelongsTo
    {
        return $this->belongsTo(MeioPagamento::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function scopeConfirmados($query)
    {
        return $query->where('status', 'confirmado');
    }

    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }

    public function isConfirmado(): bool
    {
        return $this->status === 'confirmado';
    }

    public function isEstornado(): bool
    {
        return $this->status === 'estornado';
    }
}
