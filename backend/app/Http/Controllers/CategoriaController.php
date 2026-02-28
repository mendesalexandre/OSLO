<?php

namespace App\Http\Controllers;

use App\Enums\CategoriaTipoEnum;
use App\Models\Categoria;
use App\Traits\RespostaApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    use RespostaApi;

    /**
     * Lista categorias-pai com subcategorias aninhadas.
     */
    public function index(): JsonResponse
    {
        $categorias = Categoria::with('subcategorias')
            ->raiz()
            ->orderBy('nome')
            ->get();

        return $this->sucesso($categorias);
    }

    /**
     * Lista todas as categorias (plano) — para selects.
     */
    public function todas(): JsonResponse
    {
        $categorias = Categoria::with('subcategorias')
            ->raiz()
            ->ativo()
            ->orderBy('nome')
            ->get();

        return $this->sucesso($categorias);
    }

    public function store(Request $request): JsonResponse
    {
        $dados = $request->validate([
            'nome'             => 'required|string|max:150',
            'descricao'        => 'nullable|string',
            'tipo'             => 'required|in:' . implode(',', CategoriaTipoEnum::values()),
            'icone'            => 'nullable|string|max:100',
            'cor'              => 'nullable|string|max:30',
            'is_ativo'         => 'boolean',
            'categoria_pai_id' => 'nullable|exists:categoria,id',
        ]);

        $categoria = Categoria::create($dados);
        $categoria->load('subcategorias');

        return $this->criado($categoria, 'Categoria criada com sucesso');
    }

    public function show(int $id): JsonResponse
    {
        $categoria = Categoria::with(['subcategorias', 'pai'])->find($id);

        if (!$categoria) {
            return $this->naoEncontrado('Categoria não encontrada');
        }

        return $this->sucesso($categoria);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {
            return $this->naoEncontrado('Categoria não encontrada');
        }

        $dados = $request->validate([
            'nome'             => 'sometimes|string|max:150',
            'descricao'        => 'nullable|string',
            'tipo'             => 'sometimes|in:' . implode(',', CategoriaTipoEnum::values()),
            'icone'            => 'nullable|string|max:100',
            'cor'              => 'nullable|string|max:30',
            'is_ativo'         => 'boolean',
            'categoria_pai_id' => 'nullable|exists:categoria,id',
        ]);

        if (isset($dados['categoria_pai_id']) && $dados['categoria_pai_id'] == $id) {
            return $this->erro('Uma categoria não pode ser filha de si mesma', 422);
        }

        $categoria->update($dados);
        $categoria->load('subcategorias');

        return $this->sucesso($categoria, 'Categoria atualizada com sucesso');
    }

    public function destroy(int $id): JsonResponse
    {
        $categoria = Categoria::withCount('subcategorias')->find($id);

        if (!$categoria) {
            return $this->naoEncontrado('Categoria não encontrada');
        }

        if ($categoria->subcategorias_count > 0) {
            return $this->erro('Não é possível excluir uma categoria que possui subcategorias', 422);
        }

        $categoria->delete();

        return $this->sucesso(null, 'Categoria excluída com sucesso');
    }
}
