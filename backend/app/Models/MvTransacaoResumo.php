<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MvTransacaoResumo extends Model
{
    protected $table = 'mv_transacao_resumo';

    public $incrementing = false;
    public $timestamps   = false;

    protected $primaryKey = 'indicador_pessoal_id';

    protected $fillable = [
        'indicador_pessoal_id',
        'total',
        'total_pendente',
        'total_confirmada',
        'total_liquidada',
        'total_cancelada',
        'total_entradas',
        'total_saidas',
        'saldo',
    ];

    protected function casts(): array
    {
        return [
            'total_entradas' => 'decimal:2',
            'total_saidas'   => 'decimal:2',
            'saldo'          => 'decimal:2',
        ];
    }
}
