<?php

use App\Models\CapacidadeCivil;
use App\Models\EstadoCivil;
use App\Models\IndicadorPessoal;
use App\Models\RegimeBem;
use App\Models\User;

// CPF e CNPJ válidos para testes
const CPF_VALIDO  = '52998224725';
const CNPJ_VALIDO = '11222333000181';

function usuario(): User
{
    return User::factory()->create();
}

function indicadorPF(array $attrs = []): IndicadorPessoal
{
    return IndicadorPessoal::create(array_merge([
        'tipo_pessoa' => 'F',
        'cpf_cnpj'   => CPF_VALIDO,
        'nome'       => 'João da Silva',
        'versao'     => 1,
        'is_atual'   => true,
        'data_versao' => now(),
    ], $attrs));
}

function indicadorPJ(array $attrs = []): IndicadorPessoal
{
    return IndicadorPessoal::create(array_merge([
        'tipo_pessoa' => 'J',
        'cpf_cnpj'   => CNPJ_VALIDO,
        'nome'       => 'Empresa Teste LTDA',
        'versao'     => 1,
        'is_atual'   => true,
        'data_versao' => now(),
    ], $attrs));
}

// ── Cadastro ──────────────────────────────────────────────────────────────

test('pode cadastrar pessoa física com dados válidos', function () {
    $response = $this->actingAs(usuario(), 'web')
        ->postJson('/api/v1/indicador-pessoal', [
            'tipo_pessoa'  => 'F',
            'cpf_cnpj'    => CPF_VALIDO,
            'nome'        => 'João da Silva',
            'data_nascimento' => '1985-05-10',
        ]);

    $response->assertStatus(201)
             ->assertJsonPath('sucesso', true)
             ->assertJsonPath('dados.nome', 'João da Silva')
             ->assertJsonPath('dados.versao', 1)
             ->assertJsonPath('dados.is_atual', true);
});

test('pode cadastrar pessoa jurídica com dados válidos', function () {
    $response = $this->actingAs(usuario(), 'web')
        ->postJson('/api/v1/indicador-pessoal', [
            'tipo_pessoa'  => 'J',
            'cpf_cnpj'    => CNPJ_VALIDO,
            'nome'        => 'Empresa Teste LTDA',
            'data_abertura' => '2010-03-15',
        ]);

    $response->assertStatus(201)
             ->assertJsonPath('sucesso', true)
             ->assertJsonPath('dados.tipo_pessoa', 'J')
             ->assertJsonPath('dados.cpf_cnpj', CNPJ_VALIDO);
});

test('CPF inválido retorna erro 422', function () {
    $response = $this->actingAs(usuario(), 'web')
        ->postJson('/api/v1/indicador-pessoal', [
            'tipo_pessoa' => 'F',
            'cpf_cnpj'   => '11111111111',
            'nome'       => 'Teste',
        ]);

    $response->assertStatus(422)
             ->assertJsonPath('sucesso', false);
});

test('CNPJ inválido retorna erro 422', function () {
    $response = $this->actingAs(usuario(), 'web')
        ->postJson('/api/v1/indicador-pessoal', [
            'tipo_pessoa' => 'J',
            'cpf_cnpj'   => '11111111111111',
            'nome'       => 'Empresa',
        ]);

    $response->assertStatus(422)
             ->assertJsonPath('sucesso', false);
});

test('CPF duplicado na versão atual retorna erro com mensagem explicativa', function () {
    indicadorPF();

    $response = $this->actingAs(usuario(), 'web')
        ->postJson('/api/v1/indicador-pessoal', [
            'tipo_pessoa' => 'F',
            'cpf_cnpj'   => CPF_VALIDO,
            'nome'       => 'Outro Nome',
        ]);

    $response->assertStatus(422)
             ->assertJsonPath('sucesso', false);
});

// ── Atualização / Versionamento ───────────────────────────────────────────

test('atualização cria nova versão e desativa a anterior', function () {
    $indicador = indicadorPF();

    $response = $this->actingAs(usuario(), 'web')
        ->putJson("/api/v1/indicador-pessoal/{$indicador->id}", [
            'tipo_pessoa'   => 'F',
            'cpf_cnpj'     => CPF_VALIDO,
            'nome'         => 'João da Silva Atualizado',
            'motivo_versao' => 'Correção de nome',
        ]);

    $response->assertStatus(200)
             ->assertJsonPath('dados.versao', 2)
             ->assertJsonPath('dados.is_atual', true)
             ->assertJsonPath('dados.motivo_versao', 'Correção de nome');

    // Versão anterior deve estar desativada
    expect(IndicadorPessoal::find($indicador->id)->is_atual)->toBeFalse();
});

test('motivo_versao é obrigatório na atualização', function () {
    $indicador = indicadorPF();

    $response = $this->actingAs(usuario(), 'web')
        ->putJson("/api/v1/indicador-pessoal/{$indicador->id}", [
            'tipo_pessoa' => 'F',
            'cpf_cnpj'   => CPF_VALIDO,
            'nome'       => 'João Atualizado',
        ]);

    $response->assertStatus(422);
});

