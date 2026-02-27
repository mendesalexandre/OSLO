<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Indisponibilidade extends Model
{
    use SoftDeletes;

    protected $table = 'indisponibilidade';

    const CREATED_AT = 'data_cadastro';
    const UPDATED_AT = 'data_alteracao';
    const DELETED_AT = 'data_exclusao';

    protected $fillable = [
        'is_ativo',
        'status',
        'tipo',
        'protocolo_indisponibilidade',
        'numero_processo',
        'usuario',
        'ordem_status',
        'forum_vara',
        'nome_instituicao',
        'email',
        'telefone',
        'data_pedido',
        'ordem_prioritaria',
        'segredo_justica',
        'cancelamento_protocolo',
        'cancelamento_tipo',
        'cancelamento_data',
    ];

    protected function casts(): array
    {
        return [
            'is_ativo'          => 'boolean',
            'ordem_prioritaria' => 'boolean',
            'segredo_justica'   => 'boolean',
            'data_pedido'       => 'datetime',
            'cancelamento_data' => 'datetime',
            'cancelamento_tipo' => 'integer',
        ];
    }

    public function partes(): HasMany
    {
        return $this->hasMany(IndisponibilidadeParte::class, 'indisponibilidade_id');
    }
}
