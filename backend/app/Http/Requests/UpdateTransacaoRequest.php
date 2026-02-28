<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransacaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo_transacao_id'   => 'sometimes|integer|exists:tipo_transacao,id',
            'motivo_transacao_id' => 'nullable|integer|exists:motivo_transacao,id',
            'banco_id'            => 'nullable|integer|exists:banco,id',
            'referencia'          => 'nullable|string|max:100',
            'descricao'           => 'sometimes|required|string|max:1000',
            'valor'               => 'sometimes|required|numeric|min:0.01',
            'moeda'               => 'nullable|string|max:10',
            'data_transacao'      => 'sometimes|required|date',
            'data_efetivacao'     => 'nullable|date',
            'agencia'             => 'nullable|string|max:20',
            'conta'               => 'nullable|string|max:30',
            'tipo_conta'          => 'nullable|string|max:30',
            'documento_numero'    => 'nullable|string|max:100',
            'beneficiario'        => 'nullable|string|max:255',
            'observacoes'         => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_transacao_id.exists'  => 'Tipo de transação não encontrado',
            'descricao.required'        => 'A descrição é obrigatória',
            'valor.required'            => 'O valor é obrigatório',
            'valor.min'                 => 'O valor deve ser maior que zero',
            'data_transacao.required'   => 'A data da transação é obrigatória',
            'data_transacao.date'       => 'Data da transação inválida',
        ];
    }
}
