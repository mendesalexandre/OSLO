<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProtocoloIsencao extends Model
{
    use SoftDeletes;

    protected $table = 'protocolo_isencao';

    const CREATED_AT = 'data_cadastro';
    const UPDATED_AT = 'data_alteracao';
    const DELETED_AT = 'data_exclusao';

    protected $fillable = [
        'protocolo_id',
        'tipo',
        'numero_processo',
        'vara',
        'data_decisao',
        'valor_isento',
        'documento_path',
        'observacao',
        'usuario_id',
    ];

    protected $casts = [
        'data_decisao'   => 'date',
        'valor_isento'   => 'decimal:2',
        'data_cadastro'  => 'datetime',
        'data_alteracao' => 'datetime',
        'data_exclusao'  => 'datetime',
    ];

    public function protocolo(): BelongsTo
    {
        return $this->belongsTo(Protocolo::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
