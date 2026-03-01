# OSLO — Módulo de Administração

> Documentação da implementação do módulo `/administracao`, cobrindo catálogos financeiros, naturezas e o sistema RBAC completo.

---

## Visão Geral

O módulo de administração fornece gestão dos dados de configuração do sistema:

| Seção | Itens |
|---|---|
| **Cartório** | Naturezas |
| **Financeiro** | Formas de Pagamento, Meios de Pagamento, Categorias |
| **Segurança & Acesso** | Grupos, Permissões, Usuários/Permissões |
| **Localidades** | Estados, Cidades *(somente leitura)* |

Hub principal: `GET /administracao` → `pages/administracao/Index.vue`

---

## Formas de Pagamento

### Backend

**Migration:** `2026_03_09_000002_create_forma_pagamento_table.php` + `2026_03_10_000001_add_fields_to_forma_pagamento.php`

**Tabela: `forma_pagamento`**

```
id, nome (unique), descricao (nullable), is_ativo, data_cadastro, data_alteracao, data_exclusao
```

**Model:** `app/Models/FormaPagamento.php`
- `HasFactory`, `SoftDeletes`
- Timestamps customizados (`data_cadastro`, `data_alteracao`, `data_exclusao`)
- `scopeAtivo()`, `hasMany MeioPagamento`

**Controller:** `app/Http/Controllers/FormaPagamentoController.php`
- CRUD completo via `RespostaApi`
- `destroy()` impede exclusão se houver meios de pagamento vinculados

**Rotas:**

```php
Route::apiResource('formas-pagamento', FormaPagamentoController::class)
    ->only(['index', 'store', 'show', 'update', 'destroy']);
```

### Frontend

- **Store:** `src/stores/formaPagamento/index.js` — `listar`, `criar`, `atualizar`, `excluir`
- **Page:** `src/pages/administracao/FormaPagamentoPage.vue`
- **Modal:** `src/components/administracao/ModalFormaPagamento.vue`

---

## Meios de Pagamento

### Backend

**Migration:** `2026_03_09_000003_create_meio_pagamento_table.php` + `2026_03_10_000002_add_fields_to_meio_pagamento.php`

**Tabela: `meio_pagamento`**

```
id, forma_pagamento_id (FK), nome, descricao (nullable), identificador (nullable),
taxa_percentual (decimal 5,4 nullable), taxa_fixa (decimal 10,2 nullable),
prazo_compensacao (integer nullable), is_ativo,
data_cadastro, data_alteracao, data_exclusao
```

**Model:** `app/Models/MeioPagamento.php`
- `HasFactory`, `SoftDeletes`
- `belongsTo FormaPagamento`, `scopeAtivo()`
- Casts: `taxa_percentual` e `taxa_fixa` como decimal, `prazo_compensacao` como integer

**Controller:** `app/Http/Controllers/MeioPagamentoController.php`
- CRUD completo
- `index()` aceita filtro `?forma_pagamento_id=`

**Rotas:**

```php
Route::apiResource('meios-pagamento', MeioPagamentoController::class)
    ->only(['index', 'store', 'show', 'update', 'destroy']);
```

### Frontend

- **Store:** `src/stores/meioPagamento/index.js`
- **Page:** `src/pages/administracao/MeioPagamentoPage.vue` — filtro por forma
- **Modal:** `src/components/administracao/ModalMeioPagamento.vue`

---

## Categorias

### Backend

**Migration:** `2026_03_10_000003_create_categoria_table.php`

**Tabela: `categoria`**

```
id, categoria_pai_id (FK self nullable), nome, descricao (nullable),
tipo (string 50, enum: receita|despesa|transferencia|outros),
cor (string 7 nullable), icone (string 50 nullable), is_ativo,
data_cadastro, data_alteracao, data_exclusao
```

**Model:** `app/Models/Categoria.php`
- `HasFactory`, `SoftDeletes`
- `pai()` belongsTo self, `subcategorias()` hasMany self
- `scopeAtivo()`, `scopeRaiz()` (sem pai)
- Cast: `tipo` para `CategoriaTipoEnum`

**Controller:** `app/Http/Controllers/CategoriaController.php`
- CRUD + `todas()` — retorna hierarquia pai + subcategorias
- `destroy()` impede exclusão se houver subcategorias
- Rota `GET /v1/categorias/todas` registrada **antes** do apiResource (evitar conflito com `{id}`)

**Rotas:**

```php
Route::get('categorias/todas', [CategoriaController::class, 'todas']);
Route::apiResource('categorias', CategoriaController::class)
    ->only(['index', 'store', 'show', 'update', 'destroy']);
```

