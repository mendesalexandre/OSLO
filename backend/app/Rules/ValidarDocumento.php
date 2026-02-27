<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidarDocumento implements ValidationRule
{
    public function __construct(private readonly string $tipoPessoa = 'F') {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $doc = preg_replace('/\D/', '', (string) $value);

        if ($this->tipoPessoa === 'F') {
            if (!$this->validarCpf($doc)) {
                $fail('O CPF informado é inválido.');
            }
        } else {
            if (!$this->validarCnpj($doc)) {
                $fail('O CNPJ informado é inválido.');
            }
        }
    }

    private function validarCpf(string $cpf): bool
    {
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $sum = 0;
            for ($i = 0; $i < $t; $i++) {
                $sum += (int) $cpf[$i] * ($t + 1 - $i);
            }
            $remainder = ($sum * 10) % 11;
            if ($remainder >= 10) {
                $remainder = 0;
            }
            if ($remainder !== (int) $cpf[$t]) {
                return false;
            }
        }

        return true;
    }

    private function validarCnpj(string $cnpj): bool
    {
        if (strlen($cnpj) !== 14 || preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $cnpj[$i] * $weights1[$i];
        }
        $remainder = $sum % 11;
        $digit1    = $remainder < 2 ? 0 : 11 - $remainder;
        if ($digit1 !== (int) $cnpj[12]) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += (int) $cnpj[$i] * $weights2[$i];
        }
        $remainder = $sum % 11;
        $digit2    = $remainder < 2 ? 0 : 11 - $remainder;

        return $digit2 === (int) $cnpj[13];
    }
}
