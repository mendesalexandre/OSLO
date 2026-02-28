<?php

namespace App\Enums;

enum SituacaoTransacaoEnum: string
{
    case PENDENTE   = 'pendente';
    case CONFIRMADA = 'confirmada';
    case LIQUIDADA  = 'liquidada';
    case CANCELADA  = 'cancelada';

    public function label(): string
    {
        return match($this) {
            self::PENDENTE   => 'Pendente',
            self::CONFIRMADA => 'Confirmada',
            self::LIQUIDADA  => 'Liquidada',
            self::CANCELADA  => 'Cancelada',
        };
    }

    public function classe(): string
    {
        return match($this) {
            self::PENDENTE   => 'warning',
            self::CONFIRMADA => 'positive',
            self::LIQUIDADA  => 'info',
            self::CANCELADA  => 'negative',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
