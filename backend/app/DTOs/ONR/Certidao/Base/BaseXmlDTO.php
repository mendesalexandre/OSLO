<?php

namespace App\DTOs\ONR\Certidao\Base;

/**
 * DTO base para tratamento de dados XML
 */
abstract class BaseXmlDTO
{
    /**
     * Extrai valor de campo XML que pode vir como array vazio [] ou com dados
     */
    protected static function extractFromXml($value): mixed
    {
        if (is_array($value) && empty($value)) {
            return null;
        }

        if (is_string($value) && trim($value) === '') {
            return null;
        }

        return $value;
    }

    /**
     * Extrai string de campo XML, tratando arrays
     */
    protected static function extractString($value): ?string
    {
        $extracted = self::extractFromXml($value);

        if (is_array($extracted)) {
            return isset($extracted[0]) && $extracted[0] !== '' ? (string) $extracted[0] : null;
        }

        return is_string($extracted) ? trim($extracted) : null;
    }

    /**
     * Extrai array de campo XML
     */
    protected static function extractArray($value): ?array
    {
        $extracted = self::extractFromXml($value);
        return is_array($extracted) && !empty($extracted) ? $extracted : null;
    }
}
