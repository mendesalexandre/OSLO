<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Transacao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transacao';

    const CREATED_AT = 'data_cadastro';
    const UPDATED_AT = 'data_alteracao';
    const DELETED_AT = 'data_exclusao';

    protected $fillable = [
        'indicador_pessoal_id',
        'tipo_transacao_id',
        'motivo_transacao_id',
        'banco_id',
        'numero_transacao',
        'referencia',
        'descricao',
        'valor',
        'moeda',
        'data_transacao',
        'data_efetivacao',
        'data_confirmacao',
        'agencia',
        'conta',
        'tipo_conta',
        'documento_numero',
        'beneficiario',
        'situacao',
        'observacoes',
        'is_ativo',
    ];

    protected function casts(): array
    {
        return [
            'valor'             => 'decimal:2',
            'data_transacao'    => 'date',
            'data_efetivacao'   => 'date',
            'data_confirmacao'  => 'datetime',
            'is_ativo'          => 'boolean',
        ];
    }

    // Relacionamentos

    public function indicadorPessoal(): BelongsTo
    {
        return $this->belongsTo(IndicadorPessoal::class, 'indicador_pessoal_id');
    }

    public function tipoTransacao(): BelongsTo
    {
        return $this->belongsTo(TipoTransacao::class, 'tipo_transacao_id');
    }

    public function motivoTransacao(): BelongsTo
    {
        return $this->belongsTo(MotivoTransacao::class, 'motivo_transacao_id');
    }

    public function banco(): BelongsTo
    {
        return $this->belongsTo(Banco::class, 'banco_id');
    }

    public function auditorias(): HasMany
    {
        return $this->hasMany(AuditoriaTransacao::class, 'transacao_id')->orderByDesc('created_at');
    }

    // Scopes

    public function scopeAtivo(Builder $query): Builder
    {
        return $query->where('is_ativo', true);
    }

    public function scopePorIndicador(Builder $query, int $indicadorId): Builder
    {
        return $query->where('indicador_pessoal_id', $indicadorId);
    }

    public function scopePorTipo(Builder $query, string $tipo): Builder
    {
        return $query->whereHas('tipoTransacao', fn ($q) => $q->where('tipo', $tipo));
    }

    public function scopeEntrePeriodo(Builder $query, string $inicio, string $fim): Builder
    {
        return $query->whereBetween('data_transacao', [$inicio, $fim]);
    }

    public function scopePorSituacao(Builder $query, string $situacao): Builder
    {
        return $query->where('situacao', $situacao);
    }

    public function scopeEntrada(Builder $query): Builder
    {
        return $this->scopePorTipo($query, 'entrada');
    }

    public function scopeSaida(Builder $query): Builder
    {
        return $this->scopePorTipo($query, 'saida');
    }

    // Métodos de negócio

    public function confirmar(): void
    {
        $this->update([
            'situacao'          => 'confirmada',
            'data_confirmacao'  => now(),
        ]);
        $this->registrarAuditoria('confirmacao', null, 'confirmada', 'situacao');
    }

    public function cancelar(string $motivo = ''): void
    {
        $anterior = $this->situacao;
        $this->update(['situacao' => 'cancelada']);
        $this->registrarAuditoria('cancelamento', $anterior, 'cancelada', 'situacao', $motivo);
    }

    public function registrarAuditoria(string $acao, ?string $valorAnterior, ?string $valorNovo, ?string $campo = null, string $observacao = ''): void
    {
        $usuario = Auth::guard('web')->user();

        $this->auditorias()->create([
            'usuario_id'     => $usuario?->id,
            'campo_alterado' => $campo,
            'valor_anterior' => $valorAnterior,
            'valor_novo'     => $valorNovo,
            'acao'           => $acao,
            'observacao'     => $observacao ?: null,
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
        ]);
    }

    // Hooks do modelo

    protected static function booted(): void
    {
        static::created(function (self $transacao) {
            $transacao->registrarAuditoria('criacao', null, $transacao->numero_transacao);
        });

        static::updated(function (self $transacao) {
            $dirty = $transacao->getDirty();
            $original = $transacao->getOriginal();

            $ignorar = ['data_alteracao', 'data_confirmacao'];

            foreach ($dirty as $campo => $novoValor) {
                if (in_array($campo, $ignorar)) {
                    continue;
                }
                $transacao->registrarAuditoria(
                    'atualizacao',
                    (string) ($original[$campo] ?? ''),
                    (string) $novoValor,
                    $campo
                );
            }
        });

        static::deleted(function (self $transacao) {
            $transacao->registrarAuditoria('exclusao', $transacao->situacao, null);
        });
    }
}
