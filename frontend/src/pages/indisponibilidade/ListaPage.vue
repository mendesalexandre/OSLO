<template>
  <q-page padding>
    <div class="row items-center q-mb-md">
      <div class="text-h5 col">Indisponibilidades</div>
      <q-btn
        color="primary"
        icon="add"
        label="Nova"
        unelevated
        :to="{ name: 'indisponibilidades.nova' }"
      />
    </div>

    <!-- Filtros -->
    <q-card flat bordered class="q-mb-md">
      <q-card-section>
        <div class="row q-gutter-sm">
          <q-input
            v-model="filtros.busca"
            dense
            outlined
            placeholder="Protocolo ou nº processo..."
            class="col"
            clearable
            @update:model-value="buscar"
          >
            <template #prepend>
              <q-icon name="search" />
            </template>
          </q-input>
          <q-input
            v-model="filtros.cpf_cnpj"
            dense
            outlined
            placeholder="CPF/CNPJ da parte..."
            style="min-width: 200px"
            clearable
            @update:model-value="buscar"
          />
          <q-select
            v-model="filtros.status"
            dense
            outlined
            clearable
            :options="opcoesStatus"
            option-label="label"
            option-value="value"
            emit-value
            map-options
            label="Status"
            style="min-width: 140px"
            @update:model-value="buscar"
          />
          <q-select
            v-model="filtros.tipo"
            dense
            outlined
            clearable
            :options="opcoesTipo"
            option-label="label"
            option-value="value"
            emit-value
            map-options
            label="Tipo"
            style="min-width: 120px"
            @update:model-value="buscar"
          />
        </div>
      </q-card-section>
    </q-card>

    <!-- Tabela -->
    <q-card flat bordered>
      <q-table
        :rows="store.lista"
        :columns="colunas"
        :loading="store.carregando"
        :rows-per-page-options="[15, 30, 50]"
        row-key="id"
        flat
        @request="onRequest"
      >
        <template #body-cell-status="props">
          <q-td :props="props">
            <q-chip
              dense
              :color="corStatus(props.row.status)"
              text-color="white"
              :label="labelStatus(props.row.status)"
            />
          </q-td>
        </template>

        <template #body-cell-ordem_prioritaria="props">
          <q-td :props="props" class="text-center">
            <q-icon
              v-if="props.row.ordem_prioritaria"
              name="priority_high"
              color="negative"
              size="sm"
            >
              <q-tooltip>Ordem prioritária</q-tooltip>
            </q-icon>
          </q-td>
        </template>

        <template #body-cell-data_pedido="props">
          <q-td :props="props">
            {{ formatarData(props.row.data_pedido) }}
          </q-td>
        </template>

        <template #body-cell-acoes="props">
          <q-td :props="props" class="text-right">
            <q-btn
              flat
              round
              dense
              icon="visibility"
              color="grey-7"
              :to="{ name: 'indisponibilidades.editar', params: { id: props.row.id } }"
            >
              <q-tooltip>Ver / Editar</q-tooltip>
            </q-btn>
            <q-btn
              v-if="props.row.status !== 'cancelada'"
              flat
              round
              dense
              icon="cancel"
              color="orange-8"
              @click="abrirCancelar(props.row)"
            >
              <q-tooltip>Cancelar</q-tooltip>
            </q-btn>
            <q-btn
              flat
              round
              dense
              icon="delete"
              color="negative"
              @click="confirmarExclusao(props.row)"
            >
              <q-tooltip>Excluir</q-tooltip>
            </q-btn>
          </q-td>
        </template>
      </q-table>
    </q-card>

    <!-- Dialog cancelamento -->
    <CancelarDialog
      v-model="dialogCancelar"
      :indisponibilidade="selecionada"
      @cancelada="buscar"
    />
  </q-page>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { useQuasar } from 'quasar'
import { useIndisponibilidadeStore } from 'src/stores/indisponibilidade'
import CancelarDialog from './CancelarDialog.vue'

const $q = useQuasar()
const store = useIndisponibilidadeStore()

const filtros = reactive({ busca: '', cpf_cnpj: '', status: null, tipo: null })
const dialogCancelar = ref(false)
const selecionada = ref(null)

const opcoesStatus = [
  { label: 'Pendente', value: 'pendente' },
  { label: 'Cumprida', value: 'cumprida' },
  { label: 'Cancelada', value: 'cancelada' },
  { label: 'Em análise', value: 'em_analise' },
]

const opcoesTipo = [
  { label: 'Judicial', value: 'judicial' },
  { label: 'Extrajudicial', value: 'extrajudicial' },
]

const colunas = [
  { name: 'protocolo_indisponibilidade', label: 'Protocolo', field: 'protocolo_indisponibilidade', align: 'left', sortable: true },
  { name: 'numero_processo', label: 'Nº Processo', field: 'numero_processo', align: 'left' },
  { name: 'status', label: 'Status', field: 'status', align: 'center' },
  { name: 'tipo', label: 'Tipo', field: 'tipo', align: 'center' },
  { name: 'forum_vara', label: 'Fórum/Vara', field: 'forum_vara', align: 'left' },
  { name: 'data_pedido', label: 'Data Pedido', field: 'data_pedido', align: 'center' },
  { name: 'ordem_prioritaria', label: '', field: 'ordem_prioritaria', align: 'center' },
  { name: 'acoes', label: '', field: 'acoes', align: 'right' },
]

onMounted(() => buscar())

function buscar() {
  const params = {}
  if (filtros.busca) params.busca = filtros.busca
  if (filtros.cpf_cnpj) params.cpf_cnpj = filtros.cpf_cnpj.replace(/\D/g, '')
  if (filtros.status) params.status = filtros.status
  if (filtros.tipo) params.tipo = filtros.tipo
  store.fetchLista(params)
}

function onRequest({ pagination }) {
  const params = { page: pagination.page }
  if (filtros.busca) params.busca = filtros.busca
  if (filtros.cpf_cnpj) params.cpf_cnpj = filtros.cpf_cnpj.replace(/\D/g, '')
  if (filtros.status) params.status = filtros.status
  if (filtros.tipo) params.tipo = filtros.tipo
  store.fetchLista(params)
}

function corStatus(status) {
  const mapa = { pendente: 'orange-7', cumprida: 'positive', cancelada: 'grey-6', em_analise: 'blue-6' }
  return mapa[status] ?? 'grey-5'
}

function labelStatus(status) {
  const mapa = { pendente: 'Pendente', cumprida: 'Cumprida', cancelada: 'Cancelada', em_analise: 'Em análise' }
  return mapa[status] ?? status
}

function formatarData(data) {
  if (!data) return '—'
  return new Date(data).toLocaleDateString('pt-BR')
}

function abrirCancelar(registro) {
  selecionada.value = registro
  dialogCancelar.value = true
}

function confirmarExclusao(registro) {
  $q.dialog({
    title: 'Confirmar exclusão',
    message: `Excluir indisponibilidade "${registro.protocolo_indisponibilidade}"?`,
    cancel: true,
    persistent: true,
  }).onOk(async () => {
    await store.excluir(registro.id)
    buscar()
    $q.notify({ type: 'positive', message: 'Indisponibilidade excluída com sucesso.' })
  })
}
</script>
