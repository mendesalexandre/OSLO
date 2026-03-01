<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Permissao;
use App\Models\User;
use App\Traits\RespostaApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsuarioPermissaoController extends Controller
{
    use RespostaApi;

    /**
     * Lista usuários com seus grupos.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::with('grupos')->orderBy('nome');

        if ($request->filled('nome')) {
            $query->where('nome', 'ilike', '%' . $request->nome . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'ilike', '%' . $request->email . '%');
        }

        return $this->sucesso($query->get([
            'id', 'nome', 'email', 'is_ativo', 'data_cadastro',
        ]));
    }

    /**
     * Retorna grupos e permissões individuais do usuário.
     */
    public function show(string $id): JsonResponse
    {
        $usuario = User::with([
            'grupos.permissoes',
            'permissoesIndividuais',
        ])->find($id);

        if (!$usuario) {
            return $this->naoEncontrado('Usuário não encontrado');
        }

        return $this->sucesso([
            'id'                     => $usuario->id,
            'nome'                   => $usuario->nome,
            'email'                  => $usuario->email,
            'is_ativo'               => $usuario->is_ativo,
            'grupos'                 => $usuario->grupos,
            'permissoes_individuais' => $usuario->permissoesIndividuais,
            'permissoes_efetivas'    => $usuario->obterPermissoes(),
        ]);
    }

    /**
     * Sincroniza os grupos do usuário.
     */
    public function sincronizarGrupos(Request $request, string $id): JsonResponse
    {
        $usuario = User::find($id);

        if (!$usuario) {
            return $this->naoEncontrado('Usuário não encontrado');
        }

        $request->validate([
            'grupo_ids'   => ['required', 'array'],
            'grupo_ids.*' => ['integer', 'exists:grupo,id'],
        ]);

        $usuario->grupos()->sync($request->grupo_ids);

        return $this->sucesso(
            $usuario->load('grupos'),
            'Grupos sincronizados com sucesso'
        );
    }

    /**
     * Define/atualiza uma permissão individual do usuário (permitir | negar | herdar).
     * tipo=herdar remove a sobrescrita individual.
     */
    public function definirPermissao(Request $request, string $id): JsonResponse
    {
        $usuario = User::find($id);

        if (!$usuario) {
            return $this->naoEncontrado('Usuário não encontrado');
        }

        $request->validate([
            'permissao_id' => ['required', 'integer', 'exists:permissao,id'],
            'tipo'         => ['required', 'in:permitir,negar,herdar'],
        ]);

        if ($request->tipo === 'herdar') {
            $usuario->permissoesIndividuais()->detach($request->permissao_id);
        } else {
            $usuario->permissoesIndividuais()->syncWithoutDetaching([
                $request->permissao_id => ['tipo' => $request->tipo],
            ]);

            // Atualiza o tipo caso já exista o registro
            $usuario->permissoesIndividuais()->updateExistingPivot(
                $request->permissao_id,
                ['tipo' => $request->tipo]
            );
        }

        return $this->sucesso(null, 'Permissão atualizada com sucesso');
    }

    /**
     * Retorna as permissões efetivas do usuário (resultado final calculado).
     */
    public function efetivas(string $id): JsonResponse
    {
        $usuario = User::with([
            'grupos.permissoes',
            'permissoesIndividuais',
        ])->find($id);

        if (!$usuario) {
            return $this->naoEncontrado('Usuário não encontrado');
        }

        return $this->sucesso([
            'permissoes' => $usuario->obterPermissoes(),
            'is_admin'   => $usuario->isAdmin(),
        ]);
    }
}
