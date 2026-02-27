# PROMPT — Módulo de Protocolo, Contrato e Financeiro (OSLO)

Cole este prompt no Claude CLI na raiz do projeto OSLO.

---

```
Você está no projeto OSLO, um sistema de gestão para cartório de registro de imóveis (1º Ofício de Registro de Imóveis de Sinop/MT), desenvolvido em Laravel + Quasar 2 (Vue 3) + PostgreSQL.

## OBJETIVO

Criar o módulo completo de **Protocolo, Contrato e Financeiro** do cartório, incluindo:
- Tabelas administrativas (cadastros base): natureza, ato, forma_pagamento, meio_pagamento
- Tabelas operacionais (fluxo): protocolo, protocolo_item, protocolo_pagamento, protocolo_isencao, contrato, recibo
- Backend completo: Migrations, Models, Controllers, Services, Resources, Rotas
- Retorno de API padronizado em TODAS as respostas

## REGRAS GERAIS

### Nomenclatura
- Tudo em **português** (tabelas, colunas, variáveis, rotas, comentários), exceto convenções do Laravel (queues, jobs, timestamps, etc.)
- **TABELAS NO SINGULAR**: `protocolo`, `contrato`, `ato`, `natureza`, `recibo` (NÃO usar plural)
- **ROTAS NO SINGULAR**: `/api/protocolo`, `/api/contrato`, `/api/ato` (NÃO usar plural)
- **RELACIONAMENTOS NO SINGULAR OU PLURAL conforme cardinalidade**:
  - `hasMany` → plural: `$protocolo->itens()`, `$protocolo->pagamentos()`
  - `belongsTo` → singular: `$protocolo_item->ato()`, `$protocolo->atendente()`
- Model User já usa `protected $table = 'usuario'` — seguir o mesmo padrão

### Técnico
- Autenticação via **JWT** (tymon/jwt-auth), guard `api`
- Seguir a arquitetura e padrões já existentes no projeto
- Todas as tabelas de negócio: `created_at`, `updated_at`, `deleted_at` (soft delete)
- Valores monetários: `DECIMAL(15,2)` — nunca centavos
- Se existir sistema de auditoria via triggers PostgreSQL, aplicar nas novas tabelas

---

## PADRÃO DE RETORNO DA API

TODAS as respostas da API devem seguir este padrão. Criar um trait ou helper para padronizar.

### Trait: `App\Traits\RespostaApi`

Criar esta trait e usar em TODOS os controllers:

```php
trait RespostaApi
{
    // Sucesso com dados
    protected function sucesso($dados = null, string $mensagem = 'Operação realizada com sucesso', int $codigo = 200)
    {
        return response()->json([
            'sucesso'  => true,
            'mensagem' => $mensagem,
            'dados'    => $dados,
        ], $codigo);
    }

    // Sucesso com paginação
    protected function sucessoPaginado($paginador, string $mensagem = 'Consulta realizada com sucesso')
    {
        return response()->json([
            'sucesso'  => true,
            'mensagem' => $mensagem,
            'dados'    => $paginador->items(),
            'paginacao' => [
                'pagina_atual'  => $paginador->currentPage(),
                'por_pagina'    => $paginador->perPage(),
                'total'         => $paginador->total(),
                'ultima_pagina' => $paginador->lastPage(),
            ],
        ]);
    }

    // Erro
    protected function erro(string $mensagem = 'Erro ao processar requisição', int $codigo = 400, $erros = null)
    {
        $resposta = [
            'sucesso'  => false,
            'mensagem' => $mensagem,
        ];

        if ($erros !== null) {
            $resposta['erros'] = $erros;
        }

        return response()->json($resposta, $codigo);
    }

    // Não encontrado
    protected function naoEncontrado(string $mensagem = 'Registro não encontrado')
    {
        return $this->erro($mensagem, 404);
    }

