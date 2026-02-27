<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class IndicadorPessoal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'indicador_pessoal';

    const CREATED_AT = 'data_cadastro';
    const UPDATED_AT = 'data_alteracao';
    const DELETED_AT = 'data_exclusao';

    protected $fillable = [
        'cpf_cnpj', 'versao', 'is_atual', 'motivo_versao', 'data_versao',
        'tipo_pessoa', 'ficha', 'nome', 'nome_fantasia',
        'rg', 'orgao_expedidor', 'data_expedicao_rg', 'data_nascimento', 'data_obito',
        'sexo', 'nome_pai', 'nome_mae',
        'estado_civil_id', 'regime_bem_id', 'data_casamento', 'anterior_lei_6515', 'conjuge_id',
        'capacidade_civil_id', 'representante_legal',
        'nacionalidade_id', 'naturalidade', 'profissao_id',
        'data_abertura', 'data_encerramento', 'sede', 'objeto_social',
        'tipo_empresa_id', 'porte_empresa_id', 'inscricao_estadual', 'inscricao_municipal',
        'pessoa_politicamente_exposta', 'servidor_publico', 'cargo_funcao', 'orgao_entidade',
        'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'uf', 'pais',
        'observacoes', 'is_ativo',
    ];

    protected function casts(): array
    {
        return [
            'is_atual'                    => 'boolean',
            'is_ativo'                    => 'boolean',
            'anterior_lei_6515'           => 'boolean',
            'pessoa_politicamente_exposta' => 'boolean',
            'servidor_publico'            => 'boolean',
            'data_expedicao_rg'           => 'date',
            'data_nascimento'             => 'date',
            'data_obito'                  => 'date',
            'data_casamento'              => 'date',
            'data_abertura'               => 'date',
            'data_encerramento'           => 'date',
            'data_versao'                 => 'datetime',
            'versao'                      => 'integer',
        ];
    }

    // Scopes

    public function scopeAtual(Builder $query): Builder
    {
        return $query->where('is_atual', true);
    }

    public function scopeAtivo(Builder $query): Builder
    {
        return $query->where('is_ativo', true);
    }

    // Relacionamentos

    public function estadoCivil(): BelongsTo
    {
        return $this->belongsTo(EstadoCivil::class, 'estado_civil_id');
    }

    public function regimeBem(): BelongsTo
    {
        return $this->belongsTo(RegimeBem::class, 'regime_bem_id');
    }

    public function conjuge(): BelongsTo
    {
        return $this->belongsTo(static::class, 'conjuge_id');
    }

    public function capacidadeCivil(): BelongsTo
    {
        return $this->belongsTo(CapacidadeCivil::class, 'capacidade_civil_id');
    }

    public function nacionalidade(): BelongsTo
    {
        return $this->belongsTo(Nacionalidade::class, 'nacionalidade_id');
    }

    public function profissao(): BelongsTo
    {
        return $this->belongsTo(Profissao::class, 'profissao_id');
    }

    public function tipoEmpresa(): BelongsTo
    {
        return $this->belongsTo(TipoEmpresa::class, 'tipo_empresa_id');
    }

    public function porteEmpresa(): BelongsTo
    {
        return $this->belongsTo(PorteEmpresa::class, 'porte_empresa_id');
    }

    public function socios(): HasMany
    {
        return $this->hasMany(IndicadorPessoalSocio::class, 'indicador_pessoal_id');
    }

    public function versoes(): HasMany
    {
        return $this->hasMany(static::class, 'cpf_cnpj', 'cpf_cnpj')
                    ->orderByDesc('versao');
    }

    // Cria uma nova versão desativando a atual

    public function criarNovaVersao(array $dados, string $motivo): static
    {
        return DB::transaction(function () use ($dados, $motivo) {
            $this->update(['is_atual' => false]);

            return static::create(array_merge($dados, [
                'cpf_cnpj'    => $this->cpf_cnpj,
                'versao'      => $this->versao + 1,
                'is_atual'    => true,
                'motivo_versao' => $motivo,
                'data_versao' => now(),
            ]));
        });
    }
}
