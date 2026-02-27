# OSLO — Project Phases

## Stack

- **Backend**: Laravel 12, PHP 8.4, PostgreSQL, Pest, Sanctum (HttpOnly cookies)
- **Frontend**: Quasar 2, Vue 3 Composition API, Pinia, Axios (`withCredentials: true`)
- **Monorepo**: `/home/alexandre/code/OSLO` → `backend/` e `frontend/`
- **Padrão de API**: `/api/v1/...`
- **Soft delete**: campo `data_exclusao` (nullable timestamp)
- **Campos de controle**: `is_ativo` (bool), `data_cadastro`, `data_alteracao`, `data_exclusao`
- **Interface**: pt-BR

---

## Phase 01 — Autenticação com Sanctum (HttpOnly Cookies)

### Objetivo

Implementar autenticação stateful com Laravel Sanctum usando cookies HttpOnly, sem tokens expostos no frontend.

### Backend

#### Migration

- Tabela `users`: `id`, `name`, `email`, `password`, `is_ativo`, `data_cadastro`, `data_alteracao`, `data_exclusao`

#### Configuração Sanctum

- Publicar config do Sanctum
- Configurar `SESSION_DRIVER=cookie` no `.env`
- Configurar `SANCTUM_STATEFUL_DOMAINS` para o domínio do frontend Quasar
- Configurar `cors.php` para `supports_credentials: true` e origins do frontend
- Middleware `EnsureFrontendRequestsAreStateful` nas rotas de API

#### Endpoints

- `POST /api/v1/auth/login` — autentica e retorna cookie de sessão
- `POST /api/v1/auth/logout` — destrói sessão
- `GET /api/v1/auth/me` — retorna dados do usuário autenticado
- `POST /api/v1/auth/refresh` — renova a sessão

#### Regras

- Login aceita `email` + `password`
- Retornar erro 401 com mensagem em pt-BR para credenciais inválidas
- Retornar erro 422 para validação
- Todas as rotas protegidas usam middleware `auth:sanctum`

#### Testes (Pest)

- [ ] Usuário pode fazer login com credenciais válidas
- [ ] Login com credenciais inválidas retorna 401
- [ ] Login com campos vazios retorna 422
- [ ] Usuário autenticado pode acessar `/api/v1/auth/me`
- [ ] Usuário não autenticado recebe 401 ao acessar rota protegida
- [ ] Logout destrói a sessão corretamente

### Frontend

#### Store (Pinia) — `stores/auth.js`

- State: `user`, `isAuthenticated`
- Actions: `login(email, password)`, `logout()`, `fetchMe()`
- Axios configurado com `withCredentials: true` e `baseURL` da API

#### Páginas

- `pages/auth/LoginPage.vue` — formulário de login (email + senha), validação client-side, mensagem de erro, redirecionamento após login
- Rota `/login` → `LoginPage.vue`
- Guard de rota: redireciona para `/login` se não autenticado, redireciona para `/` se já autenticado

#### Layout

- `layouts/AuthLayout.vue` — layout simples para páginas de autenticação
- `layouts/MainLayout.vue` — layout principal com header, sidebar e slot de conteúdo (esqueleto)

---

## Phase 02 — Tabelas Auxiliares com Seeds

### Objetivo

Criar todas as tabelas auxiliares necessárias para o sistema, com dados populados via seeders.

### Tabelas e Seeds

#### `estado_civil`

Campos: `id`, `descricao`, `is_ativo`, `data_cadastro`, `data_alteracao`, `data_exclusao`

Seeds:

- Solteiro(a)
- Casado(a)
- Separado(a) Judicialmente
- Divorciado(a)
- Viúvo(a)
- União Estável
- Outros

#### `regime_bem`

Campos: `id`, `descricao`, `observacao` (texto explicativo), `is_ativo`, `data_cadastro`, `data_alteracao`, `data_exclusao`

Seeds:

