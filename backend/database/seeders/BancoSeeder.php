<?php

namespace Database\Seeders;

use App\Models\Banco;
use Illuminate\Database\Seeder;

class BancoSeeder extends Seeder
{
    public function run(): void
    {
        $bancos = [
            ['codigo_bcb' => '104', 'nome' => 'Caixa Econômica Federal',        'sigla' => 'CEF'],
            ['codigo_bcb' => '001', 'nome' => 'Banco do Brasil',                'sigla' => 'BB'],
            ['codigo_bcb' => '341', 'nome' => 'Itaú Unibanco',                  'sigla' => 'ITAÚ'],
            ['codigo_bcb' => '237', 'nome' => 'Bradesco',                       'sigla' => 'BRADESCO'],
            ['codigo_bcb' => '033', 'nome' => 'Santander',                      'sigla' => 'SANTANDER'],
            ['codigo_bcb' => '077', 'nome' => 'Banco Inter',                    'sigla' => 'INTER'],
            ['codigo_bcb' => '260', 'nome' => 'Nu Pagamentos (Nubank)',          'sigla' => 'NUBANK'],
            ['codigo_bcb' => '748', 'nome' => 'Sicredi',                        'sigla' => 'SICREDI'],
            ['codigo_bcb' => '756', 'nome' => 'Sicoob',                         'sigla' => 'SICOOB'],
        ];

        foreach ($bancos as $banco) {
            Banco::updateOrCreate(
                ['nome' => $banco['nome']],
                array_merge($banco, ['is_ativo' => true]),
            );
        }
    }
}
