<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use App\Traits\RespostaApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EstadoController extends Controller
{
    use RespostaApi;

    /**
     * Lista todos os estados ativos, ordenados por nome.
     */
    public function index(): JsonResponse
    {
        $estados = Estado::ativo()
            ->orderBy('nome')
            ->get(['id', 'nome', 'sigla', 'ibge_codigo']);

        return $this->sucesso($estados);
    }
}