- Comunhão Parcial de Bens
- Comunhão Universal de Bens
- Separação Total de Bens
- Separação Obrigatória de Bens
- Participação Final nos Aquestos

#### `nacionalidade`

Campos: `id`, `descricao`, `gentilico`, `is_ativo`, `data_cadastro`, `data_alteracao`, `data_exclusao`

Seeds: Popular com principais nacionalidades (Brasileiro(a), Argentino(a), Americano(a), Português(a), Italiano(a), Espanhol(a), Alemão/Alemã, Japonês/Japonesa, e demais países)

#### `capacidade_civil`

Campos: `id`, `descricao`, `observacao`, `is_ativo`, `data_cadastro`, `data_alteracao`, `data_exclusao`

Seeds:

- Plenamente Capaz
- Relativamente Incapaz (16 a 18 anos)
- Absolutamente Incapaz
- Emancipado(a)

#### `profissao`

Campos: `id`, `descricao`, `codigo_cbo` (nullable), `is_ativo`, `data_cadastro`, `data_alteracao`, `data_exclusao`

Seeds: Popular com profissões comuns (Advogado, Engenheiro, Médico, Contador, Corretor de Imóveis, Agricultor, Comerciante, Aposentado, Do Lar, Estudante, Servidor Público, etc.)

#### `tipo_empresa`

Campos: `id`, `descricao`, `sigla`, `is_ativo`, `data_cadastro`, `data_alteracao`, `data_exclusao`

Seeds:

- LTDA — Sociedade Limitada
- S/A — Sociedade Anônima
- MEI — Microempreendedor Individual
- ME — Microempresa
- EPP — Empresa de Pequeno Porte
- EIRELI — Empresa Individual de Responsabilidade Limitada
- SS — Sociedade Simples
- SLU — Sociedade Limitada Unipessoal

#### `porte_empresa`

Campos: `id`, `descricao`, `is_ativo`, `data_cadastro`, `data_alteracao`, `data_exclusao`

Seeds:

- Microempreendedor Individual (MEI)
- Microempresa (ME)
- Empresa de Pequeno Porte (EPP)
- Médio Porte
- Grande Porte

### Endpoints (para todas as tabelas auxiliares)

- `GET /api/v1/auxiliares/{tabela}` — lista todos os registros ativos
- Não é necessário CRUD completo nessa fase, apenas listagem para popular selects

### Testes (Pest)

- [ ] Cada tabela auxiliar retorna lista correta de registros ativos
- [ ] Seeds foram populados corretamente (assert count mínimo)

### Frontend

#### Store (Pinia) — `stores/auxiliares.js`

- State: objeto com cada tabela auxiliar
- Action: `fetchAuxiliares()` — busca todas as auxiliares de uma vez no boot da aplicação
- Cachear no store para não repetir requisições

#### Componente reutilizável

- `components/base/SelectAuxiliar.vue` — q-select que recebe `tabela` como prop e busca do store

---

## Phase 03 — Indicador Pessoal (Livro 5)

### Objetivo

Implementar o cadastro completo do Indicador Pessoal com versionamento histórico, suportando Pessoa Física e Pessoa Jurídica em uma única tabela com campos dinâmicos.

### Backend

#### Migration — `indicador_pessoal`

