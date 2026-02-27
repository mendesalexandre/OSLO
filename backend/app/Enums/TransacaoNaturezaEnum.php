<?php

namespace App\Enums;

enum TransacaoNaturezaEnum: string
{
    case CONTA_PAGAR = 'CONTA_PAGAR';
    case CONTA_RECEBER = 'CONTA_RECEBER';
    case TRANSFERENCIA = 'TRANSFERENCIA';
    case AJUSTE = 'AJUSTE';
    case SANGRIA = 'SANGRIA';
    case REFORCO = 'REFORCO';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
