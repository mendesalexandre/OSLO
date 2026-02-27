# PROMPT 5 — Refatoração da UI do OSLO (Quasar 2 / Vue 3)

Cole este prompt no Claude CLI na raiz do projeto OSLO.

---

```
Você está no projeto OSLO, um sistema de gestão para cartório de registro de imóveis (1º Ofício de Registro de Imóveis de Sinop/MT), desenvolvido em Laravel + Quasar 2 (Vue 3) + PostgreSQL.

## OBJETIVO

Refatorar a UI completa do OSLO para um visual corporativo, limpo e profissional. O sistema é ferramenta de trabalho — precisa funcionar bem, ser fácil de navegar e ter aparência séria. NADA de degradês, cores vibrantes, animações exageradas ou estilo "startup". É sistema de cartório.

## DIRETRIZES DE DESIGN

### Filosofia
- **Funcional primeiro** — cada pixel serve um propósito
- **Corporativo e sóbrio** — parece software profissional, não template de landing page
- **Hierarquia visual clara** — o olho sabe onde ir
- **Consistência total** — mesmos padrões em todas as telas
- **Sem firulas** — sem degradês, sem sombras exageradas, sem animações desnecessárias

### Paleta de Cores

```
// Cor principal (sidebar, botões primários, links)
$primary:        #1E3A5F    // Azul corporativo escuro

// Variações da principal
$primary-light:  #2D5282    // Hover, estados ativos
$primary-dark:   #152B47    // Sidebar background, pressed
$primary-bg:     #F0F4F8    // Background sutil com tom azulado

// Sidebar
$sidebar-bg:     #1A2332    // Fundo da sidebar (escuro)
$sidebar-text:   #8899AA    // Texto/ícones inativos
$sidebar-active: #FFFFFF    // Texto/ícone ativo
$sidebar-hover:  #232F3E    // Hover dos itens
$sidebar-accent: #3B82F6    // Indicador do item ativo (barra lateral ou destaque)

// Conteúdo
$background:     #F5F6F8    // Fundo geral da área de conteúdo
$surface:        #FFFFFF    // Cards, modais, tabelas
$border:         #E2E5EA    // Bordas sutis
$border-light:   #F0F1F3    // Divisores internos

// Texto
$text-primary:   #1A1D21    // Títulos, texto principal
$text-secondary: #5F6B7A    // Texto secundário, labels
$text-muted:     #9CA3AF    // Placeholders, texto auxiliar

// Status
$success:        #059669    // Sucesso, confirmado, ativo
$warning:        #D97706    // Atenção, pendente
$danger:         #DC2626    // Erro, cancelado, excluir
$info:           #2563EB    // Informação

// Badges de status do protocolo/contrato
$status-aberto:       #3B82F6
$status-pago-parcial: #D97706
$status-pago:         #059669
$status-em-andamento: #7C3AED
$status-concluido:    #059669
$status-cancelado:    #DC2626
$status-isento:       #6B7280
```

### Tipografia

```
// Fonte única: Inter (já configurada ou instalar via Google Fonts)
$font-family:    'Inter', -apple-system, BlinkMacSystemFont, sans-serif

// Escalas
$text-xs:   0.75rem    // 12px — badges, tags
$text-sm:   0.8125rem  // 13px — texto secundário, labels de form
$text-base: 0.875rem   // 14px — texto padrão do sistema
$text-lg:   1rem       // 16px — subtítulos
$text-xl:   1.25rem    // 20px — títulos de seção
$text-2xl:  1.5rem     // 24px — título da página

