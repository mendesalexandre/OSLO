<?php

namespace App\Http\Controllers;

use App\Models\Permissao;
use App\Traits\RespostaApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissaoController extends Controller
{
    use RespostaApi;

    /**
     * Lista todas as permissões, agrupadas por módulo.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Permissao::orderBy('modulo')->orderBy('nome');

        if ($request->filled('busca')) {
            $query->where(function ($q) use ($request) {
                $q->where('nome', 'ilike', '%' . $request->busca . '%')
                  ->orWhere('descricao', 'ilike', '%' . $request->busca . '%')
                  ->orWhere('modulo', 'ilike', '%' . $request->busca . '%');
            });
        }

        $permissoes = $query->get();

        if ($request->boolean('agrupado', false)) {
            $agrupado = $permissoes
                ->groupBy('modulo')
                ->map(fn($items, $modulo) => [
                    'modulo'      => $modulo,
                    'permissoes'  => $items->values(),
                ])
                ->values();

            return $this->sucesso($agrupado);
        }

        return $this->sucesso($permissoes);
    }

    /**
     * Lista os módulos distintos.
     */
    public function modulos(): JsonResponse
    {
        $modulos = Permissao::distinct()->orderBy('modulo')->pluck('modulo');

        return $this->sucesso($modulos);
    }
}
