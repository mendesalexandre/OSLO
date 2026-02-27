# PROMPT 3 — Sistema de Permissões e Controle de Acesso (OSLO)

Cole este prompt no Claude CLI na raiz do projeto OSLO. Executar APÓS os Prompts 1 e 2.

---

```
Você está no projeto OSLO, um sistema de gestão para cartório de registro de imóveis (1º Ofício de Registro de Imóveis de Sinop/MT), desenvolvido em Laravel + Quasar 2 (Vue 3) + PostgreSQL.

## OBJETIVO

Criar um sistema completo de permissões **sem Spatie**, com:
- Grupos de permissões (ex: Administrador, Atendente, Caixa)
- Permissões individuais em CAIXA ALTA (ex: `PERMITIR_PROTOCOLO_CRIAR`)
- Cada permissão tem: nome (caixa alta), descrição e módulo
- Usuário pertence a grupo(s) + pode ter permissões individuais extras
- Controle no backend (middleware + verificação no controller)
- Controle no frontend (composable + filtragem de menu + proteção de rota)
- Permissões carregadas no login junto com o token JWT

## REGRAS GERAIS

### Nomenclatura
- Tudo em **português**
- **Tabelas no singular**: `grupo`, `permissao`, `grupo_permissao`, `usuario_grupo`, `usuario_permissao`
- **Rotas no singular**: `/api/grupo`, `/api/permissao`

### Timestamps em Português
TODAS as tabelas usam:
- `data_cadastro` (não `created_at`)
- `data_alteracao` (não `updated_at`)
- `data_exclusao` (não `deleted_at`)

Usar trait `TimestampsPortugues` em TODOS os Models (já criada nos prompts anteriores).

### Padrão de API
Usar trait `RespostaApi` em TODOS os controllers (já criada nos prompts anteriores):
`{ sucesso: true/false, mensagem: "...", dados: ... }`

---

## FASE 1 — ANÁLISE (EXECUTAR PRIMEIRO, NÃO PULAR)

1. Ler `composer.json` — verificar se Spatie está instalado
2. Listar `database/migrations/` — migrations do Spatie?
3. Ler Model `User` — traits do Spatie? (`HasRoles`, `HasPermissions`)
4. Verificar `config/permission.php`
5. Ler `app/Http/Kernel.php` ou `bootstrap/app.php` — middlewares
6. Ler `routes/api.php` — middleware de permissão do Spatie?
7. Ler controllers — `$this->authorize()`, `can()`, middleware Spatie?
8. Verificar traits `RespostaApi`, `TimestampsPortugues`, `Auditavel`
9. **Frontend**:
   - `src/router/` — rotas
   - `src/layouts/` — layout principal, sidebar/drawer
   - `src/stores/` ou `src/composables/` — stores Pinia
   - `src/boot/` — boot files (axios, auth)
   - `src/pages/` — listar páginas
10. Verificar fluxo de login atual (controller, store, token)

Apresentar resumo completo. Se Spatie encontrado, listar TODOS os usos.

---

## FASE 2 — REMOÇÃO DO SPATIE (se existir)

1. Mapear TUDO: Model User, Controllers, Middleware, Config, Migrations
2. Migration para dropar tabelas Spatie
3. `composer remove spatie/laravel-permission`
4. Remover `config/permission.php`
5. Remover traits do Model User

Se NÃO existir, pular.

---

## FASE 3 — MODELAGEM DO BANCO DE DADOS

### 3.1 Tabela: `grupo`
```
grupo
├── id                  BIGSERIAL PK
├── nome                VARCHAR(100) NOT NULL UNIQUE
├── descricao           TEXT NULL
├── ativo               BOOLEAN NOT NULL DEFAULT TRUE
├── data_cadastro       TIMESTAMP NULL
├── data_alteracao      TIMESTAMP NULL
└── data_exclusao       TIMESTAMP NULL
```

### 3.2 Tabela: `permissao`
```
permissao
├── id                  BIGSERIAL PK
├── nome                VARCHAR(100) NOT NULL UNIQUE  -- "PERMITIR_PROTOCOLO_CRIAR"
├── descricao           VARCHAR(300) NOT NULL         -- "Permite criar novos protocolos"
├── modulo              VARCHAR(50) NOT NULL           -- "PROTOCOLO", "CONTRATO", "FINANCEIRO"
├── ativo               BOOLEAN NOT NULL DEFAULT TRUE
├── data_cadastro       TIMESTAMP NULL
├── data_alteracao      TIMESTAMP NULL
└── data_exclusao       TIMESTAMP NULL
```

### 3.3 Tabela: `grupo_permissao`
```
grupo_permissao
├── id                  BIGSERIAL PK
├── grupo_id            BIGINT FK → grupo.id NOT NULL
├── permissao_id        BIGINT FK → permissao.id NOT NULL
├── data_cadastro       TIMESTAMP NULL
└── UNIQUE (grupo_id, permissao_id)
```

### 3.4 Tabela: `usuario_grupo`
```
usuario_grupo
├── id                  BIGSERIAL PK
├── usuario_id          BIGINT FK → usuario.id NOT NULL
├── grupo_id            BIGINT FK → grupo.id NOT NULL
├── data_cadastro       TIMESTAMP NULL
└── UNIQUE (usuario_id, grupo_id)
```

### 3.5 Tabela: `usuario_permissao`
```
usuario_permissao
├── id                  BIGSERIAL PK
├── usuario_id          BIGINT FK → usuario.id NOT NULL
├── permissao_id        BIGINT FK → permissao.id NOT NULL
├── tipo                VARCHAR(10) NOT NULL DEFAULT 'permitir'  -- 'permitir' ou 'negar'
├── data_cadastro       TIMESTAMP NULL
└── UNIQUE (usuario_id, permissao_id)
```

---

## FASE 4 — IMPLEMENTAÇÃO BACKEND

### 4.1 Migrations (todas com `data_cadastro`, `data_alteracao`, `data_exclusao`)

1. `criar_tabela_grupo`
2. `criar_tabela_permissao`
3. `criar_tabela_grupo_permissao`
4. `criar_tabela_usuario_grupo`
5. `criar_tabela_usuario_permissao`

Se existir `php artisan auditoria:migration`, aplicar em todas.

### 4.2 Models

`Grupo` e `Permissao`:
- `use SoftDeletes, TimestampsPortugues, Auditavel;`
- Relacionamentos em português

### 4.3 Atualizar Model User

Adicionar (SEM remover nada existente):

```php
// Relacionamentos
public function grupos()                // belongsToMany Grupo via usuario_grupo
public function permissoesIndividuais() // belongsToMany Permissao via usuario_permissao (com pivot 'tipo')

