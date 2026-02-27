<?php

namespace Database\Seeders;

use App\Models\Profissao;
use Illuminate\Database\Seeder;

class ProfissaoSeeder extends Seeder
{
    public function run(): void
    {
        $registros = [
            ['descricao' => 'Advogado(a)',                         'codigo_cbo' => '2410-05'],
            ['descricao' => 'Agricultor(a)',                       'codigo_cbo' => '6110-10'],
            ['descricao' => 'Analista de Sistemas',                'codigo_cbo' => '2124-05'],
            ['descricao' => 'Aposentado(a)',                       'codigo_cbo' => null],
            ['descricao' => 'Arquiteto(a)',                        'codigo_cbo' => '2141-05'],
            ['descricao' => 'Auxiliar Administrativo(a)',          'codigo_cbo' => '4110-10'],
            ['descricao' => 'Comerciante',                         'codigo_cbo' => '1430-05'],
            ['descricao' => 'Contador(a)',                         'codigo_cbo' => '2522-05'],
            ['descricao' => 'Corretor(a) de Imóveis',              'codigo_cbo' => '3720-05'],
            ['descricao' => 'Dentista',                            'codigo_cbo' => '2232-04'],
            ['descricao' => 'Desempregado(a)',                     'codigo_cbo' => null],
            ['descricao' => 'Do Lar',                              'codigo_cbo' => null],
            ['descricao' => 'Eletricista',                         'codigo_cbo' => '7156-10'],
            ['descricao' => 'Empresário(a)',                       'codigo_cbo' => '1231-05'],
            ['descricao' => 'Engenheiro(a) Civil',                 'codigo_cbo' => '2142-05'],
            ['descricao' => 'Engenheiro(a) Elétrico(a)',           'codigo_cbo' => '2143-05'],
            ['descricao' => 'Estudante',                           'codigo_cbo' => null],
            ['descricao' => 'Farmacêutico(a)',                     'codigo_cbo' => '2234-05'],
            ['descricao' => 'Médico(a)',                           'codigo_cbo' => '2251-05'],
            ['descricao' => 'Microempreendedor(a) Individual (MEI)', 'codigo_cbo' => null],
            ['descricao' => 'Motorista',                           'codigo_cbo' => '7824-10'],
            ['descricao' => 'Pedreiro(a)',                         'codigo_cbo' => '7152-10'],
            ['descricao' => 'Pintor(a)',                           'codigo_cbo' => '7166-05'],
            ['descricao' => 'Policial Militar',                    'codigo_cbo' => '3301-05'],
            ['descricao' => 'Professor(a)',                        'codigo_cbo' => '2312-05'],
            ['descricao' => 'Psicólogo(a)',                        'codigo_cbo' => '2515-05'],
            ['descricao' => 'Servidor(a) Público(a)',              'codigo_cbo' => null],
            ['descricao' => 'Técnico(a) em Informática',           'codigo_cbo' => '3172-05'],
            ['descricao' => 'Outros',                              'codigo_cbo' => null],
        ];

        foreach ($registros as $dados) {
            Profissao::updateOrCreate(
                ['descricao'  => $dados['descricao']],
                ['codigo_cbo' => $dados['codigo_cbo'], 'is_ativo' => true],
            );
        }
    }
}
