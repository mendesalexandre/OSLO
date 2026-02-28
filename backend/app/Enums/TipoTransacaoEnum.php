<?php

namespace App\Enums;

enum TipoTransacaoEnum: string
{
    case ENTRADA = 'entrada';
    case SAIDA   = 'saida';
    case CAIXA   = 'caixa';

    public function label(): string
    {
        return match($this) {
            self::ENTRADA => 'Entrada',
            self::SAIDA   => 'Saída',
            self::CAIXA   => 'Caixa',
        };
    }

    public function cor(): string
    {
        return match($this) {
            self::ENTRADA => 'positive',
            self::SAIDA   => 'negative',
            self::CAIXA   => 'info',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
