<?php

use App\Models\AuditoriaTransacao;
use App\Models\IndicadorPessoal;
use App\Models\Transacao;
use App\Models\TipoTransacao;
use App\Models\User;

function usuarioTransacao(): User
{
    return User::factory()->create();
}

function indicadorParaTransacao(array $attrs = []): IndicadorPessoal
{
    return IndicadorPessoal::create(array_merge([
        'tipo_pessoa'  => 'F',
        'cpf_cnpj'    => '529982247' . rand(10, 99),
        'nome'         => 'Titular Teste',
        'versao'       => 1,
        'is_atual'     => true,
        'data_versao'  => now(),
    ], $attrs));
}

function tipoEntrada(): TipoTransacao
{
    return TipoTransacao::create([
        'descricao' => 'Recebimento ' . uniqid(),
        'tipo'      => 'entrada',
        'is_ativo'  => true,
    ]);
}

function tipoSaida(): TipoTransacao
{
    return TipoTransacao::create([
        'descricao' => 'Pagamento ' . uniqid(),
        'tipo'      => 'saida',
        'is_ativo'  => true,
    ]);
}

function criarTransacao(array $attrs = []): Transacao
{
    $indicador = indicadorParaTransacao();
    $tipo      = tipoEntrada();

    return Transacao::create(array_merge([
        'indicador_pessoal_id' => $indicador->id,
        'tipo_transacao_id'    => $tipo->id,
        'numero_transacao'     => 'TRX-' . uniqid(),
        'descricao'            => 'Transação de teste',
        'valor'                => 100.00,
        'data_transacao'       => now()->toDateString(),
        'situacao'             => 'pendente',
        'is_ativo'             => true,
    ], $attrs));
}

// ── Criação ───────────────────────────────────────────────────────────────

test('pode criar transação com dados válidos', function () {
    $indicador = indicadorParaTransacao(['cpf_cnpj' => '52998224725']);
    $tipo      = tipoEntrada();
    $usuario   = usuarioTransacao();

    $response = $this->actingAs($usuario, 'web')
        ->postJson('/api/v1/transacoes', [
            'indicador_pessoal_id' => $indicador->id,
            'tipo_transacao_id'    => $tipo->id,
            'descricao'            => 'Emolumentos recebidos',
            'valor'                => 250.50,
            'data_transacao'       => '2026-03-07',
        ]);

    $response->assertStatus(201)
             ->assertJsonPath('sucesso', true)
             ->assertJsonPath('dados.situacao', 'pendente')
             ->assertJsonPath('dados.descricao', 'Emolumentos recebidos');

    expect($response->json('dados.numero_transacao'))->toStartWith('TRX-');
});

test('número de transação é gerado automaticamente', function () {
    $indicador = indicadorParaTransacao(['cpf_cnpj' => '11222333000181']);
    $tipo      = tipoEntrada();

    $r1 = $this->actingAs(usuarioTransacao(), 'web')
        ->postJson('/api/v1/transacoes', [
            'indicador_pessoal_id' => $indicador->id,
            'tipo_transacao_id'    => $tipo->id,
            'descricao'            => 'Transação 1',
            'valor'                => 100.00,
            'data_transacao'       => now()->toDateString(),
        ]);

    $r2 = $this->actingAs(usuarioTransacao(), 'web')
        ->postJson('/api/v1/transacoes', [
            'indicador_pessoal_id' => $indicador->id,
            'tipo_transacao_id'    => $tipo->id,
            'descricao'            => 'Transação 2',
            'valor'                => 200.00,
            'data_transacao'       => now()->toDateString(),
        ]);

    expect($r1->json('dados.numero_transacao'))->not->toBe($r2->json('dados.numero_transacao'));
});

test('validação falha sem campos obrigatórios', function () {
    $this->actingAs(usuarioTransacao(), 'web')
        ->postJson('/api/v1/transacoes', [])
        ->assertStatus(422);
});

