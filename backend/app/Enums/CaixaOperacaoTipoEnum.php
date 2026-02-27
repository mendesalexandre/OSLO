<?php

namespace App\Enums;

enum CaixaOperacaoTipoEnum: string
{
    case SANGRIA = 'SANGRIA';
    case REFORCO = 'REFORCO';
    case TRANSFERENCIA = 'TRANSFERENCIA';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::SANGRIA => '💸 Sangria',
            self::REFORCO => '💰 Reforço',
            self::TRANSFERENCIA => '🔄 Transferência',
        };
    }

    public function descricao(): string
    {
        return match ($this) {
            self::SANGRIA => 'Retirada de dinheiro do caixa',
            self::REFORCO => 'Adição de dinheiro no caixa',
            self::TRANSFERENCIA => 'Transferência entre caixas',
        };
    }

    public function icone(): string
    {
        return match ($this) {
            self::SANGRIA => 'remove_circle',
            self::REFORCO => 'add_circle',
            self::TRANSFERENCIA => 'swap_horiz',
        };
    }

    public function cor(): string
    {
        return match ($this) {
            self::SANGRIA => 'negative',
            self::REFORCO => 'positive',
            self::TRANSFERENCIA => 'primary',
        };
    }
}
