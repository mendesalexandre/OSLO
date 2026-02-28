<?php

namespace App\Http\Controllers;

use App\Models\Protocolo;
use App\Services\PagamentoService;
use App\Traits\RespostaApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProtocoloIsencaoController extends Controller
{
    use RespostaApi;

    public function __construct(
        private PagamentoService $pagamentoService
    ) {}

    public function listar(string $id): JsonResponse
    {
        $protocolo = Protocolo::find($id);

        if (!$protocolo) {
            return $this->naoEncontrado('Protocolo não encontrado');
        }

        $isencoes = $protocolo->isencoes()
            ->with('usuario:id,nome')
            ->orderByDesc('data_cadastro')
            ->get();

        return $this->sucesso($isencoes);
    }

    public function registrar(Request $request, string $id): JsonResponse
    {
        $protocolo = Protocolo::find($id);

        if (!$protocolo) {
            return $this->naoEncontrado('Protocolo não encontrado');
        }

        $dados = $request->validate([
            'tipo'            => 'required|string|max:50',
            'numero_processo' => 'nullable|string|max:50',
            'vara'            => 'nullable|string|max:100',
            'data_decisao'    => 'nullable|date',
            'valor_isento'    => 'required|numeric|min:0.01',
            'documento_path'  => 'nullable|string|max:500',
            'observacao'      => 'nullable|string',
        ]);

        $isencao = $this->pagamentoService->registrarIsencao($protocolo, $dados);

        return $this->criado($isencao, 'Isenção registrada com sucesso');
    }
}
