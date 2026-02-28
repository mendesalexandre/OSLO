<?php

namespace App\Services;

use App\Models\Protocolo;
use App\Models\ProtocoloIsencao;
use App\Models\ProtocoloPagamento;

class PagamentoService
{
    public function registrar(Protocolo $protocolo, array $dados): ProtocoloPagamento
    {
        $dados['usuario_id']    = auth()->id();
        $dados['data_pagamento'] = $dados['data_pagamento'] ?? now();

        $pagamento = $protocolo->pagamentos()->create($dados);

        $protocolo->recalcularValores();

        return $pagamento->load([
            'formaPagamento:id,nome',
            'meioPagamento:id,nome',
            'usuario:id,nome',
        ]);
    }

    public function estornar(ProtocoloPagamento $pagamento, string $motivo): void
    {
        $pagamento->update([
            'status'     => 'estornado',
            'observacao' => trim(($pagamento->observacao ?? '') . "\nEstorno: {$motivo}"),
        ]);

        $pagamento->protocolo->recalcularValores();
    }

    public function registrarIsencao(Protocolo $protocolo, array $dados): ProtocoloIsencao
    {
        $dados['usuario_id'] = auth()->id();

        $isencao = $protocolo->isencoes()->create($dados);

        $protocolo->recalcularValores();

        return $isencao->load('usuario:id,nome');
    }
}
