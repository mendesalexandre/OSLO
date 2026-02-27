<?php

namespace App\DTOs\ONR\Certidao;

use App\DTOs\ONR\Certidao\Base\BaseXmlDTO;

/**
 * DTO para dados do solicitante
 */
class SolicitanteDTO extends BaseXmlDTO
{
    public function __construct(
        public readonly string $nome,
        public readonly ?string $telefone,
        public readonly ?string $email,
        public readonly string $cpfCnpj,
        public readonly ?string $inscricaoMunicipal,
        public readonly bool $isentoCcm,
        public readonly ?string $numeroCcm,
        public readonly EnderecoDTO $endereco
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            nome: $data['NOME'],
            telefone: self::extractString($data['TELEFONE']),
            email: self::extractString($data['EMAIL']),
            cpfCnpj: $data['CPFCNPJ'],
            inscricaoMunicipal: self::extractString($data['INSCRICAO_MUNICIPAL']),
            isentoCcm: $data['ISENTO_CCM'] === '1',
            numeroCcm: self::extractString($data['NUMERO_CCM']),
            endereco: EnderecoDTO::fromArray($data['ENDERECO'])
        );
    }

    public function toArray(): array
    {
        return [
            'nome' => $this->nome,
            'telefone' => $this->telefone,
            'email' => $this->email,
            'cpf_cnpj' => $this->cpfCnpj,
            'inscricao_municipal' => $this->inscricaoMunicipal,
            'isento_ccm' => $this->isentoCcm,
            'numero_ccm' => $this->numeroCcm,
            'endereco' => $this->endereco->toArray(),
        ];
    }
}