### Frontend

- **Store:** `src/stores/categoria/index.js`
- **Page:** `src/pages/administracao/CategoriaPage.vue` — exibição em cards hierárquicos
- **Modal:** `src/components/categoria/ModalCategoria.vue`

---

## Naturezas

### Backend

**Migration:** `2026_03_08_000002_create_natureza_table.php`

**Tabela: `natureza`**

```
id, uuid (uuid unique), codigo (nullable), nome, descricao (text nullable),
is_ativo, data_cadastro, data_alteracao, data_exclusao
```

**Model:** `app/Models/Natureza.php`
- `HasFactory`, `AuxiliarModel` (SoftDeletes + scopeAtivo)
- UUID gerado automaticamente no `booted()`

**Controller:** `app/Http/Controllers/NaturezaController.php`
- CRUD completo via `apiResource`
- `index()` suporta dois modos:
  - `?admin=1` → retorna tudo (inclui inativos, todos os campos) para gestão
  - Sem parâmetro → somente ativos, colunas mínimas (autocomplete)
- Filtros via `?nome=` e `?is_ativo=`

**Rotas:**

```php
Route::apiResource('naturezas', NaturezaController::class)
    ->only(['index', 'store', 'show', 'update', 'destroy']);
```

### Frontend

- **Store:** `src/stores/natureza/index.js`
  - `fetchNaturezas(nome)` — autocomplete (sem `?admin=1`)
  - `listar(filtros)` — gestão (com `?admin=1`)
  - `criar`, `atualizar`, `excluir`
- **Page:** `src/pages/administracao/NaturezaPage.vue` — filtros por nome + status
- **Modal:** `src/components/natureza/ModalNatureza.vue` — campos: nome, código, descrição, is_ativo

---

## Sistema RBAC (Grupos, Permissões, Usuários)

### Conceito

O sistema RBAC (Role-Based Access Control) funciona em três camadas:

1. **Permissões** — ações atômicas (`PROTOCOLO_CRIAR`, `CAIXA_ABRIR` etc.)
2. **Grupos** — conjuntos de permissões (`Registrador`, `Atendente` etc.)
3. **Usuário → Grupo** — usuário pertence a um ou mais grupos
4. **Sobrescritas individuais** — permissão individual pode *permitir* ou *negar* uma ação, sobrepondo o grupo

**Regra de resolução:**

```
permissão_efetiva = (perms_dos_grupos ∪ individuais_permitir) \ individuais_negar
```

**Administrador** tem bypass total: `isAdmin() === true` → 100% das permissões sem verificar tabelas.

### Backend — Migrations

| Arquivo | Tabela |
|---|---|
| `2026_03_11_000001_create_grupo_table.php` | `grupo` |
| `2026_03_11_000002_create_permissao_table.php` | `permissao` |
| `2026_03_11_000003_create_grupo_permissao_table.php` | `grupo_permissao` (pivot) |
| `2026_03_11_000004_create_usuario_grupo_table.php` | `usuario_grupo` (pivot) |
| `2026_03_11_000005_create_usuario_permissao_table.php` | `usuario_permissao` (sobrescritas) |

**Tabela `grupo`:**

```
id, nome (unique), descricao (nullable), is_ativo,
data_cadastro, data_alteracao, data_exclusao (softDelete)
```

**Tabela `permissao`:**

```
id, nome (unique, ex: PROTOCOLO_CRIAR), descricao (nullable), modulo (ex: Protocolo),
data_cadastro, data_alteracao
```

**Tabela `grupo_permissao`** (pivot):

```
id, grupo_id (FK), permissao_id (FK), unique([grupo_id, permissao_id]), timestamps
```

**Tabela `usuario_grupo`** (pivot):

```
id, usuario_id (FK → usuario), grupo_id (FK → grupo), unique([usuario_id, grupo_id]), timestamps
```

**Tabela `usuario_permissao`** (sobrescritas individuais):

```
id, usuario_id (FK), permissao_id (FK), tipo ('permitir'|'negar'),
unique([usuario_id, permissao_id]), timestamps
```

### Backend — Models

**`app/Models/Grupo.php`**

```php
use HasFactory, SoftDeletes;
const CREATED_AT = 'data_cadastro'; // ...

public function permissoes(): BelongsToMany  // via grupo_permissao
public function usuarios(): BelongsToMany    // via usuario_grupo
public function scopeAtivo($query)
```

**`app/Models/Permissao.php`**

```php
use HasFactory;
const CREATED_AT = 'data_cadastro'; // ...

public function grupos(): BelongsToMany      // via grupo_permissao
public function usuarios(): BelongsToMany    // via usuario_permissao (pivot: tipo)
public function scopePorModulo($query, $modulo)
```