// Pesos
$font-regular:   400
$font-medium:    500
$font-semibold:  600
$font-bold:      700
```

### Espaçamento

```
$space-xs:  4px
$space-sm:  8px
$space-md:  16px
$space-lg:  24px
$space-xl:  32px
$space-2xl: 48px
```

### Sombras (SUTIS — quase imperceptíveis)

```
$shadow-sm:  0 1px 2px rgba(0, 0, 0, 0.04)
$shadow-md:  0 2px 4px rgba(0, 0, 0, 0.06)
$shadow-lg:  0 4px 12px rgba(0, 0, 0, 0.08)
```

### Bordas

```
$radius-sm:  4px     // inputs, botões
$radius-md:  6px     // cards
$radius-lg:  8px     // modais
$radius-full: 9999px // badges, avatares
```

---

## FASE 1 — ANÁLISE (EXECUTAR PRIMEIRO)

1. Ler a estrutura completa do frontend: `src/`
2. Identificar TODOS os layouts: `src/layouts/`
3. Identificar TODOS os componentes globais/reutilizáveis
4. Verificar como o Quasar está configurado: `quasar.config.js` ou `quasar.conf.js`
5. Verificar se usa SCSS/Sass ou CSS puro
6. Ler `src/css/` — variáveis existentes, arquivos de estilo global
7. Ler o layout principal (MainLayout ou similar)
8. Verificar se tem tema do Quasar customizado
9. Verificar se Inter já está importada
10. Listar TODAS as páginas em `src/pages/`
11. Verificar componentes de tabela, formulário e modal reutilizáveis

Apresentar resumo com a lista de arquivos que serão alterados antes de prosseguir.

---

## FASE 2 — CONFIGURAÇÃO BASE

### 2.1 Variáveis SCSS/CSS

Criar ou atualizar o arquivo de variáveis globais (`src/css/quasar.variables.scss` ou `src/css/variables.scss`) com TODA a paleta definida acima.

Sobrescrever as variáveis do Quasar:
```scss
$primary:   #1E3A5F;
$secondary: #5F6B7A;
$accent:    #3B82F6;
$positive:  #059669;
$negative:  #DC2626;
$info:      #2563EB;
$warning:   #D97706;
$dark:      #1A2332;
```

### 2.2 Estilos Globais

Criar/atualizar `src/css/app.scss`:

```scss
// Reset base
* {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

body {
    background: #F5F6F8;
    color: #1A1D21;
    font-size: 14px;
    line-height: 1.5;
    -webkit-font-smoothing: antialiased;
}

// Scrollbar customizada (sutil)
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
::-webkit-scrollbar-track {
    background: transparent;
}
::-webkit-scrollbar-thumb {
    background: #CBD5E1;
    border-radius: 3px;
}

// Override global do Quasar — remover visual Material Design
.q-card {
    border-radius: 6px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    border: 1px solid #E2E5EA;
}

.q-btn {
    text-transform: none !important;  // SEM uppercase nos botões
    font-weight: 500;
    letter-spacing: 0;
    border-radius: 4px;
}

.q-field {
    .q-field__control {
        border-radius: 4px;
    }
}

.q-table {
    border-radius: 6px;
    border: 1px solid #E2E5EA;

    th {
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #5F6B7A;
        background: #F8F9FA;
    }
}

.q-dialog__inner {
    .q-card {
        border-radius: 8px;
    }
}
```

### 2.3 Importar fonte Inter

No `index.html` ou via boot file:
```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
```

---

## FASE 3 — SIDEBAR / LAYOUT PRINCIPAL

### Comportamento da Sidebar:
- **Estado padrão**: Mini (60px de largura, só ícones)
- **Hover/click**: Expande (240px, mostra ícones + labels)
- **Mobile**: Drawer com overlay
- **Fundo escuro**: `#1A2332`
- **Item ativo**: background `#232F3E` + barra lateral esquerda de 3px em `#3B82F6` + texto/ícone branco
- **Item inativo**: ícone e texto em `#8899AA`
- **Hover**: background `#232F3E` + texto clareia para `#C5CDD6`

### Estrutura da Sidebar:

```
┌──────────────────────────────────┐
│  🏛 OSLO          [<<]          │  ← Logo + nome (some no mini) + botão toggle
│─────────────────────────────────│
│                                  │
│  █ Dashboard                     │  ← Item ativo (barra azul à esquerda)
│  ○ Protocolo                     │
│  ○ Contrato                     │
│  ○ Recibo                       │
│                                  │
│  FINANCEIRO ──────               │  ← Separador de seção (label pequeno, cinza)
│  ○ Natureza                     │
│  ○ Ato Cartorário               │
│  ○ Forma Pagamento              │
│  ○ Meio Pagamento               │
│                                  │
│  ADMINISTRAÇÃO ───               │
│  ○ Usuário                      │
│  ○ Grupo                        │
│  ○ Permissão                    │
│  ○ Auditoria                    │
│                                  │
│─────────────────────────────────│
│  👤 Nome do Usuário              │  ← Rodapé: avatar + nome + botão sair
│  ○ Sair                         │
└──────────────────────────────────┘
```

No estado **mini** (colapsado):
- Só ícones centralizados
- Sem labels de seção
- Tooltip no hover mostrando o nome
- Logo reduzida (só ícone ou letra "O")
- Sem nome do usuário, só avatar

### Header (top bar):

```
┌──────────────────────────────────────────────────────────────┐
│  ☰  │  Protocolo > Criar Novo            🔔  👤 Alexandre  │
│     │  (breadcrumb)                                          │
└──────────────────────────────────────────────────────────────┘
```

- Fundo branco `#FFFFFF`
- Borda inferior sutil `1px solid #E2E5EA`
- Altura: 56px
- Hamburger `☰` para toggle da sidebar
- Breadcrumb da página atual
- À direita: notificações (se tiver) + avatar/nome do usuário + dropdown com "Meu Perfil" e "Sair"

---

## FASE 4 — TELA DE LOGIN

### Layout:
- Split: esquerda (60%) fundo escuro + direita (40%) formulário em fundo branco
- NÃO usar cards transparentes com "features" no lado esquerdo — é filler inútil
- Lado esquerdo: fundo `#1A2332` sólido + logo OSLO grande + tagline simples
- Lado direito: formulário limpo, centrado verticalmente

### Lado esquerdo:
```
┌─────────────────────────────┐
│                             │
│                             │
│       🏛                    │
│       OSLO                  │
│                             │
│       Sistema de Gestão     │
│       Cartorária            │
│                             │
│                             │
└─────────────────────────────┘
```
- Fundo: `#1A2332` sólido (SEM degradê, SEM imagem de fundo)
- Logo/nome em branco
- Tagline em `#8899AA`
- Se quiser um detalhe sutil: um pattern geométrico muito discreto (linhas finas em `#232F3E`) ou nada

### Lado direito:
```
┌─────────────────────────────┐
│                             │
│   Bem-vindo de volta        │  ← $text-2xl, $font-bold, $text-primary
│   Acesse sua conta          │  ← $text-sm, $text-muted
│                             │
│   E-mail ou Telefone        │  ← Label $text-sm, $text-secondary
│   [________________]        │  ← Input com borda $border, foco em $primary
│                             │
│   Senha                     │
│   [________________] 👁     │
│                             │
│   [ Esqueci minha senha ]   │  ← Link discreto, $text-muted
│                             │
│   [      ENTRAR      ]      │  ← Botão full-width, bg $primary, text branco
│                             │
│                             │
│   © 2025 OSLO               │  ← Footer discreto
└─────────────────────────────┘
```

- Fundo: branco puro
- Input: borda `#E2E5EA`, border-radius 4px, altura 44px
- Input focus: borda `#1E3A5F`, sombra sutil `0 0 0 3px rgba(30, 58, 95, 0.1)`
- Botão ENTRAR: bg `#1E3A5F`, text branco, height 44px, font-weight 600, hover `#2D5282`
- SEM ícones dentro dos inputs (remove o ícone de usuário e cadeado atuais)
- Toggle de mostrar/esconder senha: ícone discreto à direita

---

## FASE 5 — PÁGINA DE ADMINISTRAÇÃO

### Redesign dos cards de módulo:

```
┌──────────────────────────────────────────────────────────────┐
│                                                              │
│  Administração                                               │  ← $text-2xl, $font-bold
│  Gerencie as configurações e módulos do sistema              │  ← $text-sm, $text-secondary
│                                                              │
│  ── CARTÓRIO ────────────────────────────────────────────    │  ← Separador: texto $text-xs uppercase, $text-muted
│                                                              │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐       │
│  │ ≡        │ │ ◆        │ │ 📅       │ │ 📋       │       │
│  │Naturezas │ │ Domínios │ │Feriados  │ │Tab.Custas│       │
│  │→         │ │→         │ │→         │ │→         │       │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘       │
│                                                              │
│  ┌──────────┐                                                │
│  │ 📄       │                                                │
│  │  Atos    │                                                │
│  │→         │                                                │
│  └──────────┘                                                │
│                                                              │
│  ── FINANCEIRO ──────────────────────────────────────────    │
│  ...                                                         │
└──────────────────────────────────────────────────────────────┘
```

Cada card:
- Fundo branco `#FFFFFF`
- Borda `1px solid #E2E5EA`
- Border-radius `6px`
- Sombra `$shadow-sm`
- Padding `16px`
- Hover: borda muda para `#1E3A5F` + sombra `$shadow-md` + cursor pointer
- Ícone: `24px`, cor `#1E3A5F`
- Título: `$text-base`, `$font-semibold`, `$text-primary`
- Subtítulo: `$text-xs`, `$text-muted` — REMOVER o subtítulo se não agregar (ex: "Naturezas dos atos" é redundante)
- Seta `→` discreta no canto inferior direito em `$text-muted`

Layout: grid com `gap: 16px`, 4 colunas desktop, 2 tablet, 1 mobile.

Separadores de seção:
- Texto uppercase, `$text-xs`, `$font-semibold`, `$text-muted`, `letter-spacing: 0.05em`
- Linha horizontal sutil depois do texto
- `margin-top: 32px`, `margin-bottom: 16px`

---

## FASE 6 — TABELAS / LISTAGENS (Q-TABLE)

Padrão para TODAS as telas de listagem:

```
┌──────────────────────────────────────────────────────────────┐
│  Protocolos                    [ Filtros ▼ ]  [ + Novo ]     │  ← Header da página
│──────────────────────────────────────────────────────────────│
│  🔍 Buscar por número, solicitante...                        │  ← Campo de busca
│──────────────────────────────────────────────────────────────│
│  Nº    │ Solicitante  │ Valor    │ Status     │ Data   │ ⋯  │  ← Header tabela
│────────│──────────────│──────────│────────────│────────│─── │
│  2025/ │ João Silva   │ 1.440,00 │ 🟢 Pago    │ 07/02  │ ⋯  │
│  000001│              │          │            │        │     │
│────────│──────────────│──────────│────────────│────────│─── │
│  2025/ │ Maria Santos │ 2.100,00 │ 🟡 Parcial │ 06/02  │ ⋯  │
│  000002│              │          │            │        │     │
│──────────────────────────────────────────────────────────────│
│  Mostrando 1-15 de 150                    [ < 1 2 3 ... > ] │
└──────────────────────────────────────────────────────────────┘
```

### Regras da tabela:
- Container: card branco com borda `#E2E5EA`, radius `6px`
- Header da tabela: bg `#F8F9FA`, texto uppercase `$text-xs`, `$font-semibold`, `$text-secondary`
- Linhas: altura `48px`, borda bottom `#F0F1F3`
- Hover da linha: bg `#F8F9FB`
- Texto das células: `$text-sm`, `$text-primary`
- Paginação: alinhada à direita, estilo discreto

### Badges de status:
- Usar badges com dot colorido (bolinha 8px + texto)
- NÃO usar badges com fundo colorido forte — é muito poluído
- Formato: `● Pago`, `● Parcial`, `● Cancelado`

```scss
.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 500;

    &::before {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    &.pago::before         { background: #059669; }
    &.pago-parcial::before { background: #D97706; }
    &.aberto::before       { background: #3B82F6; }
    &.cancelado::before    { background: #DC2626; }
    &.em-andamento::before { background: #7C3AED; }
    &.concluido::before    { background: #059669; }
    &.isento::before       { background: #6B7280; }
}
```

### Botão "Novo":
- Posição: canto superior direito, alinhado com o título da página
- Estilo: bg `#1E3A5F`, texto branco, ícone `+`, `$text-sm`, padding `8px 16px`

### Campo de busca:
- Full-width no topo da tabela, dentro do card
- Ícone de lupa à esquerda, placeholder "Buscar..."
- Borda bottom sutil, sem borda lateral
- Bg `#F8F9FA`

### Botões de ação na linha:
- Ícones discretos (edit, delete, view), tamanho `20px`, cor `$text-muted`
- Hover: cor `$primary` (edit/view) ou `$danger` (delete)
- Usar `q-btn flat round dense` do Quasar

---

## FASE 7 — FORMULÁRIOS / MODAIS

### Modal de criação/edição:

```
┌──────────────────────────────────────────────────────────────┐
│  Criar Novo Protocolo                                    ✕   │  ← Header: bg $primary-bg, texto $text-primary
│──────────────────────────────────────────────────────────────│
│                                                              │
│  Tipo de Protocolo                                           │
│  [ Normal ] [ Orçamento ] [ Processo ] [ Exame ]             │  ← Toggle buttons: outline, ativo = $primary fill
│                                                              │
│  Origem da Solicitação                                       │  ← Label: $text-sm, $font-medium, $text-secondary
│  [________________________▼]                                 │  ← Select: borda #E2E5EA, h:40px
│                                                              │
│  ┌─────────────────────┐ ┌─────────────────────┐            │
│  │ Serviço Principal   │ │ Estado               │            │  ← Grid 2 colunas
│  │ [_______________▼]  │ │ [_______________▼]  │            │
│  └─────────────────────┘ └─────────────────────┘            │
│                                                              │
│  Solicitante                                                 │
│  [_________________________________] [+]                     │  ← Input com botão de ação
│                                                              │
│──────────────────────────────────────────────────────────────│
│                              [ Cancelar ]  [ Salvar ]        │  ← Footer fixo
└──────────────────────────────────────────────────────────────┘
```

### Regras do formulário:
- Modal: max-width `700px`, border-radius `8px`
- Header: bg `#F0F4F8`, padding `16px 24px`, `$text-lg`, `$font-semibold`
- Botão fechar (✕): canto direito, discreto, cor `$text-muted`
- Body: padding `24px`
- Labels: `$text-sm`, `$font-medium`, `$text-secondary`, margin-bottom `4px`
- Inputs: altura `40px`, borda `#E2E5EA`, radius `4px`, font-size `14px`
- Input focus: borda `#1E3A5F`, sombra `0 0 0 3px rgba(30, 58, 95, 0.1)`
- Espaço entre campos: `20px`
- Grid: usar `row` e `col` do Quasar, `gutter-md`
- Footer: borda top `#E2E5EA`, padding `16px 24px`, bg branco
- Botão Cancelar: `flat`, cor `$text-secondary`
- Botão Salvar: bg `#1E3A5F`, texto branco
- SEM botões vermelhos para Cancelar — vermelho é só para ações destrutivas (excluir)
- Toggle buttons (tipo de protocolo): usar `q-btn-toggle` com `outline` e cor `$primary`

### Seções dentro do formulário:
Se o formulário for longo, agrupar em seções:
```
── INFORMAÇÕES DO PROTOCOLO ──────────────
(campos)

── DADOS DO SOLICITANTE ──────────────────
(campos)
```
- Separador: texto uppercase `$text-xs`, `$font-semibold`, `$text-muted`, com linha
- Margin-top `24px`, margin-bottom `16px`

---

## FASE 8 — COMPONENTES REUTILIZÁVEIS

Criar/refatorar componentes globais que serão usados em todo o sistema:

### 8.1 `OsloPaginaTitulo.vue`
Header padrão de todas as páginas:
```vue
<template>
  <div class="oslo-pagina-titulo">
    <div>
      <h1>{{ titulo }}</h1>
      <p v-if="subtitulo">{{ subtitulo }}</p>
    </div>
    <div class="oslo-pagina-titulo__acoes">
      <slot name="acoes" />
    </div>
  </div>
</template>
```
- Flexbox, space-between, align-center
- h1: `$text-2xl`, `$font-bold`
- p: `$text-sm`, `$text-secondary`
- Slot `acoes` para botões à direita

### 8.2 `OsloStatusBadge.vue`
Badge de status reutilizável:
```vue
<template>
  <span :class="['oslo-status-badge', status]">
    {{ label }}
  </span>
</template>
```
Props: `status` (string), `label` (string)
Usa as classes de badge definidas na Fase 6.

### 8.3 `OsloCardAdmin.vue`
Card clicável da página de administração:
```vue
<template>
  <router-link :to="rota" class="oslo-card-admin">
    <q-icon :name="icone" />
    <span class="oslo-card-admin__titulo">{{ titulo }}</span>
  </router-link>
</template>
```

### 8.4 `OsloFormSecao.vue`
Separador de seção dentro de formulários:
```vue
<template>
  <div class="oslo-form-secao">
    <span>{{ titulo }}</span>
  </div>
</template>
```

### 8.5 `OsloConfirmacao.vue`
Dialog de confirmação padronizado (para excluir, cancelar, etc.):
- Título, mensagem, botão cancelar (flat) e confirmar (cor variável)

---

## FASE 9 — APLICAR EM TODAS AS TELAS

Após criar os estilos globais e componentes:

1. Refatorar o layout principal (sidebar + header)
2. Refatorar a tela de login
3. Refatorar a página de administração
4. Refatorar TODAS as telas de listagem para usar o padrão da Fase 6
5. Refatorar TODOS os formulários/modais para usar o padrão da Fase 7
6. Substituir componentes inline por componentes reutilizáveis (Fase 8)

Para cada arquivo alterado, manter a lógica/funcionalidade intacta — só alterar visual/CSS/classes.

---

## FASE 10 — VALIDAÇÃO

1. Verificar que TODAS as telas usam a fonte Inter
2. Verificar que a sidebar funciona (mini ↔ expandida)
3. Verificar que os botões NÃO têm uppercase (text-transform: none)
4. Verificar que os q-cards têm borda sutil e sombra mínima
5. Verificar que as tabelas seguem o padrão (header cinza, hover, badges)
6. Verificar que os formulários seguem o padrão (labels, inputs, footer)
7. Verificar responsividade básica (tablet e mobile)
8. Verificar que a tela de login funciona e ficou limpa
9. Listar TODOS os arquivos alterados

---

## IMPORTANTE

- **FUNCIONAL** — o sistema precisa funcionar, a UI é secundária. NÃO quebrar funcionalidades.
- **SEM degradês** — fundos sólidos, sem gradientes
- **SEM sombras pesadas** — só `$shadow-sm` e `$shadow-md` onde necessário
- **SEM animações exageradas** — transições sutis (150ms) nos hovers, mais nada
- **SEM uppercase nos botões** — `text-transform: none` global
- **Botões**: texto sempre capitalizado normalmente ("Salvar", não "SALVAR")
- **Cor vermelha**: SOMENTE para ações destrutivas (excluir, cancelar protocolo). Botão "Cancelar" de fechar modal é CINZA/flat, nunca vermelho.
- **Inter 400/500/600/700** — não usar outros pesos
- **Consistência**: se definiu o padrão, aplica em TUDO. Não deixar uma tela diferente.
- **NÃO instalar bibliotecas CSS extras** (Tailwind, Bootstrap, etc.) — usar SCSS do Quasar + customizações
- Se algum componente customizado já existe e funciona, manter — só ajustar o visual
- Commitar cada fase separadamente
```
