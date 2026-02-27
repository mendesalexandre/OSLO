# PROMPT — Sistema de Auditoria com Triggers PostgreSQL (OSLO)

Cole este prompt no Claude CLI na raiz do projeto OSLO.

---

```
Você está no projeto OSLO, um sistema de gestão para cartório (1º Ofício de Registro de Imóveis de Sinop/MT), desenvolvido em Laravel + Quasar 2 (Vue 3) + PostgreSQL.

## OBJETIVO

Implementar um sistema completo de auditoria usando **triggers PostgreSQL** com schema separado `auditoria`. O sistema deve capturar INSERT, UPDATE e DELETE em tabelas auditadas, identificando o usuário da aplicação via variável de sessão do PostgreSQL.

## REGRAS GERAIS

- Tudo em **português** (nomes de tabelas, colunas, variáveis, comentários), exceto convenções do Laravel que quebrariam o framework (queues, jobs, etc.)
- Seguir a arquitetura e padrões já existentes no projeto (Controllers, Services, Models, etc.)
- NÃO alterar lógica de negócio existente — apenas adicionar a camada de auditoria
- Usar a conexão PostgreSQL já configurada no projeto

## FASE 1 — ANÁLISE (EXECUTAR PRIMEIRO, NÃO PULAR)

Antes de criar qualquer arquivo, analisar:

1. Ler `config/database.php` — identificar a conexão PostgreSQL
2. Ler `config/auth.php` — identificar o guard atual (deve ser JWT após migração recente)
3. Listar todas as migrations em `database/migrations/` — mapear as tabelas existentes
4. Ler o Model User — verificar tabela (`usuario`), traits, e estrutura
5. Ler `app/Http/Kernel.php` ou `bootstrap/app.php` — identificar onde registrar middleware
6. Ler `routes/api.php` — entender o padrão de agrupamento de rotas
7. Verificar se existe algum sistema de auditoria atual (Spatie Activitylog, observers, etc.)
8. Ler 1-2 Controllers existentes para seguir o padrão do projeto
9. Verificar a versão do Laravel (`composer.json`)

Apresentar um resumo do que encontrou antes de prosseguir.

## FASE 2 — IMPLEMENTAÇÃO

### 2.1 Migration: Schema e Função Genérica

Criar migration que:
- Cria `CREATE SCHEMA IF NOT EXISTS auditoria`
- Cria tabela `auditoria.registro` com:
  - `id` BIGSERIAL PK
  - `tabela_schema` VARCHAR(128) DEFAULT 'public'
  - `tabela_nome` VARCHAR(128) NOT NULL
  - `operacao` VARCHAR(10) CHECK (IN 'INSERT','UPDATE','DELETE')
  - `usuario_id` BIGINT (nullable — pode ser NULL se operação for via SQL direto)
  - `registro_id` TEXT
  - `dados_anteriores` JSONB
  - `dados_novos` JSONB
  - `campos_alterados` TEXT[] (apenas no UPDATE, lista dos campos que mudaram)
  - `ip_address` VARCHAR(45)
  - `user_agent` TEXT
  - `registrado_em` TIMESTAMPTZ DEFAULT NOW()
- Criar índices para: tabela_nome+registrado_em, usuario_id+registrado_em, registro_id, operacao, e GIN nos JSONB
- Criar função `auditoria.fn_registrar_auditoria()` RETURNS TRIGGER que:
  - Lê `current_setting('app.usuario_id', TRUE)` para o usuario_id
  - Lê `current_setting('app.ip_address', TRUE)` para o IP
  - Lê `current_setting('app.user_agent', TRUE)` para o user-agent
  - No UPDATE: compara campo a campo e só registra se algo mudou de fato, preenchendo `campos_alterados`
  - No INSERT: grava dados_novos
  - No DELETE: grava dados_anteriores
- Criar função auxiliar `auditoria.fn_aplicar_auditoria(p_schema TEXT, p_tabela TEXT)` que aplica o trigger AFTER INSERT OR UPDATE OR DELETE em qualquer tabela
- Criar função auxiliar `auditoria.fn_remover_auditoria(p_schema TEXT, p_tabela TEXT)` que remove o trigger
- Criar função `auditoria.fn_limpar_registros_antigos(p_dias INTEGER DEFAULT 365)` para retenção

### 2.2 Middleware: AuditoriaContexto

Criar middleware `App\Http\Middleware\AuditoriaContexto` que:
- Executa ANTES do controller
- Faz `SET LOCAL app.usuario_id = {auth()->id()}` (0 se não autenticado)
- Faz `SET LOCAL app.ip_address = {request->ip()}`
- Faz `SET LOCAL app.user_agent = {request->userAgent()}` (truncar em 512 chars)
- Usa try/catch — se falhar, não bloqueia o request
- Registrar no grupo de middleware `api`

### 2.3 Trait: Auditavel

Criar trait `App\Traits\Auditavel` com métodos:
- `auditoria(int $limite = 50): Collection` — histórico do registro
- `ultimaAlteracao(): ?object` — última alteração
- `static auditoriaTabela(int $limite = 100): Collection` — histórico da tabela
- `static auditoriaPorUsuario(int $usuarioId, int $limite = 50): Collection`
- `static auditoriaPorPeriodo(string $inicio, string $fim, int $limite = 500): Collection`
- Todos devem decodificar JSONB e parsear o array PostgreSQL de `campos_alterados`

### 2.4 Adicionar Trait no Model User

Adicionar `use Auditavel;` no Model User existente. NÃO alterar mais nada no Model.

### 2.5 Controller: AuditoriaController

Criar controller com endpoints:
- `GET /` — listar com filtros (tabela, operacao, usuario_id, data_inicio, data_fim, limite, pagina)
- `GET /tabela/{tabela}` — por tabela
- `GET /tabela/{tabela}/registro/{registroId}` — histórico de um registro
- `GET /usuario/{usuarioId}` — por usuário
- `GET /tabelas` — listar tabelas auditadas (consultar information_schema.triggers)
- `GET /estatisticas` — totais por tabela/operação e por dia (últimos 30 dias)

### 2.6 Rotas

Adicionar em `routes/api.php`:
```php
Route::prefix('auditoria')->middleware('auth:api')->group(function () {
    Route::get('/', [AuditoriaController::class, 'listar']);
    Route::get('/tabela/{tabela}', [AuditoriaController::class, 'porTabela']);
    Route::get('/tabela/{tabela}/registro/{registroId}', [AuditoriaController::class, 'porRegistro']);
    Route::get('/usuario/{usuarioId}', [AuditoriaController::class, 'porUsuario']);
    Route::get('/tabelas', [AuditoriaController::class, 'tabelasAuditadas']);
    Route::get('/estatisticas', [AuditoriaController::class, 'estatisticas']);
});
```

### 2.7 Comandos Artisan

Criar DOIS comandos:

---

#### Comando 1: `auditoria:aplicar` — Gerenciar triggers em tempo real (aplica direto no banco)

Signature:
```
auditoria:aplicar
    {tabelas? : Nomes das tabelas separados por vírgula}
    {--schema=public : Schema da tabela}
    {--remover : Remove a auditoria ao invés de aplicar}
    {--todas : Aplica em todas as tabelas do schema}
    {--listar : Lista tabelas com auditoria ativa}
