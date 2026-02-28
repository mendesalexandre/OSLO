<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransacaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'indicador_pessoal_id' => 'required|integer|exists:indicador_pessoal,id',
            'tipo_transacao_id'    => 'required|integer|exists:tipo_transacao,id',
            'motivo_transacao_id'  => 'nullable|integer|exists:motivo_transacao,id',
            'banco_id'             => 'nullable|integer|exists:banco,id',
            'referencia'           => 'nullable|string|max:100',
            'descricao'            => 'required|string|max:1000',
            'valor'                => 'required|numeric|min:0.01',
            'moeda'                => 'nullable|string|max:10',
            'data_transacao'       => 'required|date',
            'data_efetivacao'      => 'nullable|date',
            'agencia'              => 'nullable|string|max:20',
            'conta'                => 'nullable|string|max:30',
            'tipo_conta'           => 'nullable|string|max:30',
            'documento_numero'     => 'nullable|string|max:100',
            'beneficiario'         => 'nullable|string|max:255',
            'observacoes'          => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'indicador_pessoal_id.required' => 'O indicador pessoal é obrigatório',
            'indicador_pessoal_id.exists'   => 'Indicador pessoal não encontrado',
            'tipo_transacao_id.required'    => 'O tipo de transação é obrigatório',
            'tipo_transacao_id.exists'      => 'Tipo de transação não encontrado',
            'descricao.required'            => 'A descrição é obrigatória',
            'valor.required'                => 'O valor é obrigatório',
            'valor.min'                     => 'O valor deve ser maior que zero',
            'data_transacao.required'       => 'A data da transação é obrigatória',
            'data_transacao.date'           => 'Data da transação inválida',
        ];
    }
}
