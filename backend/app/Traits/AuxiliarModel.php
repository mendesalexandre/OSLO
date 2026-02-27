<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

trait AuxiliarModel
{
    use SoftDeletes;

    protected function casts(): array
    {
        return ['is_ativo' => 'boolean'];
    }

    public function scopeAtivo(Builder $query): Builder
    {
        return $query->where('is_ativo', true);
    }
}