```

Exemplos:
```bash
php artisan auditoria:aplicar usuario,protocolo       # aplica direto
php artisan auditoria:aplicar --todas                  # todas as tabelas
php artisan auditoria:aplicar usuario --remover        # remove
php artisan auditoria:aplicar --listar                 # lista ativas
```

Tabelas a ignorar no `--todas`: migrations, password_resets, password_reset_tokens, personal_access_tokens, failed_jobs, jobs, job_batches, cache, cache_locks, sessions.

---

#### Comando 2: `auditoria:migration` — Gerar arquivo de migration para tabelas (NÃO executa, apenas gera o arquivo)

Signature:
```
auditoria:migration
    {tabelas : Nomes das tabelas separados por vírgula}
    {--schema=public : Schema das tabelas}
    {--remover : Gera migration de remoção ao invés de aplicação}
```

Este comando deve:

1. Receber uma ou mais tabelas separadas por vírgula
2. **Validar se cada tabela existe** no banco consultando `information_schema.tables`. Se não existir, exibir `✗ {tabela} — tabela não encontrada no schema {schema}` e pular.
3. **Verificar se a tabela já tem trigger** de auditoria consultando `information_schema.triggers WHERE trigger_name LIKE 'trg_auditoria_%'`. Se já tiver e NÃO for `--remover`, exibir `⚠ {tabela} — já possui trigger de auditoria (pulada)` e pular.
4. **Gerar o arquivo de migration** em `database/migrations/` com:
   - Timestamp no formato padrão do Laravel `Y_m_d_His`
   - Nome: `{timestamp}_aplicar_auditoria_{tabela1}_{tabela2}_{etc}.php`
   - Se `--remover`: `{timestamp}_remover_auditoria_{tabela1}_{tabela2}_{etc}.php`
   - Se o nome ficar muito longo (mais de 5 tabelas), truncar: `{timestamp}_aplicar_auditoria_multiplas_tabelas.php`
5. **Conteúdo da migration gerada**:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Aplica auditoria nas tabelas: usuario, forma_pagamento, natureza
 *
 * Gerado automaticamente por: php artisan auditoria:migration
 * Data: {data_geracao}
 */
return new class extends Migration
{
    private array $tabelas = [
        ['schema' => 'public', 'tabela' => 'usuario'],
        ['schema' => 'public', 'tabela' => 'forma_pagamento'],
        ['schema' => 'public', 'tabela' => 'natureza'],
    ];

    public function up(): void
    {
        foreach ($this->tabelas as $item) {
            DB::statement(
                "SELECT auditoria.fn_aplicar_auditoria(?, ?);",
                [$item['schema'], $item['tabela']]
            );
        }
    }

    public function down(): void
    {
        foreach ($this->tabelas as $item) {
            DB::statement(
                "SELECT auditoria.fn_remover_auditoria(?, ?);",
                [$item['schema'], $item['tabela']]
            );
        }
    }
};
```

