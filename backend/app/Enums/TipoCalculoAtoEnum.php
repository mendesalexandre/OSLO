<?php
// app/Enums/TipoCalculoAto.php

namespace App\Enums;

enum TipoCalculoAtoEnum: string
{
    case FIXO = 'FIXO';
    case FAIXA_PROGRESSIVA = 'FAIXA_PROGRESSIVA';
    case GRATUITO = 'GRATUITO';
    // Futuro: case POR_PAGINA = 'POR_PAGINA';
    // Futuro: case POR_AREA = 'POR_AREA';

    public function label(): string
    {
        return match ($this) {
            self::FIXO => 'Valor Fixo',
            self::FAIXA_PROGRESSIVA => 'Faixa Progressiva',
            self::GRATUITO => 'Gratuito',
        };
    }
}
