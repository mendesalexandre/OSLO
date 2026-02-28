<?php

namespace App\Models;

use App\Traits\AuxiliarModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Natureza extends Model
{
    use HasFactory, AuxiliarModel;

    protected $table = 'natureza';

    const CREATED_AT = 'data_cadastro';
    const UPDATED_AT = 'data_alteracao';
    const DELETED_AT = 'data_exclusao';

    protected $fillable = [
        'is_ativo',
        'uuid',
        'nome',
        'descricao',
        'codigo',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function scopeBuscarPorNome($query, string $nome)
    {
        return $query->where('nome', 'ilike', "%{$nome}%");
    }
}
