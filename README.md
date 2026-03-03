# OSLO — Open Source Registry Office Management System

> Sistema open source de gestão cartorária para o Brasil.
> Feito para os cartórios que ninguém vê — os pequenos, os remotos, os que mais precisam.

<p align="center">
  <strong>Português</strong> · <a href="#english">English</a>
</p>

---

## O Problema

O Brasil possui mais de **13.000 cartórios extrajudiciais** que atendem 215 milhões de pessoas em cada momento importante da vida: nascimento, casamento, compra de imóvel, óbito. São infraestrutura pública essencial.

Mas a realidade dos cartórios pequenos — especialmente em municípios do interior, áreas rurais e regiões remotas da Amazônia, do sertão e do cerrado — é dura:

- **Sistemas proprietários caros** que consomem boa parte da receita de uma serventia pequena
- **Dependência de internet estável** que simplesmente não existe em muitas localidades
- **Suporte técnico distante** — quando o sistema trava, não há técnico a 800 km de distância
- **Nenhuma alternativa open source** — até agora

Esses cartórios atendem comunidades inteiras que dependem deles para registrar filhos, formalizar uniões, transferir propriedades. Quando o cartório não funciona, a vida civil dessas pessoas para.

## A Solução

O **OSLO** é o primeiro e único sistema open source completo de gestão cartorária para o Brasil.

Foi pensado desde o início para funcionar onde as condições são difíceis:

- **Arquitetura offline-first** planejada — para funcionar mesmo com internet intermitente
- **Leve e eficiente** — roda em hardware modesto, sem exigir infraestrutura cara
- **Sem licença, sem mensalidade** — o cartório instala, usa e adapta como precisar
- **Código aberto e auditável** — transparência total para um serviço que é público por natureza
- **Multi-tenant** — um único servidor pode atender vários cartórios com isolamento completo de dados

### Funcionalidades

- **Gestão de protocolos** com workflow completo (Atendimento → Distribuição → Análise → Registro → Concluído)
- **Cálculo automático de emolumentos** conforme tabela de custas estadual, com suporte a cálculo fixo, faixa progressiva e gratuidade
- **Sistema financeiro integrado** — caixa, transações, formas e meios de pagamento
- **Indicador Pessoal** — cadastro completo de pessoas físicas e jurídicas com versionamento
- **Indisponibilidade de bens** — consulta e vinculação a indicadores pessoais
- **RBAC granular** — ~138 permissões organizadas por módulo (Protocolo, Contrato, Financeiro, Caixa, Administração)
- **Auditoria completa** via triggers PostgreSQL — cada operação é rastreável
- **Assinatura digital** via ICP-Brasil (Lacuna/RestPKI)
- **Integração com serviços públicos** — Receita Federal (DOI/RFB), BrasilAPI (CNPJ)

---

## Stack Técnica

| Camada | Tecnologia |
|---|---|
| Backend | Laravel 12 · PHP 8.4+ · PostgreSQL · Redis |
| Frontend | Vue 3.4 · Quasar 2.18 · Pinia 3 · Axios |
| Autenticação | Laravel Sanctum (cookies HttpOnly, stateful) |
| PDF | mPDF 8 + wkhtmltopdf (Snappy) |
| Filas | Redis (queues + sessions + cache) |
| Assinatura Digital | Lacuna/RestPKI (ICP-Brasil) |

**Monorepo** — backend e frontend no mesmo repositório para simplificar deploy e contribuição.

---

## Instalação Rápida

### Pré-requisitos

- PHP 8.4+
- Composer
- Node.js 18+
- PostgreSQL 15+
- Redis

### Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate

# Configurar banco de dados no .env
php artisan migrate --seed

php artisan serve --port=8000
```

### Frontend

```bash
cd frontend
npm install

# Configurar API_URL no .env (ex: http://localhost:8000/api)
npx quasar dev
```

O frontend estará disponível em `http://localhost:9000`.

---

## Estrutura do Projeto

