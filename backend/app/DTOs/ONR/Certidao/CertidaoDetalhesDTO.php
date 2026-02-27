<?php

namespace App\DTOs\ONR\Certidao;

use App\DTOs\ONR\Certidao\PessoaDTO;
use App\DTOs\ONR\Certidao\EnderecoDTO;
use App\DTOs\ONR\Certidao\MatriculaDTO;
use App\Helpers\XmlHelper;

/**
 * DTO para detalhes da certidão - aceita qualquer tipo sem validação
 */
class CertidaoDetalhesDTO
{
    public function __construct(
        public readonly string $tipo,
        public readonly string $pedidoPor,
        public readonly string $tipoFinalidade,
        public readonly ?MatriculaDTO $matricula,
        public readonly ?EnderecoDTO $endereco,
        public readonly ?PessoaDTO $pessoa,
        public readonly ?string $protocolo,
        public readonly ?string $observacao,
        public readonly ?array $transcricao,
        public readonly ?array $pactoAntenupcial,
        public readonly ?array $convencao,
        public readonly ?array $livro3
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            tipo: $data['TIPO'],
            pedidoPor: $data['PEDIDO_POR'],
            tipoFinalidade: $data['TIPOFINALIDADE'],
            matricula: XmlHelper::extractFromXml($data['MATRICULA']) ? MatriculaDTO::fromArray($data['MATRICULA']) : null,
            endereco: XmlHelper::extractFromXml($data['ENDERECO']) ? EnderecoDTO::fromArray($data['ENDERECO']) : null,
            pessoa: XmlHelper::extractFromXml($data['PESSOA']) ? PessoaDTO::fromArray($data['PESSOA']) : null,
            protocolo: XmlHelper::extractString($data['PROTOCOLO']),
            observacao: XmlHelper::extractString($data['OBSERVACAO']),
            transcricao: XmlHelper::extractArray($data['TRANSCRICAO']),
            pactoAntenupcial: XmlHelper::extractArray($data['PACTO_ANTENUPCIAL']),
            convencao: XmlHelper::extractArray($data['CONVENCAO']),
            livro3: XmlHelper::extractArray($data['LIVRO3'])
        );
    }

    public function toArray(): array
    {
        return [
            'tipo' => $this->tipo,
            'pedido_por' => $this->pedidoPor,
            'tipo_finalidade' => $this->tipoFinalidade,
            'matricula' => $this->matricula?->toArray(),
            'endereco' => $this->endereco?->toArray(),
            'pessoa' => $this->pessoa?->toArray(),
            'protocolo' => $this->protocolo,
            'observacao' => $this->observacao,
            'transcricao' => $this->transcricao,
            'pacto_antenupcial' => $this->pactoAntenupcial,
            'convencao' => $this->convencao,
            'livro3' => $this->livro3,
        ];
    }

    /**
     * Retorna descrição do tipo de certidão
     */
    public function getTipoDescricao(): string
    {
        return match ($this->tipo) {
            '1' => 'Certidão de Protocolo',
            '2' => 'Certidão de Transcrição',
            '3' => 'Certidão de Imóvel',
            '9' => 'Certidão de Pessoa',
            '10' => 'Certidão de Penhor',
            default => "Certidão Tipo {$this->tipo}"
        };
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

    /**
     * Verificações úteis
     */
    public function isCertidaoImovel(): bool
    {
        return $this->tipo === '3';
    }

    public function isCertidaoPessoa(): bool
    {
        return $this->tipo === '9';
    }

    public function isCertidaoPenhor(): bool
    {
        return $this->tipo === '10';
    }

    public function isPedidoPorMatricula(): bool
    {
        return $this->pedidoPor === '4';
    }

    public function hasPessoa(): bool
    {
        return !is_null($this->pessoa);
    }

    public function hasMatricula(): bool
    {
        return !is_null($this->matricula);
    }

    /**
     * Retorna o número da matrícula como string
     */
    public function getNumeroMatricula(): ?string
    {
        return $this->matricula?->numero;
    }

    public function hasEndereco(): bool
    {
        return !is_null($this->endereco);
    }
}