Se for `--remover`, inverter: `up()` chama `fn_remover_auditoria` e `down()` chama `fn_aplicar_auditoria`.

6. **Exibir resumo no terminal**:
```
Gerando migration de auditoria...

  ✓ usuario — trigger será aplicado
  ✓ forma_pagamento — trigger será aplicado
  ✗ tabela_fake — tabela não encontrada no schema public
  ⚠ protocolo — já possui trigger de auditoria (pulada)

Migration criada:
  database/migrations/2024_01_15_143022_aplicar_auditoria_usuario_forma_pagamento.php

Tabelas com auditoria ativa após migração:
  +--------+-------------------+
  | Schema | Tabela            |
  +--------+-------------------+
  | public | usuario           |
  | public | forma_pagamento   |
  | public | protocolo         | (já existia)
  +--------+-------------------+

Próximo passo: php artisan migrate
```

7. Se NENHUMA tabela for válida (todas inexistentes ou já auditadas), NÃO gerar migration e exibir:
```
Nenhuma tabela válida para gerar migration.
```

Exemplos de uso:
```bash
# Gerar migration para várias tabelas
php artisan auditoria:migration usuario,forma_pagamento,meio_pagamento,natureza

# Gerar migration de remoção
php artisan auditoria:migration usuario,protocolo --remover

# Tabela em outro schema
php artisan auditoria:migration conta --schema=financeiro
```

---

### 2.8 Scheduler (Limpeza)

Adicionar no scheduler do Laravel uma tarefa diária que executa `auditoria.fn_limpar_registros_antigos(365)`.

## FASE 3 — VALIDAÇÃO

Após implementar tudo:

1. Verificar se todas as migrations rodam sem erro: `php artisan migrate --pretend`
2. Verificar se o middleware está registrado corretamente
3. Verificar se as rotas aparecem: `php artisan route:list --path=auditoria`
4. Verificar se os comandos aparecem: `php artisan list auditoria`
5. Testar: `php artisan auditoria:aplicar --listar`
6. Testar: `php artisan auditoria:migration usuario` (verificar se gera o arquivo correto)
7. Listar todos os arquivos criados/alterados com resumo

## IMPORTANTE

- NÃO usar Spatie ou qualquer pacote externo de auditoria
- NÃO usar Eloquent observers — a auditoria é 100% via triggers PostgreSQL
- O middleware apenas seta as variáveis de sessão, quem faz o trabalho pesado é o trigger
- Usar `SET LOCAL` (não `SET`) para que as variáveis existam apenas na transação atual
- O comando `auditoria:migration` é GERADOR de migrations, NÃO executor — ele cria o arquivo .php, o dev roda `php artisan migrate` depois
- O comando `auditoria:aplicar` é EXECUTOR direto — aplica/remove triggers imediatamente no banco sem criar migration
- Se encontrar algo inesperado na análise (Fase 1), perguntar antes de prosseguir
- Commitar cada fase separadamente se possível
```
