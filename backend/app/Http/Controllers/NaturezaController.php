<?php

namespace App\Http\Controllers;

use App\Models\Natureza;
use App\Traits\RespostaApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NaturezaController extends Controller
{
    use RespostaApi;

    /**
     * Lista naturezas. Aceita ?nome=xxx para busca por nome (mínimo 2 chars).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Natureza::ativo()->orderBy('nome');

        if ($request->filled('nome') && strlen($request->nome) >= 2) {
            $query->buscarPorNome($request->nome);
        }

        $naturezas = $query->get(['id', 'uuid', 'nome', 'descricao', 'codigo']);

        return $this->sucesso($naturezas);
    }
}
