<?php

namespace App\Http\Requests;

use App\Enums\TransacaoTipo;
use App\Enums\TransacaoNatureza;
use App\Enums\TransacaoNaturezaEnum;
use App\Enums\TransacaoStatus;
use App\Enums\TransacaoStatusEnum;
use App\Enums\TransacaoTipoEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class TransacaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'is_ativo' => 'boolean',
            'caixa_id' => 'required|exists:caixa,id',
            'tipo' => ['required', new Enum(TransacaoTipoEnum::class)],
            'natureza' => ['required', new Enum(TransacaoNaturezaEnum::class)],
            'categoria_id' => 'nullable|exists:categoria,id',
            'tipo_pagamento_id' => 'nullable|exists:tipo_pagamento,id',
            'meio_pagamento_id' => 'nullable|exists:meio_pagamento,id',
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
            'valor_pago' => 'nullable|numeric|min:0',
            'status' => ['nullable', new Enum(TransacaoStatusEnum::class)],
            'data_vencimento' => 'nullable|date',
            'data_pagamento' => 'nullable|date',
            'observacao' => 'nullable|string',
            'documento' => 'nullable|string|max:100',
            'pessoa_id' => 'nullable|exists:pessoa,id',
            // 'usuario_id' => 'required|exists:usuario,id',
        ];

        // No update, os campos podem ser opcionais
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['caixa_id'] = 'sometimes|required|exists:caixa,id';
            $rules['tipo'] = ['sometimes', 'required', new Enum(TransacaoTipoEnum::class)];
            $rules['natureza'] = ['sometimes', 'required', new Enum(TransacaoNaturezaEnum::class)];
            $rules['descricao'] = 'sometimes|required|string|max:255';
            $rules['valor'] = 'sometimes|required|numeric|min:0';
            $rules['data_vencimento'] = 'sometimes|required|date';
            $rules['usuario_id'] = 'sometimes|required|exists:users,id';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'caixa_id.required' => 'O caixa é obrigatório',
            'caixa_id.exists' => 'Caixa inválido',
            'tipo.required' => 'O tipo é obrigatório',
            'natureza.required' => 'A natureza é obrigatória',
            'categoria_id.exists' => 'Categoria inválida',
            'tipo_pagamento_id.exists' => 'Tipo de pagamento inválido',
            'meio_pagamento_id.exists' => 'Meio de pagamento inválido',
            'descricao.required' => 'A descrição é obrigatória',
            'descricao.max' => 'A descrição não pode ter mais de 255 caracteres',
            'valor.required' => 'O valor é obrigatório',
            'valor.numeric' => 'O valor deve ser um número',
            'valor.min' => 'O valor não pode ser negativo',
            'data_vencimento.required' => 'A data de vencimento é obrigatória',
            'data_vencimento.date' => 'Data de vencimento inválida',
            'pessoa_id.exists' => 'Pessoa inválida',
            'usuario_id.required' => 'O usuário é obrigatório',
            'usuario_id.exists' => 'Usuário inválido',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Define valores padrão
        if (!$this->has('is_ativo')) {
            $this->merge(['is_ativo' => true]);
        }

        if (!$this->has('status')) {
            $this->merge(['status' => TransacaoStatusEnum::PENDENTE->value]);
        }

        if (!$this->has('valor_pago')) {
            $this->merge(['valor_pago' => 0]);
        }

        // Definir usuário atual se não informado
        if (!$this->has('usuario_id') && auth()->check()) {
            $this->merge(['usuario_id' => auth()->id()]);
        }
    }
}
