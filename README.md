# OSLO — Open Source Registry Office Management System

> The first and only open-source registry office (_cartório_) management system for Brazil.
> Built for the offices no one sees — the small ones, the remote ones, the ones that need it most.

<p align="center">
  <a href="#português">Português</a> · <strong>English</strong>
</p>

---

## The Problem

Brazil has over **13,000 registry offices** (_cartórios_) serving **215 million people** across **8.5 million km²** of territory — larger than the contiguous United States.

To understand what a _cartório_ does, consider that a single Brazilian registry office combines the functions that are spread across **multiple separate institutions** in the US and Europe:

| Function | Brazil (Cartório) | United States | Europe |
|---|---|---|---|
| Birth / marriage / death records | Civil Registry | County Clerk, Vital Records Office | _Standesamt_ (DE), _État civil_ (FR), _Registro Civil_ (ES) |
| Property registration | Real Estate Registry | County Recorder, Title Companies | _Grundbuchamt_ (DE), _Cadastre_ (FR), HM Land Registry (UK) |
| Notarial services | Notary Office | Notary Public (minimal role) | _Notar_ (DE), _Notaire_ (FR) |
| Document authentication | Registry of Titles & Docs | Notary Public, Secretary of State | Notary, _Apostille_ authorities |
| Protest of bills | Protest Registry | No equivalent (courts) | _Huissier de justice_ (FR) |
| Legal entity registration | RTDPJ | Secretary of State | Chamber of Commerce |

**You do not legally exist in Brazil without a cartório.** No birth certificate means no ID card, no school enrollment, no healthcare, no government benefits. No property registration means no legal proof of ownership. No death certificate means frozen bank accounts and unsettled estates.

### The Scale Challenge

What makes Brazil fundamentally different from the US or Europe is **continental scale combined with extreme inequality**:

- The **Amazon region alone** is larger than all of Western Europe — towns separated by hundreds of kilometers of river, no roads, often just satellite internet
- In the **sertão** (semi-arid northeast), small towns of 5,000–10,000 people depend on a single registry office — the only point of legal documentation access within 100+ km
- Germany has ~7,500 notary offices serving 84 million people with excellent digital infrastructure. Brazil has **13,000+ offices** serving **215 million**, many with **unreliable electricity, let alone internet**
- Brazilian _cartórios_ are **privately operated under government concession** — each office must fund its own technology. A small office earning R$5,000–10,000/month (~$1,000–2,000 USD) cannot afford proprietary software that costs R$2,000–5,000/month

**The result:** thousands of offices serving millions of citizens either use outdated systems, manual paper processes, or cobble together spreadsheets — in 2026.

---

## The Solution

**OSLO** is the first and only open-source complete registry office management system for Brazil. It was designed from the ground up for the reality of small, remote offices:

- **Zero licensing cost** — install, use, and adapt freely. No monthly fees
- **Lightweight** — runs on modest hardware without expensive infrastructure
- **Offline-first architecture** planned — for offices with intermittent or no internet
- **Open source and auditable** — full transparency for a service that is public by nature
- **Multi-tenant** — a single server can serve multiple offices with complete data isolation

---

## Features

### Protocol Management

Complete workflow with state machine: **Reception → Distribution → Analysis → Registration → Completed**, with branches for Requirements and Cancellation. Every transition is recorded in the protocol history with timestamp, user, and notes.

### Fee Calculation Engine (Emolumentos)

Automated calculation based on state-regulated fee tables:

- **Fixed** — flat fee per act × quantity
- **Progressive bracket** — base fee + incremental charges based on property/transaction value, with ceiling caps
- **Free-of-charge** (_Gratuidade_) — for low-income citizens (see below)

Currently supporting **Mato Grosso 2025** fee table with 4 tax layers applied in order (Registro Civil, FUNAJURIS, FUNAMP, ISSQN). Designed to scale to all 27 Brazilian states.

### Free Document Issuance for Low-Income Citizens

Brazilian law (Lei 9.534/97 and the Federal Constitution) guarantees that **low-income citizens** have the right to free birth certificates, death certificates, marriage certificates, and copies needed for government benefits.

OSLO enforces this constitutional right by:
- Tracking which documents qualify for fee exemption based on legal criteria
- Recording exemption reasons for audit and reporting to the state oversight body (_corregedoria_)
- Calculating the fee that _would have been charged_ for compensation fund reporting
- Generating reports for the _Fundo de Compensação_ that partially reimburses offices

