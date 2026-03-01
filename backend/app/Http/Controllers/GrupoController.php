<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Permissao;
use App\Traits\RespostaApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GrupoController extends Controller
{
    use RespostaApi;

    public function index(Request $request): JsonResponse
    {
        $query = Grupo::withCount('usuarios')->orderBy('nome');

        if ($request->filled('nome')) {
            $query->where('nome', 'ilike', '%' . $request->nome . '%');
        }

        if ($request->filled('is_ativo')) {
            $query->where('is_ativo', filter_var($request->is_ativo, FILTER_VALIDATE_BOOLEAN));
        }

        return $this->sucesso($query->get());
    }

    public function show(string $id): JsonResponse
    {
        $grupo = Grupo::with('permissoes')->find($id);

        if (!$grupo) {
            return $this->naoEncontrado('Grupo não encontrado');
        }

        return $this->sucesso($grupo);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nome'      => ['required', 'string', 'max:100', 'unique:grupo,nome'],
            'descricao' => ['nullable', 'string', 'max:255'],
            'is_ativo'  => ['boolean'],
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'nome.unique'   => 'Já existe um grupo com este nome.',
        ]);

        $grupo = Grupo::create([
            'nome'      => $request->nome,
            'descricao' => $request->descricao,
            'is_ativo'  => $request->boolean('is_ativo', true),
        ]);

        return $this->criado($grupo, 'Grupo criado com sucesso');
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $grupo = Grupo::find($id);

        if (!$grupo) {
            return $this->naoEncontrado('Grupo não encontrado');
        }

        $request->validate([
            'nome'      => ['required', 'string', 'max:100', 'unique:grupo,nome,' . $id],
            'descricao' => ['nullable', 'string', 'max:255'],
            'is_ativo'  => ['boolean'],
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'nome.unique'   => 'Já existe um grupo com este nome.',
        ]);

        $grupo->update([
            'nome'      => $request->nome,
            'descricao' => $request->descricao,
            'is_ativo'  => $request->boolean('is_ativo', $grupo->is_ativo),
        ]);

        return $this->sucesso($grupo, 'Grupo atualizado com sucesso');
    }

    public function destroy(string $id): JsonResponse
    {
        $grupo = Grupo::withCount('usuarios')->find($id);

        if (!$grupo) {
            return $this->naoEncontrado('Grupo não encontrado');
        }

        if ($grupo->usuarios_count > 0) {
            return $this->erro('Não é possível excluir um grupo com usuários vinculados', 422);
        }

        $grupo->delete();

        return $this->sucesso(null, 'Grupo excluído com sucesso');
    }

    /**
     * Sincroniza as permissões do grupo (substitui todas).
     */
    public function sincronizarPermissoes(Request $request, string $id): JsonResponse
    {
        $grupo = Grupo::find($id);

        if (!$grupo) {
            return $this->naoEncontrado('Grupo não encontrado');
        }

        $request->validate([
            'permissao_ids'   => ['required', 'array'],
            'permissao_ids.*' => ['integer', 'exists:permissao,id'],
        ]);

        $grupo->permissoes()->sync($request->permissao_ids);

        return $this->sucesso(
            $grupo->load('permissoes'),
            'Permissões sincronizadas com sucesso'
        );
    }
}
