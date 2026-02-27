<?php

namespace App\Models;

use App\Traits\AuxiliarModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoCivil extends Model
{
    use HasFactory, AuxiliarModel;

    protected $table = 'estado_civil';

    const CREATED_AT = 'data_cadastro';
    const UPDATED_AT = 'data_alteracao';
    const DELETED_AT = 'data_exclusao';

    protected $fillable = ['descricao', 'is_ativo'];
}
