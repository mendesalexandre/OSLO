<?php

namespace App\DTOs\ONR\Certidao;

abstract class CertidaoBaseDTO
{
    public function __construct(
        public readonly string $tipo,
        public readonly string $pedidoPor,
        public readonly string $tipoFinalidade
    ) {}

    abstract public static function fromArray(array $data): static;
    abstract public function toArray(): array;
    abstract public function getTipoDescricao(): string;

    /**
     * Verifica se o pedido foi feito por matrícula
     */
    public function isPedidoPorMatricula(): bool
    {
        return $this->pedidoPor === '4';
    }

    /**
     * Retorna o tipo de critério usado no pedido
     */
    public function getTipoPedido(): string
    {
        return match ($this->pedidoPor) {
            '1' => 'Nome da Pessoa',
            '2' => 'Lote, Quadra e Bairro',
            '3' => 'Endereço',
            '4' => 'Número da Matrícula',
            '5' => 'CPF/CNPJ',
            '6' => 'Outros Critérios',
            default => 'Critério Desconhecido'
        };
    }
}
