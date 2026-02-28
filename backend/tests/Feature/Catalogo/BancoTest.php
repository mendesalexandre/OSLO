<?php

use App\Models\Banco;
use App\Models\User;

function usuarioBanco(): User
{
    return User::factory()->create();
}

// ── Listar bancos ─────────────────────────────────────────────────────────

test('lista bancos ativos ordenados por nome', function () {
    Banco::create(['nome' => 'Banco Z Ativo',   'sigla' => 'BZA', 'is_ativo' => true]);
    Banco::create(['nome' => 'Banco A Ativo',   'sigla' => 'BAA', 'is_ativo' => true]);
    Banco::create(['nome' => 'Banco Inativo',   'sigla' => 'BI',  'is_ativo' => false]);

    $response = $this->actingAs(usuarioBanco(), 'web')
        ->getJson('/api/v1/bancos');

    $response->assertStatus(200)
             ->assertJsonPath('sucesso', true);

    $nomes = collect($response->json('dados'))->pluck('nome');
    expect($nomes)->toContain('Banco Z Ativo');
    expect($nomes)->toContain('Banco A Ativo');
    expect($nomes)->not->toContain('Banco Inativo');

    // Verifica ordenação por nome
    $index_a = $nomes->search('Banco A Ativo');
    $index_z = $nomes->search('Banco Z Ativo');
    expect($index_a)->toBeLessThan($index_z);
});

test('retorna lista vazia quando não há bancos ativos', function () {
    $response = $this->actingAs(usuarioBanco(), 'web')
        ->getJson('/api/v1/bancos');

    $response->assertStatus(200)
             ->assertJsonPath('sucesso', true);
    expect($response->json('dados'))->toBeArray();
});

test('requer autenticação para listar bancos', function () {
    $this->getJson('/api/v1/bancos')
         ->assertStatus(401);
});
