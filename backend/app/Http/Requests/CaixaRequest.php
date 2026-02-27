<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CaixaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'nome' => 'required|string|max:100',
            'descricao' => 'nullable|string|max:255',
            'is_ativo' => 'boolean',
            'saldo_inicial' => 'required|numeric|min:0',
            'saldo_atual' => 'nullable|numeric',
        ];

        // No update, os campos podem ser opcionais
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['nome'] = 'sometimes|required|string|max:100';
            $rules['saldo_inicial'] = 'sometimes|required|numeric|min:0';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome é obrigatório',
            'nome.max' => 'O nome não pode ter mais de 100 caracteres',
            'descricao.max' => 'A descrição não pode ter mais de 255 caracteres',
            'saldo_inicial.required' => 'O saldo inicial é obrigatório',
            'saldo_inicial.numeric' => 'O saldo inicial deve ser um número',
            'saldo_inicial.min' => 'O saldo inicial não pode ser negativo',
            'saldo_atual.numeric' => 'O saldo atual deve ser um número',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Define valores padrão
        if (!$this->has('is_ativo')) {
            $this->merge(['is_ativo' => true]);
        }

        // Se saldo_atual não for informado, assume o saldo_inicial
        if (!$this->has('saldo_atual') && $this->has('saldo_inicial')) {
            $this->merge(['saldo_atual' => $this->saldo_inicial]);
        }
    }
}