    // Criado com sucesso
    protected function criado($dados = null, string $mensagem = 'Registro criado com sucesso')
    {
        return $this->sucesso($dados, $mensagem, 201);
    }
}
```

### Exemplos de resposta:

**Sucesso simples:**
```json
{
    "sucesso": true,
    "mensagem": "Protocolo criado com sucesso",
    "dados": {
        "id": 1,
        "numero": "2025/000001",
        "status": "aberto"
    }
}
```

**Sucesso com paginação:**
```json
{
    "sucesso": true,
    "mensagem": "Consulta realizada com sucesso",
    "dados": [ ... ],
    "paginacao": {
        "pagina_atual": 1,
        "por_pagina": 15,
        "total": 150,
        "ultima_pagina": 10
    }
}
```

**Erro de validação:**
```json
{
    "sucesso": false,
    "mensagem": "Erro de validação",
    "erros": {
        "solicitante_nome": ["O campo solicitante nome é obrigatório"],
        "valor": ["O valor não pode ser maior que o restante"]
    }
}
```

**Não encontrado:**
```json
{
    "sucesso": false,
    "mensagem": "Protocolo não encontrado"
}
```

### Exception Handler

Criar ou adaptar o exception handler para que TODAS as exceções retornem no mesmo padrão:
- `ValidationException` → 422 com erros de validação
- `ModelNotFoundException` → 404
- `AuthenticationException` → 401
- `AuthorizationException` → 403
- Qualquer outro erro → 500

---

## FASE 1 — ANÁLISE (EXECUTAR PRIMEIRO, NÃO PULAR)

Antes de criar qualquer arquivo:

1. Ler `composer.json` — versão do Laravel e dependências
2. Listar `database/migrations/` — mapear tabelas existentes
3. Listar `app/Models/` — ver models existentes e padrão (traits, casts, fillable/guarded)
4. Ler 2-3 Controllers — identificar padrão (Service? Repository? Form Request? Resource?)
5. Ler `routes/api.php` — convenção de rotas
6. Verificar se existe sistema de auditoria (schema `auditoria`, comando artisan)
7. Verificar se existe Handler de exceções customizado
8. Ler o Model User como referência
9. Verificar se já existe alguma tabela de protocolo, ato, contrato ou similar

Apresentar resumo antes de prosseguir. Se encontrar algo inesperado, PERGUNTAR.

---

## FASE 2 — MODELAGEM DO BANCO DE DADOS

### Diagrama de relacionamento:

```
[natureza] 1──N [ato] 1──N [protocolo_item] N──1 [protocolo] 1──N [contrato]
                                                      │
[forma_pagamento] 1──N [protocolo_pagamento] N──1─────┘
[meio_pagamento]  1──N [protocolo_pagamento]          │
                                                      │
                       [protocolo_isencao]  N──1──────┘
                                                      │
                       [recibo]             N──1──────┘

