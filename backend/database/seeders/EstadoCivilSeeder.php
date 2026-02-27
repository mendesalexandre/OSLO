<?php

namespace Database\Seeders;

use App\Models\EstadoCivil;
use Illuminate\Database\Seeder;

class EstadoCivilSeeder extends Seeder
{
    public function run(): void
    {
        $registros = [
            'Solteiro(a)',
            'Casado(a)',
            'Separado(a) Judicialmente',
            'Divorciado(a)',
            'Viúvo(a)',
            'União Estável',
            'Outros',
        ];

        foreach ($registros as $descricao) {
            EstadoCivil::updateOrCreate(
                ['descricao' => $descricao],
                ['is_ativo'  => true],
            );
        }
    }
}
