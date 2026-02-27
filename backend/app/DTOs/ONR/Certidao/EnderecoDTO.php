<?php

namespace App\DTOs\ONR\Certidao;

use App\DTOs\ONR\Certidao\Base\BaseXmlDTO;

/**
 * DTO para endereço
 */
class EnderecoDTO extends BaseXmlDTO
{
    public function __construct(
        public readonly string $cep,
        public readonly string $tipoLogradouro,
        public readonly string $logradouro,
        public readonly string $numero,
        public readonly ?string $complemento,
        public readonly string $bairro,
        public readonly string $cidade,
        public readonly string $uf
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            cep: $data['CEP'],
            tipoLogradouro: $data['TIPO_LOGRADOURO'],
            logradouro: $data['LOGRADOURO'],
            numero: $data['NUMERO'],
            complemento: self::extractString($data['COMPLEMENTO']),
            bairro: $data['BAIRRO'],
            cidade: $data['CIDADE'],
            uf: $data['UF']
        );
    }

    public function toArray(): array
    {
        return [
            'cep' => $this->cep,
            'tipo_logradouro' => $this->tipoLogradouro,
            'logradouro' => $this->logradouro,
            'numero' => $this->numero,
            'complemento' => $this->complemento,
            'bairro' => $this->bairro,
            'cidade' => $this->cidade,
            'uf' => $this->uf,
        ];
    }

    public function getEnderecoCompleto(): string
    {
        $endereco = "{$this->tipoLogradouro} {$this->logradouro}, {$this->numero}";

        if ($this->complemento) {
            $endereco .= " - {$this->complemento}";
        }

        return "{$endereco}, {$this->bairro}, {$this->cidade}/{$this->uf} - CEP: {$this->cep}";
    }
}
