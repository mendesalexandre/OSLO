<?php

namespace App\Enums;

enum NivelDificuldadeEnum: int
{
    case MUITO_LEVE = 1;
    case LEVE = 2;
    case MODERADO = 3;
    case EXIGENTE = 4;
    case MUITO_EXIGENTE = 5;
    case DESAFIADOR = 6;
    case MUITO_DESAFIADOR = 7;

    /**
     * Retorna o nome formatado do nível
     */
    public function label(): string
    {
        return match ($this) {
            self::MUITO_LEVE => '1 - Muito Leve',
            self::LEVE => '2 - Leve',
            self::MODERADO => '3 - Moderado',
            self::EXIGENTE => '4 - Exigente',
            self::MUITO_EXIGENTE => '5 - Muito exigente',
            self::DESAFIADOR => '6 - Desafiador',
            self::MUITO_DESAFIADOR => '7 - Muito desafiador',
        };
    }

    /**
     * Retorna array com todos os níveis para select/dropdown
     */
    public static function options(): array
    {
        return array_column(
            array_map(fn($case) => [
                'value' => $case->value,
                'label' => $case->label()
            ], self::cases()),
            'label',
            'value'
        );
    }

    /**
     * Retorna array simples valor => label
     */
    public static function toArray(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[$case->value] = $case->label();
        }
        return $result;
    }

    /**
     * Criar enum a partir de valor
     */
    public static function fromValue(int $value): ?self
    {
        return self::tryFrom($value);
    }

    /**
     * Verificar se é um nível alto (5+)
     */
    public function isHigh(): bool
    {
        return $this->value >= 5;
    }

    /**
     * Verificar se é um nível baixo (1-2)
     */
    public function isLow(): bool
    {
        return $this->value <= 2;
    }

    /**
     * Verificar se é um nível médio (3-4)
     */
    public function isMedium(): bool
    {
        return $this->value >= 3 && $this->value <= 4;
    }
}
