<?php

namespace Database\Seeders;

use App\Models\CapacidadeCivil;
use Illuminate\Database\Seeder;

class CapacidadeCivilSeeder extends Seeder
{
    public function run(): void
    {
        $registros = [
            [
                'descricao'  => 'Plenamente Capaz',
                'observacao' => 'Pessoa maior de 18 anos que não se enquadra em nenhuma das hipóteses de incapacidade previstas no Código Civil.',
            ],
            [
                'descricao'  => 'Relativamente Incapaz (16 a 18 anos)',
                'observacao' => 'Menor entre 16 e 18 anos. Pode praticar atos da vida civil, mas com assistência do representante legal, salvo exceções legais.',
            ],
            [
                'descricao'  => 'Absolutamente Incapaz',
                'observacao' => 'Pessoa que, por enfermidade ou deficiência mental, não tem o necessário discernimento para a prática dos atos da vida civil. Necessita de representação.',
            ],
            [
                'descricao'  => 'Emancipado(a)',
                'observacao' => 'Menor de 18 anos que adquiriu plena capacidade civil por concessão dos pais, sentença judicial, casamento, exercício de emprego público, conclusão de curso superior ou estabelecimento civil ou comercial.',
            ],
        ];

        foreach ($registros as $dados) {
            CapacidadeCivil::updateOrCreate(
                ['descricao' => $dados['descricao']],
                ['observacao' => $dados['observacao'], 'is_ativo' => true],
            );
        }
    }
}
