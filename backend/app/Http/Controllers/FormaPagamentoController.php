<?php

namespace App\Http\Controllers;

use App\Models\FormaPagamento;
use App\Traits\RespostaApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FormaPagamentoController extends Controller
{
    use RespostaApi;

    public function index(Request $request): JsonResponse
    {
        $query = FormaPagamento::query();

        if ($request->filled('busca')) {
            $query->where('nome', 'ilike', "%{$request->busca}%");
        }

        if ($request->filled('is_ativo')) {
            $query->where('is_ativo', $request->boolean('is_ativo'));
        }

        if ($request->boolean('paginado')) {
            $porPagina = $request->integer('por_pagina', 15);
            return $this->sucessoPaginado($query->orderBy('nome')->paginate($porPagina));
        }

        return $this->sucesso($query->orderBy('nome')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $dados = $request->validate([
            'nome'      => 'required|string|max:100|unique:forma_pagamento,nome',
            'descricao' => 'nullable|string',
            'is_ativo'  => 'boolean',
        ]);

        return $this->criado(FormaPagamento::create($dados), 'Forma de pagamento criada com sucesso');
    }

    public function show(int $id): JsonResponse
    {
        $forma = FormaPagamento::with('meiosPagamento')->find($id);

        if (!$forma) {
            return $this->naoEncontrado('Forma de pagamento não encontrada');
        }

        return $this->sucesso($forma);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $forma = FormaPagamento::find($id);

        if (!$forma) {
            return $this->naoEncontrado('Forma de pagamento não encontrada');
        }

        $dados = $request->validate([
            'nome'      => "sometimes|string|max:100|unique:forma_pagamento,nome,{$id}",
            'descricao' => 'nullable|string',
            'is_ativo'  => 'boolean',
        ]);

        $forma->update($dados);

        return $this->sucesso($forma->fresh(), 'Forma de pagamento atualizada com sucesso');
    }

    public function destroy(int $id): JsonResponse
    {
        $forma = FormaPagamento::find($id);

        if (!$forma) {
            return $this->naoEncontrado('Forma de pagamento não encontrada');
        }

        if ($forma->meiosPagamento()->exists()) {
            return $this->erro('Não é possível excluir uma forma de pagamento que possui meios vinculados', 422);
        }

        $forma->delete();

        return $this->sucesso(null, 'Forma de pagamento excluída com sucesso');
    }
}