// Verificação
public function temPermissao(string $nomePermissao): bool
// 1. Individual 'negar' → FALSE
// 2. Individual 'permitir' → TRUE
// 3. Grupo tem → TRUE/FALSE

public function temAlgumaPermissao(array $permissoes): bool  // OR
public function temTodasPermissoes(array $permissoes): bool   // AND
public function pertenceAoGrupo(string $nomeGrupo): bool
public function eAdmin(): bool

// Carregar permissões
public function obterPermissoes(): array        // ['PERMITIR_PROTOCOLO_CRIAR', ...]
public function obterPermissoesPorModulo(): array  // { PROTOCOLO: [...], CONTRATO: [...] }
```

### 4.4 Trait: `VerificaPermissao`

```php
trait VerificaPermissao
{
    protected function verificarPermissao(string $permissao): void
    {
        if (!auth()->user()->temPermissao($permissao)) {
            abort(403, "Sem permissão: {$permissao}");
        }
    }

    protected function verificarAlgumaPermissao(array $permissoes): void
    {
        if (!auth()->user()->temAlgumaPermissao($permissoes)) {
            abort(403, 'Sem permissão para esta ação');
        }
    }
}
```

### 4.5 Middleware: `ChecarPermissao`

```php
// Uso: ->middleware('permissao:PERMITIR_PROTOCOLO_CRIAR')
// Múltiplas (OR): ->middleware('permissao:PERMITIR_PROTOCOLO_CRIAR,PERMITIR_PROTOCOLO_LISTAR')
// Administrador bypassa tudo
// Retorno 403 no padrão { sucesso: false, mensagem: '...', permissoes_necessarias: [...] }
```

Registrar com alias `permissao`.

### 4.6 Adaptar Login (AuthController)

Login retorna permissões junto com o token:

```php
return response()->json([
    'sucesso' => true,
    'mensagem' => 'Login realizado com sucesso',
    'dados' => [
        'token' => $token,
        'tipo' => 'bearer',
        'expira_em' => auth()->factory()->getTTL() * 60,
        'usuario' => [
            'id' => $usuario->id,
            'nome' => $usuario->nome,
            'email' => $usuario->email,
        ],
        'permissoes' => $usuario->obterPermissoes(),
        'modulos' => $usuario->obterPermissoesPorModulo(),
        'grupos' => $usuario->grupos->pluck('nome')->toArray(),
    ],
]);
```

Também no `me()` e `refresh()`.

### 4.7 Controllers CRUD

`GrupoController` (com `RespostaApi` + `VerificaPermissao`):
- `listar`, `criar`, `exibir`, `atualizar`, `excluir`, `restaurar`
- `sincronizarPermissoes($id)`, `adicionarPermissao($id, $permissaoId)`, `removerPermissao($id, $permissaoId)`

`PermissaoController`:
- `listar`, `criar`, `exibir`, `atualizar`, `excluir`, `restaurar`
- `listarModulos`, `listarPorModulo($modulo)`

`UsuarioPermissaoController`:
- `listar($usuarioId)`, `permissoesEfetivas($usuarioId)`
- `sincronizarGrupos($usuarioId)`, `adicionarGrupo`, `removerGrupo`
- `adicionarPermissao`, `removerPermissao`

### 4.8 Rotas — SINGULAR + middleware permissao

```php
Route::middleware('auth:api')->group(function () {

    Route::prefix('grupo')->group(function () {
        Route::get('/',    [GrupoController::class, 'listar'])->middleware('permissao:PERMITIR_GRUPO_LISTAR');
        Route::post('/',   [GrupoController::class, 'criar'])->middleware('permissao:PERMITIR_GRUPO_CRIAR');
        Route::get('/{id}', [GrupoController::class, 'exibir'])->middleware('permissao:PERMITIR_GRUPO_LISTAR');
        Route::put('/{id}', [GrupoController::class, 'atualizar'])->middleware('permissao:PERMITIR_GRUPO_EDITAR');
        Route::delete('/{id}', [GrupoController::class, 'excluir'])->middleware('permissao:PERMITIR_GRUPO_EXCLUIR');
        Route::post('/{id}/restaurar', [GrupoController::class, 'restaurar'])->middleware('permissao:PERMITIR_GRUPO_EDITAR');
        Route::post('/{id}/permissao/sincronizar', [GrupoController::class, 'sincronizarPermissoes'])->middleware('permissao:PERMITIR_GRUPO_EDITAR');
        Route::post('/{id}/permissao/{permissaoId}', [GrupoController::class, 'adicionarPermissao'])->middleware('permissao:PERMITIR_GRUPO_EDITAR');
        Route::delete('/{id}/permissao/{permissaoId}', [GrupoController::class, 'removerPermissao'])->middleware('permissao:PERMITIR_GRUPO_EDITAR');
    });

    Route::prefix('permissao')->group(function () {
        Route::get('/',          [PermissaoController::class, 'listar'])->middleware('permissao:PERMITIR_PERMISSAO_LISTAR');
        Route::post('/',         [PermissaoController::class, 'criar'])->middleware('permissao:PERMITIR_PERMISSAO_CRIAR');
        Route::get('/modulo',    [PermissaoController::class, 'listarModulos'])->middleware('permissao:PERMITIR_PERMISSAO_LISTAR');
        Route::get('/modulo/{modulo}', [PermissaoController::class, 'listarPorModulo'])->middleware('permissao:PERMITIR_PERMISSAO_LISTAR');
        Route::get('/{id}',      [PermissaoController::class, 'exibir'])->middleware('permissao:PERMITIR_PERMISSAO_LISTAR');
        Route::put('/{id}',      [PermissaoController::class, 'atualizar'])->middleware('permissao:PERMITIR_PERMISSAO_EDITAR');
        Route::delete('/{id}',   [PermissaoController::class, 'excluir'])->middleware('permissao:PERMITIR_PERMISSAO_EXCLUIR');
        Route::post('/{id}/restaurar', [PermissaoController::class, 'restaurar'])->middleware('permissao:PERMITIR_PERMISSAO_EDITAR');
    });

    Route::prefix('usuario/{usuarioId}/permissao')->group(function () {
        Route::get('/',                [UsuarioPermissaoController::class, 'listar'])->middleware('permissao:PERMITIR_USUARIO_PERMISSAO_LISTAR');
        Route::get('/efetiva',         [UsuarioPermissaoController::class, 'permissoesEfetivas'])->middleware('permissao:PERMITIR_USUARIO_PERMISSAO_LISTAR');
        Route::post('/grupo/sincronizar', [UsuarioPermissaoController::class, 'sincronizarGrupos'])->middleware('permissao:PERMITIR_USUARIO_PERMISSAO_EDITAR');
        Route::post('/grupo/{grupoId}',    [UsuarioPermissaoController::class, 'adicionarGrupo'])->middleware('permissao:PERMITIR_USUARIO_PERMISSAO_EDITAR');
        Route::delete('/grupo/{grupoId}',  [UsuarioPermissaoController::class, 'removerGrupo'])->middleware('permissao:PERMITIR_USUARIO_PERMISSAO_EDITAR');
        Route::post('/individual',         [UsuarioPermissaoController::class, 'adicionarPermissao'])->middleware('permissao:PERMITIR_USUARIO_PERMISSAO_EDITAR');
        Route::delete('/individual/{permissaoId}', [UsuarioPermissaoController::class, 'removerPermissao'])->middleware('permissao:PERMITIR_USUARIO_PERMISSAO_EDITAR');
    });

    Route::get('usuario/minhas-permissoes', function () {
        $usuario = auth()->user();
        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Permissões carregadas',
            'dados' => [
                'permissoes' => $usuario->obterPermissoes(),
                'modulos' => $usuario->obterPermissoesPorModulo(),
                'grupos' => $usuario->grupos->pluck('nome')->toArray(),
            ],
        ]);
    });
});
```

### 4.9 Aplicar middleware nas rotas dos prompts anteriores

Voltar nas rotas de protocolo, contrato, financeiro e aplicar middleware `permissao:NOME` em cada rota.

### 4.10 Seeder: `PermissaoSeeder`

```php
$permissoes = [
    // PROTOCOLO
    ['nome' => 'PERMITIR_PROTOCOLO_LISTAR',         'descricao' => 'Permite listar protocolos',                 'modulo' => 'PROTOCOLO'],
    ['nome' => 'PERMITIR_PROTOCOLO_CRIAR',          'descricao' => 'Permite criar novos protocolos',            'modulo' => 'PROTOCOLO'],
    ['nome' => 'PERMITIR_PROTOCOLO_EDITAR',         'descricao' => 'Permite editar protocolos',                 'modulo' => 'PROTOCOLO'],
    ['nome' => 'PERMITIR_PROTOCOLO_EXCLUIR',        'descricao' => 'Permite excluir protocolos',                'modulo' => 'PROTOCOLO'],
    ['nome' => 'PERMITIR_PROTOCOLO_CANCELAR',       'descricao' => 'Permite cancelar protocolos',               'modulo' => 'PROTOCOLO'],
    ['nome' => 'PERMITIR_PROTOCOLO_ITEM_ADICIONAR', 'descricao' => 'Permite adicionar itens ao protocolo',      'modulo' => 'PROTOCOLO'],
    ['nome' => 'PERMITIR_PROTOCOLO_ITEM_REMOVER',   'descricao' => 'Permite remover itens do protocolo',        'modulo' => 'PROTOCOLO'],

    // PAGAMENTO
    ['nome' => 'PERMITIR_PAGAMENTO_REGISTRAR',      'descricao' => 'Permite registrar pagamentos',              'modulo' => 'PAGAMENTO'],
    ['nome' => 'PERMITIR_PAGAMENTO_ESTORNAR',       'descricao' => 'Permite estornar pagamentos',               'modulo' => 'PAGAMENTO'],
    ['nome' => 'PERMITIR_PAGAMENTO_LISTAR',         'descricao' => 'Permite listar pagamentos',                 'modulo' => 'PAGAMENTO'],
    ['nome' => 'PERMITIR_ISENCAO_REGISTRAR',        'descricao' => 'Permite registrar isenções (AJG)',          'modulo' => 'PAGAMENTO'],

    // CONTRATO
    ['nome' => 'PERMITIR_CONTRATO_LISTAR',          'descricao' => 'Permite listar contratos',                  'modulo' => 'CONTRATO'],
    ['nome' => 'PERMITIR_CONTRATO_CRIAR',           'descricao' => 'Permite criar contratos',                   'modulo' => 'CONTRATO'],
    ['nome' => 'PERMITIR_CONTRATO_EDITAR',          'descricao' => 'Permite editar contratos',                  'modulo' => 'CONTRATO'],
    ['nome' => 'PERMITIR_CONTRATO_ALTERAR_STATUS',  'descricao' => 'Permite alterar status do contrato',        'modulo' => 'CONTRATO'],
    ['nome' => 'PERMITIR_CONTRATO_CONCLUIR',        'descricao' => 'Permite concluir contratos',                'modulo' => 'CONTRATO'],
    ['nome' => 'PERMITIR_CONTRATO_CANCELAR',        'descricao' => 'Permite cancelar contratos',                'modulo' => 'CONTRATO'],
    ['nome' => 'PERMITIR_CONTRATO_EXIGENCIA_CRIAR', 'descricao' => 'Permite criar exigências',                  'modulo' => 'CONTRATO'],
    ['nome' => 'PERMITIR_CONTRATO_EXIGENCIA_CUMPRIR','descricao' => 'Permite cumprir exigências',               'modulo' => 'CONTRATO'],

    // RECIBO
    ['nome' => 'PERMITIR_RECIBO_GERAR',             'descricao' => 'Permite gerar recibos',                     'modulo' => 'RECIBO'],
    ['nome' => 'PERMITIR_RECIBO_LISTAR',            'descricao' => 'Permite listar recibos',                    'modulo' => 'RECIBO'],

    // FINANCEIRO
    ['nome' => 'PERMITIR_NATUREZA_LISTAR',          'descricao' => 'Permite listar naturezas',                  'modulo' => 'FINANCEIRO'],
    ['nome' => 'PERMITIR_NATUREZA_CRIAR',           'descricao' => 'Permite criar naturezas',                   'modulo' => 'FINANCEIRO'],
    ['nome' => 'PERMITIR_NATUREZA_EDITAR',          'descricao' => 'Permite editar naturezas',                  'modulo' => 'FINANCEIRO'],
    ['nome' => 'PERMITIR_NATUREZA_EXCLUIR',         'descricao' => 'Permite excluir naturezas',                 'modulo' => 'FINANCEIRO'],
    ['nome' => 'PERMITIR_ATO_LISTAR',               'descricao' => 'Permite listar atos cartorários',           'modulo' => 'FINANCEIRO'],
    ['nome' => 'PERMITIR_ATO_CRIAR',                'descricao' => 'Permite criar atos cartorários',            'modulo' => 'FINANCEIRO'],
    ['nome' => 'PERMITIR_ATO_EDITAR',               'descricao' => 'Permite editar atos cartorários',           'modulo' => 'FINANCEIRO'],
    ['nome' => 'PERMITIR_ATO_EXCLUIR',              'descricao' => 'Permite excluir atos cartorários',          'modulo' => 'FINANCEIRO'],
    ['nome' => 'PERMITIR_FORMA_PAGAMENTO_LISTAR',   'descricao' => 'Permite listar formas de pagamento',        'modulo' => 'FINANCEIRO'],
    ['nome' => 'PERMITIR_FORMA_PAGAMENTO_CRIAR',    'descricao' => 'Permite criar formas de pagamento',         'modulo' => 'FINANCEIRO'],
    ['nome' => 'PERMITIR_FORMA_PAGAMENTO_EDITAR',   'descricao' => 'Permite editar formas de pagamento',        'modulo' => 'FINANCEIRO'],
    ['nome' => 'PERMITIR_FORMA_PAGAMENTO_EXCLUIR',  'descricao' => 'Permite excluir formas de pagamento',       'modulo' => 'FINANCEIRO'],
    ['nome' => 'PERMITIR_MEIO_PAGAMENTO_LISTAR',    'descricao' => 'Permite listar meios de pagamento',         'modulo' => 'FINANCEIRO'],
    ['nome' => 'PERMITIR_MEIO_PAGAMENTO_CRIAR',     'descricao' => 'Permite criar meios de pagamento',          'modulo' => 'FINANCEIRO'],
    ['nome' => 'PERMITIR_MEIO_PAGAMENTO_EDITAR',    'descricao' => 'Permite editar meios de pagamento',         'modulo' => 'FINANCEIRO'],
    ['nome' => 'PERMITIR_MEIO_PAGAMENTO_EXCLUIR',   'descricao' => 'Permite excluir meios de pagamento',        'modulo' => 'FINANCEIRO'],

    // ADMINISTRACAO
    ['nome' => 'PERMITIR_GRUPO_LISTAR',             'descricao' => 'Permite listar grupos',                     'modulo' => 'ADMINISTRACAO'],
    ['nome' => 'PERMITIR_GRUPO_CRIAR',              'descricao' => 'Permite criar grupos',                      'modulo' => 'ADMINISTRACAO'],
    ['nome' => 'PERMITIR_GRUPO_EDITAR',             'descricao' => 'Permite editar grupos e suas permissões',   'modulo' => 'ADMINISTRACAO'],
    ['nome' => 'PERMITIR_GRUPO_EXCLUIR',            'descricao' => 'Permite excluir grupos',                    'modulo' => 'ADMINISTRACAO'],
    ['nome' => 'PERMITIR_PERMISSAO_LISTAR',         'descricao' => 'Permite listar permissões',                 'modulo' => 'ADMINISTRACAO'],
    ['nome' => 'PERMITIR_PERMISSAO_CRIAR',          'descricao' => 'Permite criar permissões',                  'modulo' => 'ADMINISTRACAO'],
    ['nome' => 'PERMITIR_PERMISSAO_EDITAR',         'descricao' => 'Permite editar permissões',                 'modulo' => 'ADMINISTRACAO'],
    ['nome' => 'PERMITIR_PERMISSAO_EXCLUIR',        'descricao' => 'Permite excluir permissões',                'modulo' => 'ADMINISTRACAO'],
    ['nome' => 'PERMITIR_USUARIO_LISTAR',           'descricao' => 'Permite listar usuários',                   'modulo' => 'ADMINISTRACAO'],
    ['nome' => 'PERMITIR_USUARIO_CRIAR',            'descricao' => 'Permite criar usuários',                    'modulo' => 'ADMINISTRACAO'],
    ['nome' => 'PERMITIR_USUARIO_EDITAR',           'descricao' => 'Permite editar usuários',                   'modulo' => 'ADMINISTRACAO'],
    ['nome' => 'PERMITIR_USUARIO_EXCLUIR',          'descricao' => 'Permite excluir usuários',                  'modulo' => 'ADMINISTRACAO'],
    ['nome' => 'PERMITIR_USUARIO_PERMISSAO_LISTAR', 'descricao' => 'Permite ver permissões de usuários',        'modulo' => 'ADMINISTRACAO'],
    ['nome' => 'PERMITIR_USUARIO_PERMISSAO_EDITAR', 'descricao' => 'Permite editar permissões de usuários',     'modulo' => 'ADMINISTRACAO'],

    // AUDITORIA
    ['nome' => 'PERMITIR_AUDITORIA_LISTAR',         'descricao' => 'Permite consultar auditoria',               'modulo' => 'AUDITORIA'],
    ['nome' => 'PERMITIR_AUDITORIA_ESTATISTICA',    'descricao' => 'Permite ver estatísticas de auditoria',     'modulo' => 'AUDITORIA'],
];
```

### 4.11 Seeder: `GrupoSeeder`

- Administrador → TODAS as permissões
- Registrador → protocolo, contrato, pagamento, recibo
- Atendente → protocolo (criar, listar), pagamento (registrar)
- Caixa → pagamento, recibo
- Consulta → todas as `_LISTAR`

---

## FASE 5 — IMPLEMENTAÇÃO FRONTEND (Quasar 2 / Vue 3)

### 5.1 Store Auth (adaptar existente)

No login, armazenar permissões, módulos e grupos no Pinia. Persistir no localStorage.

### 5.2 Composable: `src/composables/usePermissao.js`

```javascript
export function usePermissao() {
    const authStore = useAuthStore()

    function temPermissao(permissao) {
        if (authStore.eAdmin) return true
        return authStore.permissoes.includes(permissao)
    }

    function temAlgumaPermissao(permissoes) {
        if (authStore.eAdmin) return true
        return permissoes.some(p => authStore.permissoes.includes(p))
    }

    function temTodasPermissoes(permissoes) {
        if (authStore.eAdmin) return true
        return permissoes.every(p => authStore.permissoes.includes(p))
    }

    function temAcessoModulo(modulo) {
        if (authStore.eAdmin) return true
        return modulo in authStore.modulos && authStore.modulos[modulo].length > 0
    }

    function filtrarMenu(itens) {
        return itens
            .filter(item => {
                if (!item.permissao && !item.modulo) return true
                if (item.permissao) return temPermissao(item.permissao)
                if (item.modulo) return temAcessoModulo(item.modulo)
                return true
            })
            .map(item => item.filhos ? { ...item, filhos: filtrarMenu(item.filhos) } : item)
            .filter(item => !(item.filhos && item.filhos.length === 0))
    }

    return { temPermissao, temAlgumaPermissao, temTodasPermissoes, temAcessoModulo, filtrarMenu }
}
```

### 5.3 Diretiva: `v-permissao`

```javascript
// Uso: <q-btn v-permissao="'PERMITIR_PROTOCOLO_CRIAR'" label="Novo" />
// Uso array (OR): <q-btn v-permissao="['PERMITIR_PROTOCOLO_EDITAR', 'PERMITIR_PROTOCOLO_EXCLUIR']" />
```

Esconde o elemento se não tem permissão. Administrador vê tudo.
Registrar via boot file.

### 5.4 Menu: `src/config/menu.js`

```javascript
export const menuItens = [
    { label: 'Dashboard', icon: 'dashboard', rota: '/dashboard' },
    { label: 'Protocolo', icon: 'description', rota: '/protocolo', permissao: 'PERMITIR_PROTOCOLO_LISTAR' },
    { label: 'Contrato', icon: 'assignment', rota: '/contrato', permissao: 'PERMITIR_CONTRATO_LISTAR' },
    { label: 'Recibo', icon: 'receipt', rota: '/recibo', permissao: 'PERMITIR_RECIBO_LISTAR' },
    {
        label: 'Financeiro', icon: 'payments', modulo: 'FINANCEIRO',
        filhos: [
            { label: 'Natureza', icon: 'category', rota: '/natureza', permissao: 'PERMITIR_NATUREZA_LISTAR' },
            { label: 'Ato Cartorário', icon: 'gavel', rota: '/ato', permissao: 'PERMITIR_ATO_LISTAR' },
            { label: 'Forma de Pagamento', icon: 'credit_card', rota: '/forma-pagamento', permissao: 'PERMITIR_FORMA_PAGAMENTO_LISTAR' },
            { label: 'Meio de Pagamento', icon: 'account_balance', rota: '/meio-pagamento', permissao: 'PERMITIR_MEIO_PAGAMENTO_LISTAR' },
        ],
    },
    {
        label: 'Administração', icon: 'admin_panel_settings', modulo: 'ADMINISTRACAO',
        filhos: [
            { label: 'Usuário', icon: 'people', rota: '/usuario', permissao: 'PERMITIR_USUARIO_LISTAR' },
            { label: 'Grupo', icon: 'groups', rota: '/grupo', permissao: 'PERMITIR_GRUPO_LISTAR' },
            { label: 'Permissão', icon: 'vpn_key', rota: '/permissao', permissao: 'PERMITIR_PERMISSAO_LISTAR' },
            { label: 'Auditoria', icon: 'history', rota: '/auditoria', permissao: 'PERMITIR_AUDITORIA_LISTAR' },
        ],
    },
]
```

### 5.5 Sidebar/Drawer

Adaptar layout para usar `filtrarMenu(menuItens)` do composable.

### 5.6 Route Guard

```javascript
router.beforeEach((to, from, next) => {
    if (to.meta.publica) return next()
    if (!authStore.token) return next('/login')
    if (to.meta.permissao) {
        const permissoes = Array.isArray(to.meta.permissao) ? to.meta.permissao : [to.meta.permissao]
        if (!temAlgumaPermissao(permissoes)) return next('/sem-permissao')
    }
    next()
})
```

Definir `meta.permissao` em cada rota do router.

### 5.7 Página `SemPermissaoPage.vue`

Mensagem amigável + botão voltar ao dashboard.

### 5.8 Uso nos componentes

```vue
<q-btn v-if="temPermissao('PERMITIR_PROTOCOLO_CRIAR')" label="Novo Protocolo" />
<q-btn v-permissao="'PERMITIR_PROTOCOLO_EDITAR'" icon="edit" />
```

---

## FASE 6 — VALIDAÇÃO

### Backend:
1. `php artisan migrate --pretend`
2. Seeders: `PermissaoSeeder`, `GrupoSeeder`
3. `php artisan route:list` — middleware `permissao:`
4. Testar login retorna permissões
5. TODOS os Models usam `TimestampsPortugues`

### Frontend:
1. Composable `usePermissao` funciona
2. Diretiva `v-permissao` esconde elementos
3. Menu filtra corretamente
4. Route guard bloqueia rotas
5. Página sem permissão aparece

### Geral:
6. Listar TODOS os arquivos criados/alterados

---

## IMPORTANTE

- TABELAS E ROTAS NO **SINGULAR**
- TIMESTAMPS: `data_cadastro`, `data_alteracao`, `data_exclusao`
- TODOS os Models usam `TimestampsPortugues`
- Permissões em **CAIXA ALTA**: `PERMITIR_{MODULO}_{ACAO}`
- API padronizada `{ sucesso, mensagem, dados }`
- Administrador bypassa tudo
- `tipo = 'negar'` sobrescreve grupo
- Frontend: composable é fonte única de verificação
- Menu no código, filtrado pelas permissões do backend
- NÃO criar páginas CRUD do frontend — só infraestrutura de permissões
- Remover Spatie se existir
- Commitar cada fase separadamente
```
