<template>
  <q-page padding>
    <div class="row items-center q-mb-md">
      <div class="text-h5 col">Transações</div>
      <q-btn
        color="primary"
        icon="add"
        label="Nova Transação"
        unelevated
        @click="abrirNovo"
      />
    </div>

    <!-- Filtros -->
    <q-card flat bordered class="q-mb-md">
      <q-card-section>
        <div class="row q-gutter-sm">
          <q-select
            v-model="filtros.tipo"
            dense outlined clearable
            :options="opcoesTipo"
            option-label="label"
            option-value="value"
            emit-value map-options
            label="Tipo"
            style="min-width: 150px"
            @update:model-value="buscar"
          />
          <q-select
            v-model="filtros.situacao"
            dense outlined clearable
            :options="opcoesSituacao"
            option-label="label"
            option-value="value"
            emit-value map-options
            label="Situação"
            style="min-width: 150px"
            @update:model-value="buscar"
          />
          <v-date
            v-model="filtros.data_inicio"
            dense outlined clearable
            label="De"
            style="min-width: 140px"
            @update:model-value="buscar"
          />
          <v-date
            v-model="filtros.data_fim"
            dense outlined clearable
            label="Até"
            style="min-width: 140px"
            @update:model-value="buscar"
          />
        </div>
      </q-card-section>
    </q-card>

    <!-- Tabela -->
    <q-card flat bordered>
      <q-table
        :rows="transacaoStore.lista"
        :columns="colunas"
        :loading="transacaoStore.carregando"
        :rows-per-page-options="[15, 30, 50]"
        row-key="id"
        flat
        @request="onRequest"
      >
        <template #body-cell-situacao="props">
          <q-td :props="props">
            <q-badge :color="corSituacao(props.row.situacao)" :label="props.row.situacao" />
          </q-td>
        </template>

        <template #body-cell-tipo="props">
          <q-td :props="props">
            <q-badge
              :color="props.row.tipo_transacao?.tipo === 'entrada' ? 'positive' : (props.row.tipo_transacao?.tipo === 'saida' ? 'negative' : 'info')"
              :label="props.row.tipo_transacao?.descricao"
            />
          </q-td>
        </template>

        <template #body-cell-valor="props">
          <q-td :props="props" class="text-right">
            {{ formatarValor(props.row.valor) }}
          </q-td>
        </template>

        <template #body-cell-acoes="props">
          <q-td :props="props" class="text-right">
            <q-btn
              v-if="props.row.situacao === 'pendente'"
              flat dense round
              color="positive"
              icon="check_circle"
              title="Confirmar"
              @click="confirmarTransacao(props.row)"
            />
            <q-btn
              v-if="props.row.situacao === 'pendente'"
              flat dense round
              color="primary"
              icon="edit"
              title="Editar"
              @click="abrirEditar(props.row)"
            />
            <q-btn
              flat dense round
              color="negative"
              icon="delete"
              title="Excluir"
              @click="excluirTransacao(props.row)"
            />
          </q-td>
        </template>
      </q-table>
    </q-card>

    <modal-transacao
      v-model="modalAberto"
      :transacao="transacaoSelecionada"
      @salvo="carregarLista"
    />
  </q-page>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { useQuasar } from 'quasar'
import { useTransacaoStore } from 'src/stores/transacao'
import ModalTransacao from 'src/components/transacao/ModalTransacao.vue'

const $q             = useQuasar()
const transacaoStore = useTransacaoStore()

const modalAberto          = ref(false)
const transacaoSelecionada = ref(null)

const filtros = reactive({
  tipo:        null,
  situacao:    null,
  data_inicio: null,
  data_fim:    null,
})

const opcoesTipo = [
  { value: 'entrada', label: 'Entrada' },
  { value: 'saida',   label: 'Saída'   },
  { value: 'caixa',   label: 'Caixa'   },
]

const opcoesSituacao = [
  { value: 'pendente',   label: 'Pendente'   },
  { value: 'confirmada', label: 'Confirmada' },
  { value: 'liquidada',  label: 'Liquidada'  },
  { value: 'cancelada',  label: 'Cancelada'  },
]

const colunas = [
  { name: 'numero_transacao', label: 'Número',       field: 'numero_transacao', align: 'left',  sortable: true },
  { name: 'tipo',             label: 'Tipo',          field: 'tipo_transacao',   align: 'left'  },
  { name: 'descricao',        label: 'Descrição',     field: 'descricao',        align: 'left'  },
  { name: 'valor',            label: 'Valor',         field: 'valor',            align: 'right', sortable: true },
  { name: 'data_transacao',   label: 'Data',          field: 'data_transacao',   align: 'left',  sortable: true },
  { name: 'situacao',         label: 'Situação',      field: 'situacao',         align: 'left'  },
  { name: 'acoes',            label: 'Ações',         field: 'acoes',            align: 'right' },
]

function corSituacao(situacao) {
  const mapa = {
    pendente:   'warning',
    confirmada: 'positive',
    liquidada:  'info',
    cancelada:  'negative',
  }
  return mapa[situacao] ?? 'grey'
}

function formatarValor(valor) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(valor)
}

async function carregarLista(params = {}) {
  await transacaoStore.fetchLista({ ...filtros, ...params })
}

function buscar() {
  carregarLista()
}

function onRequest(props) {
  const { page, rowsPerPage } = props.pagination
  carregarLista({ page, per_page: rowsPerPage })
}

function abrirNovo() {
  transacaoSelecionada.value = null
  modalAberto.value = true
}

function abrirEditar(transacao) {
  transacaoSelecionada.value = transacao
  modalAberto.value = true
}

async function confirmarTransacao(transacao) {
  try {
    await transacaoStore.confirmar(transacao.id)
    $q.notify({ type: 'positive', message: 'Transação confirmada', position: 'top' })
    carregarLista()
  } catch (e) {
    $q.notify({
      type: 'negative',
      message: e.response?.data?.mensagem ?? 'Erro ao confirmar transação',
      position: 'top',
    })
  }
}

async function excluirTransacao(transacao) {
  $q.dialog({
    title: 'Excluir Transação',
    message: `Deseja excluir a transação ${transacao.numero_transacao}?`,
    cancel: true,
    persistent: true,
  }).onOk(async () => {
    try {
      await transacaoStore.excluir(transacao.id)
      $q.notify({ type: 'positive', message: 'Transação excluída', position: 'top' })
      carregarLista()
    } catch (e) {
      $q.notify({
        type: 'negative',
        message: e.response?.data?.mensagem ?? 'Erro ao excluir transação',
        position: 'top',
      })
    }
  })
}

onMounted(carregarLista)
</script>
