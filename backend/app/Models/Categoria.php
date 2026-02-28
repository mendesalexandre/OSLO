<?php

namespace App\Models;

use App\Enums\CategoriaTipoEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria extends Model
{
    use SoftDeletes;

    protected $table = 'categoria';

    const CREATED_AT = 'data_cadastro';
    const UPDATED_AT = 'data_alteracao';
    const DELETED_AT = 'data_exclusao';

    protected $fillable = [
        'is_ativo', 'categoria_pai_id', 'nome', 'descricao', 'tipo', 'icone', 'cor',
    ];

    protected $casts = [
        'is_ativo' => 'boolean',
        'tipo'     => CategoriaTipoEnum::class,
    ];

    public function pai(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_pai_id');
    }

    public function subcategorias(): HasMany
    {
        return $this->hasMany(Categoria::class, 'categoria_pai_id')
            ->where('is_ativo', true)
            ->orderBy('nome');
    }

    public function scopeAtivo($query)
    {
        return $query->where('is_ativo', true);
    }

    public function scopeRaiz($query)
    {
        return $query->whereNull('categoria_pai_id');
    }
}
