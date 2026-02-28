<?php

namespace Database\Seeders;

use App\Models\Ato;
use App\Models\FormaPagamento;
use App\Models\Natureza;
use App\Models\Protocolo;
use App\Models\ProtocoloItem;
use App\Models\ProtocoloPagamento;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProtocoloSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedNaturezas();
            $this->seedAtos();
            $this->seedFormasPagamento();
            $this->seedProtocolos();
        });
    }

    private function seedNaturezas(): void
    {
        $naturezas = [
            ['nome' => 'Escritura Pública de Compra e Venda', 'codigo' => 'ECV'],
            ['nome' => 'Escritura Pública de Doação', 'codigo' => 'EDO'],
            ['nome' => 'Escritura Pública de Permuta', 'codigo' => 'EPE'],
            ['nome' => 'Contrato de Financiamento Imobiliário', 'codigo' => 'CFI'],
            ['nome' => 'Formal de Partilha', 'codigo' => 'FPA'],
            ['nome' => 'Instrumento Particular com Força de Escritura', 'codigo' => 'IPF'],
        ];

        foreach ($naturezas as $dados) {
            Natureza::firstOrCreate(
                ['codigo' => $dados['codigo']],
                ['nome' => $dados['nome'], 'is_ativo' => true]
            );
        }
    }

    private function seedAtos(): void
    {
        $atos = [
            ['codigo' => 'A001', 'nome' => 'Registro de Imóveis'],
            ['codigo' => 'A002', 'nome' => 'Registro de Contrato de Compra e Venda'],
            ['codigo' => 'A003', 'nome' => 'Averbação de Construção'],
            ['codigo' => 'A004', 'nome' => 'Averbação de Demolição'],
            ['codigo' => 'A005', 'nome' => 'Registro de Hipoteca'],
            ['codigo' => 'A006', 'nome' => 'Certidão de Inteiro Teor'],
            ['codigo' => 'A007', 'nome' => 'Certidão de Ônus Reais'],
        ];

        foreach ($atos as $dado) {
            Ato::firstOrCreate(['codigo' => $dado['codigo']], ['nome' => $dado['nome'], 'is_ativo' => true]);
        }
    }

    private function seedFormasPagamento(): void
    {
        $formas = ['Dinheiro', 'Cartão de Débito', 'Cartão de Crédito', 'PIX', 'Transferência Bancária'];

        foreach ($formas as $nome) {
            FormaPagamento::firstOrCreate(['nome' => $nome], ['is_ativo' => true]);
        }
    }

    private function seedProtocolos(): void
    {
        $usuario = User::first();
        if (!$usuario) return;

        $naturezas = Natureza::all();
        $atos      = Ato::all();
        $formas    = FormaPagamento::all();

        $protocolos = [
            [
                'status'            => 'aberto',
                'solicitante_nome'   => 'João da Silva Santos',
                'solicitante_cpf_cnpj' => '529.982.247-25',
                'solicitante_telefone' => '(65) 99876-5432',
                'solicitante_email' => 'joao.silva@exemplo.com',
                'natureza_nome'     => 'Escritura Pública de Compra e Venda',
                'valor_total'       => 850.00,
                'valor_final'       => 850.00,
                'valor_pago'        => 0,
                'matricula'         => '12.345',
                'tipo'              => 'NORMAL',
            ],
            [
                'status'            => 'pago',
                'solicitante_nome'   => 'Maria Oliveira Costa',
                'solicitante_cpf_cnpj' => '112.223.330-01',
                'solicitante_telefone' => '(65) 98765-4321',
                'solicitante_email' => null,
                'natureza_nome'     => 'Contrato de Financiamento Imobiliário',
                'valor_total'       => 1200.50,
                'valor_final'       => 1200.50,
                'valor_pago'        => 1200.50,
                'matricula'         => '67.890',
                'tipo'              => 'NORMAL',
            ],
            [
                'status'            => 'pago_parcial',
                'solicitante_nome'   => 'Empresa ABC Ltda',
                'solicitante_cpf_cnpj' => '11.222.333/0001-81',
                'solicitante_telefone' => '(65) 3624-5678',
                'solicitante_email' => 'financeiro@empresaabc.com.br',
                'natureza_nome'     => 'Escritura Pública de Doação',
                'valor_total'       => 3500.00,
                'valor_final'       => 3500.00,
                'valor_pago'        => 1750.00,
                'matricula'         => null,
                'tipo'              => 'NORMAL',
            ],
            [
                'status'            => 'aberto',
                'solicitante_nome'   => 'Carlos Eduardo Ferreira',
                'solicitante_cpf_cnpj' => '321.654.987-00',
                'solicitante_telefone' => null,
                'solicitante_email' => 'carlos.ferreira@email.com',
                'natureza_nome'     => 'Formal de Partilha',
                'valor_total'       => 0,
                'valor_final'       => 0,
                'valor_pago'        => 0,
                'matricula'         => '55.123',
                'tipo'              => 'ORCAMENTO',
            ],
            [
                'status'            => 'cancelado',
                'solicitante_nome'   => 'Ana Paula Rodrigues',
                'solicitante_cpf_cnpj' => '777.888.999-11',
                'solicitante_telefone' => '(65) 97654-3210',
                'solicitante_email' => null,
                'natureza_nome'     => 'Escritura Pública de Permuta',
                'valor_total'       => 650.00,
                'valor_final'       => 650.00,
                'valor_pago'        => 0,
                'matricula'         => null,
                'tipo'              => 'NORMAL',
                'motivo_cancelamento' => 'Solicitante desistiu do negócio',
            ],
            [
                'status'            => 'aberto',
                'solicitante_nome'   => 'Roberto Mendes',
                'solicitante_cpf_cnpj' => '555.444.333-22',
                'solicitante_telefone' => '(66) 98888-7777',
                'solicitante_email' => 'roberto@mendes.com',
                'natureza_nome'     => 'Instrumento Particular com Força de Escritura',
                'valor_total'       => 450.00,
                'valor_final'       => 405.00,
                'valor_pago'        => 0,
                'valor_desconto'    => 45.00,
                'matricula'         => '88.765',
                'tipo'              => 'NORMAL',
                'observacao'        => 'Cliente solicitou desconto por ser portador de deficiência.',
            ],
        ];

        foreach ($protocolos as $i => $dados) {
            $natureza = $naturezas->firstWhere('nome', $dados['natureza_nome']);
            $ano      = now()->year;
            $seq      = $i + 1;
            $numero   = sprintf('%d/%06d', $ano, $seq);

            // Evitar duplicação
            if (Protocolo::where('numero', $numero)->exists()) continue;

            $protocolo = Protocolo::create([
                'numero'              => $numero,
                'ano'                 => $ano,
                'atendente_id'        => $usuario->id,
                'natureza_id'         => $natureza?->id,
                'tipo'                => $dados['tipo'],
                'solicitante_nome'    => $dados['solicitante_nome'],
                'solicitante_cpf_cnpj' => $dados['solicitante_cpf_cnpj'] ?? null,
                'solicitante_telefone' => $dados['solicitante_telefone'] ?? null,
                'solicitante_email'   => $dados['solicitante_email'] ?? null,
                'matricula'           => $dados['matricula'] ?? null,
                'observacao'          => $dados['observacao'] ?? null,
                'motivo_cancelamento' => $dados['motivo_cancelamento'] ?? null,
                'valor_total'         => $dados['valor_total'],
                'valor_desconto'      => $dados['valor_desconto'] ?? 0,
                'valor_isento'        => 0,
                'valor_final'         => $dados['valor_final'],
                'valor_pago'          => $dados['valor_pago'],
                'status'              => $dados['status'],
                'is_ativo'            => true,
            ]);

            // Adicionar atos (exceto cancelado/orçamento sem valor)
            if ($dados['valor_total'] > 0 && $atos->isNotEmpty()) {
                $ato1 = $atos->first();
                ProtocoloItem::create([
                    'protocolo_id'   => $protocolo->id,
                    'ato_id'         => $ato1->id,
                    'descricao'      => $ato1->nome,
                    'quantidade'     => 1,
                    'base_calculo'   => null,
                    'valor_unitario' => $dados['valor_total'],
                    'valor_total'    => $dados['valor_total'],
                ]);

                // Protocolo grande tem 2 atos
                if ($dados['valor_total'] >= 1000 && $atos->count() > 1) {
                    $ato2 = $atos->skip(1)->first();
                    ProtocoloItem::create([
                        'protocolo_id'   => $protocolo->id,
                        'ato_id'         => $ato2->id,
                        'descricao'      => $ato2->nome,
                        'quantidade'     => 1,
                        'base_calculo'   => null,
                        'valor_unitario' => 0,
                        'valor_total'    => 0,
                    ]);
                }
            }

            // Pagamento para protocolos pagos
            if ($dados['valor_pago'] > 0 && $formas->isNotEmpty()) {
                $formaPix = $formas->firstWhere('nome', 'PIX') ?? $formas->first();
                ProtocoloPagamento::create([
                    'protocolo_id'      => $protocolo->id,
                    'forma_pagamento_id' => $formaPix->id,
                    'meio_pagamento_id'  => null,
                    'usuario_id'        => $usuario->id,
                    'valor'             => $dados['valor_pago'],
                    'data_pagamento'    => now()->subDays(rand(1, 10)),
                    'status'            => 'confirmado',
                    'observacao'        => 'Pagamento via PIX confirmado',
                ]);
            }
        }
    }
}
