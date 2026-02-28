<?php

namespace App\Http\Controllers;

use App\Models\Protocolo;
use App\Models\ProtocoloPagamento;
use App\Services\PagamentoService;
use App\Traits\RespostaApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProtocoloPagamentoController extends Controller
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

        $pagamentos = $protocolo->pagamentos()
            ->with(['formaPagamento:id,nome', 'meioPagamento:id,nome', 'usuario:id,nome'])
            ->orderByDesc('data_pagamento')
            ->get();

        return $this->sucesso($pagamentos);
    }

    public function registrar(Request $request, string $id): JsonResponse
    {
        $protocolo = Protocolo::find($id);

        if (!$protocolo) {
            return $this->naoEncontrado('Protocolo não encontrado');
        }

        $dados = $request->validate([
            'forma_pagamento_id' => 'required|exists:forma_pagamento,id',
            'meio_pagamento_id'  => 'nullable|exists:meio_pagamento,id',
            'valor'              => 'required|numeric|min:0.01',
            'data_pagamento'     => 'nullable|date',
            'comprovante'        => 'nullable|string|max:200',
            'observacao'         => 'nullable|string',
        ]);

        $pagamento = $this->pagamentoService->registrar($protocolo, $dados);

        return $this->criado($pagamento, 'Pagamento registrado com sucesso');
    }

    public function estornar(Request $request, string $id, string $pagId): JsonResponse
    {
        $pagamento = ProtocoloPagamento::where('protocolo_id', $id)->find($pagId);

        if (!$pagamento) {
            return $this->naoEncontrado('Pagamento não encontrado');
        }

        if ($pagamento->isEstornado()) {
            return $this->erro('Pagamento já foi estornado', 422);
        }

        $dados = $request->validate([
            'motivo' => 'required|string',
        ]);

        $this->pagamentoService->estornar($pagamento, $dados['motivo']);

        return $this->sucesso(null, 'Pagamento estornado com sucesso');
    }

    public function excluir(string $id, string $pagId): JsonResponse
    {
        $pagamento = ProtocoloPagamento::where('protocolo_id', $id)->find($pagId);

        if (!$pagamento) {
            return $this->naoEncontrado('Pagamento não encontrado');
        }

        if ($pagamento->isConfirmado()) {
            return $this->erro('Estorne o pagamento antes de excluí-lo', 422);
        }

        $protocolo = $pagamento->protocolo;
        $pagamento->delete();
        $protocolo->recalcularValores();

        return $this->sucesso(null, 'Pagamento excluído com sucesso');
    }
}
