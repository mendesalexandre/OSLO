<?php

use App\Models\Indisponibilidade;
use App\Models\IndisponibilidadeParte;
use App\Models\IndicadorPessoal;
use App\Models\User;

function usuarioIndisp(): User
{
    return User::factory()->create();
}

function criarIndisponibilidade(array $attrs = []): Indisponibilidade
{
    return Indisponibilidade::create(array_merge([
        'status'                      => 'pendente',
        'protocolo_indisponibilidade' => 'PROT-' . uniqid(),
    ], $attrs));
}

function criarIndisponibilidadeComParte(string $cpfCnpj = '52998224725', array $attrs = []): Indisponibilidade
{
    $ind = criarIndisponibilidade($attrs);
    $ind->partes()->create([
        'cpf_cnpj'   => $cpfCnpj,
        'nome_razao' => 'João da Silva',
    ]);
    return $ind;
}

// ── Cadastro ──────────────────────────────────────────────────────────────

test('pode cadastrar indisponibilidade com partes e matrículas', function () {
    $response = $this->actingAs(usuarioIndisp(), 'web')
        ->postJson('/api/v1/indisponibilidades', [
            'status'                      => 'pendente',
            'protocolo_indisponibilidade' => 'PROT-001',
            'numero_processo'             => '0001234-56.2026.8.11.0001',
            'forum_vara'                  => '1ª Vara Cível',
            'partes' => [
                [
                    'cpf_cnpj'   => '52998224725',
                    'nome_razao' => 'João da Silva',
                    'matriculas' => [
                        ['matricula' => '12345-A'],
                        ['matricula' => '67890-B'],
                    ],
                ],
            ],
        ]);

    $response->assertStatus(201)
             ->assertJsonPath('sucesso', true)
             ->assertJsonPath('dados.protocolo_indisponibilidade', 'PROT-001')
             ->assertJsonPath('dados.status', 'pendente');

    expect(IndisponibilidadeParte::where('cpf_cnpj', '52998224725')->count())->toBe(1);
});

test('protocolo_indisponibilidade único — duplicata retorna erro 422', function () {
    criarIndisponibilidade(['protocolo_indisponibilidade' => 'PROT-DUP']);

    $response = $this->actingAs(usuarioIndisp(), 'web')
        ->postJson('/api/v1/indisponibilidades', [
            'status'                      => 'pendente',
            'protocolo_indisponibilidade' => 'PROT-DUP',
        ]);

    $response->assertStatus(422);
});

// ── Listagem ──────────────────────────────────────────────────────────────

test('listagem retorna registros paginados', function () {
    criarIndisponibilidade(['protocolo_indisponibilidade' => 'PROT-A']);
    criarIndisponibilidade(['protocolo_indisponibilidade' => 'PROT-B']);

    $response = $this->actingAs(usuarioIndisp(), 'web')
        ->getJson('/api/v1/indisponibilidades');

    $response->assertStatus(200)
             ->assertJsonPath('sucesso', true);

    expect($response->json('dados.total'))->toBeGreaterThanOrEqual(2);
});

test('filtro por cpf_cnpj retorna indisponibilidades corretas', function () {
    criarIndisponibilidadeComParte('52998224725', ['protocolo_indisponibilidade' => 'PROT-CPF1']);
    criarIndisponibilidadeComParte('11222333000181', ['protocolo_indisponibilidade' => 'PROT-CPF2']);

    $response = $this->actingAs(usuarioIndisp(), 'web')
        ->getJson('/api/v1/indisponibilidades?cpf_cnpj=52998224725');

    $response->assertStatus(200);
    $protocolos = collect($response->json('dados.data'))->pluck('protocolo_indisponibilidade');
    expect($protocolos)->toContain('PROT-CPF1');
    expect($protocolos)->not->toContain('PROT-CPF2');
});

// ── Cancelamento ──────────────────────────────────────────────────────────

test('cancelamento preenche campos de cancelamento corretamente', function () {
    $ind = criarIndisponibilidade(['protocolo_indisponibilidade' => 'PROT-CANCEL']);

    $response = $this->actingAs(usuarioIndisp(), 'web')
        ->postJson("/api/v1/indisponibilidades/{$ind->id}/cancelar", [
            'cancelamento_protocolo' => 'CANCEL-001',
            'cancelamento_tipo'      => 1,
            'cancelamento_data'      => '2026-03-01',
        ]);

    $response->assertStatus(200)
             ->assertJsonPath('dados.status', 'cancelada')
             ->assertJsonPath('dados.cancelamento_protocolo', 'CANCEL-001');

    $ind->refresh();
    expect($ind->cancelamento_protocolo)->toBe('CANCEL-001');
    expect($ind->cancelamento_tipo)->toBe(1);
});

// ── Soft delete ───────────────────────────────────────────────────────────

