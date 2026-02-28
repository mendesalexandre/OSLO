<?php

namespace App\Http\Controllers;

use App\Models\TipoTransacao;
use App\Traits\RespostaApi;
use Illuminate\Http\JsonResponse;

class TipoTransacaoController extends Controller
{
    use RespostaApi;

    public function index(): JsonResponse
    {
        $tipos = TipoTransacao::with(['motivosTransacao' => fn ($q) => $q->ativo()])
            ->ativo()
            ->orderBy('descricao')
            ->get();

        return $this->sucesso($tipos);
    }

    public function porTipo(string $tipo): JsonResponse
    {
        $tipoTransacao = TipoTransacao::ativo()->porTipo($tipo)->first();

        if (!$tipoTransacao) {
            return $this->naoEncontrado('Tipo de transação não encontrado');
        }

        $motivos = $tipoTransacao->motivosTransacao()->ativo()->orderBy('descricao')->get();

        return $this->sucesso($motivos);
    }
}
