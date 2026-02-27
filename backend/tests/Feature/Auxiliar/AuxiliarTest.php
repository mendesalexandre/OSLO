<?php

use App\Models\CapacidadeCivil;
use App\Models\EstadoCivil;
use App\Models\Nacionalidade;
use App\Models\PorteEmpresa;
use App\Models\Profissao;
use App\Models\RegimeBem;
use App\Models\TipoEmpresa;
use App\Models\User;
use Database\Seeders\CapacidadeCivilSeeder;
use Database\Seeders\EstadoCivilSeeder;
use Database\Seeders\NacionalidadeSeeder;
use Database\Seeders\PorteEmpresaSeeder;
use Database\Seeders\ProfissaoSeeder;
use Database\Seeders\RegimeBemSeeder;
use Database\Seeders\TipoEmpresaSeeder;

// =============================================================================
// AUTENTICAÇÃO OBRIGATÓRIA
// =============================================================================

test('rota de auxiliares exige autenticação', function () {
    $this->getJson('/api/v1/auxiliares/estado-civil')
        ->assertStatus(401);
});

test('tabela inexistente retorna 404', function () {
    $usuario = User::factory()->create();

    $this->actingAs($usuario, 'web')
        ->getJson('/api/v1/auxiliares/tabela-que-nao-existe')
        ->assertStatus(404)
        ->assertJson(['sucesso' => false]);
});

// =============================================================================
// ESTADO CIVIL
// =============================================================================

test('estado civil retorna lista de registros ativos', function () {
    $this->seed(EstadoCivilSeeder::class);
    $usuario = User::factory()->create();

    $response = $this->actingAs($usuario, 'web')
        ->getJson('/api/v1/auxiliares/estado-civil');

    $response->assertOk()
        ->assertJson(['sucesso' => true])
        ->assertJsonStructure(['dados' => [['id', 'descricao', 'is_ativo']]]);
});

test('estado civil seed possui ao menos 7 registros', function () {
    $this->seed(EstadoCivilSeeder::class);

    expect(EstadoCivil::ativo()->count())->toBeGreaterThanOrEqual(7);
});

test('estado civil inclui Casado(a) no seed', function () {
    $this->seed(EstadoCivilSeeder::class);

    expect(EstadoCivil::where('descricao', 'Casado(a)')->exists())->toBeTrue();
});

// =============================================================================
// REGIME DE BENS
// =============================================================================

test('regime bem retorna lista de registros ativos', function () {
    $this->seed(RegimeBemSeeder::class);
    $usuario = User::factory()->create();

    $response = $this->actingAs($usuario, 'web')
        ->getJson('/api/v1/auxiliares/regime-bem');

    $response->assertOk()
        ->assertJsonStructure(['dados' => [['id', 'descricao']]]);
});

test('regime bem seed possui ao menos 5 registros', function () {
    $this->seed(RegimeBemSeeder::class);

    expect(RegimeBem::ativo()->count())->toBeGreaterThanOrEqual(5);
});

// =============================================================================
// NACIONALIDADE
// =============================================================================

test('nacionalidade retorna lista com campo gentilico', function () {
    $this->seed(NacionalidadeSeeder::class);
    $usuario = User::factory()->create();

    $response = $this->actingAs($usuario, 'web')
        ->getJson('/api/v1/auxiliares/nacionalidade');

    $response->assertOk()
        ->assertJsonStructure(['dados' => [['id', 'descricao', 'gentilico']]]);
});

test('nacionalidade seed possui ao menos 10 registros', function () {
    $this->seed(NacionalidadeSeeder::class);

    expect(Nacionalidade::ativo()->count())->toBeGreaterThanOrEqual(10);
});

test('nacionalidade seed inclui Brasileira', function () {
    $this->seed(NacionalidadeSeeder::class);

    expect(Nacionalidade::where('descricao', 'Brasileira')->exists())->toBeTrue();
});

// =============================================================================
// CAPACIDADE CIVIL
// =============================================================================

test('capacidade civil retorna lista com observacao', function () {
    $this->seed(CapacidadeCivilSeeder::class);
    $usuario = User::factory()->create();

    $response = $this->actingAs($usuario, 'web')
        ->getJson('/api/v1/auxiliares/capacidade-civil');

    $response->assertOk()
        ->assertJsonStructure(['dados' => [['id', 'descricao']]]);
});

test('capacidade civil seed possui ao menos 4 registros', function () {
    $this->seed(CapacidadeCivilSeeder::class);

    expect(CapacidadeCivil::ativo()->count())->toBeGreaterThanOrEqual(4);
});

// =============================================================================
// PROFISSÃO
// =============================================================================

test('profissao retorna lista com codigo_cbo', function () {
    $this->seed(ProfissaoSeeder::class);
    $usuario = User::factory()->create();

    $response = $this->actingAs($usuario, 'web')
        ->getJson('/api/v1/auxiliares/profissao');

    $response->assertOk()
        ->assertJsonStructure(['dados' => [['id', 'descricao']]]);
});

test('profissao seed possui ao menos 10 registros', function () {
    $this->seed(ProfissaoSeeder::class);

    expect(Profissao::ativo()->count())->toBeGreaterThanOrEqual(10);
});

// =============================================================================
// TIPO DE EMPRESA
// =============================================================================

test('tipo empresa retorna lista com sigla', function () {
    $this->seed(TipoEmpresaSeeder::class);
    $usuario = User::factory()->create();

    $response = $this->actingAs($usuario, 'web')
        ->getJson('/api/v1/auxiliares/tipo-empresa');

    $response->assertOk()
        ->assertJsonStructure(['dados' => [['id', 'descricao', 'sigla']]]);
});

test('tipo empresa seed possui ao menos 8 registros', function () {
    $this->seed(TipoEmpresaSeeder::class);

    expect(TipoEmpresa::ativo()->count())->toBeGreaterThanOrEqual(8);
});

test('tipo empresa inclui LTDA no seed', function () {
    $this->seed(TipoEmpresaSeeder::class);

    expect(TipoEmpresa::where('sigla', 'LTDA')->exists())->toBeTrue();
});

// =============================================================================
// PORTE DE EMPRESA
// =============================================================================

test('porte empresa retorna lista de registros ativos', function () {
    $this->seed(PorteEmpresaSeeder::class);
    $usuario = User::factory()->create();

    $response = $this->actingAs($usuario, 'web')
        ->getJson('/api/v1/auxiliares/porte-empresa');

    $response->assertOk()
        ->assertJsonStructure(['dados' => [['id', 'descricao']]]);
});

test('porte empresa seed possui ao menos 5 registros', function () {
    $this->seed(PorteEmpresaSeeder::class);

    expect(PorteEmpresa::ativo()->count())->toBeGreaterThanOrEqual(5);
});

// =============================================================================
// REGISTROS INATIVOS NÃO APARECEM
// =============================================================================

test('registros inativos não aparecem na listagem', function () {
    EstadoCivil::create(['descricao' => 'Inativo Teste', 'is_ativo' => false]);

    $usuario = User::factory()->create();

    $response = $this->actingAs($usuario, 'web')
        ->getJson('/api/v1/auxiliares/estado-civil');

    $dados = $response->json('dados');
    $descricoes = collect($dados)->pluck('descricao');

    expect($descricoes->contains('Inativo Teste'))->toBeFalse();
});
