<?php

namespace App\DTOs\ONR\Certidao\TiposCertidao;

use App\DTOs\ONR\Certidao\Base\BaseXmlDTO;
use App\DTOs\ONR\Certidao\CertidaoBaseDTO;
use App\DTOs\ONR\Certidao\PessoaDTO;
use App\Helpers\XmlHelper;

/**
 * DTO para certidão de pessoa (Tipo 9)
 */
class CertidaoPessoaDTO extends CertidaoBaseDTO
{

    public function __construct(
        string $tipo,
        string $pedidoPor,
        string $tipoFinalidade,
        public readonly ?PessoaDTO $pessoa,
        public readonly ?string $protocolo,
        public readonly ?array $pactoAntenupcial,
        public readonly ?array $convencao,
        public readonly ?array $livro3,
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
            pessoa: XmlHelper::extractFromXml($data['PESSOA']) ? PessoaDTO::fromArray($data['PESSOA']) : null,
            protocolo: XmlHelper::extractFromXml($data['PROTOCOLO']),
            pactoAntenupcial: XmlHelper::extractArray($data['PACTO_ANTENUPCIAL']),
            convencao: XmlHelper::extractArray($data['CONVENCAO']),
            livro3: XmlHelper::extractArray($data['LIVRO3']),
            observacao: XmlHelper::extractFromXml($data['OBSERVACAO'])
        );
    }

    public function toArray(): array
    {
        return [
            'tipo' => $this->tipo,
            'pedido_por' => $this->pedidoPor,
            'tipo_finalidade' => $this->tipoFinalidade,
            'pessoa' => $this->pessoa?->toArray(),
            'protocolo' => $this->protocolo,
            'pacto_antenupcial' => $this->pactoAntenupcial,
            'convencao' => $this->convencao,
            'livro3' => $this->livro3,
            'observacao' => $this->observacao,
        ];
    }

    public function getTipoDescricao(): string
    {
        return 'Certidão de Pessoa';
    }

    public function hasPessoa(): bool
    {
        return !is_null($this->pessoa);
    }
}
