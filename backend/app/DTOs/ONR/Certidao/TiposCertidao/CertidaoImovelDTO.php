<?php

namespace App\DTOs\ONR\Certidao\TiposCertidao;

use App\DTOs\ONR\Certidao\CertidaoBaseDTO;
use App\DTOs\ONR\Certidao\EnderecoDTO;
use App\Helpers\XmlHelper;

class CertidaoImovelDTO extends CertidaoBaseDTO
{

    public function __construct(
        string $tipo,
        string $pedidoPor,
        string $tipoFinalidade,
        public readonly ?string $matricula,
        public readonly ?EnderecoDTO $endereco,
        public readonly ?array $transcricao,
        public readonly ?string $protocolo,
        public readonly ?string $observacao
    ) {
        parent::__construct($tipo, $pedidoPor, $tipoFinalidade);
    }

    public static function fromArray(array $data): static
    {
        return new static(
            tipo: $data['TIPO'],
            pedidoPor: $data['PEDIDO_POR'],
            tipoFinalidade: $data['TIPOFINALIDADE'],
            matricula: XmlHelper::extractString($data['MATRICULA']),
            endereco: XmlHelper::extractFromXml($data['ENDERECO']) ? EnderecoDTO::fromArray($data['ENDERECO']) : null,
            transcricao: XmlHelper::extractArray($data['TRANSCRICAO']),
            protocolo: XmlHelper::extractString($data['PROTOCOLO']),
            observacao: XmlHelper::extractString($data['OBSERVACAO'])
        );
    }

    public function toArray(): array
    {
        return [
            'tipo' => $this->tipo,
            'pedido_por' => $this->pedidoPor,
            'tipo_finalidade' => $this->tipoFinalidade,
            'matricula' => $this->matricula,
            'endereco' => $this->endereco?->toArray(),
            'transcricao' => $this->transcricao,
            'protocolo' => $this->protocolo,
            'observacao' => $this->observacao,
        ];
    }

    public function getTipoDescricao(): string
    {
        return 'Certidão de Imóvel';
    }

    public function hasMatricula(): bool
    {
        return !empty($this->matricula);
    }
}
