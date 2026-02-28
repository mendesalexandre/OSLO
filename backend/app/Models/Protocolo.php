<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Protocolo extends Model
{
    use SoftDeletes;

    protected $table = 'protocolo';

    const CREATED_AT = 'data_cadastro';
    const UPDATED_AT = 'data_alteracao';
    const DELETED_AT = 'data_exclusao';

    protected $fillable = [
        'numero',
        'ano',
        'atendente_id',
        'natureza_id',
        'solicitante_nome',
        'solicitante_cpf_cnpj',
        'solicitante_telefone',
        'solicitante_email',
        'tipo',
        'matricula',
        'meio_solicitacao_id',
        'estado_id',
        'observacao',
        'motivo_cancelamento',
        'valor_total',
        'valor_desconto',
        'valor_isento',
        'valor_final',
        'valor_pago',
        'status',
        'is_ativo',
    ];

    protected $casts = [
        'ano'             => 'integer',
        'valor_total'     => 'decimal:2',
        'valor_desconto'  => 'decimal:2',
        'valor_isento'    => 'decimal:2',
        'valor_final'     => 'decimal:2',
        'valor_pago'      => 'decimal:2',
        'is_ativo'        => 'boolean',
        'data_cadastro'   => 'datetime',
        'data_alteracao'  => 'datetime',
        'data_exclusao'   => 'datetime',
    ];

    // Relacionamentos

    public function atendente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atendente_id');
    }

    public function natureza(): BelongsTo
    {
        return $this->belongsTo(Natureza::class);
    }

    public function itens(): HasMany
    {
        return $this->hasMany(ProtocoloItem::class);
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(ProtocoloPagamento::class);
    }

    public function isencoes(): HasMany
    {
        return $this->hasMany(ProtocoloIsencao::class);
    }

    // Métodos de negócio

    public function recalcularValores(): void
    {
        $this->valor_total  = $this->itens()->sum('valor_total');
        $this->valor_isento = $this->isencoes()->sum('valor_isento');
        $this->valor_pago   = $this->pagamentos()->confirmados()->sum('valor');
        $this->valor_final  = max(0, $this->valor_total - $this->valor_desconto - $this->valor_isento);
        $this->save();

        $this->atualizarStatus();
    }

    public function estaPago(): bool
    {
        return $this->valor_pago >= $this->valor_final && $this->valor_final > 0;
    }

    public function temPagamentoParcial(): bool
    {
        return $this->valor_pago > 0 && $this->valor_pago < $this->valor_final;
    }

    public function eIsento(): bool
    {
        return $this->valor_isento >= $this->valor_total && $this->valor_total > 0;
    }

    public function valorRestante(): float
    {
        return max(0, $this->valor_final - $this->valor_pago);
    }

    public function atualizarStatus(): void
    {
        if ($this->status === 'cancelado') {
            return;
        }

        if ($this->eIsento()) {
            $this->status = 'isento';
        } elseif ($this->estaPago()) {
            $this->status = 'pago';
        } elseif ($this->temPagamentoParcial()) {
            $this->status = 'pago_parcial';
        } else {
            $this->status = 'aberto';
        }

        $this->saveQuietly();
    }

    public static function gerarNumero(): string
    {
        $ano = now()->year;

        $ultimoNumero = DB::table('protocolo')
            ->where('ano', $ano)
            ->max(DB::raw("CAST(SPLIT_PART(numero, '/', 2) AS INTEGER)"));

        $sequencial = ($ultimoNumero ?? 0) + 1;

        return sprintf('%d/%06d', $ano, $sequencial);
    }

    public function gerarAndamentos(): array
    {
        $andamentos = [];

        $andamentos[] = [
            'descricao'    => 'Protocolo criado',
            'usuario'      => $this->atendente
                ? ['id' => $this->atendente->id, 'nome' => $this->atendente->nome]
                : null,
            'data_cadastro' => $this->data_cadastro,
        ];

        if ($this->itens()->count() > 0) {
            $primeiroItem = $this->itens()->oldest('data_cadastro')->first();
            $totalItens   = $this->itens()->count();
            $andamentos[] = [
                'descricao'    => $totalItens === 1 ? '1 ato adicionado' : "{$totalItens} atos adicionados",
                'usuario'      => $this->atendente
                    ? ['id' => $this->atendente->id, 'nome' => $this->atendente->nome]
                    : null,
                'data_cadastro' => $primeiroItem->data_cadastro,
            ];
        }

        foreach ($this->pagamentos()->with('usuario')->orderBy('data_pagamento')->get() as $pagamento) {
            $andamentos[] = [
                'descricao'    => 'Pagamento ' . ($pagamento->status === 'confirmado' ? 'confirmado' : $pagamento->status)
                    . ' - R$ ' . number_format($pagamento->valor, 2, ',', '.'),
                'usuario'      => $pagamento->usuario
                    ? ['id' => $pagamento->usuario->id, 'nome' => $pagamento->usuario->nome]
                    : null,
                'data_cadastro' => $pagamento->data_pagamento ?? $pagamento->data_cadastro,
            ];
        }

        if ($this->status === 'cancelado') {
            $andamentos[] = [
                'descricao'    => 'Protocolo cancelado'
                    . ($this->motivo_cancelamento ? ': ' . $this->motivo_cancelamento : ''),
                'usuario'      => null,
                'data_cadastro' => $this->data_alteracao,
            ];
        }

        usort($andamentos, fn ($a, $b) => $a['data_cadastro'] <=> $b['data_cadastro']);

        return $andamentos;
    }
}
