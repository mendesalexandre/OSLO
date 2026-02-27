# Refatoração de Cores e Sidebar — OSLO Frontend

**Data:** 2026-02-09
**Escopo:** Migração completa da paleta azul escuro (#1E3A5F) para laranja (#FF7A00), sidebar branca com ícones grafite, e ajustes globais de UI.

---

## Resumo

O frontend OSLO utilizava uma paleta baseada em azul escuro (#1E3A5F) com sidebar escura (#1A2332). A refatoração migrou para:

- **Cor principal:** Laranja `#FF7A00` (estilo Banco Inter)
- **Sidebar:** Fundo branco com ícones grafite e acentos laranja
- **Textos:** Grafite Google (`#202124`, `#5F6368`, `#9AA0A6`)
- **Referência visual:** Banco Inter + Google Workspace + Notion

---

## Paleta Anterior vs. Nova

### Cores Principais

| Token | Antes | Depois |
|-------|-------|--------|
| `$primary` / `--primary` | `#1E3A5F` (azul escuro) | `#FF7A00` (laranja) |
| `$secondary` | `#5F6B7A` | `#5F6368` (grafite Google) |
| `$accent` / `--primary-light` | `#3B82F6` / `#2D5282` | `#FF9A33` (laranja claro) |
| `$dark` | `#1A2332` | `#202124` (quase preto) |
| `--primary-dark` | `#152B47` | `#E06C00` (laranja pressed) |
| `--primary-bg` | `#F0F4F8` | `#FFF7F0` (alaranjado sutil) |
| `--primary-rgb` | *(não existia)* | `255, 122, 0` |

### Sidebar

| Token | Antes | Depois |
|-------|-------|--------|
| `--sidebar-bg` | `#1A2332` (escuro) | `#FFFFFF` (branco) |
| `--sidebar-text` | `#8899AA` (azul claro) | `#5F6368` (grafite) |
| `--sidebar-active` | `#FFFFFF` (branco) | `#FF7A00` (laranja) |
| `--sidebar-hover` | `#232F3E` (escuro hover) | `#F5F5F5` (cinza claro) |
| `--sidebar-accent` | `#3B82F6` (azul) | `#FF7A00` (laranja) |
| `--sidebar-active-bg` | *(não existia)* | `#FFF3E8` (alaranjado sutil) |
| `--sidebar-border` | *(não existia)* | `#E8EAED` |

### Textos e Bordas

| Token | Antes | Depois |
|-------|-------|--------|
| `--text-color` | `#1A1D21` | `#202124` |
| `--text-secondary` | `#5F6B7A` | `#5F6368` |
| `--text-muted` | `#9CA3AF` | `#9AA0A6` |
| `--border-color` | `#E2E5EA` | `#E8EAED` |
| `--border-color-light` | `#F0F1F3` | `#F1F3F4` |
| `--border-color-dark` | `#CBD5E1` | `#DADCE0` |
| `--background` | `#F5F6F8` | `#F8F9FA` |
| `--bg-subtle` | `#F1F5F9` | `#F5F5F5` |

### Status (mantidos)

| Token | Valor |
|-------|-------|
| `--success` / `$positive` | `#059669` |
| `--danger` / `$negative` | `#DC2626` |
| `--info` / `$info` | `#2563EB` |
| `--warning` / `$warning` | `#D97706` |

---

## Arquivos Modificados

### 1. `src/css/quasar.variables.scss`

Variáveis SCSS do Quasar que definem as brand colors usadas em `color="primary"` etc.

**Alterações:**
- `$primary`: `#1E3A5F` → `#FF7A00`
- `$secondary`: `#5F6B7A` → `#5F6368`
- `$accent`: `#3B82F6` → `#FF9A33`
- `$dark`: `#1A2332` → `#202124`
- `$light`: `#F5F6F8` → `#F8F9FA`

### 2. `src/css/app.scss`

Estilos globais com CSS custom properties e overrides do Quasar.

**Alterações:**
- Todas as variáveis `:root` atualizadas (ver tabelas acima)
- Nova variável `--primary-rgb` para uso em `rgba()`
- Novas variáveis de sidebar: `--sidebar-active-bg`, `--sidebar-border`
- Input focus: `box-shadow: rgba(30, 58, 95, 0.1)` → `rgba(255, 122, 0, 0.12)`
- Badge status: `#3B82F6` hardcoded → `var(--info)`
- Scrollbar: `#CBD5E1` → `#DADCE0`, hover `#94A3B8` → `#9AA0A6`
- Input outlined: `border-radius: 6px`, estados de hover/focus/error
- Checkbox: customização com `$primary` (agora laranja)

### 3. `src/layouts/MainLayout.vue`

Layout principal com sidebar e header.

**Template:**
- Header comentado (removido temporariamente pelo usuário)
- Separadores: removido atributo `dark` (sidebar agora é branca)
- Ícones de menu: `size="18px"` → `size="16px"`
- Novo bloco: informações da empresa na sidebar
- Novo componente: `<trial-banner />`

**CSS (scoped):**
- `.oslo-sidebar`: fundo `var(--sidebar-bg)` (branco) + `border-right: 1px solid var(--sidebar-border)`
- `.oslo-sidebar__logo`: cor texto `var(--text-color)`, ícone `var(--primary)` (laranja)
- `.oslo-sidebar__separator`: `background: var(--border-color)` (sem opacity hack)
- `.oslo-sidebar__section`: `font-size: 11px`, `color: var(--text-muted)` (sem `opacity: 0.6`)
- `.oslo-sidebar__item`:
  - `border-radius: var(--radius-md)` (6px)
  - Hover: `background: var(--sidebar-hover)`, mantém cor grafite
  - Active: `background: var(--sidebar-active-bg)` (#FFF3E8), cor `var(--sidebar-active)` (laranja)
  - Removida barra lateral `::before` no active (simplificado)
  - Logout hover: `#F87171` → `#DC2626`
- `.oslo-sidebar__icon .q-icon`: `font-size: 16px !important`
- `.oslo-sidebar__label`: `font-weight: 400` (normal), active `500`
- Mini mode: `!important` e `:deep(.q-item__section)` para forçar centralização
- Novo: `.oslo-sidebar__empresa` (bloco de info da empresa)

### 4. `src/pages/autenticacao/login/Index.vue`

Tela de login.

**Template:**
- Novo link "Criar conta grátis" com `text-orange`

**CSS:**
- `.login-left` background: `#1A2332` → `#1A1A1A` (grafite escuro)
- `.login-left__icon`: `#3B82F6` → `#FF7A00` (laranja)
- `.login-left__tagline`: `#8899AA` → `#9AA0A6`
- `.login-left__footer`: `#5F6B7A` → `#5F6368`
- Input focus shadow: `rgba(30, 58, 95, 0.1)` → `rgba(255, 122, 0, 0.12)`

---

## Cores Removidas (não devem aparecer no frontend)

Estas cores da paleta antiga foram completamente eliminadas:

| Cor | Uso anterior |
|-----|-------------|
| `#1E3A5F` | Primary (azul escuro) |
| `#152B47` | Primary dark |
| `#2D5282` | Primary light |
| `#1A2332` | Sidebar bg / dark |
| `#232F3E` | Sidebar hover |
| `#8899AA` | Sidebar text |
| `#3B82F6` | Accent / sidebar accent (azul) |
| `#5F6B7A` | Secondary / text-secondary antigo |
| `#F0F4F8` | Primary bg antigo |
| `#F5F6F8` | Background antigo |
| `#E2E5EA` | Border color antigo |
| `#F0F1F3` | Border light antigo |
| `#CBD5E1` | Border dark antigo |
| `#1A1D21` | Text color antigo |
| `#9CA3AF` | Text muted antigo |
| `#F1F5F9` | Bg subtle antigo |

---

## Impacto nos Componentes

Todos os componentes que usam `color="primary"` do Quasar automaticamente passaram a usar `#FF7A00`:
- Botões (q-btn)
- Badges (q-badge)
- Spinners (q-spinner)
- Checkboxes (q-checkbox)
- Tabs (q-tabs)
- Toggle buttons (q-btn-toggle)
- Links e ícones

Componentes que usam CSS custom properties (`var(--primary)`, `var(--sidebar-*))` etc.) também foram atualizados automaticamente via `:root`.

---

## Referência Visual

O design segue a identidade visual de:
- **Banco Inter** — laranja como cor de acento, branco como base
- **Google Workspace** — sidebar branca, ícones grafite (#5F6368), tipografia limpa
- **Notion** — minimalismo, funcional, sem firulas

O laranja é usado como **acento** (item ativo, botões de ação, destaques pontuais), **não** como tema dominante.
