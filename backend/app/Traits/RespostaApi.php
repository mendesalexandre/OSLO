<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait RespostaApi
{
    protected function sucesso(mixed $dados = null, string $mensagem = 'Operação realizada com sucesso', int $status = 200): JsonResponse
    {
        return response()->json([
            'sucesso'  => true,
            'mensagem' => $mensagem,
            'dados'    => $dados,
        ], $status);
    }

    protected function criado(mixed $dados = null, string $mensagem = 'Registro criado com sucesso'): JsonResponse
    {
        return response()->json([
            'sucesso'  => true,
            'mensagem' => $mensagem,
            'dados'    => $dados,
        ], 201);
    }

    protected function erro(string $mensagem = 'Erro ao processar a solicitação', int $status = 400, array $erros = []): JsonResponse
    {
        $body = [
            'sucesso'  => false,
            'mensagem' => $mensagem,
        ];

        if (!empty($erros)) {
            $body['erros'] = $erros;
        }

        return response()->json($body, $status);
    }

    protected function naoEncontrado(string $mensagem = 'Registro não encontrado'): JsonResponse
    {
        return response()->json([
            'sucesso'  => false,
            'mensagem' => $mensagem,
        ], 404);
    }

    protected function semPermissao(string $mensagem = 'Acesso negado'): JsonResponse
    {
        return response()->json([
            'sucesso'  => false,
            'mensagem' => $mensagem,
        ], 403);
    }
}