test('soft delete preenche data_exclusao', function () {
    $ind = criarIndisponibilidade(['protocolo_indisponibilidade' => 'PROT-DEL']);

    $this->actingAs(usuarioIndisp(), 'web')
        ->deleteJson("/api/v1/indisponibilidades/{$ind->id}")
        ->assertStatus(200);

    expect(Indisponibilidade::withTrashed()->find($ind->id)->data_exclusao)->not->toBeNull();
});

// ── Busca por CPF/CNPJ ────────────────────────────────────────────────────

test('busca por cpf_cnpj via endpoint específico retorna resultados corretos', function () {
    criarIndisponibilidadeComParte('52998224725', ['protocolo_indisponibilidade' => 'PROT-BUSCA1']);
    criarIndisponibilidadeComParte('52998224725', ['protocolo_indisponibilidade' => 'PROT-BUSCA2']);
    criarIndisponibilidadeComParte('11222333000181', ['protocolo_indisponibilidade' => 'PROT-OUTRO']);

    $response = $this->actingAs(usuarioIndisp(), 'web')
        ->getJson('/api/v1/indisponibilidades/cpf-cnpj/52998224725');

    $response->assertStatus(200);
    $protocolos = collect($response->json('dados'))->pluck('protocolo_indisponibilidade');
    expect($protocolos)->toHaveCount(2);
    expect($protocolos)->toContain('PROT-BUSCA1');
    expect($protocolos)->toContain('PROT-BUSCA2');
    expect($protocolos)->not->toContain('PROT-OUTRO');
});

// ── Phase 05: Integração com IndicadorPessoal ─────────────────────────────

test('listagem de indicador pessoal inclui indisponibilidades_count correto', function () {
    $indicador = IndicadorPessoal::create([
        'tipo_pessoa' => 'F',
        'cpf_cnpj'   => '52998224725',
        'nome'       => 'João Teste',
        'versao'     => 1,
        'is_atual'   => true,
        'data_versao' => now(),
    ]);

    // 2 ativas, 1 cancelada
    criarIndisponibilidadeComParte('52998224725', ['protocolo_indisponibilidade' => 'PROT-COUNT1', 'status' => 'pendente']);
    criarIndisponibilidadeComParte('52998224725', ['protocolo_indisponibilidade' => 'PROT-COUNT2', 'status' => 'cumprida']);
    criarIndisponibilidadeComParte('52998224725', ['protocolo_indisponibilidade' => 'PROT-COUNT3', 'status' => 'cancelada']);

    $response = $this->actingAs(usuarioIndisp(), 'web')
        ->getJson('/api/v1/indicador-pessoal');

    $response->assertStatus(200);
    $registro = collect($response->json('dados.data'))->firstWhere('cpf_cnpj', '52998224725');
    expect($registro['indisponibilidades_count'])->toBe(2);
});

test('count não inclui indisponibilidades canceladas ou excluídas', function () {
    IndicadorPessoal::create([
        'tipo_pessoa' => 'F',
        'cpf_cnpj'   => '52998224725',
        'nome'       => 'João Teste',
        'versao'     => 1,
        'is_atual'   => true,
        'data_versao' => now(),
    ]);

    $ativa = criarIndisponibilidadeComParte('52998224725', ['protocolo_indisponibilidade' => 'PROT-OK']);
    $cancelada = criarIndisponibilidadeComParte('52998224725', ['protocolo_indisponibilidade' => 'PROT-CANC', 'status' => 'cancelada']);
    $excluida = criarIndisponibilidadeComParte('52998224725', ['protocolo_indisponibilidade' => 'PROT-EXCL']);
    $excluida->delete();

    $response = $this->actingAs(usuarioIndisp(), 'web')
        ->getJson('/api/v1/indicador-pessoal');

    $registro = collect($response->json('dados.data'))->firstWhere('cpf_cnpj', '52998224725');
    expect($registro['indisponibilidades_count'])->toBe(1);
});

test('detalhe do indicador pessoal inclui lista de indisponibilidades ativas', function () {
    $indicador = IndicadorPessoal::create([
        'tipo_pessoa' => 'F',
        'cpf_cnpj'   => '52998224725',
        'nome'       => 'João Teste',
        'versao'     => 1,
        'is_atual'   => true,
        'data_versao' => now(),
    ]);

    criarIndisponibilidadeComParte('52998224725', ['protocolo_indisponibilidade' => 'PROT-SHOW1']);
    criarIndisponibilidadeComParte('52998224725', ['protocolo_indisponibilidade' => 'PROT-SHOW2', 'status' => 'cancelada']);

    $response = $this->actingAs(usuarioIndisp(), 'web')
        ->getJson("/api/v1/indicador-pessoal/{$indicador->id}");

    $response->assertStatus(200);
    $indisp = collect($response->json('dados.indisponibilidades'));
    expect($indisp)->toHaveCount(1);
    expect($indisp->first()['protocolo_indisponibilidade'])->toBe('PROT-SHOW1');
});