test('validação falha quando indicador_pessoal_id não existe', function () {
    $tipo = tipoEntrada();

    $this->actingAs(usuarioTransacao(), 'web')
        ->postJson('/api/v1/transacoes', [
            'indicador_pessoal_id' => 99999,
            'tipo_transacao_id'    => $tipo->id,
            'descricao'            => 'Teste',
            'valor'                => 10.00,
            'data_transacao'       => now()->toDateString(),
        ])
        ->assertStatus(422);
});

test('validação falha quando valor é zero', function () {
    $indicador = indicadorParaTransacao(['cpf_cnpj' => '52998224725']);
    $tipo      = tipoEntrada();

    $this->actingAs(usuarioTransacao(), 'web')
        ->postJson('/api/v1/transacoes', [
            'indicador_pessoal_id' => $indicador->id,
            'tipo_transacao_id'    => $tipo->id,
            'descricao'            => 'Teste zero',
            'valor'                => 0,
            'data_transacao'       => now()->toDateString(),
        ])
        ->assertStatus(422);
});

// ── Listagem ──────────────────────────────────────────────────────────────

test('listagem retorna transações paginadas', function () {
    criarTransacao();
    criarTransacao();

    $response = $this->actingAs(usuarioTransacao(), 'web')
        ->getJson('/api/v1/transacoes');

    $response->assertStatus(200)
             ->assertJsonPath('sucesso', true);

    expect($response->json('dados.total'))->toBeGreaterThanOrEqual(2);
});

test('filtro por situação funciona corretamente', function () {
    $t1 = criarTransacao(['situacao' => 'pendente',   'descricao' => 'Pendente Test']);
    $t2 = criarTransacao(['situacao' => 'confirmada', 'descricao' => 'Confirmada Test']);

    $response = $this->actingAs(usuarioTransacao(), 'web')
        ->getJson('/api/v1/transacoes?situacao=pendente');

    $response->assertStatus(200);
    $desc = collect($response->json('dados.data'))->pluck('descricao');
    expect($desc)->toContain('Pendente Test');
    expect($desc)->not->toContain('Confirmada Test');
});

// ── Detalhe ───────────────────────────────────────────────────────────────

test('detalhe retorna transação com relacionamentos', function () {
    $transacao = criarTransacao();

    $response = $this->actingAs(usuarioTransacao(), 'web')
        ->getJson("/api/v1/transacoes/{$transacao->id}");

    $response->assertStatus(200)
             ->assertJsonPath('sucesso', true)
             ->assertJsonPath('dados.id', $transacao->id);

    expect($response->json('dados.tipo_transacao'))->not->toBeNull();
    expect($response->json('dados.indicador_pessoal'))->not->toBeNull();
    expect($response->json('dados.auditorias'))->toBeArray();
});

test('retorna 404 para transação inexistente', function () {
    $this->actingAs(usuarioTransacao(), 'web')
        ->getJson('/api/v1/transacoes/99999')
        ->assertStatus(404);
});

// ── Atualização ───────────────────────────────────────────────────────────

test('pode atualizar transação pendente', function () {
    $transacao = criarTransacao(['situacao' => 'pendente']);

    $response = $this->actingAs(usuarioTransacao(), 'web')
        ->putJson("/api/v1/transacoes/{$transacao->id}", [
            'descricao' => 'Descrição atualizada',
            'valor'     => 999.99,
        ]);

    $response->assertStatus(200)
             ->assertJsonPath('dados.descricao', 'Descrição atualizada');
});

test('não pode atualizar transação confirmada', function () {
    $transacao = criarTransacao(['situacao' => 'confirmada']);

    $this->actingAs(usuarioTransacao(), 'web')
        ->putJson("/api/v1/transacoes/{$transacao->id}", [
            'descricao' => 'Tentativa de edição',
        ])
        ->assertStatus(422);
});

// ── Confirmar ─────────────────────────────────────────────────────────────

