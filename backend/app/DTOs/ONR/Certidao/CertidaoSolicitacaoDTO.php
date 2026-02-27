<?php

namespace App\DTOs\ONR\Certidao;

use App\DTOs\ONR\Certidao\SolicitanteDTO;
use App\DTOs\ONR\Certidao\CertidaoDetalhesDTO;
use App\Helpers\XmlHelper;

/**
 * DTO principal para solicitação de certidão
 */
class CertidaoSolicitacaoDTO
{
    public function __construct(
        public readonly string $protocoloSolicitacao,
        public readonly string $dataPedido,
        public readonly string $statusSolicitacao,
        public readonly string $tipoCobranca,
        public readonly string $valor,
        public readonly SolicitanteDTO $solicitante,
        public readonly CertidaoDetalhesDTO $certidao
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            protocoloSolicitacao: $data['PROTOCOLO_SOLICITACAO'],
            dataPedido: $data['DATA_PEDIDO'],
            statusSolicitacao: $data['STATUS_SOLICITACAO'],
            tipoCobranca: $data['TIPO_COBRANCA'],
            valor: $data['VALOR'],
            solicitante: SolicitanteDTO::fromArray($data['SOLICITANTE']),
            certidao: CertidaoDetalhesDTO::fromArray($data['CERTIDAO'])
        );
    }

    public function toArray(): array
    {
        return [
            'protocolo_solicitacao' => $this->protocoloSolicitacao,
            'data_pedido' => $this->dataPedido,
            'status_solicitacao' => $this->statusSolicitacao,
            'tipo_cobranca' => $this->tipoCobranca,
            'valor' => $this->valor,
            'solicitante' => $this->solicitante->toArray(),
            'certidao' => $this->certidao->toArray(),
        ];
    }

    public function getValorNumerico(): float
    {
        return XmlHelper::extractNumber($this->valor) ?? 0.0;
    }

    public function getDataPedidoFormatada(): ?string
    {
        return XmlHelper::extractDate($this->dataPedido, 'd/m/Y H:i:s');
    }

    public function getTipoCertidao(): string
    {
        return $this->certidao->getTipoDescricao();
    }

    public function getSolicitanteInfo(): string
    {
        return "{$this->solicitante->nome} ({$this->solicitante->cpfCnpj})";
    }
}
