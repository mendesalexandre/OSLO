<?php

namespace App\Models;

use App\Traits\AuxiliarModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nacionalidade extends Model
{
    use HasFactory, AuxiliarModel;

    protected $table = 'nacionalidade';

    const CREATED_AT = 'data_cadastro';
    const UPDATED_AT = 'data_alteracao';
    const DELETED_AT = 'data_exclusao';

    protected $fillable = ['descricao', 'gentilico', 'is_ativo'];
}
