<?php

namespace App\Models;

use App\Traits\AuxiliarModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MotivoTransacao extends Model
{
    use HasFactory, AuxiliarModel;

    protected $table = 'motivo_transacao';

    const CREATED_AT = 'data_cadastro';
    const UPDATED_AT = 'data_alteracao';
    const DELETED_AT = 'data_exclusao';

    protected $fillable = [
        'tipo_transacao_id',
        'descricao',
        'descricao_expandida',
        'exige_documento',
        'exige_beneficiario',
        'is_ativo',
    ];

    protected function casts(): array
    {
        return [
            'is_ativo'            => 'boolean',
            'exige_documento'     => 'boolean',
            'exige_beneficiario'  => 'boolean',
        ];
    }

    public function tipoTransacao(): BelongsTo
    {
        return $this->belongsTo(TipoTransacao::class, 'tipo_transacao_id');
    }
}
