<template>
  <q-page padding>
    <!-- Header -->
    <div class="row items-center q-mb-lg">
      <div class="col">
        <div class="oslo-page-title">Meios de Pagamento</div>
        <div class="oslo-page-subtitle">Gerencie os meios de pagamento disponíveis</div>
      </div>
      <div class="col-auto">
        <q-btn
          v-permissao="'MEIO_PAGAMENTO_CRIAR'"
          unelevated color="primary" no-caps
          @click="abrirModal()"
        >
          <l-icon name="plus" :size="16" class="q-mr-sm" />
          Novo Meio
        </q-btn>
      </div>
    </div>

    <!-- Filtro por forma -->
    <q-card flat bordered class="q-mb-md">
      <q-card-section class="q-py-sm">
        <div class="row q-col-gutter-md items-end">
          <div class="col-12 col-md-4">
            <v-label label="Filtrar por Forma de Pagamento" />
            <q-select
              v-model="filtroFormaId"
              :options="opcoesFormas"
              option-value="value"
              option-label="label"
              emit-value map-options
              outlined dense clearable
              placeholder="Todas"
            />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Tabela -->
    <q-card flat bordered>
      <q-table
        :rows="meiosFiltrados"
        :columns="columns"
        row-key="id"
        :loading="meioPagamentoStore.carregando"
        flat
        hide-bottom
        no-data-label="Nenhum meio de pagamento cadastrado"
      >
        <template #body-cell-forma="props">
          <q-td :props="props">
            <q-badge v-if="props.value" color="primary" :label="props.value" />
            <span v-else class="text-grey-5">Não vinculado</span>
          </q-td>
        </template>

        <template #body-cell-taxas="props">
          <q-td :props="props">
            <div v-if="Number(props.row.taxa_percentual) > 0 || Number(props.row.taxa_fixa) > 0">
              <div v-if="Number(props.row.taxa_percentual) > 0" class="text-caption">
                {{ Number(props.row.taxa_percentual).toFixed(2) }}%
              </div>
              <div v-if="Number(props.row.taxa_fixa) > 0" class="text-caption">
                R$ {{ Number(props.row.taxa_fixa).toFixed(2) }}
              </div>
            </div>
            <span v-else class="text-grey-5">—</span>
          </q-td>
        </template>

        <template #body-cell-is_ativo="props">
          <q-td :props="props">
            <q-badge :color="props.value ? 'positive' : 'grey'" :label="props.value ? 'Ativo' : 'Inativo'" />
          </q-td>
        </template>

        <template #body-cell-acoes="props">
          <q-td :props="props">
            <q-btn
              v-permissao="'MEIO_PAGAMENTO_EDITAR'"
              flat dense round color="primary" size="sm"
              @click="abrirModal(props.row)"
            >
              <l-icon name="pen" :size="14" />
              <q-tooltip>Editar</q-tooltip>
            </q-btn>
            <q-btn
              v-permissao="'MEIO_PAGAMENTO_EXCLUIR'"
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
    <ModalMeioPagamento
      v-model="modalAberto"
      :meio="meioSelecionado"
      @salvo="recarregar"
    />
  </q-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { useFormaPagamentoStore } from 'src/stores/formaPagamento'
import { useMeioPagamentoStore } from 'src/stores/meioPagamento'
import ModalMeioPagamento from 'src/components/administracao/ModalMeioPagamento.vue'

defineOptions({ name: 'MeioPagamentoPage' })

const $q = useQuasar()
const formaPagamentoStore = useFormaPagamentoStore()
const meioPagamentoStore  = useMeioPagamentoStore()

const modalAberto    = ref(false)
const meioSelecionado = ref(null)
const filtroFormaId  = ref(null)

const columns = [
  { name: 'id',       label: 'ID',                 field: 'id',       align: 'left', sortable: true, style: 'width: 60px' },
  { name: 'nome',     label: 'Nome',               field: 'nome',     align: 'left', sortable: true },
  { name: 'forma',    label: 'Forma',              field: (r) => r.forma_pagamento?.nome || null, align: 'left' },
  { name: 'taxas',    label: 'Taxas',              field: 'taxas',    align: 'center', style: 'width: 120px' },
  { name: 'prazo',    label: 'Prazo',              field: 'prazo_compensacao', align: 'center', format: (v) => v > 0 ? `${v} dias` : '—', style: 'width: 100px' },
  { name: 'is_ativo', label: 'Status',             field: 'is_ativo', align: 'center', style: 'width: 100px' },
  { name: 'acoes',    label: 'Ações',              field: 'acoes',    align: 'center', style: 'width: 100px' },
]

const opcoesFormas = computed(() => [
  { label: 'Todas', value: null },
  ...formaPagamentoStore.formasAtivas.map((f) => ({ label: f.nome, value: f.id })),
])

const meiosFiltrados = computed(() => {
  if (!filtroFormaId.value) return meioPagamentoStore.meios
  return meioPagamentoStore.porFormaPagamento(filtroFormaId.value)
})

function abrirModal(meio = null) {
  meioSelecionado.value = meio
  modalAberto.value = true
}

function confirmarExcluir(meio) {
  $q.dialog({
    title:   'Excluir meio de pagamento',
    message: `Deseja excluir "<strong>${meio.nome}</strong>"?`,
    html:    true,
    cancel:  { label: 'Cancelar', flat: true, color: 'grey-7' },
    ok:      { label: 'Excluir',  unelevated: true, color: 'negative', noCaps: true },
  }).onOk(async () => {
    try {
      await meioPagamentoStore.excluir(meio.id)
      $q.notify({ type: 'positive', message: 'Meio de pagamento excluído', position: 'top' })
    } catch (e) {
      $q.notify({
        type:    'negative',
        message: e.response?.data?.mensagem ?? 'Erro ao excluir',
        position: 'top',
      })
    }
  })
}

async function recarregar() {
  await meioPagamentoStore.listar(true)
}

onMounted(async () => {
  await Promise.all([
    formaPagamentoStore.listar(),
    meioPagamentoStore.listar(),
  ])
})
</script>
