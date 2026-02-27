<?php

namespace Database\Seeders;

use App\Models\PorteEmpresa;
use Illuminate\Database\Seeder;

class PorteEmpresaSeeder extends Seeder
{
    public function run(): void
    {
        $registros = [
            'Microempreendedor Individual (MEI)',
            'Microempresa (ME)',
            'Empresa de Pequeno Porte (EPP)',
            'Médio Porte',
            'Grande Porte',
        ];

        foreach ($registros as $descricao) {
            PorteEmpresa::updateOrCreate(
                ['descricao' => $descricao],
                ['is_ativo'  => true],
            );
        }
    }
}