// ── Listagem ──────────────────────────────────────────────────────────────

test('listagem retorna apenas versões atuais', function () {
    // Cria versão 1 e logo depois cria versão 2 (desativa v1)
    $indicador = indicadorPF();
    $this->actingAs(usuario(), 'web')
        ->putJson("/api/v1/indicador-pessoal/{$indicador->id}", [
            'tipo_pessoa'   => 'F',
            'cpf_cnpj'     => CPF_VALIDO,
            'nome'         => 'João v2',
            'motivo_versao' => 'Atualização',
        ]);

    $response = $this->actingAs(usuario(), 'web')
        ->getJson('/api/v1/indicador-pessoal');

    $response->assertStatus(200);
    $nomes = collect($response->json('dados.data'))->pluck('nome');
    expect($nomes)->not->toContain('João da Silva');
    expect($nomes)->toContain('João v2');
});

// ── Busca ─────────────────────────────────────────────────────────────────

test('busca por nome retorna resultados corretos', function () {
    indicadorPF(['nome' => 'Maria Aparecida']);

    $response = $this->actingAs(usuario(), 'web')
        ->getJson('/api/v1/indicador-pessoal/busca?q=Maria');

    $response->assertStatus(200)
             ->assertJsonCount(1, 'dados');
});

test('busca por CPF retorna resultado correto', function () {
    indicadorPF(['nome' => 'João Teste']);

    $response = $this->actingAs(usuario(), 'web')
        ->getJson('/api/v1/indicador-pessoal/busca?q=' . CPF_VALIDO);

    $response->assertStatus(200)
             ->assertJsonCount(1, 'dados');
});

// ── Histórico de versões ──────────────────────────────────────────────────

test('histórico de versões retorna todas as versões do CPF/CNPJ', function () {
    $indicador = indicadorPF();

    $usuario = usuario();
    $this->actingAs($usuario, 'web')
        ->putJson("/api/v1/indicador-pessoal/{$indicador->id}", [
            'tipo_pessoa'   => 'F',
            'cpf_cnpj'     => CPF_VALIDO,
            'nome'         => 'João v2',
            'motivo_versao' => 'Primeira atualização',
        ]);

    $response = $this->actingAs($usuario, 'web')
        ->getJson('/api/v1/indicador-pessoal/' . CPF_VALIDO . '/versoes');

    $response->assertStatus(200);
    expect(count($response->json('dados')))->toBe(2);
});

// ── Soft delete ───────────────────────────────────────────────────────────

test('soft delete preenche data_exclusao', function () {
    $indicador = indicadorPF();

    $this->actingAs(usuario(), 'web')
        ->deleteJson("/api/v1/indicador-pessoal/{$indicador->id}")
        ->assertStatus(200);

    expect(IndicadorPessoal::withTrashed()->find($indicador->id)->data_exclusao)->not->toBeNull();
});

// ── Validações de negócio ─────────────────────────────────────────────────

test('cônjuge deve existir no indicador pessoal', function () {
    $response = $this->actingAs(usuario(), 'web')
        ->postJson('/api/v1/indicador-pessoal', [
            'tipo_pessoa' => 'F',
            'cpf_cnpj'   => CPF_VALIDO,
            'nome'       => 'Teste',
            'conjuge_id' => 99999,
        ]);

    $response->assertStatus(422);
});

test('estado civil casado exige regime de bens', function () {
    $estadoCivilCasado = EstadoCivil::create([
        'descricao' => 'Casado(a)',
        'is_ativo'  => true,
    ]);

    $response = $this->actingAs(usuario(), 'web')
        ->postJson('/api/v1/indicador-pessoal', [
            'tipo_pessoa'     => 'F',
            'cpf_cnpj'       => CPF_VALIDO,
            'nome'           => 'Teste Casado',
            'estado_civil_id' => $estadoCivilCasado->id,
            // sem regime_bem_id e sem conjuge_id
        ]);

    $response->assertStatus(422);
    expect($response->json('erros'))->toHaveKey('regime_bem_id');
});

test('estado civil casado com regime de bens e cônjuge válido é aceito', function () {
    $estadoCivilCasado = EstadoCivil::create([
        'descricao' => 'Casado(a)',
        'is_ativo'  => true,
    ]);
    $regimeBem = RegimeBem::create([
        'descricao' => 'Comunhão Parcial de Bens',
        'is_ativo'  => true,
    ]);
    $conjuge = indicadorPJ();

    $response = $this->actingAs(usuario(), 'web')
        ->postJson('/api/v1/indicador-pessoal', [
            'tipo_pessoa'     => 'F',
            'cpf_cnpj'       => CPF_VALIDO,
            'nome'           => 'Teste Casado',
            'estado_civil_id' => $estadoCivilCasado->id,
            'regime_bem_id'  => $regimeBem->id,
            'conjuge_id'     => $conjuge->id,
        ]);

    $response->assertStatus(201);
});
