<?php

namespace App\Http\Controllers;

use App\Models\Natureza;
use App\Traits\RespostaApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NaturezaController extends Controller
{
    use RespostaApi;

    /**
     * Lista naturezas.
     * - ?nome=xxx → busca por nome (autocomplete, mínimo 2 chars)
     * - ?admin=1  → retorna todos os campos + inativos para gestão
     */
    public function index(Request $request): JsonResponse
    {
        $query = Natureza::orderBy('nome');

        if ($request->boolean('admin')) {
            // Modo gestão: inclui inativos, retorna todos os campos
            if ($request->filled('nome')) {
                $query->buscarPorNome($request->nome);
            }
            if ($request->filled('is_ativo')) {
                $query->where('is_ativo', $request->boolean('is_ativo'));
            }
            return $this->sucesso($query->get());
        }

        // Modo autocomplete: apenas ativos
        $query->ativo();
        if ($request->filled('nome') && strlen($request->nome) >= 2) {
            $query->buscarPorNome($request->nome);
        }

        return $this->sucesso($query->get(['id', 'uuid', 'nome', 'descricao', 'codigo']));
    }

    public function store(Request $request): JsonResponse
    {
        $dados = $request->validate([
            'nome'      => 'required|string|max:255|unique:natureza,nome',
            'descricao' => 'nullable|string',
            'codigo'    => 'nullable|string|max:50|unique:natureza,codigo',
            'is_ativo'  => 'boolean',
        ]);

        return $this->criado(Natureza::create($dados), 'Natureza criada com sucesso');
    }

    public function show(int $id): JsonResponse
    {
        $natureza = Natureza::find($id);

        if (!$natureza) {
            return $this->naoEncontrado('Natureza não encontrada');
        }

        return $this->sucesso($natureza);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $natureza = Natureza::find($id);

        if (!$natureza) {
            return $this->naoEncontrado('Natureza não encontrada');
        }

        $dados = $request->validate([
            'nome'      => ['sometimes', 'string', 'max:255', Rule::unique('natureza', 'nome')->ignore($id)],
            'descricao' => 'nullable|string',
            'codigo'    => ['nullable', 'string', 'max:50', Rule::unique('natureza', 'codigo')->ignore($id)],
            'is_ativo'  => 'boolean',
        ]);

        $natureza->update($dados);

        return $this->sucesso($natureza->fresh(), 'Natureza atualizada com sucesso');
    }

    public function destroy(int $id): JsonResponse
    {
        $natureza = Natureza::find($id);

        if (!$natureza) {
            return $this->naoEncontrado('Natureza não encontrada');
        }

        $natureza->delete();

        return $this->sucesso(null, 'Natureza excluída com sucesso');
    }
}
