<?php

namespace App\DTOs\ONR\Certidao\TiposCertidao;

use App\DTOs\ONR\Certidao\CertidaoBaseDTO;

/**
 * Factory para criar DTOs específicos de certidão baseado no tipo
 */
class CertidaoFactoryDTO
{
    /**
     * Cria o DTO específico baseado no tipo da certidão
     */
    public static function create(array $data): CertidaoBaseDTO
    {
        return match ($data['TIPO']) {
            '3' => CertidaoImovelDTO::fromArray($data),
            '9' => CertidaoPessoaDTO::fromArray($data),
            // Adicione outros tipos conforme necessário:
            // '1' => CertidaoProtocoloDTO::fromArray($data),
            // '2' => CertidaoTranscricaoDTO::fromArray($data),
            default => throw new \InvalidArgumentException("Tipo de certidão '{$data['TIPO']}' não suportado")
        };
    }

    /**
     * Lista todos os tipos de certidão suportados
     */
    public static function getTiposSuportados(): array
    {
        return [
            '3' => 'Certidão de Imóvel',
            '9' => 'Certidão de Pessoa',
            // Adicione aqui conforme implementar novos tipos
        ];
    }

    /**
     * Verifica se um tipo é suportado
     */
    public static function isTipoSuportado(string $tipo): bool
    {
        return array_key_exists($tipo, self::getTiposSuportados());
    }

    /**
     * Retorna a descrição de um tipo
     */
    public static function getDescricaoTipo(string $tipo): string
    {
        return self::getTiposSuportados()[$tipo] ?? 'Tipo Desconhecido';
    }
}
