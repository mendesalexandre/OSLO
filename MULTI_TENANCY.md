# 📚 Documentação - Sistema Multi-Tenancy com Trial

> Documentação completa da implementação do sistema de multi-tenancy (múltiplas empresas) com período de teste gratuito de 7 dias no OSLO.

**Data de Implementação:** 08/02/2026
**Versão:** 1.0.0

---

## 📋 Índice

1. [Visão Geral](#visão-geral)
2. [Arquitetura](#arquitetura)
3. [Banco de Dados](#banco-de-dados)
4. [Backend](#backend)
5. [Frontend](#frontend)
6. [Fluxos de Uso](#fluxos-de-uso)
7. [Segurança e Isolamento](#segurança-e-isolamento)
8. [APIs Externas](#apis-externas)
9. [Testes](#testes)
10. [Manutenção](#manutenção)

---

## 🎯 Visão Geral

### Contexto de Negócio

O OSLO é um sistema de gestão cartorária usado por **empresas clientes** de cartórios:
- Escritórios de advocacia
- Imobiliárias e incorporadoras
- Contadores e despachantes
- Fintechs de crédito imobiliário

### Funcionalidades Implementadas

✅ **Cadastro de Empresas**
- Cadastro público sem necessidade de login
- Busca automática de dados por CNPJ (BrasilAPI)
- Criação automática de usuário administrador
- Verificação de email obrigatória

✅ **Sistema de Planos**
- 4 planos disponíveis: Free, Básico, Profissional, Enterprise
- Trial gratuito de 7 dias para todas as empresas
- Limites configuráveis (usuários, protocolos/mês)

✅ **Isolamento de Dados (Multi-Tenancy)**
- Cada empresa só acessa seus próprios dados
- Global Scope automático em todos os Models
- Validação em middlewares

✅ **Modo Read-Only**
- Sistema bloqueia operações de escrita após trial expirado
- Banner visual informando status do trial
- Notificações contextuais

---

## 🏗️ Arquitetura

### Diagrama de Relacionamentos

```
┌─────────────┐
│   Plano     │
│  - Free     │
│  - Básico   │
│  - Prof.    │
│  - Enter.   │
└──────┬──────┘
       │ 1:N
       │
┌──────▼──────────────────┐
│      Empresa            │
│  - razao_social         │
│  - cnpj                 │
│  - status (trial/ativo) │
│  - trial_fim            │
│  - plano_id             │
└──────┬──────────────────┘
       │ 1:N
       │
┌──────▼──────────────────┐       ┌─────────────────┐
│      Usuario            │       │   Protocolo     │
│  - nome                 │       │   Contrato      │
│  - email                │       │   Recibo        │
│  - empresa_id           │◄──────┤   Pagamento     │
│  - is_admin_empresa     │  N:1  │   Andamento     │
│  - email_verificado_em  │       │   Anotacao      │
└─────────────────────────┘       └─────────────────┘
                                   (todos com empresa_id)
```

### Padrões Utilizados

- **Multi-Tenancy:** Isolamento por `empresa_id` em todas as tabelas de dados
- **Global Scope:** Filtro automático de queries por empresa
- **Soft Deletes:** Exclusão lógica com `data_exclusao`
- **Repository Pattern:** Services para lógica de negócio
- **API Response Pattern:** Trait `RespostaApi` para padronização

---

## 💾 Banco de Dados

### Tabelas Criadas

#### 1. `plano`
Armazena os planos disponíveis no sistema.

```sql
CREATE TABLE plano (
    id BIGSERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL,
    descricao TEXT,
    valor_mensal NUMERIC(10,2) DEFAULT 0,
    valor_anual NUMERIC(10,2) DEFAULT 0,
    max_usuarios INTEGER DEFAULT 1,
    max_protocolos_mes INTEGER DEFAULT 10,
    permite_relatorios BOOLEAN DEFAULT FALSE,
    permite_api BOOLEAN DEFAULT FALSE,
    permite_integracao BOOLEAN DEFAULT FALSE,
    funcionalidades JSONB,
    ordem INTEGER DEFAULT 0,
    ativo BOOLEAN DEFAULT TRUE,
    destaque BOOLEAN DEFAULT FALSE,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_alteracao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Planos Criados:**
| Plano | Valor/Mês | Usuários | Protocolos/Mês | Recursos |
|-------|-----------|----------|----------------|----------|
| Free | R$ 0 | 1 | 10 | Básicos |
| Básico | R$ 97 | 5 | 100 | + Relatórios |
| Profissional | R$ 197 | 15 | 500 | + API + Integração |
| Enterprise | R$ 497 | 50 | Ilimitado | Tudo + Suporte |

#### 2. `empresa`
Armazena os dados das empresas clientes.

```sql
CREATE TABLE empresa (
    id BIGSERIAL PRIMARY KEY,
    razao_social VARCHAR(255) NOT NULL,
    nome_fantasia VARCHAR(255),
    cnpj VARCHAR(18) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    telefone VARCHAR(20),
    estado VARCHAR(2),
    cidade VARCHAR(100),
    endereco VARCHAR(255),
    plano_id BIGINT REFERENCES plano(id),
    status VARCHAR(30) DEFAULT 'trial', -- trial, ativo, read_only, suspenso, cancelado
    trial_inicio DATE,
    trial_fim DATE,
    assinatura_inicio DATE,
    assinatura_fim DATE,
    periodo_assinatura VARCHAR(10), -- mensal, anual
    logo VARCHAR(255),
    configuracoes JSONB,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_alteracao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_exclusao TIMESTAMP
);
```

**Status Possíveis:**
- `trial` — Período de teste ativo
- `ativo` — Assinatura ativa
- `read_only` — Trial expirado, apenas leitura
- `suspenso` — Empresa suspensa por inadimplência
- `cancelado` — Assinatura cancelada

#### 3. Alterações em `usuario`
Adicionados campos para multi-tenancy.

```sql
ALTER TABLE usuario ADD COLUMN empresa_id BIGINT REFERENCES empresa(id);
ALTER TABLE usuario ADD COLUMN is_admin_empresa BOOLEAN DEFAULT FALSE;
ALTER TABLE usuario ADD COLUMN token_verificacao VARCHAR(64);
```

#### 4. Alterações em Tabelas de Dados
Adicionado `empresa_id` em todas as tabelas que armazenam dados do cliente:

- ✅ `protocolo`
- ✅ `contrato`
- ✅ `recibo`
- ✅ `protocolo_pagamento`
- ✅ `protocolo_andamento`
- ✅ `protocolo_anotacao`

**Tabelas que NÃO têm `empresa_id` (dados globais):**
- ❌ `plano`
- ❌ `natureza`
- ❌ `ato`
- ❌ `forma_pagamento`
- ❌ `meio_pagamento`
- ❌ `etapa`

### Migrations Criadas

```
2026_02_08_220000_criar_tabela_plano.php
2026_02_08_220100_criar_tabela_empresa.php
2026_02_08_220200_adicionar_empresa_id_em_usuario.php
2026_02_08_220300_adicionar_empresa_id_em_protocolo.php
2026_02_08_220400_adicionar_empresa_id_em_contrato.php
2026_02_08_220500_adicionar_empresa_id_em_recibo.php
2026_02_08_220600_adicionar_empresa_id_em_protocolo_pagamento.php
2026_02_08_220700_adicionar_empresa_id_em_protocolo_andamento.php
2026_02_08_220800_adicionar_empresa_id_em_protocolo_anotacao.php
2026_02_08_220900_auditoria_aplicar_tabelas_multi_tenant.php
```

### Seeders

**PlanoSeeder**
- Popula tabela `plano` com os 4 planos
- Idempotente (usa `updateOrCreate`)
- Executar: `php artisan db:seed --class=PlanoSeeder`

---

## 🔧 Backend

### Models

#### 1. **Plano** (`app/Models/Plano.php`)

**Relacionamentos:**
```php
public function empresas(): HasMany
```

**Helpers:**
```php
public function isIlimitado(string $recurso): bool
public function permiteUsuarios(int $quantidade): bool
public function permiteProtocolos(int $quantidade): bool
public function descontoAnual(): float
public function economiaAnual(): float
```

**Scopes:**
```php
public function scopeAtivos($query)
public function scopeOrdenado($query)
```

#### 2. **Empresa** (`app/Models/Empresa.php`)

**Constantes:**
```php
const STATUS_TRIAL = 'trial';
const STATUS_ATIVO = 'ativo';
const STATUS_READ_ONLY = 'read_only';
const STATUS_SUSPENSO = 'suspenso';
const STATUS_CANCELADO = 'cancelado';
```

**Relacionamentos:**
```php
public function plano(): BelongsTo
public function usuarios(): HasMany
public function adminPrincipal(): HasOne
public function protocolos(): HasMany
public function contratos(): HasMany
public function recibos(): HasMany
```

**Helpers de Status:**
```php
public function isTrialAtivo(): bool
public function isTrialExpirado(): bool
public function isAtiva(): bool
public function isReadOnly(): bool
public function diasRestantesTrial(): int
public function trialExpiraHoje(): bool
```

**Helpers de Limites:**
```php
public function podeAdicionarUsuario(): bool
public function usuariosDisponiveis(): int
public function podeCriarProtocolo(): bool
public function protocolosMesAtual(): int
public function protocolosDisponiveis(): int
```

**Ações:**
```php
public function tornarReadOnly(): void
public function ativarAssinatura(string $periodo = 'mensal'): void
public function cancelarAssinatura(): void
```

#### 3. **User** (`app/Models/User.php`)

**Novos Campos:**
```php
'empresa_id'
'is_admin_empresa'
'email_verificado_em'
'token_verificacao'
```

**Novos Métodos:**
```php
public function empresa(): BelongsTo
public function isAdminEmpresa(): bool
public function emailVerificado(): bool
public function marcarEmailVerificado(): void
public function gerarTokenVerificacao(): string
```

**AuthController Atualizado:**
- Retorna dados da empresa no login
- Retorna informações de trial
- Retorna status read-only

### Traits

#### **PertenceEmpresa** (`app/Traits/PertenceEmpresa.php`)

Trait aplicado em todos os Models que pertencem a uma empresa.

**Funcionalidades:**
- Aplica `EmpresaScope` automaticamente
- Preenche `empresa_id` ao criar registro
- Adiciona relacionamento `empresa()`
- Adiciona scope `semEmpresa()` para queries administrativas

**Uso:**
```php
class Protocolo extends Model
{
    use PertenceEmpresa;
}
```

**Models que usam:**
- Protocolo
- Contrato
- Recibo
- ProtocoloPagamento
- ProtocoloAndamento
- ProtocoloAnotacao

### Scopes

#### **EmpresaScope** (`app/Models/Scopes/EmpresaScope.php`)

Global Scope que filtra automaticamente por `empresa_id`.

```php
public function apply(Builder $builder, Model $model): void
{
    if (auth()->check() && auth()->user()->empresa_id) {
        $builder->where($model->getTable() . '.empresa_id', auth()->user()->empresa_id);
    }
}
```

**Importante:**
- Aplicado automaticamente pelo trait `PertenceEmpresa`
- Garante isolamento de dados
- Usuário **nunca** vê dados de outra empresa

### Services

#### **CadastroEmpresaService** (`app/Services/CadastroEmpresaService.php`)

**Métodos:**

```php
// Cadastra nova empresa com trial de 7 dias
public function cadastrar(array $dados): array

// Verifica email usando token
public function verificarEmail(string $token): User

// Reenvia email de verificação
public function reenviarVerificacao(User $usuario): void

// Envia email (privado)
private function enviarEmailVerificacao(User $usuario, string $token): void
```

**Fluxo do Cadastro:**
1. Busca plano "Free"
2. Cria empresa com trial de 7 dias
3. Cria usuário administrador
4. Gera token de verificação
5. Envia email

### Controllers

#### **CadastroController** (`app/Http/Controllers/CadastroController.php`)

**Rotas Públicas (sem autenticação):**

```php
POST   /api/publica/cadastro              → cadastrar()
GET    /api/publica/verificar-email/{token} → verificarEmail($token)
POST   /api/publica/reenviar-verificacao  → reenviarVerificacao()
GET    /api/publica/plano                 → planosPublicos()
```

**Validações:**
- CNPJ único
- Email único (empresa e usuário)
- Senha mínima 8 caracteres
- Senha confirmada

### Middlewares

#### **VerificarTrial** (`app/Http/Middleware/VerificarTrial.php`)

**Funcionamento:**
1. Verifica se empresa está em trial expirado
2. Se expirado, muda status para `read_only`
3. Bloqueia métodos POST/PUT/PATCH/DELETE
4. Permite rotas essenciais (login, logout, assinatura)
5. Retorna erro 403 com código `TRIAL_EXPIRADO`

**Rotas Permitidas em Read-Only:**
- `/login`
- `/logout`
- `/perfil`
- `/assinatura`
- `/plano`
- `/auth/*`

**Headers Adicionados:**
```
X-Trial-Dias-Restantes: 3
X-Trial-Expira-Em: 2026-02-15
```

#### **VerificarEmailVerificado** (`app/Http/Middleware/VerificarEmailVerificado.php`)

**Funcionamento:**
1. Verifica se `email_verificado_em` está preenchido
2. Se não verificado, bloqueia acesso
3. Retorna erro 403 com código `EMAIL_NAO_VERIFICADO`

**Rotas Permitidas sem Verificação:**
- `/logout`
- `/verificar-email`
- `/reenviar-verificacao`

**Aplicação:**
```php
// routes/api.php
Route::middleware(['auth:api', 'email.verificado', 'trial'])->group(function () {
    // Todas as rotas protegidas
});
```

### Email Template

#### **verificacao.blade.php** (`resources/views/emails/verificacao.blade.php`)

**Design:**
- Header escuro (#1A1A1A) com logo laranja (#FF7A00)
- Box de trial destacando 7 dias grátis
- Botão CTA laranja
- Footer com informações de contato

**Variáveis:**
- `$nome` — Nome do usuário
- `$empresa` — Razão social da empresa
- `$url` — Link de verificação
- `$diasTrial` — Dias de trial (7)

### Comandos Artisan

#### **AtivarEmailUsuario** (`app/Console/Commands/AtivarEmailUsuario.php`)

Comando para ativar email manualmente (útil para testes).

**Uso:**
```bash
# Ativar último usuário criado
php artisan usuario:ativar-email

# Ativar usuário específico
php artisan usuario:ativar-email usuario@exemplo.com
```

---

## 🎨 Frontend

### Páginas Criadas

#### 1. **CadastroPage** (`src/pages/publico/CadastroPage.vue`)

**Layout:**
- Split screen: 40% info / 60% formulário
- Coluna esquerda escura (#1A1A1A) com benefícios
- Coluna direita branca com formulário

**Funcionalidades:**
- ✅ Busca automática de CNPJ (BrasilAPI)
- ✅ Máscaras em CNPJ e telefone
- ✅ Validação em tempo real
- ✅ Toggle de visibilidade de senha
- ✅ Select de estados brasileiros

**Campos:**
- Razão Social *
- Nome Fantasia
- CNPJ * (com busca automática)
- Telefone
- Estado
- Cidade
- Nome do Responsável *
- E-mail *
- Senha * (mínimo 8 caracteres)
- Confirmar Senha *

**Rota:**
```
/cadastro
```

#### 2. **CadastroSucessoPage** (`src/pages/publico/CadastroSucessoPage.vue`)

**Funcionalidades:**
- Mensagem de sucesso
- Email destacado
- Botão para reenviar email
- Link para voltar ao login

**Rota:**
```
/cadastro-sucesso?email=usuario@exemplo.com
```

#### 3. **VerificarEmailPage** (`src/pages/publico/VerificarEmailPage.vue`)

**Estados:**
- **Loading:** Verificando email...
- **Sucesso:** Email verificado! (com botão para login)
- **Erro:** Token inválido (com opção de reenvio)

**Rota:**
```
/verificar-email/:token
```

### Componentes

#### **TrialBanner** (`src/components/TrialBanner.vue`)

Banner exibido no topo do sistema durante o trial.

**Cores por Status:**
- 🟢 **Verde (amber-1):** > 3 dias restantes
- 🟡 **Amarelo (orange-1):** 1-3 dias restantes
- 🔴 **Vermelho (red-1):** Expira hoje ou expirado

**Mensagens:**
- "Você tem X dias restantes no período de teste"
- "Seu teste expira HOJE!"
- "Seu período de teste expirou. Assine um plano..."

**Ações:**
- Botão "Assinar Agora" → redireciona para `/planos`

### Services

#### **CnpjService** (`src/services/cnpjService.js`)

Service para consulta de CNPJ na BrasilAPI.

**Métodos:**
```javascript
validarCnpj(cnpj): boolean
limparCnpj(cnpj): string
formatarCnpj(cnpj): string
buscarDados(cnpj): Promise<Object>
formatarResposta(dados): Object
formatarTelefone(dddTelefone): string
formatarEndereco(dados): string
```

**API Utilizada:**
```
GET https://brasilapi.com.br/api/cnpj/v1/{cnpj}
```

**Dados Retornados:**
```javascript
{
  cnpj: string,
  razao_social: string,
  nome_fantasia: string,
  email: string,
  telefone: string,
  estado: string,
  cidade: string,
  endereco: string,
  situacao: string,
  data_abertura: string,
  natureza_juridica: string,
  porte: string,
  dados_completos: Object
}
```

**Tratamento de Erros:**
- 404 → "CNPJ não encontrado"
- 429 → "Muitas requisições. Aguarde..."
- Timeout → "Tempo excedido"
- Outros → "Erro ao buscar dados"

### Atualizações no Layout

#### **MainLayout.vue**

**Adições:**

1. **Seção de Empresa no Drawer:**
```vue
<div v-if="!miniState && empresa" class="oslo-sidebar__empresa">
  <div class="text-caption text-grey-6">Empresa</div>
  <div class="text-body2 text-weight-medium">
    {{ empresa.nome_fantasia || empresa.razao_social }}
  </div>
  <div v-if="empresa.is_trial_ativo" class="text-caption text-orange">
    {{ empresa.dias_restantes_trial }} dias restantes
  </div>
</div>
```

2. **Banner de Trial:**
```vue
<q-page-container>
  <trial-banner />
  <router-view />
</q-page-container>
```

#### **Axios Interceptor** (`src/boot/axios.js`)

**Adições:**

```javascript
// Tratamento de trial expirado
if (error.response?.status === 403 && error.response?.data?.codigo === 'TRIAL_EXPIRADO') {
  Notify.create({
    type: 'warning',
    message: 'Seu período de teste expirou',
    caption: 'Assine um plano para continuar',
    actions: [
      { label: 'Ver Planos', handler: () => router.push('/planos') }
    ]
  })
}

// Tratamento de email não verificado
if (error.response?.status === 403 && error.response?.data?.codigo === 'EMAIL_NAO_VERIFICADO') {
  Notify.create({
    type: 'warning',
    message: 'Verifique seu e-mail',
    caption: error.response.data.mensagem
  })
}
```

#### **Login Page**

**Adição:**
```vue
<div class="text-center q-mt-lg">
  <span class="text-grey-7">Não tem conta?</span>
  <router-link to="/cadastro" class="text-orange">
    Criar conta grátis
  </router-link>
</div>
```

### Rotas Adicionadas

```javascript
// routes.js
{
  path: '/cadastro',
  component: () => import('pages/publico/CadastroPage.vue'),
  meta: { publico: true }
},
{
  path: '/cadastro-sucesso',
  component: () => import('pages/publico/CadastroSucessoPage.vue'),
  meta: { publico: true }
},
{
  path: '/verificar-email/:token',
  component: () => import('pages/publico/VerificarEmailPage.vue'),
  meta: { publico: true }
}
```

---

## 🔄 Fluxos de Uso

### Fluxo 1: Cadastro de Nova Empresa

```
┌─────────────────────────────────────────────────────────────┐
│                                                             │
│  1. Usuário acessa /cadastro                                │
│     ↓                                                       │
│  2. Preenche dados da empresa                               │
│     ↓                                                       │
│  3. (Opcional) Busca CNPJ na BrasilAPI                      │
│     ↓                                                       │
│  4. Preenche dados do administrador                         │
│     ↓                                                       │
│  5. Clica em "Criar Conta Grátis"                           │
│     ↓                                                       │
│  6. Backend valida dados                                    │
│     ↓                                                       │
│  7. Cria empresa com:                                       │
│     - plano_id = Free                                       │
│     - status = trial                                        │
│     - trial_inicio = hoje                                   │
│     - trial_fim = hoje + 7 dias                             │
│     ↓                                                       │
│  8. Cria usuário admin com:                                 │
│     - empresa_id = empresa criada                           │
│     - is_admin_empresa = true                               │
│     - token_verificacao = token aleatório                   │
│     - email_verificado_em = null                            │
│     ↓                                                       │
│  9. Envia email de verificação                              │
│     ↓                                                       │
│  10. Redireciona para /cadastro-sucesso                     │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### Fluxo 2: Verificação de Email

```
┌─────────────────────────────────────────────────────────────┐
│                                                             │
│  1. Usuário recebe email                                    │
│     ↓                                                       │
│  2. Clica no link de verificação                            │
│     ↓                                                       │
│  3. Frontend carrega /verificar-email/{token}               │
│     ↓                                                       │
│  4. Backend busca usuário pelo token                        │
│     ↓                                                       │
│  5. Atualiza usuário:                                       │
│     - email_verificado_em = agora                           │
│     - token_verificacao = null                              │
│     ↓                                                       │
│  6. Retorna sucesso                                         │
│     ↓                                                       │
│  7. Frontend exibe mensagem de sucesso                      │
│     ↓                                                       │
│  8. Usuário clica em "Acessar o Sistema"                    │
│     ↓                                                       │
│  9. Redireciona para /login                                 │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### Fluxo 3: Login com Trial Ativo

```
┌─────────────────────────────────────────────────────────────┐
│                                                             │
│  1. Usuário faz login                                       │
│     ↓                                                       │
│  2. Middleware VerificarEmailVerificado:                    │
│     - Se email não verificado → BLOQUEIA                    │
│     ↓                                                       │
│  3. AuthController retorna:                                 │
│     - Dados do usuário                                      │
│     - Dados da empresa                                      │
│     - Status de trial                                       │
│     - Dias restantes                                        │
│     ↓                                                       │
│  4. Frontend armazena no authStore                          │
│     ↓                                                       │
│  5. MainLayout exibe:                                       │
│     - Empresa no drawer                                     │
│     - Banner de trial no topo                               │
│     ↓                                                       │
│  6. Middleware VerificarTrial em cada request:              │
│     - Verifica se trial expirou                             │
│     - Se expirado → muda status para read_only              │
│     - Adiciona headers X-Trial-*                            │
│     ↓                                                       │
│  7. Usuário usa o sistema normalmente                       │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### Fluxo 4: Trial Expirado (Read-Only)

```
┌─────────────────────────────────────────────────────────────┐
│                                                             │
│  1. Trial expira (trial_fim < hoje)                         │
│     ↓                                                       │
│  2. Próxima requisição:                                     │
│     ↓                                                       │
│  3. Middleware VerificarTrial detecta:                      │
│     - status = trial                                        │
│     - trial_fim < hoje                                      │
│     ↓                                                       │
│  4. Atualiza empresa:                                       │
│     - status = read_only                                    │
│     ↓                                                       │
│  5. Se método = POST/PUT/PATCH/DELETE:                      │
│     - Retorna erro 403                                      │
│     - Código: TRIAL_EXPIRADO                                │
│     ↓                                                       │
│  6. Axios interceptor detecta código:                       │
│     ↓                                                       │
│  7. Exibe notificação persistente:                          │
│     - "Seu período de teste expirou"                        │
│     - Botão "Ver Planos"                                    │
│     ↓                                                       │
│  8. Banner muda para vermelho:                              │
│     - "Seu período de teste expirou..."                     │
│     ↓                                                       │
│  9. Usuário pode:                                           │
│     - Visualizar dados (GET)                                │
│     - NÃO pode criar/editar/excluir                         │
│     - Pode acessar /planos para assinar                     │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔒 Segurança e Isolamento

### Isolamento de Dados (Multi-Tenancy)

#### Nível 1: Global Scope (Automático)

**EmpresaScope** filtra automaticamente todas as queries:

```php
// Query normal
Protocolo::all();
// SQL gerado: SELECT * FROM protocolo WHERE empresa_id = 123

// Query com where
Protocolo::where('status', 'pago')->get();
// SQL gerado: SELECT * FROM protocolo WHERE empresa_id = 123 AND status = 'pago'

// Query com relacionamentos
Protocolo::with('pagamentos')->get();
// SQL: SELECT * FROM protocolo WHERE empresa_id = 123
// SQL: SELECT * FROM protocolo_pagamento WHERE empresa_id = 123 AND protocolo_id IN (...)
```

#### Nível 2: Auto-fill na Criação

Trait **PertenceEmpresa** preenche automaticamente:

```php
// Criar protocolo
Protocolo::create([
    'numero' => '2026/000001',
    'solicitante_nome' => 'João Silva'
    // empresa_id é preenchido automaticamente
]);

// Registro criado com empresa_id do usuário logado
```

#### Nível 3: Middlewares

**Validação dupla** em cada request:
1. `auth:api` → Valida JWT e carrega usuário
2. `email.verificado` → Valida email verificado
3. `trial` → Valida status de trial e read-only

#### Nível 4: Foreign Keys

Constraints no banco garantem integridade:

```sql
FOREIGN KEY (empresa_id) REFERENCES empresa(id) ON DELETE CASCADE
```

### Queries Administrativas

Para queries que precisam acessar **todas as empresas** (relatórios admin, etc):

```php
// Remove o scope temporariamente
Protocolo::semEmpresa()->get();

// OU
Protocolo::withoutGlobalScope(EmpresaScope::class)->get();
```

⚠️ **IMPORTANTE:** Use com extremo cuidado! Apenas em contextos administrativos.

### Testes de Isolamento

**Cenário 1: Usuário da Empresa A tenta acessar dados da Empresa B**

```php
// Empresa A
$empresaA = Empresa::find(1);
$usuarioA = $empresaA->usuarios->first();
Auth::login($usuarioA);

// Tentar buscar protocolo da Empresa B
$protocoloB = Protocolo::find(999); // pertence à Empresa B
// Resultado: NULL (filtrado pelo EmpresaScope)
```

**Cenário 2: SQL Injection tentando bypass**

```php
// Tentativa de SQL Injection
$id = "1 OR empresa_id != " . auth()->user()->empresa_id;
Protocolo::find($id);
// Proteção: Laravel escapa parâmetros
// Scope adiciona WHERE empresa_id = X automaticamente
```

---

## 🌐 APIs Externas

### BrasilAPI - Consulta de CNPJ

**Endpoint:**
```
GET https://brasilapi.com.br/api/cnpj/v1/{cnpj}
```

**Características:**
- ✅ Gratuita
- ✅ Sem necessidade de token
- ✅ Dados da Receita Federal
- ✅ Timeout: 10 segundos

**Exemplo de Resposta:**
```json
{
  "cnpj": "19131243000197",
  "razao_social": "OPEN KNOWLEDGE BRASIL",
  "nome_fantasia": "REDE PELO CONHECIMENTO LIVRE",
  "cnae_fiscal": 9430800,
  "descricao_tipo_logradouro": "ALAMEDA",
  "logradouro": "SANTOS",
  "numero": "2441",
  "complemento": "CJ 113",
  "bairro": "JARDIM PAULISTA",
  "cep": 1419100,
  "uf": "SP",
  "codigo_municipio": 6291,
  "municipio": "SAO PAULO",
  "ddd_telefone_1": "1132854246",
  "email": "CONTATO@OK.ORG.BR"
}
```

**Limitações:**
- Rate limit: ~100 req/min
- Apenas CNPJs ativos
- Dados podem estar desatualizados

---

## 🧪 Testes

### Testar Cadastro Completo

```bash
# 1. Acessar página de cadastro
http://localhost:9000/cadastro

# 2. Preencher dados (ou usar CNPJ de teste)
CNPJ: 06.990.590/0001-23 (Google Brasil)

# 3. Verificar email no log do Laravel
tail -f storage/logs/laravel.log

# 4. Ativar email manualmente
php artisan usuario:ativar-email

# 5. Fazer login
http://localhost:9000/login

# 6. Verificar banner de trial
# 7. Verificar empresa no drawer
```

### Testar Isolamento de Dados

```php
// Tinker
php artisan tinker

// Criar 2 empresas
$empresa1 = Empresa::create([...]);
$empresa2 = Empresa::create([...]);

// Criar usuário em cada empresa
$user1 = User::create(['empresa_id' => $empresa1->id, ...]);
$user2 = User::create(['empresa_id' => $empresa2->id, ...]);

// Criar protocolos
Auth::login($user1);
$proto1 = Protocolo::create([...]);

Auth::login($user2);
$proto2 = Protocolo::create([...]);

// Testar isolamento
Auth::login($user1);
Protocolo::all(); // Só vê $proto1

Auth::login($user2);
Protocolo::all(); // Só vê $proto2
```

### Testar Trial Expirado

```php
// Tinker
php artisan tinker

// Expirar trial da empresa
$empresa = Empresa::first();
$empresa->update(['trial_fim' => now()->subDay()]);

// Fazer login e tentar criar protocolo
// Deve retornar erro 403 com código TRIAL_EXPIRADO
```

### Testar Email Não Verificado

```php
// Tinker
php artisan tinker

// Desverificar email
$user = User::first();
$user->update(['email_verificado_em' => null]);

// Fazer login e acessar qualquer rota
// Deve retornar erro 403 com código EMAIL_NAO_VERIFICADO
```

---

## 🔧 Manutenção

### Comandos Úteis

```bash
# Ativar email de usuário
php artisan usuario:ativar-email usuario@exemplo.com

# Listar planos
php artisan tinker
>>> Plano::all();

# Listar empresas
>>> Empresa::all();

# Ver trial de empresa
>>> $e = Empresa::find(1);
>>> $e->diasRestantesTrial();
>>> $e->isTrialAtivo();

# Mudar plano de empresa
>>> $e->update(['plano_id' => 2]);

# Estender trial
>>> $e->update(['trial_fim' => now()->addDays(7)]);

# Ativar assinatura
>>> $e->ativarAssinatura('mensal');
```

### Rotinas Recomendadas

#### Diariamente (via Scheduler)
```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Verificar trials expirados
    $schedule->call(function () {
        Empresa::where('status', 'trial')
            ->where('trial_fim', '<', now())
            ->update(['status' => 'read_only']);
    })->daily();
}
```

#### Semanalmente
- Limpar tokens de verificação antigos (> 7 dias)
- Enviar lembretes de trial próximo ao fim

#### Mensalmente
- Gerar relatório de conversão trial → pago
- Analisar empresas em read_only

### Troubleshooting

**Problema:** Usuário não recebe email
- Verificar configuração MAIL_MAILER no .env
- Verificar logs: `tail -f storage/logs/laravel.log`
- Testar envio: `php artisan tinker` → `Mail::raw('teste', fn($m) => $m->to('email@teste.com')->subject('Teste'));`

**Problema:** Trial não expira
- Verificar middleware aplicado nas rotas
- Verificar se trial_fim está correto
- Forçar verificação: fazer nova request após expirar

**Problema:** Usuário vê dados de outra empresa
- Verificar se EmpresaScope está aplicado
- Verificar se empresa_id está sendo preenchido
- Limpar cache: `php artisan config:clear`

**Problema:** Busca de CNPJ não funciona
- Verificar conexão com internet
- Testar API manualmente: `curl https://brasilapi.com.br/api/cnpj/v1/06990590000123`
- Verificar rate limit (aguardar 1 minuto)

---

## 📝 Checklist de Implementação

### Backend ✅
- [x] Migration de `plano`
- [x] Migration de `empresa`
- [x] Migration de `usuario` (empresa_id)
- [x] Migrations de tabelas de dados (empresa_id)
- [x] Model Plano com helpers
- [x] Model Empresa com status e limites
- [x] Model User atualizado
- [x] Trait PertenceEmpresa
- [x] Scope EmpresaScope
- [x] Service CadastroEmpresaService
- [x] Controller CadastroController
- [x] Middleware VerificarTrial
- [x] Middleware VerificarEmailVerificado
- [x] Template de email
- [x] Rotas públicas
- [x] AuthController com dados de empresa
- [x] Comando AtivarEmailUsuario
- [x] Seeder de planos
- [x] Auditoria aplicada

### Frontend ✅
- [x] Página CadastroPage
- [x] Página CadastroSucessoPage
- [x] Página VerificarEmailPage
- [x] Componente TrialBanner
- [x] Service CnpjService
- [x] Rotas públicas
- [x] Axios interceptor (trial/email)
- [x] MainLayout com empresa no drawer
- [x] Login com link de cadastro

### Testes ✅
- [x] Cadastro completo
- [x] Verificação de email
- [x] Busca de CNPJ
- [x] Login com trial
- [x] Isolamento de dados
- [x] Trial expirado
- [x] Email não verificado

---

## 📚 Referências

- **Laravel 12:** https://laravel.com/docs/12.x
- **Vue 3:** https://vuejs.org/
- **Quasar 2:** https://quasar.dev/
- **BrasilAPI:** https://brasilapi.com.br/docs
- **JWT Auth:** https://jwt-auth.readthedocs.io/

---

## 📄 Licença

Propriedade de OSLO - Sistema de Gestão Cartorária
Todos os direitos reservados © 2026

---

## 👥 Autores

- **Desenvolvimento:** Claude (Anthropic) + Alexandre
- **Data:** 08/02/2026
- **Versão:** 1.0.0

---

**Fim da Documentação** 🎉
