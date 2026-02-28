<?php

namespace Database\Seeders;

use App\Models\MotivoTransacao;
use App\Models\TipoTransacao;
use Illuminate\Database\Seeder;

class MotivoTransacaoSeeder extends Seeder
{
    public function run(): void
    {
        $motivos = [
            // Recebimento
            ['tipo' => 'Recebimento', 'descricao' => 'Emolumentos',               'exige_documento' => false, 'exige_beneficiario' => false],
            ['tipo' => 'Recebimento', 'descricao' => 'Honorários',                'exige_documento' => false, 'exige_beneficiario' => false],
            ['tipo' => 'Recebimento', 'descricao' => 'Outros Recebimentos',       'exige_documento' => false, 'exige_beneficiario' => false],

            // Reforço de Caixa
            ['tipo' => 'Reforço de Caixa', 'descricao' => 'Reforço Bancário',    'exige_documento' => true,  'exige_beneficiario' => false],
            ['tipo' => 'Reforço de Caixa', 'descricao' => 'Reforço em Espécie',  'exige_documento' => false, 'exige_beneficiario' => false],

            // Pagamento
            ['tipo' => 'Pagamento', 'descricao' => 'Fornecedor',                  'exige_documento' => true,  'exige_beneficiario' => true ],
            ['tipo' => 'Pagamento', 'descricao' => 'Recolhimento de Imposto',     'exige_documento' => true,  'exige_beneficiario' => false],
            ['tipo' => 'Pagamento', 'descricao' => 'Salário',                     'exige_documento' => false, 'exige_beneficiario' => true ],
            ['tipo' => 'Pagamento', 'descricao' => 'Outros Pagamentos',           'exige_documento' => false, 'exige_beneficiario' => false],

            // Sangria
            ['tipo' => 'Sangria', 'descricao' => 'Sangria para Conta Bancária',  'exige_documento' => false, 'exige_beneficiario' => false],
            ['tipo' => 'Sangria', 'descricao' => 'Sangria para Cofre',            'exige_documento' => false, 'exige_beneficiario' => false],

            // Transferência
            ['tipo' => 'Transferência', 'descricao' => 'Entre Caixas',            'exige_documento' => false, 'exige_beneficiario' => false],
            ['tipo' => 'Transferência', 'descricao' => 'Caixa para Banco',        'exige_documento' => false, 'exige_beneficiario' => false],
        ];

        foreach ($motivos as $motivo) {
            $tipo = TipoTransacao::where('descricao', $motivo['tipo'])->first();

            if (!$tipo) {
                continue;
            }

            MotivoTransacao::updateOrCreate(
                [
                    'tipo_transacao_id' => $tipo->id,
                    'descricao'         => $motivo['descricao'],
                ],
                [
                    'exige_documento'    => $motivo['exige_documento'],
                    'exige_beneficiario' => $motivo['exige_beneficiario'],
                    'is_ativo'           => true,
                ],
            );
        }
    }
}
