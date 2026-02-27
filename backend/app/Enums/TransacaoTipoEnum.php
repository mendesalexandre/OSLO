<?php

namespace App\Enums;

enum TransacaoTipoEnum: string
{
    case ENTRADA = 'ENTRADA';
    case SAIDA = 'SAIDA';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