**`app/Models/User.php`** — métodos RBAC adicionados:

```php
public function grupos(): BelongsToMany           // via usuario_grupo
public function permissoesIndividuais(): BelongsToMany  // via usuario_permissao (pivot: tipo)

public function isAdmin(): bool
// → true se pertence ao grupo 'Administrador'

public function obterPermissoes(): array
// → resolve hierarquia: grupos + individuais - negar
// → se isAdmin(), retorna todas as permissões do sistema

public function temPermissao(string $permissao): bool
```

### Backend — Controllers

**`GrupoController`**

| Método | Rota | Descrição |
|---|---|---|
| `index` | `GET /v1/grupos` | Lista grupos com count de usuários; filtros: nome, is_ativo |
| `show` | `GET /v1/grupos/{id}` | Detalhe com permissões carregadas |
| `store` | `POST /v1/grupos` | Cria grupo |
| `update` | `PUT /v1/grupos/{id}` | Atualiza grupo |
| `destroy` | `DELETE /v1/grupos/{id}` | Exclui (bloqueia se tiver usuários) |
| `sincronizarPermissoes` | `POST /v1/grupos/{id}/permissoes` | Substitui todas as permissões do grupo |

**`PermissaoController`**

| Método | Rota | Descrição |
|---|---|---|
| `index` | `GET /v1/permissoes` | Lista permissões; `?agrupado=1` retorna `[{ modulo, permissoes }]`; `?busca=` filtra |
| `modulos` | `GET /v1/permissoes/modulos` | Lista módulos distintos |

**`UsuarioPermissaoController`**

| Método | Rota | Descrição |
|---|---|---|
| `index` | `GET /v1/usuarios-permissoes` | Lista usuários com grupos; filtros: nome, email |
| `show` | `GET /v1/usuarios-permissoes/{id}` | Usuário com grupos, permissões individuais e efetivas |
| `efetivas` | `GET /v1/usuarios-permissoes/{id}/efetivas` | Resultado final calculado |
| `sincronizarGrupos` | `POST /v1/usuarios-permissoes/{id}/grupos` | Substitui grupos do usuário |
| `definirPermissao` | `POST /v1/usuarios-permissoes/{id}/permissao` | Define sobrescrita individual: `permitir`, `negar` ou `herdar` (remove sobrescrita) |

### Backend — Seeders

**`PermissaoSeeder`** — 82 permissões em 10 módulos:

| Módulo | Prefixo | Exemplo |
|---|---|---|
| Protocolo | `PROTOCOLO_` | LISTAR, CRIAR, VISUALIZAR, EDITAR, CANCELAR, PAGAR, ESTORNAR, PAGAMENTO_EXCLUIR, ISENTAR |
| Contrato | `CONTRATO_` | LISTAR, CRIAR, VISUALIZAR, EDITAR, CONCLUIR, CANCELAR |
| Recibo | `RECIBO_` | LISTAR, VISUALIZAR, GERAR |
| Arquivo | `ARQUIVO_` | LISTAR, VISUALIZAR, EXCLUIR |
| Ato | `ATO_` | LISTAR, CRIAR, VISUALIZAR, EDITAR, EXCLUIR |
| Indicador Pessoal | `INDICADOR_PESSOAL_` | LISTAR, CRIAR, VISUALIZAR, EDITAR, EXCLUIR |
| Indisponibilidade | `INDISPONIBILIDADE_` | LISTAR, CRIAR, VISUALIZAR, EDITAR, CANCELAR |
| Financeiro | `FORMA_PAGAMENTO_`, `MEIO_PAGAMENTO_`, `TRANSACAO_` | LISTAR, CRIAR, VISUALIZAR, EDITAR, EXCLUIR |
| Caixa | `CAIXA_`, `CAIXA_MOVIMENTO_`, `CAIXA_OPERACAO_` | LISTAR, ABRIR, FECHAR, CONFERIR, SANGRIA |
| Administração | `NATUREZA_`, `GRUPO_`, `PERMISSAO_`, etc. | LISTAR, CRIAR, EDITAR, EXCLUIR |

**`GrupoSeeder`** — 5 grupos padrão:

| Grupo | Perfil |
|---|---|
| **Administrador** | Bypass total — acesso irrestrito |
| **Registrador** | Operacional completo (protocolo, atos, financeiro, caixa) |
| **Atendente** | Criação de protocolos e contratos |
| **Caixa** | Operações financeiras e de caixa |
| **Consulta** | Somente leitura (`_LISTAR` + `_VISUALIZAR`) |