```
id
-- Controle de versão
cpf_cnpj (string 20, indexed) — chave de agrupamento entre versões
versao (integer, default 1)
is_atual (boolean, default true)
motivo_versao (text, nullable) — ex: "Alteração de estado civil"
data_versao (timestamp)

-- Identificação
tipo_pessoa (char 1) — 'F' ou 'J'
ficha (string 20, nullable, unique)
nome (string 255)
nome_fantasia (string 255, nullable) — PJ

-- Pessoa Física
rg (string 30, nullable)
orgao_expedidor (string 20, nullable)
data_expedicao_rg (date, nullable)
data_nascimento (date, nullable)
data_obito (date, nullable)
sexo (char 1, nullable) — 'M', 'F', 'O'
nome_pai (string 255, nullable)
nome_mae (string 255, nullable)

-- Estado civil (PF)
estado_civil_id (FK, nullable)
regime_bem_id (FK, nullable)
data_casamento (date, nullable)
anterior_lei_6515 (boolean, nullable) — casamentos antes de 26/12/1977
conjuge_id (FK -> indicador_pessoal, nullable)

-- Capacidade civil
capacidade_civil_id (FK, nullable)
representante_legal (string 255, nullable)

-- Nacionalidade/Profissão
nacionalidade_id (FK, nullable)
naturalidade (string 255, nullable)
profissao_id (FK, nullable)

-- Pessoa Jurídica
data_abertura (date, nullable)
data_encerramento (date, nullable)
sede (string 255, nullable)
objeto_social (text, nullable)
tipo_empresa_id (FK, nullable)
porte_empresa_id (FK, nullable)
inscricao_estadual (string 50, nullable)
inscricao_municipal (string 50, nullable)

-- COAF / Compliance
pessoa_politicamente_exposta (boolean, default false)
servidor_publico (boolean, default false)
cargo_funcao (string 255, nullable)
orgao_entidade (string 255, nullable)

-- Endereço
cep (string 10, nullable)
logradouro (string 255, nullable)
numero (string 20, nullable)
complemento (string 100, nullable)
bairro (string 100, nullable)
cidade (string 100, nullable)
uf (char 2, nullable)
pais (string 100, default 'Brasil')

-- Controle
observacoes (text, nullable)
is_ativo (boolean, default true)
data_cadastro (timestamp, useCurrent)
data_alteracao (timestamp, useCurrent)
data_exclusao (timestamp, nullable)

-- Índices
index: cpf_cnpj
index: nome
index: [cpf_cnpj, versao] unique
index: [cpf_cnpj, is_atual]
```

#### Migration — `indicador_pessoal_socio`

```
id
indicador_pessoal_id (FK -> indicador_pessoal, cascadeOnDelete)
socio_id (FK -> indicador_pessoal) — o sócio também é um indicador pessoal
participacao_percentual (decimal 5,2, nullable)
cargo (string 100, nullable)
data_entrada (date, nullable)
data_saida (date, nullable)
is_ativo (boolean, default true)
data_cadastro (timestamp, useCurrent)
data_alteracao (timestamp, useCurrent)
data_exclusao (timestamp, nullable)
```

#### Model `IndicadorPessoal`

- Soft delete via `data_exclusao`
- Scope `atual()` — filtra `is_atual = true`
- Scope `ativo()` — filtra `is_ativo = true`
- Relacionamentos: `estadoCivil()`, `regimeBem()`, `nacionalidade()`, `capacidadeCivil()`, `profissao()`, `tipoEmpresa()`, `porteEmpresa()`, `conjuge()`, `socios()`, `versoes()`
- Método `criarNovaVersao(array $dados, string $motivo)` — desativa versão atual, cria nova com `versao + 1`
- Accessor `indisponibilidades_count` — count em `indisponibilidade_parte` pelo `cpf_cnpj`

#### Endpoints

- `GET /api/v1/indicador-pessoal` — listagem paginada (só `is_atual = true`), busca por nome/CPF/CNPJ
- `GET /api/v1/indicador-pessoal/{id}` — detalhe da versão atual
- `GET /api/v1/indicador-pessoal/{cpf_cnpj}/versoes` — histórico de versões pelo CPF/CNPJ
- `POST /api/v1/indicador-pessoal` — cadastra nova pessoa (verifica se CPF/CNPJ já existe)
- `PUT /api/v1/indicador-pessoal/{id}` — cria nova versão com motivo obrigatório
- `DELETE /api/v1/indicador-pessoal/{id}` — soft delete (data_exclusao)
- `GET /api/v1/indicador-pessoal/busca` — busca rápida por nome/CPF/CNPJ (para autocomplete)

