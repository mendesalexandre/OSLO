<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditoriaTransacao extends Model
{
    protected $table = 'auditoria_transacao';

    protected $fillable = [
        'transacao_id',
        'usuario_id',
        'campo_alterado',
        'valor_anterior',
        'valor_novo',
        'acao',
        'observacao',
        'ip_address',
        'user_agent',
    ];

    public function transacao(): BelongsTo
    {
        return $this->belongsTo(Transacao::class, 'transacao_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