Without proper software, small offices either **fail to track free services** (losing compensation they're entitled to) or **incorrectly charge citizens** who should be exempt.

### Personal Index (Indicador Pessoal)

Complete registration of individuals (CPF) and legal entities (CNPJ) with:
- Full versioning — every change creates a new version, preserving history
- CPF/CNPJ validation with mathematical algorithms and real-time status check against the Federal Revenue Service
- Partner/associate tracking for legal entities
- Conditional validation based on person type (married, incapacitated, minor)

### Asset Unavailability (Indisponibilidade de Bens)

Before any property transfer, a registry office **must verify** whether the seller's assets are under judicial restriction (court-ordered liens, tax enforcement, criminal proceedings). Failing to do so means:
- **Void transactions** — the sale is legally null
- **Personal liability** — the registrar is personally liable
- **Criminal prosecution** — in cases of negligence

OSLO cross-references every party in a property transaction against unavailability records and **alerts the clerk before any registration proceeds**. This is the equivalent of a US title search checking for liens and encumbrances — except in Brazil, the registry office itself is legally responsible, not a separate title company.

### Financial Module

Full cashier and transaction management:
- Cash register operations (open, close, withdraw, verify)
- Transaction tracking with audit trail (every insert, update, delete recorded)
- Materialized views for real-time financial summaries
- Multiple payment methods and types

### RBAC (Role-Based Access Control)

~138 granular permissions organized by module:

| Module | Permissions |
|---|---|
| Protocol | list, create, view, edit, cancel, pay, reverse, exempt |
| Contract | list, create, view, edit, complete, cancel |
| Financial | forms, methods, transactions — full CRUD |
| Cashier | open, close, verify, withdraw, movements |
| Administration | domains, natures, states, cities — full CRUD |

Default groups: **Administrator** (full bypass), **Registrar** (full operational), **Clerk** (protocols and contracts), **Cashier** (financial), **Read-only** (view only).

### Auditing

PostgreSQL trigger-based auditing in a dedicated `auditoria` schema. Every INSERT, UPDATE, and DELETE across all tables is recorded with:
- User ID, IP address, user agent
- Full before/after state of the record
- Timestamp with timezone

This is **compliance-grade auditing** — required by Brazilian registry regulations and enforced at the database level, not the application level.

### Digital Signatures

Integration with **ICP-Brasil** (Brazil's national PKI) via Lacuna/RestPKI for legally valid digital signatures on documents and certificates.

---

## Government Integrations

OSLO integrates with Brazil's national civil registry infrastructure, transforming isolated offices into connected nodes of the national identity system.

| Integration | Description | Status |
|---|---|---|
| **DOI / Receita Federal** | Mandatory declaration of real estate transactions to the Federal Revenue Service — reports parties (CPF/CNPJ), property values, and tax assessments for every property transfer | **Active** |
| **BrasilAPI (CNPJ)** | Real-time CNPJ lookup — company name, address, legal nature, partners | **Active** |
| **CPF Validation** | Real-time verification of CPF status (active, suspended, deceased, cancelled) against the Federal Revenue Service | **Active** |
| **ICP-Brasil** | National PKI for digital signatures with full legal validity via Lacuna/RestPKI | **Active** |
| **CNIB** | _Central Nacional de Indisponibilidade de Bens_ — national database of court-ordered asset freezes | **Integrated (local)** |
| **CEI/MT** | Mato Grosso state electronic integration hub for inter-office communication | Planned |
| **RTDPJ** | National registry for legal entity documents and titles | Planned |
| **NEXTYR** | Modern court-registry integration platform | Planned |
| **e-SAJ / CNJ** | Judiciary case management — court orders, liens, judicial communications | Planned |
| **SINTER** | National land information system linking property registries with tax authorities | Planned |
| **ONR** | National electronic platform for remote registry service requests | Planned |

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12 · PHP 8.4+ · PostgreSQL · Redis |
| Frontend | Vue 3.4 · Quasar 2.18 · Pinia 3 · Axios |
| Authentication | Laravel Sanctum (HttpOnly cookies, stateful sessions) |
| PDF Generation | mPDF 8 + wkhtmltopdf (Snappy) |
| Queue / Cache | Redis |
| Digital Signatures | Lacuna/RestPKI (ICP-Brasil) |

**Monorepo** — backend and frontend in the same repository for simpler deployment and contribution.

### Maturity Indicators

- **82 passing tests** (Pest/PHPUnit) covering auth, RBAC, CRUD, and business logic
- **138 RBAC permissions** implemented across all modules
- **PostgreSQL trigger-based auditing** at the database level
- **Idempotent seeders** with real regulatory data (MT 2025 fee tables, tax rates, legal act definitions)
- **Typed enums**, reusable traits, consistent coding patterns
- **Comprehensive technical documentation** (CLAUDE.md, MULTI_TENANCY.md, project phases)

---

## Quick Start

### Prerequisites

- PHP 8.4+, Composer
- Node.js 18+
- PostgreSQL 15+
- Redis

### Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate

# Configure database credentials in .env
php artisan migrate --seed

php artisan serve --port=8000
```

### Frontend

```bash
cd frontend
npm install

# Configure API_URL in .env (e.g., http://localhost:8000/api)
npx quasar dev
```

The frontend will be available at `http://localhost:9000`.

---

## Project Structure

```
OSLO/
├── backend/                → Laravel API
│   ├── app/
│   │   ├── Models/             → Eloquent models with traits (PertenceEmpresa, Auditavel)
│   │   ├── Http/Controllers/   → Controllers using RespostaApi trait
│   │   ├── Services/           → Business logic (fee calculation, workflow, etc.)
│   │   └── Traits/             → PertenceEmpresa, Auditavel, Arquivavel, RespostaApi
│   ├── database/
│   │   ├── migrations/         → PostgreSQL migrations
│   │   └── seeders/            → Idempotent seeders with real regulatory data
│   └── routes/api.php          → API routes (/v1/...)
│
├── frontend/               → Vue/Quasar SPA
│   └── src/
│       ├── pages/              → Pages organized by module
│       ├── components/         → Reusable components
│       ├── stores/             → Pinia stores (all API calls go through stores)
│       ├── composables/        → usePermissao, etc.
│       └── router/routes.js    → Frontend routes
│
└── docs/                   → Project documentation
```

---

## Roadmap

### Completed

- [x] Sanctum authentication (stateful, HttpOnly cookies)
- [x] Auxiliary tables (marital status, property regime, nationality, profession, etc.)
- [x] Personal Index with full versioning
- [x] Asset unavailability tracking
- [x] Transaction catalogs (types, reasons, banks)
- [x] Transactions with audit trail
- [x] Complete RBAC system (groups, permissions, users)
- [x] Administration interface
- [x] Federal Revenue Service integration (DOI/RFB)
- [x] CPF/CNPJ validation and lookup

### In Development

- [ ] Complete protocol module (full workflow with state machine)
- [ ] Fee calculation by state (currently MT 2025)
- [ ] Cashier module (open, close, withdraw, verify)
- [ ] PDF generation for receipts and certificates

### Planned

- [ ] Offline mode with background sync
- [ ] Fee tables for all 27 Brazilian states
- [ ] Government integrations: CEI/MT, RTDPJ, NEXTYR, e-SAJ/CNJ, SINTER, ONR
- [ ] Mobile app for field operations
- [ ] Complete API documentation (OpenAPI/Swagger)
- [ ] Multi-language support

---

## Contributing

Contributions are welcome. This project exists to serve registry offices that have no alternatives.

### How to Contribute

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/my-feature`)
3. Commit your changes (`git commit -m 'feat: description'`)
4. Push to the branch (`git push origin feature/my-feature`)
5. Open a Pull Request

### Areas That Need Help

- **State fee tables** — if you know the fee legislation for your Brazilian state, your help is invaluable
- **Testing** — increase automated test coverage
- **Documentation** — installation guides, usage tutorials, contribution guides
- **UI/UX** — improve the interface for non-technical users
- **Offline mode** — sync implementation for areas without stable internet
- **Translations** — help translate the interface and documentation

---

## License

This project is free software distributed under the [MIT](LICENSE) license.

---

<a name="português"></a>

## Português

### O Problema

O Brasil possui mais de **13.000 cartórios extrajudiciais** que atendem **215 milhões de pessoas** em um território de **8,5 milhões de km²** — maior que os Estados Unidos contíguos.

Um único cartório brasileiro concentra funções que nos EUA estão espalhadas entre County Clerk, County Recorder, Title Companies, Notary Public e Secretary of State. Na Europa, o equivalente seria somar o _Standesamt_, o _Grundbuchamt_ e o _Notar_ da Alemanha, ou o _État civil_, _Cadastre_ e _Notaire_ da França.

**Sem cartório, você não existe legalmente no Brasil.** Sem certidão de nascimento: sem RG, sem matrícula escolar, sem SUS, sem benefícios sociais. Sem registro de imóvel: sem prova de propriedade. Sem certidão de óbito: contas bancárias congeladas, heranças travadas, pensões pagas indevidamente.

A realidade dos cartórios pequenos — no interior da Amazônia, no sertão nordestino, em municípios do cerrado — é brutal:

- **Sistemas proprietários caros** (R$2.000–5.000/mês) que consomem a receita de serventias que faturam R$5.000–10.000/mês
- **Internet inexistente ou intermitente** — cidades ligadas por rio, sem fibra óptica, dependendo de satélite
- **Suporte técnico a 800 km de distância** — quando o sistema trava, não há técnico por perto
- **Nenhuma alternativa open source** — até agora

Enquanto isso, a Alemanha tem ~7.500 cartórios para 84 milhões de pessoas com infraestrutura digital excelente. O Brasil tem **13.000+ para 215 milhões**, muitos sem energia elétrica estável, quem dirá internet.

**O resultado:** milhares de cartórios atendendo milhões de cidadãos com sistemas ultrapassados, processos em papel ou planilhas improvisadas — em 2026.

### A Solução

O **OSLO** é o primeiro e único sistema open source completo de gestão cartorária para o Brasil.

- **Custo zero de licença** — instala, usa e adapta sem mensalidade
- **Leve** — roda em hardware modesto
- **Offline-first planejado** — para funcionar com internet intermitente
- **Código aberto e auditável** — transparência para um serviço público por natureza
- **Multi-tenant** — um servidor atende vários cartórios com isolamento total de dados

### Funcionalidades Principais

- **Gestão de protocolos** com workflow completo (Atendimento → Distribuição → Análise → Registro → Concluído)
- **Cálculo automático de emolumentos** por tabela de custas estadual (fixo, faixa progressiva e gratuidade)
- **Gratuidade constitucional** — rastreamento de isenções para cidadãos de baixa renda (Lei 9.534/97), com relatórios para o Fundo de Compensação
- **Indicador Pessoal** — cadastro de pessoas físicas (CPF) e jurídicas (CNPJ) com versionamento completo
- **Indisponibilidade de bens** — verificação obrigatória de restrições judiciais antes de qualquer transferência de imóvel
- **Sistema financeiro integrado** — caixa, transações, formas e meios de pagamento, com auditoria em cada operação
- **RBAC granular** — ~138 permissões organizadas por módulo
- **Auditoria via triggers PostgreSQL** — cada INSERT, UPDATE e DELETE é registrado com usuário, IP e timestamp
- **Assinatura digital ICP-Brasil** via Lacuna/RestPKI
- **Integrações governamentais** — Receita Federal (DOI/RFB), BrasilAPI (CNPJ), validação de CPF em tempo real

### Integrações com Sistemas Públicos

| Integração | Descrição | Status |
|---|---|---|
| **DOI / Receita Federal** | Declaração obrigatória de operações imobiliárias ao fisco federal | **Ativa** |
| **BrasilAPI (CNPJ)** | Consulta de dados completos de pessoa jurídica | **Ativa** |
| **Validação CPF** | Verificação de situação cadastral (ativo, suspenso, falecido, cancelado) | **Ativa** |
| **ICP-Brasil** | Assinatura digital com validade jurídica via Lacuna/RestPKI | **Ativa** |
| **CNIB** | Central Nacional de Indisponibilidade de Bens — restrições judiciais | **Integrado (local)** |
| **CEI/MT** | Central Eletrônica de Integração — comunicação entre serventias (MT) | Planejada |
| **RTDPJ** | Registro de Títulos e Documentos de Pessoa Jurídica | Planejada |
| **NEXTYR** | Plataforma moderna de integração tribunal-serventia | Planejada |
| **e-SAJ / CNJ** | Sistema do Poder Judiciário — ordens judiciais, penhoras, comunicações | Planejada |
| **SINTER** | Sistema Nacional de Gestão de Informações Territoriais | Planejada |
| **ONR** | Operador Nacional do Registro — pedidos remotos de serviços cartorários | Planejada |

### Stack Técnica

| Camada | Tecnologia |
|---|---|
| Backend | Laravel 12 · PHP 8.4+ · PostgreSQL · Redis |
| Frontend | Vue 3.4 · Quasar 2.18 · Pinia 3 · Axios |
| Autenticação | Laravel Sanctum (cookies HttpOnly, sessões stateful) |
| PDF | mPDF 8 + wkhtmltopdf (Snappy) |
| Filas / Cache | Redis |
| Assinatura Digital | Lacuna/RestPKI (ICP-Brasil) |

### Contribuindo

Contribuições são bem-vindas! Veja a seção [Contributing](#contributing) acima.

Se você conhece a legislação de emolumentos do seu estado, sua ajuda com tabelas de custas é especialmente valiosa.

### Licença

Software livre distribuído sob a licença [MIT](LICENSE).