#### Validações

- CPF válido para tipo_pessoa = 'F'
- CNPJ válido para tipo_pessoa = 'J'
- `motivo_versao` obrigatório ao atualizar (PUT)
- Se `estado_civil` = Casado, `regime_bem_id` obrigatório
- Se `estado_civil` = Casado, `conjuge_id` obrigatório (deve existir no indicador pessoal)
- `conjuge_id` não pode ser o próprio registro
- Se `capacidade_civil` = incapaz ou relativamente incapaz, `representante_legal` obrigatório

#### Testes (Pest)

- [ ] Pode cadastrar pessoa física com dados válidos
- [ ] Pode cadastrar pessoa jurídica com dados válidos
- [ ] CPF inválido retorna erro 422
- [ ] CNPJ inválido retorna erro 422
- [ ] CPF duplicado na mesma versão retorna erro com mensagem explicativa
- [ ] Atualização cria nova versão e desativa a anterior
- [ ] `motivo_versao` é obrigatório na atualização
- [ ] Listagem retorna apenas versões atuais
- [ ] Busca por nome retorna resultados corretos
- [ ] Busca por CPF/CNPJ retorna resultado correto
- [ ] Histórico de versões retorna todas as versões do CPF/CNPJ
- [ ] Soft delete preenche `data_exclusao`
- [ ] Cônjuge deve existir no indicador pessoal
- [ ] Estado civil casado exige regime de bens
- [ ] `indisponibilidades_count` retorna count correto

### Frontend

#### Store (Pinia) — `stores/indicadorPessoal.js`

- State: `lista`, `paginacao`, `atual`, `versoes`, `loading`, `errors`
- Actions: `fetchLista(params)`, `fetchById(id)`, `fetchVersoes(cpfCnpj)`, `criar(dados)`, `atualizar(id, dados, motivo)`, `excluir(id)`, `buscar(termo)`

#### Páginas

- `pages/indicador-pessoal/ListaPage.vue`
  - Tabela com colunas: Ficha, Nome, CPF/CNPJ, Tipo, Estado Civil, Cidade, Indisponibilidades (badge vermelho se > 0)
  - Filtros: busca por texto, tipo pessoa (PF/PJ), is_ativo
  - Botões: Novo, Editar, Ver Histórico, Excluir
  - Paginação

- `pages/indicador-pessoal/FormPage.vue` (cadastro e edição)
  - Campo `tipo_pessoa` no topo — ao mudar, altera campos exibidos dinamicamente
  - **Campos PF**: nome, cpf, rg, orgão expedidor, data expedição, data nascimento, sexo, nome pai, nome mãe, estado civil, regime de bens (aparece se casado), data casamento, anterior lei 6515 (aparece se casado), cônjuge (busca autocomplete no indicador pessoal), capacidade civil, representante legal (aparece se incapaz), nacionalidade, naturalidade, profissão, PPE, servidor público, cargo/função
  - **Campos PJ**: razão social, nome fantasia, cnpj, data abertura, data encerramento, sede, objeto social, tipo empresa, porte empresa, inscrição estadual, inscrição municipal, PPE, servidor público
  - **Endereço** (comum PF e PJ): CEP (busca ViaCEP ao digitar), logradouro, número, complemento, bairro, cidade, UF, país
  - **Sócios** (só PJ): lista de sócios com busca autocomplete no indicador pessoal, percentual, cargo, data entrada/saída
  - **Observações** (comum)
  - Em modo edição: campo `motivo_versao` obrigatório, exibir histórico de versões em timeline

- `pages/indicador-pessoal/VersoesPage.vue`
  - Timeline com todas as versões da pessoa
  - Exibe diff entre versões (campos alterados)
  - Cada versão mostrando motivo e data

#### Componentes

- `components/indicador-pessoal/BuscaAutocomplete.vue` — q-select com busca por nome/CPF/CNPJ (usado para cônjuge e sócios)
- `components/indicador-pessoal/BadgeIndisponibilidade.vue` — badge vermelho com count de indisponibilidades

