<?php

namespace Database\Seeders;

use App\Models\TipoTransacao;
use Illuminate\Database\Seeder;

class TipoTransacaoSeeder extends Seeder
{
    public function run(): void
    {
        $registros = [
            // Entradas
            ['descricao' => 'Recebimento',     'tipo' => 'entrada', 'icone' => 'arrow-down-circle', 'cor' => 'positive'],
            ['descricao' => 'Reforço de Caixa','tipo' => 'entrada', 'icone' => 'plus-circle',        'cor' => 'positive'],
            ['descricao' => 'Ajuste a Crédito','tipo' => 'entrada', 'icone' => 'trending-up',        'cor' => 'positive'],

            // Saídas
            ['descricao' => 'Pagamento',       'tipo' => 'saida',   'icone' => 'arrow-up-circle',    'cor' => 'negative'],
            ['descricao' => 'Sangria',         'tipo' => 'saida',   'icone' => 'minus-circle',       'cor' => 'negative'],
            ['descricao' => 'Ajuste a Débito', 'tipo' => 'saida',   'icone' => 'trending-down',      'cor' => 'negative'],

            // Caixa
            ['descricao' => 'Transferência',   'tipo' => 'caixa',   'icone' => 'repeat',             'cor' => 'info'],
        ];

        foreach ($registros as $registro) {
            TipoTransacao::updateOrCreate(
                ['descricao' => $registro['descricao']],
                array_merge($registro, ['is_ativo' => true]),
            );
        }
    }
}
