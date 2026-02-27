<?php

use App\Models\User;

// =============================================================================
// LOGIN
// =============================================================================

test('usuário pode fazer login com credenciais válidas', function () {
    $usuario = User::factory()->create(['senha' => 'password']);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => $usuario->email,
        'senha' => 'password',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'sucesso',
            'mensagem',
            'dados' => ['id', 'nome', 'email', 'is_ativo'],
        ])
        ->assertJson(['sucesso' => true]);
});

test('login com credenciais inválidas retorna 401', function () {
    User::factory()->create(['email' => 'teste@oslo.local', 'senha' => 'password']);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'teste@oslo.local',
        'senha' => 'senha_errada',
    ]);

    $response->assertStatus(401)
        ->assertJson(['sucesso' => false]);
});

test('login com campos vazios retorna 422', function () {
    $response = $this->postJson('/api/v1/auth/login', []);

    $response->assertStatus(422)
        ->assertJson(['sucesso' => false])
        ->assertJsonStructure(['erros' => ['email', 'senha']]);
});

test('login com e-mail inválido retorna 422', function () {
    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'nao-e-um-email',
        'senha'  => 'password',
    ]);

    $response->assertStatus(422)
        ->assertJsonStructure(['erros' => ['email']]);
});

// =============================================================================
// ME
// =============================================================================

test('usuário autenticado pode acessar /api/v1/auth/me', function () {
    $usuario = User::factory()->create();

    $response = $this->actingAs($usuario, 'web')
        ->getJson('/api/v1/auth/me');

    $response->assertOk()
        ->assertJson([
            'sucesso' => true,
            'dados'   => [
                'id'    => $usuario->id,
                'email' => $usuario->email,
                'nome'  => $usuario->nome,
            ],
        ]);
});

test('usuário não autenticado recebe 401 ao acessar rota protegida', function () {
    $response = $this->getJson('/api/v1/auth/me');

    $response->assertStatus(401)
        ->assertJson(['sucesso' => false]);
});

// =============================================================================
// LOGOUT
// =============================================================================

test('logout encerra a sessão corretamente', function () {
    $usuario = User::factory()->create();

    $this->actingAs($usuario, 'web')
        ->postJson('/api/v1/auth/logout')
        ->assertOk()
        ->assertJson(['sucesso' => true]);
});

// =============================================================================
// REFRESH
// =============================================================================

test('refresh renova a sessão e retorna dados do usuário', function () {
    $usuario = User::factory()->create();

    $response = $this->actingAs($usuario, 'web')
        ->postJson('/api/v1/auth/refresh');

    $response->assertOk()
        ->assertJson(['sucesso' => true])
        ->assertJsonStructure(['dados' => ['id', 'nome', 'email']]);
});
