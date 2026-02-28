<?php

namespace App\Services;

use App\Models\Ato;
use App\Models\Protocolo;
use App\Models\ProtocoloItem;
use Illuminate\Support\Facades\DB;

class ProtocoloService
{
    public function criar(array $dados): Protocolo
    {
        return DB::transaction(function () use ($dados) {
            $itens = $dados['itens'] ?? [];
            unset($dados['itens']);

            $dados['numero']      = Protocolo::gerarNumero();
            $dados['ano']         = now()->year;
            $dados['atendente_id'] = auth()->id();

            $protocolo = Protocolo::create($dados);

            foreach ($itens as $item) {
                $this->adicionarItem($protocolo, $item);
            }

            $protocolo->recalcularValores();

            return $protocolo->fresh()->load(['itens.ato', 'atendente:id,nome']);
        });
    }

    public function adicionarItem(Protocolo $protocolo, array $dados): ProtocoloItem
    {
        $ato = Ato::findOrFail($dados['ato_id']);

        $quantidade    = $dados['quantidade'] ?? 1;
        $baseCalculo   = $dados['base_calculo'] ?? null;
        $valorUnitario = $dados['valor_unitario'] ?? $this->calcularValorItem($ato, $baseCalculo, $quantidade);

        $item = $protocolo->itens()->create([
            'ato_id'         => $ato->id,
            'descricao'      => $dados['descricao'] ?? $ato->nome,
            'quantidade'     => $quantidade,
            'base_calculo'   => $baseCalculo,
            'valor_unitario' => $valorUnitario,
            'valor_total'    => $valorUnitario * $quantidade,
            'observacao'     => $dados['observacao'] ?? null,
        ]);

        $protocolo->recalcularValores();

        return $item;
    }

    public function removerItem(ProtocoloItem $item): void
    {
        $protocolo = $item->protocolo;
        $item->delete();
        $protocolo->recalcularValores();
    }

    public function calcularValorItem(Ato $ato, ?float $baseCalculo, int $quantidade): float
    {
        return $ato->calcularValor($baseCalculo);
    }

    public function cancelar(Protocolo $protocolo, string $motivo): void
    {
        $protocolo->update([
            'status'              => 'cancelado',
            'motivo_cancelamento' => $motivo,
        ]);
    }
}
