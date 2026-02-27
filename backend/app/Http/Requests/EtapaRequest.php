<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EtapaRequest extends FormRequest
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
            'pode_finalizar' => 'boolean',
            'pode_voltar' => 'boolean',
            'pode_redistribuir' => 'boolean',
            'forcar_troca_usuario' => 'boolean',
            'contar_prazo' => 'boolean',
            'priorizar_usuario_anterior' => 'boolean',
            'cor' => 'nullable|string|max:7',
            'tipo_atribuicao' => 'required|in:SORTEIO,PERSONALIZADO'
        ];

        // No update, os campos podem ser opcionais
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['nome'] = 'sometimes|required|string|max:100';
            $rules['tipo_atribuicao'] = 'sometimes|required|in:SORTEIO,PERSONALIZADO';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome é obrigatório',
            'nome.max' => 'O nome não pode ter mais de 100 caracteres',
            'descricao.max' => 'A descrição não pode ter mais de 255 caracteres',
            'tipo_atribuicao.required' => 'O tipo de atribuição é obrigatório',
            'tipo_atribuicao.in' => 'O tipo de atribuição deve ser SORTEIO ou PERSONALIZADO'
        ];
    }

    protected function prepareForValidation(): void
    {
        // Define valores padrão para campos boolean se não forem enviados
        $defaults = [
            'is_ativo' => true,
            'pode_finalizar' => false,
            'pode_voltar' => false,
            'pode_redistribuir' => false,
            'forcar_troca_usuario' => false,
            'contar_prazo' => false,
            'priorizar_usuario_anterior' => false,
        ];

        foreach ($defaults as $field => $default) {
            if (!$this->has($field)) {
                $this->merge([$field => $default]);
            }
        }
    }
}
