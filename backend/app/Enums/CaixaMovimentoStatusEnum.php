<?php

namespace App\Enums;

enum CaixaMovimentoStatusEnum: string
{
    case ABERTO = 'ABERTO';
    case FECHADO = 'FECHADO';
    case CONFERIDO = 'CONFERIDO';
    case CANCELADO = 'CANCELADO';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
