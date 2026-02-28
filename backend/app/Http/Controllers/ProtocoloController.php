<?php

namespace App\Http\Controllers;

use App\Models\Protocolo;
use App\Models\ProtocoloItem;
use App\Services\ProtocoloService;
use App\Traits\RespostaApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProtocoloController extends Controller
{
    use RespostaApi;

    public function __construct(
        private ProtocoloService $protocoloService
    ) {}

    public function listar(Request $request): JsonResponse
    {
        $query = Protocolo::with('atendente:id,nome');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('data_inicio')) {
            $query->where('data_cadastro', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->where('data_cadastro', '<=', $request->data_fim . ' 23:59:59');
        }

        if ($request->filled('usuario_id')) {
            $query->where('atendente_id', $request->usuario_id);
        }

        if ($request->filled('solicitante')) {
            $query->where('solicitante_nome', 'ilike', "%{$request->solicitante}%");
        }

        if ($request->filled('matricula')) {
            $query->where('matricula', 'ilike', "%{$request->matricula}%");
        }

        if ($request->filled('numero')) {
            $query->where('numero', 'ilike', "%{$request->numero}%");
        }

        $porPagina = $request->integer('por_pagina', 15);

        return $this->sucessoPaginado(
            $query->orderByDesc('data_cadastro')->paginate($porPagina)
        );
    }

    public function criar(Request $request): JsonResponse
    {
        $dados = $request->validate([
            'solicitante_nome'      => 'required|string|max:200',
            'solicitante_cpf_cnpj'  => 'nullable|string|max:18',
            'solicitante_telefone'  => 'nullable|string|max:20',
            'solicitante_email'     => 'nullable|email|max:200',
            'tipo'                  => 'nullable|string|in:NORMAL,ORCAMENTO,PROCESSO_INTERNO,EXAME_CALCULO',
            'meio_solicitacao_id'   => 'nullable|integer',
            'natureza_id'           => 'nullable|exists:natureza,id',
            'estado_id'             => 'nullable|exists:estado,id',
            'matricula'             => 'nullable|string|max:50',
            'observacao'            => 'nullable|string',
            'valor_desconto'        => 'nullable|numeric|min:0',
            'itens'                 => 'array',
            'itens.*.ato_id'        => 'required|exists:ato,id',
            'itens.*.quantidade'    => 'integer|min:1',
            'itens.*.base_calculo'  => 'nullable|numeric|min:0',
            'itens.*.valor_unitario' => 'nullable|numeric|min:0',
            'itens.*.descricao'     => 'nullable|string|max:300',
            'itens.*.observacao'    => 'nullable|string',
        ]);

        $protocolo = $this->protocoloService->criar($dados);

        return $this->criado($protocolo, 'Protocolo criado com sucesso');
    }

    public function exibir(string $id): JsonResponse
    {
        $protocolo = Protocolo::with([
            'atendente:id,nome',
            'natureza:id,nome',
            'itens.ato:id,codigo,nome',
            'pagamentos.formaPagamento:id,nome',
            'pagamentos.meioPagamento:id,nome',
            'pagamentos.usuario:id,nome',
            'isencoes.usuario:id,nome',
        ])->find($id);

        if (!$protocolo) {
            return $this->naoEncontrado('Protocolo não encontrado');
        }

        $dados               = $protocolo->toArray();
        $dados['andamentos'] = $protocolo->gerarAndamentos();
        $dados['valor_restante'] = $protocolo->valorRestante();

        return $this->sucesso($dados);
    }

    public function atualizar(Request $request, string $id): JsonResponse
    {
        $protocolo = Protocolo::find($id);

        if (!$protocolo) {
            return $this->naoEncontrado('Protocolo não encontrado');
        }

        $dados = $request->validate([
            'solicitante_nome'     => 'sometimes|string|max:200',
            'solicitante_cpf_cnpj' => 'nullable|string|max:18',
            'solicitante_telefone' => 'nullable|string|max:20',
            'solicitante_email'    => 'nullable|email|max:200',
            'matricula'            => 'nullable|string|max:50',
            'observacao'           => 'nullable|string',
            'valor_desconto'       => 'nullable|numeric|min:0',
        ]);

        $protocolo->update($dados);

        if (array_key_exists('valor_desconto', $dados)) {
            $protocolo->recalcularValores();
        }

        return $this->sucesso(
            $protocolo->fresh()->load('atendente:id,nome'),
            'Protocolo atualizado com sucesso'
        );
    }

    public function cancelar(Request $request, string $id): JsonResponse
    {
        $protocolo = Protocolo::find($id);

        if (!$protocolo) {
            return $this->naoEncontrado('Protocolo não encontrado');
        }

        if ($protocolo->status === 'cancelado') {
            return $this->erro('Protocolo já está cancelado', 422);
        }

        $dados = $request->validate([
            'motivo' => 'required|string',
        ]);

        $this->protocoloService->cancelar($protocolo, $dados['motivo']);

        return $this->sucesso(null, 'Protocolo cancelado com sucesso');
    }

    public function adicionarItem(Request $request, string $id): JsonResponse
    {
        $protocolo = Protocolo::find($id);

        if (!$protocolo) {
            return $this->naoEncontrado('Protocolo não encontrado');
        }

        $dados = $request->validate([
            'ato_id'         => 'required|exists:ato,id',
            'quantidade'     => 'integer|min:1',
            'base_calculo'   => 'nullable|numeric|min:0',
            'valor_unitario' => 'nullable|numeric|min:0',
            'descricao'      => 'nullable|string|max:300',
            'observacao'     => 'nullable|string',
        ]);

        $item = $this->protocoloService->adicionarItem($protocolo, $dados);

        return $this->criado($item->load('ato:id,codigo,nome'), 'Item adicionado com sucesso');
    }

    public function removerItem(string $id, string $itemId): JsonResponse
    {
        $item = ProtocoloItem::where('protocolo_id', $id)->find($itemId);

        if (!$item) {
            return $this->naoEncontrado('Item não encontrado');
        }

        $this->protocoloService->removerItem($item);

        return $this->sucesso(null, 'Item removido com sucesso');
    }

    public function recalcular(string $id): JsonResponse
    {
        $protocolo = Protocolo::find($id);

        if (!$protocolo) {
            return $this->naoEncontrado('Protocolo não encontrado');
        }

        $protocolo->recalcularValores();

        return $this->sucesso($protocolo->fresh(), 'Valores recalculados com sucesso');
    }
}