[usuario] 1──N [protocolo]             (atendente)
[usuario] 1──N [protocolo_pagamento]   (quem recebeu)
[usuario] 1──N [contrato]             (responsável)
[usuario] 1──N [recibo]              (quem emitiu)
```

### 2.1 Tabela: `natureza`

Classificação contábil/financeira dos valores cobrados.

```
natureza
├── id                  BIGSERIAL PK
├── nome                VARCHAR(100) NOT NULL UNIQUE  -- "Emolumento", "Taxa Judiciária", "FUNDESP", "ISS"
├── descricao           TEXT NULL
├── codigo              VARCHAR(20) NULL UNIQUE       -- código contábil interno
├── ativo               BOOLEAN NOT NULL DEFAULT TRUE
├── created_at          TIMESTAMP
├── updated_at          TIMESTAMP
└── deleted_at          TIMESTAMP NULL
```

### 2.2 Tabela: `ato`

Cadastro dos atos cartorários com valores tabelados.

```
ato
├── id                  BIGSERIAL PK
├── natureza_id         BIGINT FK → natureza.id NOT NULL
├── codigo              VARCHAR(20) NOT NULL UNIQUE   -- código do ato ("R1", "AV", "CERT-01")
├── nome                VARCHAR(200) NOT NULL         -- "Registro de Imóvel", "Averbação de Construção"
├── descricao           TEXT NULL
├── valor_fixo          DECIMAL(15,2) NULL            -- valor tabelado fixo
├── percentual          DECIMAL(8,4) NULL             -- percentual sobre base de cálculo
├── valor_minimo        DECIMAL(15,2) NULL            -- piso mínimo
├── valor_maximo        DECIMAL(15,2) NULL            -- teto máximo
├── tipo_calculo        VARCHAR(20) NOT NULL DEFAULT 'fixo'  -- 'fixo', 'percentual', 'faixa', 'manual'
├── ativo               BOOLEAN NOT NULL DEFAULT TRUE
├── created_at          TIMESTAMP
├── updated_at          TIMESTAMP
└── deleted_at          TIMESTAMP NULL
```

`tipo_calculo`:
- `fixo` → usa `valor_fixo`
- `percentual` → aplica `percentual` sobre base de cálculo do protocolo_item
- `faixa` → valor por faixas (tabela `ato_faixa`)
- `manual` → atendente informa o valor

### 2.3 Tabela: `ato_faixa`

Para atos com cálculo por faixa de valor.

```
ato_faixa
├── id                  BIGSERIAL PK
├── ato_id              BIGINT FK → ato.id NOT NULL
├── valor_de            DECIMAL(15,2) NOT NULL
├── valor_ate           DECIMAL(15,2) NULL            -- NULL = sem limite
├── valor_fixo          DECIMAL(15,2) NULL
├── percentual          DECIMAL(8,4) NULL
├── created_at          TIMESTAMP
├── updated_at          TIMESTAMP
└── deleted_at          TIMESTAMP NULL
```

### 2.4 Tabela: `forma_pagamento`

```
forma_pagamento
├── id                  BIGSERIAL PK
├── nome                VARCHAR(100) NOT NULL UNIQUE  -- "Dinheiro", "PIX", "Cartão Débito"
├── descricao           TEXT NULL
├── ativo               BOOLEAN NOT NULL DEFAULT TRUE
├── created_at          TIMESTAMP
├── updated_at          TIMESTAMP
└── deleted_at          TIMESTAMP NULL
```

### 2.5 Tabela: `meio_pagamento`

```
meio_pagamento
├── id                  BIGSERIAL PK
├── forma_pagamento_id  BIGINT FK → forma_pagamento.id NOT NULL
├── nome                VARCHAR(100) NOT NULL         -- "Máquina Stone 01", "Conta PIX BB"
├── descricao           TEXT NULL
├── identificador       VARCHAR(100) NULL             -- nº da máquina, chave PIX, conta
├── ativo               BOOLEAN NOT NULL DEFAULT TRUE
├── created_at          TIMESTAMP
├── updated_at          TIMESTAMP
└── deleted_at          TIMESTAMP NULL
```

### 2.6 Tabela: `protocolo`

Registro principal do atendimento.

```
protocolo
├── id                  BIGSERIAL PK
├── numero              VARCHAR(30) NOT NULL UNIQUE   -- "2025/000001" (gerado automaticamente)
├── ano                 INTEGER NOT NULL
├── usuario_id          BIGINT FK → usuario.id NOT NULL  -- atendente
├── solicitante_nome    VARCHAR(200) NOT NULL
├── solicitante_cpf_cnpj VARCHAR(18) NULL
├── solicitante_telefone VARCHAR(20) NULL
├── solicitante_email   VARCHAR(200) NULL
├── matricula           VARCHAR(50) NULL              -- matrícula do imóvel
├── observacao          TEXT NULL
├── valor_total         DECIMAL(15,2) NOT NULL DEFAULT 0  -- soma dos itens
├── valor_desconto      DECIMAL(15,2) NOT NULL DEFAULT 0
├── valor_isento        DECIMAL(15,2) NOT NULL DEFAULT 0  -- soma das isenções
├── valor_final         DECIMAL(15,2) NOT NULL DEFAULT 0  -- total - desconto - isento
├── valor_pago          DECIMAL(15,2) NOT NULL DEFAULT 0  -- soma dos pagamentos
├── status              VARCHAR(30) NOT NULL DEFAULT 'aberto'
│                       -- 'aberto', 'pago_parcial', 'pago', 'em_andamento',
│                       -- 'concluido', 'cancelado', 'isento'
├── created_at          TIMESTAMP
├── updated_at          TIMESTAMP
└── deleted_at          TIMESTAMP NULL
```

Número gerado: `{ANO}/{SEQUENCIAL_6_DIGITOS}` — sequência reinicia a cada ano.

### 2.7 Tabela: `protocolo_item`

```
protocolo_item
├── id                  BIGSERIAL PK
├── protocolo_id        BIGINT FK → protocolo.id NOT NULL
├── ato_id              BIGINT FK → ato.id NOT NULL
├── descricao           VARCHAR(300) NULL             -- override do ato.nome
├── quantidade          INTEGER NOT NULL DEFAULT 1
├── base_calculo        DECIMAL(15,2) NULL            -- valor base (ex: valor do imóvel)
├── valor_unitario      DECIMAL(15,2) NOT NULL
├── valor_total         DECIMAL(15,2) NOT NULL        -- quantidade * valor_unitario
├── observacao          TEXT NULL
├── created_at          TIMESTAMP
├── updated_at          TIMESTAMP
└── deleted_at          TIMESTAMP NULL
```

### 2.8 Tabela: `protocolo_pagamento`

Suporta múltiplos pagamentos e pagamento parcial.

```
protocolo_pagamento
├── id                  BIGSERIAL PK
├── protocolo_id        BIGINT FK → protocolo.id NOT NULL
├── forma_pagamento_id  BIGINT FK → forma_pagamento.id NOT NULL
├── meio_pagamento_id   BIGINT FK → meio_pagamento.id NULL
├── usuario_id          BIGINT FK → usuario.id NOT NULL  -- quem recebeu
├── valor               DECIMAL(15,2) NOT NULL
├── data_pagamento      TIMESTAMP NOT NULL DEFAULT NOW()
├── comprovante         VARCHAR(200) NULL             -- NSU, código transação
├── observacao          TEXT NULL
├── status              VARCHAR(20) NOT NULL DEFAULT 'confirmado'
│                       -- 'confirmado', 'pendente', 'cancelado', 'estornado'
├── created_at          TIMESTAMP
├── updated_at          TIMESTAMP
└── deleted_at          TIMESTAMP NULL
```

### 2.9 Tabela: `protocolo_isencao`

AJG e outras isenções.

```
protocolo_isencao
├── id                  BIGSERIAL PK
├── protocolo_id        BIGINT FK → protocolo.id NOT NULL
├── tipo                VARCHAR(50) NOT NULL          -- 'AJG', 'DECISAO_JUDICIAL', 'OUTRO'
├── numero_processo     VARCHAR(50) NULL
├── vara                VARCHAR(100) NULL
├── data_decisao        DATE NULL
├── valor_isento        DECIMAL(15,2) NOT NULL
├── documento_path      VARCHAR(500) NULL
├── observacao          TEXT NULL
├── usuario_id          BIGINT FK → usuario.id NOT NULL
├── created_at          TIMESTAMP
├── updated_at          TIMESTAMP
└── deleted_at          TIMESTAMP NULL
```

### 2.10 Tabela: `contrato`

Vinculado ao protocolo. Representa o contrato/serviço que será executado pelo cartório.

```
contrato
├── id                  BIGSERIAL PK
├── protocolo_id        BIGINT FK → protocolo.id NOT NULL
├── numero              VARCHAR(30) NOT NULL UNIQUE   -- "2025/C000001" (gerado automaticamente)
├── ano                 INTEGER NOT NULL
├── usuario_id          BIGINT FK → usuario.id NOT NULL  -- responsável pelo contrato
├── tipo                VARCHAR(50) NOT NULL          -- 'REGISTRO', 'AVERBACAO', 'CERTIDAO', 'RETIFICACAO', 'OUTRO'
├── descricao           TEXT NULL                     -- descrição do serviço a ser executado
├── matricula           VARCHAR(50) NULL              -- matrícula do imóvel (herda do protocolo ou específica)
├── parte_nome          VARCHAR(200) NULL             -- nome da parte envolvida (comprador, requerente, etc.)
├── parte_cpf_cnpj      VARCHAR(18) NULL
├── parte_qualificacao  TEXT NULL                     -- qualificação completa da parte
├── data_entrada        DATE NOT NULL DEFAULT CURRENT_DATE
├── data_previsao       DATE NULL                     -- previsão de conclusão
├── data_conclusao      DATE NULL                     -- data efetiva de conclusão
├── prazo_dias          INTEGER NULL                  -- prazo em dias úteis
├── observacao          TEXT NULL
├── observacao_interna  TEXT NULL                     -- observação só visível internamente
├── status              VARCHAR(30) NOT NULL DEFAULT 'pendente'
│                       -- 'pendente': aguardando início
│                       -- 'em_analise': sendo analisado
│                       -- 'exigencia': tem exigência a cumprir
│                       -- 'em_andamento': sendo executado
│                       -- 'concluido': finalizado
│                       -- 'cancelado': cancelado
│                       -- 'devolvido': devolvido ao solicitante
├── created_at          TIMESTAMP
├── updated_at          TIMESTAMP
└── deleted_at          TIMESTAMP NULL
```

Número gerado: `{ANO}/C{SEQUENCIAL_6_DIGITOS}` — o "C" diferencia de protocolo.

### 2.11 Tabela: `contrato_exigencia`

Exigências que o cartório faz ao solicitante para prosseguir.

```
contrato_exigencia
├── id                  BIGSERIAL PK
├── contrato_id         BIGINT FK → contrato.id NOT NULL
├── usuario_id          BIGINT FK → usuario.id NOT NULL  -- quem criou a exigência
├── descricao           TEXT NOT NULL                 -- descrição da exigência
├── prazo_dias          INTEGER NULL                  -- prazo para cumprir
├── data_cumprimento    DATE NULL                     -- quando foi cumprida
├── cumprida            BOOLEAN NOT NULL DEFAULT FALSE
├── observacao          TEXT NULL
├── created_at          TIMESTAMP
├── updated_at          TIMESTAMP
└── deleted_at          TIMESTAMP NULL
```

### 2.12 Tabela: `contrato_andamento`

Histórico de andamento/movimentação do contrato.

```
contrato_andamento
├── id                  BIGSERIAL PK
├── contrato_id         BIGINT FK → contrato.id NOT NULL
├── usuario_id          BIGINT FK → usuario.id NOT NULL  -- quem registrou
├── status_anterior     VARCHAR(30) NULL
├── status_novo         VARCHAR(30) NOT NULL
├── descricao           TEXT NOT NULL                 -- o que foi feito
├── created_at          TIMESTAMP
└── updated_at          TIMESTAMP
```

### 2.13 Tabela: `recibo`

Gerado ao confirmar pagamento.

```
recibo
├── id                  BIGSERIAL PK
├── protocolo_id        BIGINT FK → protocolo.id NOT NULL
├── numero              VARCHAR(30) NOT NULL UNIQUE   -- "2025/R000001"
├── ano                 INTEGER NOT NULL
├── usuario_id          BIGINT FK → usuario.id NOT NULL
├── solicitante_nome    VARCHAR(200) NOT NULL
├── solicitante_cpf_cnpj VARCHAR(18) NULL
├── valor_total         DECIMAL(15,2) NOT NULL
├── valor_isento        DECIMAL(15,2) NOT NULL DEFAULT 0
├── valor_pago          DECIMAL(15,2) NOT NULL
├── data_emissao        TIMESTAMP NOT NULL DEFAULT NOW()
├── observacao          TEXT NULL
├── created_at          TIMESTAMP
├── updated_at          TIMESTAMP
└── deleted_at          TIMESTAMP NULL
```

---

## FASE 3 — IMPLEMENTAÇÃO BACKEND

### 3.1 Migrations

Criar na ordem de dependência:
1. `criar_tabela_natureza`
2. `criar_tabela_ato`
3. `criar_tabela_ato_faixa`
4. `criar_tabela_forma_pagamento`
5. `criar_tabela_meio_pagamento`
6. `criar_tabela_protocolo`
7. `criar_tabela_protocolo_item`
8. `criar_tabela_protocolo_pagamento`
9. `criar_tabela_protocolo_isencao`
10. `criar_tabela_contrato`
11. `criar_tabela_contrato_exigencia`
12. `criar_tabela_contrato_andamento`
13. `criar_tabela_recibo`

Se existir `php artisan auditoria:migration`, usar para aplicar triggers em todas as novas tabelas.

### 3.2 Trait RespostaApi

Criar `App\Traits\RespostaApi` conforme especificado acima na seção PADRÃO DE RETORNO DA API.
TODOS os controllers devem usar esta trait.

### 3.3 Exception Handler

Adaptar o handler de exceções para retornar no padrão:
```php
// ValidationException → 422
{
    "sucesso": false,
    "mensagem": "Erro de validação",
    "erros": { "campo": ["mensagem"] }
}

