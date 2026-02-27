<?php

namespace App\DTOs\ONR\Certidao;

use App\DTOs\ONR\Certidao\Base\BaseXmlDTO;

/**
 * DTO para dados da pessoa na certidão
 */
class PessoaDTO extends BaseXmlDTO

{
    public function __construct(
        public readonly string $nome,
        public readonly string $cpfCnpj,
        public readonly ?string $rgIe
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            nome: trim($data['NOME']),
            cpfCnpj: $data['CPFCNPJ'],
            rgIe: self::extractString($data['RG_IE'])
        );
    }

    public function toArray(): array
    {
        return [
            'nome' => $this->nome,
            'cpf_cnpj' => $this->cpfCnpj,
            'rg_ie' => $this->rgIe,
        ];
    }

    public function isCpf(): bool
    {
        return strlen(preg_replace('/\D/', '', $this->cpfCnpj)) === 11;
    }

    public function isCnpj(): bool
    {
        return strlen(preg_replace('/\D/', '', $this->cpfCnpj)) === 14;
    }
}
