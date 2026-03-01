<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\RespostaApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use RespostaApi;

    /**
     * Autentica o usuário e inicia a sessão.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'senha' => ['required', 'string'],
        ], [
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email'    => 'Informe um e-mail válido.',
            'senha.required' => 'O campo senha é obrigatório.',
        ]);

        $credentials = [
            'email'    => $request->email,
            'password' => $request->senha,
        ];

        if (!Auth::guard('web')->attempt($credentials)) {
            return $this->erro('Credenciais inválidas', 401);
        }

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return $this->sucesso($this->dadosUsuario(Auth::guard('web')->user()), 'Login realizado com sucesso');
    }

    /**
     * Encerra a sessão do usuário.
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return $this->sucesso(null, 'Logout realizado com sucesso');
    }

    /**
     * Retorna os dados do usuário autenticado.
     */
    public function me(Request $request): JsonResponse
    {
        return $this->sucesso($this->dadosUsuario($request->user()), 'Usuário autenticado');
    }

    /**
     * Renova a sessão.
     */
    public function refresh(Request $request): JsonResponse
    {
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return $this->sucesso($this->dadosUsuario($request->user()), 'Sessão renovada');
    }

    /**
     * Formata os dados do usuário para a resposta da API.
     */
    private function dadosUsuario(User $user): array
    {
        $user->load(['grupos', 'grupos.permissoes', 'permissoesIndividuais']);

        return [
            'id'            => $user->id,
            'nome'          => $user->nome,
            'email'         => $user->email,
            'is_ativo'      => $user->is_ativo,
            'data_cadastro' => $user->data_cadastro?->format('Y-m-d H:i:s'),
            'permissoes'    => $user->obterPermissoes(),
            'grupos'        => $user->grupos->pluck('nome')->toArray(),
            'empresa'       => null,
        ];
    }
}