O primeiro usuário do banco é automaticamente vinculado ao grupo **Administrador**.

### Backend — AuthController

`dadosUsuario()` agora retorna permissões e grupos reais:

```php
private function dadosUsuario(User $user): array
{
    $user->load(['grupos', 'grupos.permissoes', 'permissoesIndividuais']);
    return [
        // ...
        'permissoes' => $user->obterPermissoes(),  // array de strings
        'grupos'     => $user->grupos->pluck('nome')->toArray(),
    ];
}
```

### Frontend — Stores

**`src/stores/grupo/index.js`**

```js
listar(filtros)       // GET /v1/grupos
buscarPorId(id)       // GET /v1/grupos/{id}
criar(dados)
atualizar(id, dados)
excluir(id)
sincronizarPermissoes(id, permissaoIds)  // POST /v1/grupos/{id}/permissoes
```

**`src/stores/permissao/index.js`**

```js
listar(params)       // GET /v1/permissoes
listarAgrupada()     // GET /v1/permissoes?agrupado=1 (com cache: carregado flag)
```

**`src/stores/usuario-permissao/index.js`**

```js
listar(filtros)
buscarPorId(id)
sincronizarGrupos(id, grupoIds)
definirPermissao(id, permissaoId, tipo)   // tipo: 'permitir'|'negar'|'herdar'
buscarEfetivas(id)
```

### Frontend — Pages

| Page | Rota | Permissão |
|---|---|---|
| `GruposPage.vue` | `/administracao/grupos` | `GRUPO_LISTAR` |
| `PermissoesPage.vue` | `/administracao/permissoes` | `PERMISSAO_LISTAR` |
| `UsuariosPermissoesPage.vue` | `/administracao/usuarios-permissoes` | `USUARIO_PERMISSAO_LISTAR` |

**`GruposPage`** — q-table com filtros por nome e status. Ações: criar, editar, excluir.

**`PermissoesPage`** — lista read-only agrupada por módulo via `q-expansion-item`. Busca por nome/descrição/módulo.

**`UsuariosPermissoesPage`** — tabela de usuários com badges dos grupos. Ação: abrir `ModalUsuarioPermissao`.

### Frontend — Components

**`src/components/grupo/ModalGrupo.vue`** — 2 abas:
- **Dados**: nome, descrição, is_ativo
- **Permissões**: checkboxes agrupados por módulo com botão "Selecionar todos" por módulo. Desabilitada ao criar (ativa somente em modo edição); porém salvar cria o grupo e já sincroniza as permissões marcadas.

**`src/components/usuario-permissao/ModalUsuarioPermissao.vue`** — 3 abas:
- **Grupos**: checkboxes com nome + descrição do grupo
- **Permissões Individuais**: `q-btn-toggle` por permissão com opções `Herdar | Permitir | Negar`; alterações são salvas imediatamente (sem precisar clicar em Salvar)
- **Resumo**: permissões efetivas agrupadas por módulo em chips; banner especial para Administrador

---

## Hub de Administração

**`src/pages/administracao/Index.vue`**

Grid de cards 4 colunas, filtrado por permissão do usuário:

```js
// Seção Cartório
{ label: 'Naturezas', to: { name: 'administracao.natureza' }, permissao: 'NATUREZA_LISTAR' }

// Seção Financeiro
{ label: 'Formas de Pagamento', to: { name: 'administracao.forma-pagamento' }, permissao: 'FORMA_PAGAMENTO_LISTAR' }
{ label: 'Meios de Pagamento',  to: { name: 'administracao.meio-pagamento' },  permissao: 'MEIO_PAGAMENTO_LISTAR' }
{ label: 'Categorias',          to: { name: 'administracao.categoria' },        permissao: 'CATEGORIA_LISTAR' }

// Seção Segurança & Acesso
{ label: 'Grupos',    to: { name: 'administracao.grupos' },              permissao: 'GRUPO_LISTAR' }
{ label: 'Permissões',to: { name: 'administracao.permissoes' },          permissao: 'PERMISSAO_LISTAR' }
{ label: 'Usuários',  to: { name: 'administracao.usuarios-permissoes' }, permissao: 'USUARIO_PERMISSAO_LISTAR' }
```

Usuários sem nenhuma permissão de administração veem mensagem de "acesso negado".

---

## Commits

| Hash | Descrição |
|---|---|
| `33cda7a` | feat: módulo de administração — Formas/Meios de Pagamento, Categorias |
| `ec50dc1` | feat: CRUD completo de Naturezas com modal e hub de administração |
| `44369ee` | feat: implementar sistema RBAC completo (grupos, permissões, usuários) |