#### Rotas

- `/indicador-pessoal` → `ListaPage`
- `/indicador-pessoal/novo` → `FormPage` (modo cadastro)
- `/indicador-pessoal/:id/editar` → `FormPage` (modo edição)
- `/indicador-pessoal/:cpfCnpj/versoes` → `VersoesPage`

---

## Phase 04 — Indisponibilidade

### Objetivo

Implementar o cadastro de indisponibilidades com vinculação ao Indicador Pessoal pelo CPF/CNPJ.

### Backend

#### Migrations

Usar exatamente o schema já definido:

```php
// indisponibilidade
$table->id();
$table->boolean('is_ativo')->default(true);
$table->string('status', 50);
$table->string('tipo', 10)->nullable();
$table->string('protocolo_indisponibilidade', 100)->unique();
$table->string('numero_processo', 50)->nullable();
$table->string('usuario', 255)->nullable();
$table->string('ordem_status', 50)->nullable();
$table->string('forum_vara', 255)->nullable();
$table->text('nome_instituicao')->nullable();
$table->string('email', 255)->nullable();
$table->string('telefone', 50)->nullable();
$table->timestamp('data_pedido')->nullable();
$table->boolean('ordem_prioritaria')->nullable();
$table->boolean('segredo_justica')->nullable();
$table->string('cancelamento_protocolo', 100)->nullable();
$table->integer('cancelamento_tipo')->nullable();
$table->timestamp('cancelamento_data')->nullable();
$table->timestamp('data_cadastro')->useCurrent();
$table->timestamp('data_alteracao')->useCurrent();
$table->timestamp('data_exclusao')->nullable();

// indisponibilidade_parte
$table->id();
$table->foreignId('indisponibilidade_id')->constrained('indisponibilidade')->cascadeOnDelete();
$table->string('cpf_cnpj', 20);
$table->string('nome_razao', 255);
$table->timestamp('data_cadastro')->useCurrent();

// indisponibilidade_matricula
$table->id();
$table->foreignId('indisponibilidade_parte_id')->constrained('indisponibilidade_parte')->cascadeOnDelete();
$table->string('matricula', 100);
$table->timestamp('data_cadastro')->useCurrent();
```

#### Models

- `Indisponibilidade` — relacionamentos: `partes()`, `cancelamento()`
- `IndisponibilidadeParte` — relacionamentos: `indisponibilidade()`, `matriculas()`, `indicadorPessoal()` (busca pelo cpf_cnpj)
- `IndisponibilidadeMatricula` — relacionamentos: `parte()`

#### Endpoints

- `GET /api/v1/indisponibilidades` — listagem paginada com filtros (status, tipo, numero_processo, cpf_cnpj)
- `GET /api/v1/indisponibilidades/{id}` — detalhe com partes e matrículas
- `POST /api/v1/indisponibilidades` — cadastra nova indisponibilidade com partes e matrículas
- `PUT /api/v1/indisponibilidades/{id}` — atualiza status/dados
- `DELETE /api/v1/indisponibilidades/{id}` — soft delete
- `GET /api/v1/indisponibilidades/cpf-cnpj/{cpfCnpj}` — lista indisponibilidades de uma pessoa
- `POST /api/v1/indisponibilidades/{id}/cancelar` — cancela com motivo e protocolo de cancelamento

#### Status possíveis

- `pendente`, `cumprida`, `cancelada`, `em_analise`

#### Testes (Pest)

- [ ] Pode cadastrar indisponibilidade com partes e matrículas
- [ ] Listagem retorna registros paginados
- [ ] Filtro por CPF/CNPJ retorna indisponibilidades corretas
- [ ] Cancelamento preenche campos de cancelamento corretamente
- [ ] Soft delete preenche `data_exclusao`
- [ ] Busca por CPF/CNPJ via endpoint específico retorna resultados corretos
- [ ] `protocolo_indisponibilidade` único — duplicata retorna erro 422

