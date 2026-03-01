<template>
  <q-page padding>
    <!-- Header -->
    <div class="oslo-page-header">
      <div>
        <h1 class="oslo-page-title">Administração</h1>
        <p class="oslo-page-subtitle">Gerencie as configurações e módulos do sistema</p>
      </div>
    </div>

    <!-- Seções -->
    <div v-for="(secao, idx) in menusFiltrados" :key="idx" class="oslo-admin-section">
      <div class="oslo-section-header">
        <span class="oslo-section-label">{{ secao.title }}</span>
        <span class="oslo-section-count">{{ secao.children.length }}</span>
      </div>

      <div class="oslo-admin-grid">
        <router-link
          v-for="(item, index) in secao.children"
          :key="index"
          :to="item.to"
          class="oslo-card-admin"
        >
          <l-icon :name="item.icon" :size="22" class="oslo-card-admin__icon" />
          <span class="oslo-card-admin__label">{{ item.label }}</span>
          <q-icon name="chevron_right" size="16px" class="oslo-card-admin__arrow" />
        </router-link>
      </div>
    </div>

    <!-- Sem permissão -->
    <div v-if="menusFiltrados.length === 0" class="oslo-empty-state">
      <l-icon name="lock" :size="48" />
      <p>Você não possui permissão para acessar módulos de administração.</p>
    </div>
  </q-page>
</template>

<script setup>
import { computed } from 'vue'
import { usePermissao } from 'src/composables/usePermissao'

defineOptions({ name: 'AdministracaoIndex' })

const { temPermissao, isAdmin } = usePermissao()

const menus = [
  {
    title: 'Cartório',
    children: [
      { label: 'Naturezas',           icon: 'list',         to: { name: 'administracao.natureza' },                     permissao: 'NATUREZA_LISTAR'     },
      { label: 'Tabelas de Custas',   icon: 'table-2',      to: { name: 'tarefas' },                                    permissao: 'TABELA_CUSTA_LISTAR' },
      { label: 'Domínios',            icon: 'layers',       to: { name: 'tarefas' },                                    permissao: 'DOMINIO_LISTAR'      },
      { label: 'Feriados',            icon: 'calendar',     to: { name: 'tarefas' },                                    permissao: 'FERIADO_LISTAR'      },
    ],
  },
  {
    title: 'Financeiro',
    children: [
      { label: 'Formas de Pagamento', icon: 'credit-card',     to: { name: 'administracao.forma-pagamento' }, permissao: 'FORMA_PAGAMENTO_LISTAR' },
      { label: 'Meios de Pagamento',  icon: 'arrow-left-right', to: { name: 'administracao.meio-pagamento' },  permissao: 'MEIO_PAGAMENTO_LISTAR'  },
      { label: 'Categorias',          icon: 'tag',              to: { name: 'administracao.categoria' },        permissao: 'CATEGORIA_LISTAR'       },
      { label: 'Caixas',              icon: 'store',            to: { name: 'caixa' },                          permissao: 'CAIXA_LISTAR'           },
      { label: 'Transações',          icon: 'repeat',           to: { name: 'transacoes.lista' },               permissao: 'TRANSACAO_LISTAR'       },
    ],
  },
  {
    title: 'Segurança & Acesso',
    children: [
      { label: 'Grupos',     icon: 'users-round',    to: { name: 'tarefas' }, permissao: 'GRUPO_LISTAR'              },
      { label: 'Permissões', icon: 'shield',         to: { name: 'tarefas' }, permissao: 'PERMISSAO_LISTAR'          },
      { label: 'Usuários',   icon: 'user-cog',       to: { name: 'tarefas' }, permissao: 'USUARIO_PERMISSAO_LISTAR'  },
      { label: 'Auditoria',  icon: 'clipboard-check', to: { name: 'tarefas' }, permissao: 'AUDITORIA_LISTAR'         },
    ],
  },
  {
    title: 'Localidades',
    children: [
      { label: 'Estados', icon: 'map',     to: { name: 'tarefas' }, permissao: 'ESTADO_LISTAR' },
      { label: 'Cidades', icon: 'map-pin', to: { name: 'tarefas' }, permissao: 'CIDADE_LISTAR' },
    ],
  },
]

const menusFiltrados = computed(() => {
  if (isAdmin.value) return menus

  return menus
    .map((secao) => ({
      ...secao,
      children: secao.children.filter((item) => {
        if (!item.permissao) return true
        return temPermissao(item.permissao)
      }),
    }))
    .filter((secao) => secao.children.length > 0)
})
</script>

<style lang="scss" scoped>
.oslo-page-header {
  padding-bottom: 1.5rem;
  border-bottom: 1px solid var(--border-color);
  margin-bottom: 1.5rem;
}

.oslo-page-title {
  font-size: var(--font-size-2xl);
  font-weight: 700;
  color: var(--text-color);
  margin: 0;
  line-height: 1.3;
}

.oslo-page-subtitle {
  font-size: var(--font-size-sm);
  color: var(--text-secondary);
  margin: 4px 0 0 0;
}

.oslo-admin-section {
  margin-bottom: 2rem;
}

.oslo-section-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 12px;
  padding-bottom: 8px;
  border-bottom: 1px solid var(--border-color-light);
}

.oslo-section-label {
  font-size: var(--font-size-xs);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--text-muted);
}

.oslo-section-count {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 20px;
  height: 20px;
  padding: 0 6px;
  border-radius: var(--radius-full);
  background-color: var(--bg-subtle);
  font-size: 0.6875rem;
  font-weight: 600;
  color: var(--text-muted);
}

.oslo-admin-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;

  @media (max-width: 1200px) { grid-template-columns: repeat(3, 1fr); }
  @media (max-width: 768px)  { grid-template-columns: repeat(2, 1fr); }
  @media (max-width: 480px)  { grid-template-columns: 1fr; }
}

.oslo-card-admin {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 16px;
  background: var(--surface);
  border: 1px solid var(--border-color);
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-sm);
  cursor: pointer;
  text-decoration: none;
  transition: border-color var(--transition), box-shadow var(--transition), transform var(--transition);

  &:hover {
    border-color: var(--primary);
    box-shadow: var(--shadow-md);
    transform: translateY(-1px);

    .oslo-card-admin__arrow { opacity: 1; color: var(--primary); }
    .oslo-card-admin__icon  { color: var(--primary); }
  }

  &__icon  { color: var(--text-secondary); flex-shrink: 0; transition: color var(--transition); }
  &__label { flex: 1; font-size: var(--font-size-base); font-weight: 500; color: var(--text-color); }
  &__arrow { color: var(--text-muted); opacity: 0; transition: opacity var(--transition), color var(--transition); flex-shrink: 0; }
}

.oslo-empty-state {
  text-align: center;
  padding: 4rem 2rem;
  color: var(--text-muted);

  p { font-size: var(--font-size-base); margin: 1rem 0 0; }
}
</style>