```
OSLO/
├── backend/          → API Laravel
│   ├── app/
│   │   ├── Models/           → Eloquent models com traits (PertenceEmpresa, Auditavel)
│   │   ├── Http/Controllers/ → Controllers com RespostaApi trait
│   │   ├── Services/         → Lógica de negócio (cálculo de emolumentos, etc.)
│   │   └── Traits/           → PertenceEmpresa, Auditavel, Arquivavel, RespostaApi
│   ├── database/
│   │   ├── migrations/       → Migrations PostgreSQL
│   │   └── seeders/          → Seeders idempotentes com dados reais
│   └── routes/api.php        → Rotas da API (/v1/...)
│
├── frontend/         → App Vue/Quasar
│   └── src/
│       ├── pages/            → Páginas organizadas por módulo
│       ├── components/       → Componentes reutilizáveis
│       ├── stores/           → Stores Pinia (toda chamada API passa por aqui)
│       ├── composables/      → usePermissao, etc.
│       └── router/routes.js  → Rotas do frontend
│
└── docs/             → Documentação do projeto
```

---

## Roadmap

### Concluído

- [x] Autenticação Sanctum (stateful, HttpOnly cookies)
- [x] Tabelas auxiliares (estado civil, regime de bens, nacionalidade, etc.)
- [x] Indicador Pessoal com versionamento
- [x] Indisponibilidade de bens
- [x] Catálogos de transação (tipos, motivos, bancos)
- [x] Transações com auditoria
- [x] Sistema RBAC completo (grupos, permissões, usuários)
- [x] Interface de administração

### Em desenvolvimento

- [ ] Módulo de protocolo completo (workflow de etapas)
- [ ] Cálculo de emolumentos por estado (atualmente MT 2025)
- [ ] Módulo de caixa (abertura, fechamento, sangria, conferência)
- [ ] Geração de recibos e certidões em PDF

### Planejado

- [ ] Modo offline com sincronização
- [ ] Tabelas de custas de todos os 27 estados
- [ ] Integrações: CEI/MT, RTDPJ, NEXTYR, e-SAJ/CNJ
- [ ] App mobile para atendimento em campo
- [ ] Documentação de API completa (OpenAPI/Swagger)

---

## Contribuindo

Contribuições são bem-vindas! Este projeto existe para servir cartórios que não têm alternativas.

### Como contribuir

1. Faça um fork do repositório
2. Crie uma branch para sua feature (`git checkout -b feature/minha-feature`)
3. Commit suas mudanças (`git commit -m 'feat: descrição da mudança'`)
4. Push para a branch (`git push origin feature/minha-feature`)
5. Abra um Pull Request

### Áreas que precisam de ajuda

- **Tabelas de custas estaduais** — Se você conhece a legislação de emolumentos do seu estado, sua ajuda é valiosa
- **Testes** — Aumentar cobertura de testes automatizados
- **Documentação** — Guias de instalação, uso e contribuição
- **UI/UX** — Melhorar a interface para usuários não técnicos
- **Modo offline** — Implementação de sincronização para áreas sem internet estável

---

## Licença

Este projeto é software livre distribuído sob a licença [MIT](LICENSE).

---

<a name="english"></a>

## English

### What is OSLO?

OSLO is the **first and only open-source registry office management system for Brazil**. Brazilian registry offices (_cartórios_) handle every citizen's critical life documents — birth certificates, marriage records, property registration, death certificates — yet most run expensive proprietary software that small offices in remote areas simply cannot afford.

Over 13,000 registry offices serve 215 million Brazilians. In rural and remote areas — the Amazon, the _sertão_, small towns hundreds of kilometers from the nearest city — these offices often struggle with unreliable internet, no local tech support, and software costs that eat into their limited revenue. **OSLO exists to change that.**

### Key Features

- **Complete protocol management** with state-machine workflow (Reception → Distribution → Analysis → Registration → Completed)
- **Automated fee calculation** based on state-regulated fee tables (fixed, progressive bracket, and free-of-charge)
- **Full financial module** — cashier, transactions, payment methods
- **Granular RBAC** — ~138 permissions across all modules
- **PostgreSQL trigger-based auditing** — complete traceability of every operation
- **Multi-tenant SaaS architecture** — one server, multiple offices, fully isolated data
- **Digital signatures** via ICP-Brasil (Lacuna/RestPKI)
- **Government integrations** — Federal Revenue Service (DOI/RFB), BrasilAPI (CNPJ lookup)

### Tech Stack

Laravel 12 + PHP 8.4 + PostgreSQL + Redis (backend) · Vue 3.4 + Quasar 2.18 + Pinia (frontend) · Laravel Sanctum (auth) · Monorepo

### Contributing

Contributions are welcome. See the [Contributing](#contribuindo) section above. If you know Brazilian registry office regulations for your state, your help with fee tables is especially valuable.

### License

This project is free software distributed under the [MIT](LICENSE) license.
