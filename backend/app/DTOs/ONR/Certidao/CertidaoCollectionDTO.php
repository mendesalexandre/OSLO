<?php

namespace App\DTOs\ONR\Certidao;

/**
 * Collection DTO para múltiplas certidões
 */
class CertidaoCollectionDTO
{
    /**
     * @param CertidaoDTO[] $certidoes
     */
    public function __construct(
        public readonly array $certidoes
    ) {}

    public static function fromArray(array $data): self
    {
        $certidoes = array_map(
            fn(array $certidaoData) => CertidaoDTO::fromArray($certidaoData),
            $data['PEDIDO_CERTIDAO'] ?? []
        );

        return new self($certidoes);
    }

    public function count(): int
    {
        return count($this->certidoes);
    }

    public function isEmpty(): bool
    {
        return empty($this->certidoes);
    }

    /**
     * Filtra certidões por status
     */
    public function filterByStatus(string $status): array
    {
        return array_filter(
            $this->certidoes,
            fn(CertidaoDTO $certidao) => $certidao->statusSolicitacao === $status
        );
    }

    /**
     * Filtra certidões para processamento (Em aberto ou Processando)
     */
    public function getParaProcessamento(): array
    {
        return array_filter(
            $this->certidoes,
            fn(CertidaoDTO $certidao) => in_array($certidao->statusSolicitacao, ['Em aberto', 'Processando'])
        );
    }

    /**
     * Filtra apenas certidões de imóvel pedidas pela internet
     */
    public function getCertidoesImovelInternet(): array
    {
        return array_filter(
            $this->certidoes,
            fn(CertidaoDTO $certidao) =>
            $certidao->certidao->isCertidaoImovel() &&
                $certidao->certidao->isPedidoInternet()
        );
    }

    public function toArray(): array
    {
        return [
            'certidoes' => array_map(
                fn(CertidaoDTO $certidao) => $certidao->toArray(),
                $this->certidoes
            ),
            'total' => $this->count()
        ];
    }
}