### Frontend

#### Store (Pinia) — `stores/indisponibilidade.js`

- State: `lista`, `paginacao`, `atual`, `loading`, `errors`
- Actions: `fetchLista(params)`, `fetchById(id)`, `fetchPorCpfCnpj(cpfCnpj)`, `criar(dados)`, `atualizar(id, dados)`, `cancelar(id, dados)`, `excluir(id)`

#### Páginas

- `pages/indisponibilidade/ListaPage.vue`
  - Tabela: Protocolo, Nº Processo, Status (chip colorido), Tipo, Fórum/Vara, Data Pedido, Prioritária (ícone)
  - Filtros: status, tipo, busca por processo/protocolo, CPF/CNPJ
  - Ações: Ver, Editar, Cancelar, Excluir

- `pages/indisponibilidade/FormPage.vue`
  - Seção dados principais: protocolo, nº processo, usuário, status, tipo, fórum/vara, instituição, email, telefone, data pedido, prioritária (toggle), segredo de justiça (toggle)
  - Seção partes: lista de partes com CPF/CNPJ + nome (autocomplete no indicador pessoal), cada parte com suas matrículas
  - Ao informar CPF/CNPJ de uma parte, buscar automaticamente o nome no indicador pessoal

- `pages/indisponibilidade/CancelarDialog.vue`
  - Dialog para cancelamento: protocolo cancelamento, tipo cancelamento, data cancelamento

#### Rotas

- `/indisponibilidades` → `ListaPage`
- `/indisponibilidades/nova` → `FormPage`
- `/indisponibilidades/:id/editar` → `FormPage`

---

## Phase 05 — Integração Indicador Pessoal ↔ Indisponibilidade

### Objetivo

Exibir contagem e lista de indisponibilidades ativas no perfil do Indicador Pessoal, e criar a tela de consulta unificada.

### Backend

#### Ajustes no endpoint do Indicador Pessoal

- `GET /api/v1/indicador-pessoal` — incluir `indisponibilidades_count` (ativas) em cada registro
- `GET /api/v1/indicador-pessoal/{id}` — incluir lista completa de indisponibilidades ativas da pessoa

#### Query otimizada

```sql
SELECT COUNT(*)
FROM indisponibilidade_parte ip
JOIN indisponibilidade i ON i.id = ip.indisponibilidade_id
WHERE ip.cpf_cnpj = :cpf_cnpj
AND i.status NOT IN ('cancelada')
AND i.data_exclusao IS NULL
```

#### Testes (Pest)

- [ ] Listagem inclui `indisponibilidades_count` correto
- [ ] Detalhe inclui lista de indisponibilidades ativas
- [ ] Count não inclui indisponibilidades canceladas ou excluídas

### Frontend

#### Ajustes na `ListaPage` do Indicador Pessoal

- Coluna "Indisponibilidades": badge vermelho com número se > 0, verde "Livre" se = 0
- Clique no badge abre dialog com lista das indisponibilidades

#### Ajustes na `FormPage` do Indicador Pessoal

- Aba ou seção "Indisponibilidades" mostrando lista das indisponibilidades ativas da pessoa
- Botão "Ver detalhes" que navega para a indisponibilidade

#### Componente

- `components/indicador-pessoal/IndisponibilidadesPanel.vue`
  - Recebe `cpfCnpj` como prop
  - Lista indisponibilidades com status, protocolo, processo, data
  - Badge de contagem no título do painel

#### Tela de Consulta Unificada

- `pages/consulta/ConsultaGeralPage.vue`
  - Campo de busca único: CPF/CNPJ ou nome
  - Retorna: dados do indicador pessoal (versão atual) + indisponibilidades ativas + histórico de versões
  - Layout em cards/seções
  - Exportar resultado em PDF (básico)

#### Rota

- `/consulta` → `ConsultaGeralPage`
