<?php

namespace App\Models;

use App\Traits\AuxiliarModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoTransacao extends Model
{
    use HasFactory, AuxiliarModel;

    protected $table = 'tipo_transacao';

    const CREATED_AT = 'data_cadastro';
    const UPDATED_AT = 'data_alteracao';
    const DELETED_AT = 'data_exclusao';

    protected $fillable = ['descricao', 'tipo', 'icone', 'cor', 'is_ativo'];

    public function motivosTransacao(): HasMany
    {
        return $this->hasMany(MotivoTransacao::class, 'tipo_transacao_id');
    }

    public function scopePorTipo(Builder $query, string $tipo): Builder
    {
        return $query->where('tipo', $tipo);
    }
}
