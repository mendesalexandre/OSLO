<?php

namespace Database\Seeders;

use App\Models\RegimeBem;
use Illuminate\Database\Seeder;

class RegimeBemSeeder extends Seeder
{
    public function run(): void
    {
        $registros = [
            [
                'descricao'  => 'Comunhão Parcial de Bens',
                'observacao' => 'Regime legal supletivo. Os bens adquiridos onerosamente na constância do casamento são comuns. Os bens anteriores ao casamento e os recebidos por herança ou doação são particulares.',
            ],
            [
                'descricao'  => 'Comunhão Universal de Bens',
                'observacao' => 'Todos os bens presentes e futuros dos cônjuges, inclusive os adquiridos antes do casamento e os recebidos por herança ou doação, passam a ser comuns.',
            ],
            [
                'descricao'  => 'Separação Total de Bens',
                'observacao' => 'Cada cônjuge conserva o domínio, a posse e a administração exclusiva dos seus bens presentes e futuros. Pode ser convencional (voluntária) ou obrigatória por lei.',
            ],
            [
                'descricao'  => 'Separação Obrigatória de Bens',
                'observacao' => 'Imposta por lei para casamentos de pessoa maior de 70 anos, ou que necessite de suprimento judicial para casar (art. 1.641 do CC). Aplica-se a Súmula 377 do STF sobre bens adquiridos na constância.',
            ],
            [
                'descricao'  => 'Participação Final nos Aquestos',
                'observacao' => 'Durante o casamento, cada cônjuge possui patrimônio próprio e os bens são administrados individualmente. Na dissolução, cada cônjuge tem direito à metade dos bens adquiridos onerosamente pelo casal durante o casamento.',
            ],
        ];

        foreach ($registros as $dados) {
            RegimeBem::updateOrCreate(
                ['descricao' => $dados['descricao']],
                ['observacao' => $dados['observacao'], 'is_ativo' => true],
            );
        }
    }
}