// ModelNotFoundException → 404
{
    "sucesso": false,
    "mensagem": "Registro não encontrado"
}

// AuthenticationException → 401
{
    "sucesso": false,
    "mensagem": "Não autenticado"
}

// Qualquer outro → 500
{
    "sucesso": false,
    "mensagem": "Erro interno do servidor"
}
```

### 3.4 Models

Criar um Model por tabela. Cada Model deve:
- `protected $table = 'nome_no_singular'`
- `use SoftDeletes;` (exceto `contrato_andamento`)
- `use Auditavel;` (se existir)
- Definir `$fillable`
- Definir `$casts` para datas, decimais, booleans
- Definir relacionamentos com nomes em português

Models:
- `Natureza` → tabela `natureza`
- `Ato` → tabela `ato`
- `AtoFaixa` → tabela `ato_faixa`
- `FormaPagamento` → tabela `forma_pagamento`
- `MeioPagamento` → tabela `meio_pagamento`
- `Protocolo` → tabela `protocolo`
- `ProtocoloItem` → tabela `protocolo_item`
- `ProtocoloPagamento` → tabela `protocolo_pagamento`
- `ProtocoloIsencao` → tabela `protocolo_isencao`
- `Contrato` → tabela `contrato`
- `ContratoExigencia` → tabela `contrato_exigencia`
- `ContratoAndamento` → tabela `contrato_andamento`
- `Recibo` → tabela `recibo`

#### Relacionamentos do Protocolo:
```php
$protocolo->itens()           // hasMany ProtocoloItem
$protocolo->pagamentos()      // hasMany ProtocoloPagamento
$protocolo->isencoes()        // hasMany ProtocoloIsencao
$protocolo->contratos()       // hasMany Contrato
$protocolo->recibos()         // hasMany Recibo
$protocolo->atendente()       // belongsTo User (usuario_id)
```

#### Relacionamentos do Contrato:
```php
$contrato->protocolo()        // belongsTo Protocolo
$contrato->responsavel()      // belongsTo User (usuario_id)
$contrato->exigencias()       // hasMany ContratoExigencia
$contrato->andamentos()       // hasMany ContratoAndamento
```

#### Métodos de negócio no Protocolo:
```php
public function recalcularValores(): void
public function estaPago(): bool
public function temPagamentoParcial(): bool
public function eIsento(): bool
public static function gerarNumero(): string
public function valorRestante(): float
public function atualizarStatus(): void
```

#### Métodos de negócio no Contrato:
```php
public static function gerarNumero(): string
public function temExigenciaPendente(): bool
public function registrarAndamento(string $statusNovo, string $descricao, int $usuarioId): ContratoAndamento
```

### 3.5 Services

#### `ProtocoloService`
- `criar(array $dados): Protocolo` — cria com itens, gera número
- `adicionarItem(Protocolo $protocolo, array $dados): ProtocoloItem` — calcula valor, recalcula totais
- `removerItem(ProtocoloItem $item): void` — recalcula totais
- `calcularValorItem(Ato $ato, ?float $baseCalculo, int $quantidade): float`
- `cancelar(Protocolo $protocolo, string $motivo): void`

#### `PagamentoService`
- `registrar(Protocolo $protocolo, array $dados): ProtocoloPagamento` — valida valor restante, atualiza status
- `estornar(ProtocoloPagamento $pagamento, string $motivo): void`
- `registrarIsencao(Protocolo $protocolo, array $dados): ProtocoloIsencao`

#### `ContratoService`
- `criar(Protocolo $protocolo, array $dados): Contrato` — gera número, registra andamento inicial
- `atualizarStatus(Contrato $contrato, string $novoStatus, string $descricao, int $usuarioId): void`
- `adicionarExigencia(Contrato $contrato, array $dados): ContratoExigencia`
- `cumprirExigencia(ContratoExigencia $exigencia): void`
- `concluir(Contrato $contrato, int $usuarioId): void`

#### `ReciboService`
- `gerar(Protocolo $protocolo): Recibo`
- `gerarNumero(): string`

### 3.6 Controllers

TODOS usam `use RespostaApi;` e retornam no padrão.

#### CRUD Administrativo (mesmo padrão para todos):

`NaturezaController`, `AtoController`, `FormaPagamentoController`, `MeioPagamentoController`

Cada um com:
- `listar(Request)` → paginado, busca por nome, filtro por ativo → `$this->sucessoPaginado(...)`
- `criar(Request)` → validação → `$this->criado($dados)`
- `exibir($id)` → `$this->sucesso($dados)`
- `atualizar(Request, $id)` → `$this->sucesso($dados, 'Registro atualizado')`
- `excluir($id)` → soft delete → `$this->sucesso(null, 'Registro excluído')`
- `restaurar($id)` → `$this->sucesso($dados, 'Registro restaurado')`

`AtoFaixaController` (aninhado dentro de ato):
- `listar($atoId)`, `criar($atoId)`, `atualizar($atoId, $id)`, `excluir($atoId, $id)`

#### Operacional:

`ProtocoloController`:
- `listar` → filtros: status, data_inicio, data_fim, usuario_id, solicitante, matricula
- `criar` → cria protocolo com itens
- `exibir($id)` → retorna com itens, pagamentos, isenções, contratos e recibos
- `atualizar($id)`
- `cancelar($id)`
- `adicionarItem($id)`
- `removerItem($id, $itemId)`
- `recalcular($id)`

`ProtocoloPagamentoController`:
- `listar($protocoloId)`
- `registrar($protocoloId)`
- `estornar($protocoloId, $pagamentoId)`

`ProtocoloIsencaoController`:
- `listar($protocoloId)`
- `registrar($protocoloId)`

`ContratoController`:
- `listar` → filtros: status, tipo, data_entrada, protocolo_id, responsavel
- `criar($protocoloId)` → cria contrato vinculado ao protocolo
- `exibir($id)` → retorna com exigências, andamentos e dados do protocolo
- `atualizar($id)`
- `alterarStatus($id)` → recebe novo status + descrição, registra andamento
- `concluir($id)`
- `cancelar($id)`

`ContratoExigenciaController`:
- `listar($contratoId)`
- `criar($contratoId)`
- `cumprir($contratoId, $exigenciaId)` → marca como cumprida

`ReciboController`:
- `listar` → filtros: data, protocolo_id
- `exibir($id)`
- `gerar($protocoloId)` → gera recibo

### 3.7 Rotas (api.php)

TODAS no singular:

```php
Route::middleware('auth:api')->group(function () {

    // ===================================
    // ADMINISTRATIVO
    // ===================================

    // Natureza
    Route::prefix('natureza')->group(function () {
        Route::get('/',             [NaturezaController::class, 'listar']);
        Route::post('/',            [NaturezaController::class, 'criar']);
        Route::get('/{id}',        [NaturezaController::class, 'exibir']);
        Route::put('/{id}',        [NaturezaController::class, 'atualizar']);
        Route::delete('/{id}',     [NaturezaController::class, 'excluir']);
        Route::post('/{id}/restaurar', [NaturezaController::class, 'restaurar']);
    });

    // Ato
    Route::prefix('ato')->group(function () {
        Route::get('/',             [AtoController::class, 'listar']);
        Route::post('/',            [AtoController::class, 'criar']);
        Route::get('/{id}',        [AtoController::class, 'exibir']);
        Route::put('/{id}',        [AtoController::class, 'atualizar']);
        Route::delete('/{id}',     [AtoController::class, 'excluir']);
        Route::post('/{id}/restaurar',  [AtoController::class, 'restaurar']);
        Route::post('/{id}/calcular',   [AtoController::class, 'calcularValor']);

        // Faixas
        Route::prefix('/{atoId}/faixa')->group(function () {
            Route::get('/',         [AtoFaixaController::class, 'listar']);
            Route::post('/',        [AtoFaixaController::class, 'criar']);
            Route::put('/{id}',    [AtoFaixaController::class, 'atualizar']);
            Route::delete('/{id}', [AtoFaixaController::class, 'excluir']);
        });
    });

    // Forma de Pagamento
    Route::prefix('forma-pagamento')->group(function () {
        Route::get('/',             [FormaPagamentoController::class, 'listar']);
        Route::post('/',            [FormaPagamentoController::class, 'criar']);
        Route::get('/{id}',        [FormaPagamentoController::class, 'exibir']);
        Route::put('/{id}',        [FormaPagamentoController::class, 'atualizar']);
        Route::delete('/{id}',     [FormaPagamentoController::class, 'excluir']);
        Route::post('/{id}/restaurar', [FormaPagamentoController::class, 'restaurar']);
    });

    // Meio de Pagamento
    Route::prefix('meio-pagamento')->group(function () {
        Route::get('/',             [MeioPagamentoController::class, 'listar']);
        Route::post('/',            [MeioPagamentoController::class, 'criar']);
        Route::get('/{id}',        [MeioPagamentoController::class, 'exibir']);
        Route::put('/{id}',        [MeioPagamentoController::class, 'atualizar']);
        Route::delete('/{id}',     [MeioPagamentoController::class, 'excluir']);
        Route::post('/{id}/restaurar', [MeioPagamentoController::class, 'restaurar']);
    });

    // ===================================
    // OPERACIONAL
    // ===================================

    // Protocolo
    Route::prefix('protocolo')->group(function () {
        Route::get('/',             [ProtocoloController::class, 'listar']);
        Route::post('/',            [ProtocoloController::class, 'criar']);
        Route::get('/{id}',        [ProtocoloController::class, 'exibir']);
        Route::put('/{id}',        [ProtocoloController::class, 'atualizar']);
        Route::post('/{id}/cancelar',   [ProtocoloController::class, 'cancelar']);
        Route::post('/{id}/recalcular', [ProtocoloController::class, 'recalcular']);

        // Itens
        Route::post('/{id}/item',              [ProtocoloController::class, 'adicionarItem']);
        Route::delete('/{id}/item/{itemId}',   [ProtocoloController::class, 'removerItem']);

        // Pagamentos
        Route::get('/{id}/pagamento',                      [ProtocoloPagamentoController::class, 'listar']);
        Route::post('/{id}/pagamento',                     [ProtocoloPagamentoController::class, 'registrar']);
        Route::post('/{id}/pagamento/{pagId}/estornar',    [ProtocoloPagamentoController::class, 'estornar']);

        // Isenções
        Route::get('/{id}/isencao',            [ProtocoloIsencaoController::class, 'listar']);
        Route::post('/{id}/isencao',           [ProtocoloIsencaoController::class, 'registrar']);

        // Contrato (criar a partir do protocolo)
        Route::post('/{id}/contrato',          [ContratoController::class, 'criar']);

        // Recibo
        Route::post('/{id}/recibo',            [ReciboController::class, 'gerar']);
    });

    // Contrato (consultas e operações independentes)
    Route::prefix('contrato')->group(function () {
        Route::get('/',             [ContratoController::class, 'listar']);
        Route::get('/{id}',        [ContratoController::class, 'exibir']);
        Route::put('/{id}',        [ContratoController::class, 'atualizar']);
        Route::post('/{id}/status',     [ContratoController::class, 'alterarStatus']);
        Route::post('/{id}/concluir',   [ContratoController::class, 'concluir']);
        Route::post('/{id}/cancelar',   [ContratoController::class, 'cancelar']);

        // Exigências
        Route::get('/{id}/exigencia',               [ContratoExigenciaController::class, 'listar']);
        Route::post('/{id}/exigencia',              [ContratoExigenciaController::class, 'criar']);
        Route::post('/{id}/exigencia/{exId}/cumprir', [ContratoExigenciaController::class, 'cumprir']);

        // Andamentos (somente leitura — andamentos são criados automaticamente)
        Route::get('/{id}/andamento',               [ContratoController::class, 'andamentos']);
    });

    // Recibo (consulta geral)
    Route::prefix('recibo')->group(function () {
        Route::get('/',             [ReciboController::class, 'listar']);
        Route::get('/{id}',        [ReciboController::class, 'exibir']);
    });
});
```

### 3.8 Seeders

#### `NaturezaSeeder`:
- Emolumento
- Taxa de Fiscalização Judiciária (TFJ)
- FUNDESP (Fundo Especial de Reaparelhamento — MT)
- ISS / ISSQN

#### `FormaPagamentoSeeder`:
- Dinheiro
- PIX
- Cartão de Débito
- Cartão de Crédito
- Boleto Bancário
- Transferência Bancária

#### `MeioPagamentoSeeder`:
- Caixa Principal (→ Dinheiro)
- Conta PIX Cartório (→ PIX)

### 3.9 Aplicar Auditoria

Se existir `php artisan auditoria:migration`, executar:
```bash
php artisan auditoria:migration natureza,ato,ato_faixa,forma_pagamento,meio_pagamento,protocolo,protocolo_item,protocolo_pagamento,protocolo_isencao,contrato,contrato_exigencia,contrato_andamento,recibo
```

---

## FASE 4 — VALIDAÇÃO

1. `php artisan migrate --pretend` — verificar migrations
2. `php artisan route:list` — verificar rotas (todas no singular)
3. Rodar seeders
4. Verificar que TODOS os controllers usam `RespostaApi` e retornam no padrão
5. Verificar que o exception handler retorna no padrão
6. Verificar que TODOS os endpoints retornam:
   - `{ "sucesso": true/false, "mensagem": "...", "dados": ... }`
7. Listar TODOS os arquivos criados/alterados

---

## IMPORTANTE

- TABELAS E ROTAS NO **SINGULAR** — `protocolo`, `contrato`, `ato` (NUNCA plural)
- RETORNO DA API **PADRONIZADO** em absolutamente todas as respostas, incluindo erros
- NÃO criar tabela separada de `cliente`/`pessoa` — solicitante é registrado no protocolo
- NÃO criar módulo de permissões — será feito em prompt separado
- Valores em DECIMAL(15,2)
- Números (protocolo, contrato, recibo) gerados automaticamente por ano
- Status do protocolo atualiza automaticamente ao registrar pagamento/isenção
- Contrato registra andamento automaticamente ao mudar de status
- Soft delete em todas as tabelas de negócio (exceto contrato_andamento)
- Se algo estiver ambíguo, PERGUNTAR antes de implementar
- Commitar cada fase separadamente
```
