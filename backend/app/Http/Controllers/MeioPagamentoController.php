<?php

namespace App\Http\Controllers;

use App\Models\MeioPagamento;
use App\Traits\RespostaApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeioPagamentoController extends Controller
{
    use RespostaApi;

    public function index(Request $request): JsonResponse
    {
        $query = MeioPagamento::with('formaPagamento:id,nome');

        if ($request->filled('busca')) {
            $query->where('nome', 'ilike', "%{$request->busca}%");
        }

        if ($request->filled('is_ativo')) {
            $query->where('is_ativo', $request->boolean('is_ativo'));
        }

        if ($request->filled('forma_pagamento_id')) {
            $query->where('forma_pagamento_id', $request->forma_pagamento_id);
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
            'forma_pagamento_id' => 'required|exists:forma_pagamento,id',
            'nome'               => 'required|string|max:100',
            'descricao'          => 'nullable|string|max:255',
            'identificador'      => 'nullable|string|max:100',
            'taxa_percentual'    => 'numeric|min:0',
            'taxa_fixa'          => 'numeric|min:0',
            'prazo_compensacao'  => 'integer|min:0',
            'is_ativo'           => 'boolean',
        ]);

        $meio = MeioPagamento::create($dados);

        return $this->criado($meio->load('formaPagamento:id,nome'), 'Meio de pagamento criado com sucesso');
    }

    public function show(int $id): JsonResponse
    {
        $meio = MeioPagamento::with('formaPagamento:id,nome')->find($id);

        if (!$meio) {
            return $this->naoEncontrado('Meio de pagamento não encontrado');
        }

        return $this->sucesso($meio);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $meio = MeioPagamento::find($id);

        if (!$meio) {
            return $this->naoEncontrado('Meio de pagamento não encontrado');
        }

        $dados = $request->validate([
            'forma_pagamento_id' => 'sometimes|exists:forma_pagamento,id',
            'nome'               => 'sometimes|string|max:100',
            'descricao'          => 'nullable|string|max:255',
            'identificador'      => 'nullable|string|max:100',
            'taxa_percentual'    => 'numeric|min:0',
            'taxa_fixa'          => 'numeric|min:0',
            'prazo_compensacao'  => 'integer|min:0',
            'is_ativo'           => 'boolean',
        ]);

        $meio->update($dados);

        return $this->sucesso(
            $meio->fresh()->load('formaPagamento:id,nome'),
            'Meio de pagamento atualizado com sucesso'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $meio = MeioPagamento::find($id);

        if (!$meio) {
            return $this->naoEncontrado('Meio de pagamento não encontrado');
        }

        $meio->delete();

        return $this->sucesso(null, 'Meio de pagamento excluído com sucesso');
    }
}
