<?php

namespace App\Http\Requests;

use App\Enums\CategoriaTipo;
use App\Enums\CategoriaTipoEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CategoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'nome'             => 'required|string|max:100',
            'descricao'        => 'nullable|string|max:255',
            'tipo'             => ['required', new Enum(CategoriaTipoEnum::class)],
            'cor'              => 'nullable|string|max:7',
            'icone'            => 'nullable|string|max:50',
            'is_ativo'         => 'boolean',
            'categoria_pai_id' => 'nullable|exists:categoria,id',
        ];

        // No update, os campos podem ser opcionais
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['nome'] = 'sometimes|required|string|max:100';
            $rules['tipo'] = ['sometimes', 'required', new Enum(CategoriaTipoEnum::class)];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome é obrigatório',
            'nome.max' => 'O nome não pode ter mais de 100 caracteres',
            'descricao.max' => 'A descrição não pode ter mais de 255 caracteres',
            'tipo.required' => 'O tipo é obrigatório',
            'cor.max' => 'A cor deve ter no máximo 7 caracteres (ex: #FF0000)',
            'icone.max' => 'O ícone não pode ter mais de 50 caracteres',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Define valores padrão
        if (!$this->has('is_ativo')) {
            $this->merge(['is_ativo' => true]);
        }
    }
}
