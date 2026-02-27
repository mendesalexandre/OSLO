<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class IndicadorPessoalSocio extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'indicador_pessoal_socio';

    const CREATED_AT = 'data_cadastro';
    const UPDATED_AT = 'data_alteracao';
    const DELETED_AT = 'data_exclusao';

    protected $fillable = [
        'indicador_pessoal_id',
        'socio_id',
        'participacao_percentual',
        'cargo',
        'data_entrada',
        'data_saida',
        'is_ativo',
    ];

    protected function casts(): array
    {
        return [
            'participacao_percentual' => 'decimal:2',
            'data_entrada'            => 'date',
            'data_saida'              => 'date',
            'is_ativo'                => 'boolean',
        ];
    }

    public function indicadorPessoal(): BelongsTo
    {
        return $this->belongsTo(IndicadorPessoal::class, 'indicador_pessoal_id');
    }

    public function socio(): BelongsTo
    {
        return $this->belongsTo(IndicadorPessoal::class, 'socio_id');
    }
}
