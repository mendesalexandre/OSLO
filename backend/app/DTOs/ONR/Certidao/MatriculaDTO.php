<?php

namespace App\DTOs\ONR\Certidao;

use App\Helpers\XmlHelper;

/**
 * DTO principal para dados de matrícula do ONR
 */
class MatriculaDTO
{
    public function __construct(
        public readonly string $numero,
        public readonly ?string $letra,
        public readonly string $matriculaMae,
        public readonly ?string $identificacaoAdicionalMatriculaMae,
        public readonly ?float $numeroAto,
    ) {}

    public static function fromArray(array $data): ?self
    {
        // Se array vazio, retorna null
        if (empty($data)) {
            return null;
        }

        // Se não tem NUMERO, retorna null
        if (!isset($data['NUMERO']) || empty($data['NUMERO'])) {
            return null;
        }

        return new self(
            numero: $data['NUMERO'],
            letra: XmlHelper::extractString($data['LETRA'] ?? null),
            matriculaMae: $data['MATRICULA_MAE'] ?? '0',
            identificacaoAdicionalMatriculaMae: XmlHelper::extractString($data['IDENTIFICACAO_ADICIONAL_MATRICULA_MAE'] ?? null),
            numeroAto: XmlHelper::extractNumber($data['NUMERO_ATO'] ?? null),
        );
    }

    public function toArray(): array
    {
        return [
            'numero' => $this->numero,
            'letra' => $this->letra,
            'matricula_mae' => $this->matriculaMae,
            'identificacao_adicional_matricula_mae' => $this->identificacaoAdicionalMatriculaMae,
            'numero_ato' => $this->numeroAto,
        ];
    }
}