test('pode confirmar transação pendente', function () {
    $transacao = criarTransacao(['situacao' => 'pendente']);

    $response = $this->actingAs(usuarioTransacao(), 'web')
        ->postJson("/api/v1/transacoes/{$transacao->id}/confirmar");

    $response->assertStatus(200)
             ->assertJsonPath('dados.situacao', 'confirmada');

    $transacao->refresh();
    expect($transacao->situacao)->toBe('confirmada');
    expect($transacao->data_confirmacao)->not->toBeNull();
});

test('não pode confirmar transação já confirmada', function () {
    $transacao = criarTransacao(['situacao' => 'confirmada']);

    $this->actingAs(usuarioTransacao(), 'web')
        ->postJson("/api/v1/transacoes/{$transacao->id}/confirmar")
        ->assertStatus(422);
});

// ── Auditoria ─────────────────────────────────────────────────────────────

test('criação de transação gera registro de auditoria', function () {
    $indicador = indicadorParaTransacao(['cpf_cnpj' => '52998224725']);
    $tipo      = tipoEntrada();
    $usuario   = usuarioTransacao();

    $response = $this->actingAs($usuario, 'web')
        ->postJson('/api/v1/transacoes', [
            'indicador_pessoal_id' => $indicador->id,
            'tipo_transacao_id'    => $tipo->id,
            'descricao'            => 'Teste auditoria',
            'valor'                => 50.00,
            'data_transacao'       => now()->toDateString(),
        ]);

    $transacaoId = $response->json('dados.id');
    expect(AuditoriaTransacao::where('transacao_id', $transacaoId)->where('acao', 'criacao')->count())->toBe(1);
});

test('confirmação gera registro de auditoria', function () {
    $transacao = criarTransacao();
    $usuario   = usuarioTransacao();

    $this->actingAs($usuario, 'web')
        ->postJson("/api/v1/transacoes/{$transacao->id}/confirmar");

    expect(
        AuditoriaTransacao::where('transacao_id', $transacao->id)
            ->where('acao', 'confirmacao')
            ->count()
    )->toBe(1);
});

test('exclusão soft-deletes a transação', function () {
    $transacao = criarTransacao();

    $this->actingAs(usuarioTransacao(), 'web')
        ->deleteJson("/api/v1/transacoes/{$transacao->id}")
        ->assertStatus(200);

    expect(Transacao::withTrashed()->find($transacao->id)->data_exclusao)->not->toBeNull();
});

// ── Resumo ────────────────────────────────────────────────────────────────

test('resumo retorna totais por indicador pessoal', function () {
    $indicador = indicadorParaTransacao(['cpf_cnpj' => '52998224725']);
    $tipoE     = tipoEntrada();
    $tipoS     = tipoSaida();

    Transacao::create([
        'indicador_pessoal_id' => $indicador->id,
        'tipo_transacao_id'    => $tipoE->id,
        'numero_transacao'     => 'TRX-E-001',
        'descricao'            => 'Entrada',
        'valor'                => 500.00,
        'data_transacao'       => now()->toDateString(),
        'situacao'             => 'confirmada',
        'is_ativo'             => true,
    ]);

    Transacao::create([
        'indicador_pessoal_id' => $indicador->id,
        'tipo_transacao_id'    => $tipoS->id,
        'numero_transacao'     => 'TRX-S-001',
        'descricao'            => 'Saída',
        'valor'                => 200.00,
        'data_transacao'       => now()->toDateString(),
        'situacao'             => 'confirmada',
        'is_ativo'             => true,
    ]);

    $response = $this->actingAs(usuarioTransacao(), 'web')
        ->getJson("/api/v1/transacoes/resumo?indicador_pessoal_id={$indicador->id}");

    $response->assertStatus(200)
             ->assertJsonPath('sucesso', true);

    $resumo = $response->json('dados');
    expect((float) $resumo['total_entradas'])->toBe(500.00);
    expect((float) $resumo['total_saidas'])->toBe(200.00);
    expect((float) $resumo['saldo'])->toBe(300.00);
});

test('resumo retorna 422 sem indicador_pessoal_id', function () {
    $this->actingAs(usuarioTransacao(), 'web')
        ->getJson('/api/v1/transacoes/resumo')
        ->assertStatus(422);
});
