<?php

namespace Database\Seeders;

use App\Models\TipoEmpresa;
use Illuminate\Database\Seeder;

class TipoEmpresaSeeder extends Seeder
{
    public function run(): void
    {
        $registros = [
            ['sigla' => 'LTDA',   'descricao' => 'Sociedade Limitada'],
            ['sigla' => 'S/A',    'descricao' => 'Sociedade Anônima'],
            ['sigla' => 'MEI',    'descricao' => 'Microempreendedor Individual'],
            ['sigla' => 'ME',     'descricao' => 'Microempresa'],
            ['sigla' => 'EPP',    'descricao' => 'Empresa de Pequeno Porte'],
            ['sigla' => 'EIRELI', 'descricao' => 'Empresa Individual de Responsabilidade Limitada'],
            ['sigla' => 'SS',     'descricao' => 'Sociedade Simples'],
            ['sigla' => 'SLU',    'descricao' => 'Sociedade Limitada Unipessoal'],
        ];

        foreach ($registros as $dados) {
            TipoEmpresa::updateOrCreate(
                ['sigla'    => $dados['sigla']],
                ['descricao' => $dados['descricao'], 'is_ativo' => true],
            );
        }
    }
}
