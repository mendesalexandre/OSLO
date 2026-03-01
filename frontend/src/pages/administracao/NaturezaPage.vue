<template>
  <q-page padding>
    <!-- Header -->
    <div class="row items-center q-mb-lg">
      <div class="col">
        <div class="oslo-page-title">Naturezas</div>
        <div class="oslo-page-subtitle">Gerencie os tipos de ato registral</div>
      </div>
      <div class="col-auto">
        <q-btn
          v-permissao="'NATUREZA_CRIAR'"
          unelevated color="primary" no-caps
          @click="abrirModal()"
        >
          <l-icon name="plus" :size="16" class="q-mr-sm" />
          Nova Natureza
        </q-btn>
      </div>
    </div>

    <!-- Filtros -->
    <q-card flat bordered class="q-mb-md">
      <q-card-section class="q-py-sm">
        <div class="row q-col-gutter-md items-end">
          <div class="col-12 col-md-5">
            <v-label label="Buscar por nome" />
            <q-input
              v-model="filtroNome"
              outlined dense clearable
              placeholder="Digite para filtrar..."
              debounce="400"
              @update:model-value="listar"
            >
              <template #prepend><l-icon name="search" :size="16" /></template>
            </q-input>
          </div>
          <div class="col-12 col-md-3">
            <v-label label="Status" />
            <q-select
              v-model="filtroAtivo"
              :options="opcoesStatus"
              option-value="value"
              option-label="label"
              emit-value map-options
              outlined dense
              @update:model-value="listar"
            />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Tabela -->
    <q-card flat bordered>
      <q-table
        :rows="naturezaStore.lista"
        :columns="columns"
        row-key="id"
        :loading="naturezaStore.carregando"
        flat
        hide-bottom
        no-data-label="Nenhuma natureza encontrada"
      >
        <template #body-cell-codigo="props">
          <q-td :props="props">
            <code v-if="props.value" class="codigo-chip">{{ props.value }}</code>
            <span v-else class="text-grey-5">—</span>
          </q-td>
        </template>

        <template #body-cell-is_ativo="props">
          <q-td :props="props">
            <q-badge
              :color="props.value ? 'positive' : 'grey'"
              :label="props.value ? 'Ativa' : 'Inativa'"
            />
          </q-td>
        </template>

        <template #body-cell-acoes="props">
          <q-td :props="props">
            <q-btn
              v-permissao="'NATUREZA_EDITAR'"
              flat dense round color="primary" size="sm"
              @click="abrirModal(props.row)"
            >
              <l-icon name="pen" :size="14" />
              <q-tooltip>Editar</q-tooltip>
            </q-btn>
            <q-btn
              v-permissao="'NATUREZA_EXCLUIR'"
              flat dense round color="negative" size="sm"
              @click="confirmarExcluir(props.row)"
            >
              <l-icon name="trash-2" :size="14" />
              <q-tooltip>Excluir</q-tooltip>
            </q-btn>
          </q-td>
        </template>
      </q-table>
    </q-card>

    <!-- Modal -->
    <ModalNatureza
      v-model="modalAberto"
      :natureza="naturezaSelecionada"
      @salvo="listar"
    />
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { useNaturezaStore } from 'src/stores/natureza'
import ModalNatureza from 'src/components/natureza/ModalNatureza.vue'

defineOptions({ name: 'NaturezaPage' })

const $q            = useQuasar()
const naturezaStore = useNaturezaStore()

const modalAberto       = ref(false)
const naturezaSelecionada = ref(null)
const filtroNome        = ref('')
const filtroAtivo       = ref(null)

const opcoesStatus = [
  { label: 'Todos',    value: null  },
  { label: 'Ativas',   value: true  },
  { label: 'Inativas', value: false },
]

const columns = [
  { name: 'id',        label: 'ID',        field: 'id',       align: 'left',   sortable: true, style: 'width: 60px' },
  { name: 'codigo',    label: 'Código',    field: 'codigo',   align: 'left',   sortable: true, style: 'width: 120px' },
  { name: 'nome',      label: 'Nome',      field: 'nome',     align: 'left',   sortable: true },
  { name: 'descricao', label: 'Descrição', field: 'descricao', align: 'left',  format: (v) => v || '—' },
  { name: 'is_ativo',  label: 'Status',    field: 'is_ativo', align: 'center', style: 'width: 100px' },
  { name: 'acoes',     label: 'Ações',     field: 'acoes',    align: 'center', style: 'width: 100px' },
]

function listar() {
  const filtros = {}
  if (filtroNome.value)    filtros.nome    = filtroNome.value
  if (filtroAtivo.value !== null && filtroAtivo.value !== undefined) {
    filtros.is_ativo = filtroAtivo.value
  }
  naturezaStore.listar(filtros)
}

function abrirModal(natureza = null) {
  naturezaSelecionada.value = natureza
  modalAberto.value = true
}

function confirmarExcluir(natureza) {
  $q.dialog({
    title:   'Excluir natureza',
    message: `Deseja excluir "<strong>${natureza.nome}</strong>"?`,
    html:    true,
    cancel:  { label: 'Cancelar', flat: true, color: 'grey-7' },
    ok:      { label: 'Excluir',  unelevated: true, color: 'negative', noCaps: true },
  }).onOk(async () => {
    try {
      await naturezaStore.excluir(natureza.id)
      $q.notify({ type: 'positive', message: 'Natureza excluída com sucesso', position: 'top' })
    } catch (e) {
      $q.notify({
        type:    'negative',
        message: e.response?.data?.mensagem ?? 'Erro ao excluir',
        position: 'top',
      })
    }
  })
}

onMounted(listar)
</script>

<style scoped>
.codigo-chip {
  background: var(--bg-subtle);
  color: var(--text-secondary);
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-family: monospace;
}
</style>
