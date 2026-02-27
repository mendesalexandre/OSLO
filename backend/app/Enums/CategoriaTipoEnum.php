<?php

namespace App\Enums;

enum CategoriaTipoEnum: string
{
    case DESPESA = 'DESPESA';
    case RECEITA = 'RECEITA';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
