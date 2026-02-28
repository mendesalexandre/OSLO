<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Usuário padrão
        User::factory()->create([
            'nome'  => 'Administrador',
            'email' => 'admin@oslo.local',
            'senha' => 'password',
        ]);

        // Tabelas auxiliares
        $this->call([
            EstadoCivilSeeder::class,
            RegimeBemSeeder::class,
            NacionalidadeSeeder::class,
            CapacidadeCivilSeeder::class,
            ProfissaoSeeder::class,
            TipoEmpresaSeeder::class,
            PorteEmpresaSeeder::class,
        ]);

        // Tabelas globais de sistema
        $this->call([
            EstadoSeeder::class,
        ]);

        // Catálogos de transação (ordem importa: motivos dependem de tipos)
        $this->call([
            TipoTransacaoSeeder::class,
            MotivoTransacaoSeeder::class,
            BancoSeeder::class,
        ]);

        // Protocolos de exemplo
        $this->call([
            ProtocoloSeeder::class,
        ]);
    }
}
