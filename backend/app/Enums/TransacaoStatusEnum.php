<?php

namespace App\Enums;

enum TransacaoStatusEnum: string
{
    case PENDENTE = 'PENDENTE';
    case PAGO = 'PAGO';
    case CANCELADO = 'CANCELADO';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
