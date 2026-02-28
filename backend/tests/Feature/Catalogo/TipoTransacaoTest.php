<?php

use App\Models\MotivoTransacao;
use App\Models\TipoTransacao;
use App\Models\User;

function usuarioCatalogo(): User
{
    return User::factory()->create();
}

function criarTipoTransacao(array $attrs = []): TipoTransacao
{
    return TipoTransacao::create(array_merge([
        'descricao' => 'Recebimento ' . uniqid(),
        'tipo'      => 'entrada',
        'is_ativo'  => true,
    ], $attrs));
}

function criarMotivoTransacao(TipoTransacao $tipo, array $attrs = []): MotivoTransacao
{
    return MotivoTransacao::create(array_merge([
        'tipo_transacao_id' => $tipo->id,
        'descricao'         => 'Motivo ' . uniqid(),
        'is_ativo'          => true,
    ], $attrs));
}

// ── Listar tipos ──────────────────────────────────────────────────────────

test('lista tipos de transação ativos com motivos', function () {
    $tipo = criarTipoTransacao(['descricao' => 'Tipo Teste Lista']);
    criarMotivoTransacao($tipo, ['descricao' => 'Motivo A']);
    criarMotivoTransacao($tipo, ['descricao' => 'Motivo B']);

    $response = $this->actingAs(usuarioCatalogo(), 'web')
        ->getJson('/api/v1/tipos-transacao');

    $response->assertStatus(200)
             ->assertJsonPath('sucesso', true);

    $tipos = collect($response->json('dados'));
    $tipoEncontrado = $tipos->firstWhere('descricao', 'Tipo Teste Lista');
    expect($tipoEncontrado)->not->toBeNull();
    expect(count($tipoEncontrado['motivos_transacao']))->toBe(2);
});

test('tipos inativos não aparecem na listagem', function () {
    criarTipoTransacao(['descricao' => 'Tipo Inativo', 'is_ativo' => false]);

    $response = $this->actingAs(usuarioCatalogo(), 'web')
        ->getJson('/api/v1/tipos-transacao');

    $response->assertStatus(200);
    $tipos = collect($response->json('dados'));
    expect($tipos->firstWhere('descricao', 'Tipo Inativo'))->toBeNull();
});

test('requer autenticação para listar tipos', function () {
    $this->getJson('/api/v1/tipos-transacao')
         ->assertStatus(401);
});

// ── Motivos por tipo ───────────────────────────────────────────────────────

test('busca motivos ativos pelo tipo da transação', function () {
    $tipo = criarTipoTransacao(['descricao' => 'Tipo Motivos', 'tipo' => 'saida']);
    criarMotivoTransacao($tipo, ['descricao' => 'Motivo Ativo']);
    criarMotivoTransacao($tipo, ['descricao' => 'Motivo Inativo', 'is_ativo' => false]);

    $response = $this->actingAs(usuarioCatalogo(), 'web')
        ->getJson('/api/v1/tipos-transacao/saida/motivos');

    $response->assertStatus(200)
             ->assertJsonPath('sucesso', true);

    $motivos = collect($response->json('dados'));
    expect($motivos->pluck('descricao'))->toContain('Motivo Ativo');
    expect($motivos->pluck('descricao'))->not->toContain('Motivo Inativo');
});

test('tipo inexistente retorna 404', function () {
    $this->actingAs(usuarioCatalogo(), 'web')
        ->getJson('/api/v1/tipos-transacao/inexistente/motivos')
        ->assertStatus(404);
});
