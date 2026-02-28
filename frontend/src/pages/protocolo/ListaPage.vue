<template>
  <q-page padding>
    <!-- Header -->
    <div class="flex items-center justify-between q-mb-md">
      <div>
        <div class="text-h6 text-weight-medium">Protocolos</div>
        <div class="text-caption text-grey-6">Gerencie os protocolos do cartório</div>
      </div>
      <q-btn
        v-permissao="'PROTOCOLO_CRIAR'"
        label="Novo Protocolo"
        color="primary"
        unelevated
        @click="abrirCriar"
      >
        <template v-slot:prepend>
          <l-icon name="plus" :size="16" class="q-mr-xs" />
        </template>
      </q-btn>
    </div>

    <!-- Filtros -->
    <q-card flat bordered class="q-mb-md">
      <q-card-section class="q-py-sm">
        <div class="row q-col-gutter-sm items-center">
          <div class="col-md-3 col-12">
            <q-input
              v-model="filtros.numero"
              placeholder="Buscar por número..."
              dense
              outlined
              clearable
              @update:model-value="buscar"
            >
              <template v-slot:prepend>
                <l-icon name="hash" :size="14" />
              </template>
            </q-input>
          </div>
          <div class="col-md-3 col-12">
            <q-input
              v-model="filtros.solicitante"
              placeholder="Buscar por solicitante..."
              dense
              outlined
              clearable
              @update:model-value="buscar"
            >
              <template v-slot:prepend>
                <l-icon name="user" :size="14" />
              </template>
            </q-input>
          </div>
          <div class="col-md-2 col-6">
            <q-select
              v-model="filtros.status"
              :options="opcoesStatus"
              option-value="value"
              option-label="label"
              emit-value
              map-options
              dense
              outlined
              clearable
              placeholder="Status"
              @update:model-value="buscar"
            />
          </div>
          <div class="col-md-2 col-6">
            <q-input
              v-model="filtros.data_inicio"
              type="date"
              dense
              outlined
              label="Data início"
              @update:model-value="buscar"
            />
          </div>
          <div class="col-md-2 col-6">
            <q-input
              v-model="filtros.data_fim"
              type="date"
              dense
              outlined
              label="Data fim"
              @update:model-value="buscar"
            />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Tabela -->
    <q-card flat bordered>
      <q-table
        :rows="store.protocolos"
        :columns="colunas"
        :loading="store.carregando"
        :pagination="paginacao"
        row-key="id"
        flat
        binary-state-sort
        @request="onRequest"
      >
        <!-- Número -->
        <template v-slot:body-cell-numero="props">
          <q-td :props="props">
            <span
              class="text-primary text-weight-bold cursor-pointer"
              @click="abrirDetalhe(props.row)"
            >{{ props.row.numero }}</span>
          </q-td>
        </template>

        <!-- Status -->
        <template v-slot:body-cell-status="props">
          <q-td :props="props">
            <q-chip
              :color="corStatus(props.row.status)"
              text-color="white"
              size="sm"
              dense
            >
              {{ labelStatus(props.row.status) }}
            </q-chip>
          </q-td>
        </template>

        <!-- Valor final -->
        <template v-slot:body-cell-valor_final="props">
          <q-td :props="props" class="text-right">
            {{ formatarDinheiro(props.row.valor_final) }}
          </q-td>
        </template>

        <!-- Data -->
        <template v-slot:body-cell-data_cadastro="props">
          <q-td :props="props">
            {{ formatarData(props.row.data_cadastro) }}
          </q-td>
        </template>

        <!-- Ações -->
        <template v-slot:body-cell-acoes="props">
          <q-td :props="props" class="text-right">
            <q-btn flat dense round @click="abrirDetalhe(props.row)">
              <l-icon name="eye" :size="14" />
              <q-tooltip>Ver detalhes</q-tooltip>
            </q-btn>
          </q-td>
        </template>

        <!-- Empty state -->
        <template v-slot:no-data>
          <div class="full-width flex flex-center q-pa-xl text-grey-5">
            <div class="text-center">
              <l-icon name="files" :size="48" class="q-mb-md" />
              <div class="text-h6 q-mb-xs">Nenhum protocolo encontrado</div>
              <div class="text-body2">Crie o primeiro protocolo clicando em "Novo Protocolo"</div>
            </div>
          </div>
        </template>
      </q-table>
    </q-card>

    <!-- Modal criar protocolo -->
    <criar-protocolo v-model="modalCriar" @criado="onCriado" />
  </q-page>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useProtocoloStore } from 'src/stores/protocolo'
import CriarProtocolo from 'src/components/CriarProtocolo.vue'

const router = useRouter()
const store  = useProtocoloStore()

const modalCriar = ref(false)

const filtros = reactive({
  numero:      '',
  solicitante: '',
  status:      '',
  data_inicio: '',
  data_fim:    '',
})

const paginacao = ref({
  sortBy:      'data_cadastro',
  descending:  true,
  page:        1,
  rowsPerPage: 15,
  rowsNumber:  0,
})

const colunas = [
  { name: 'numero',       label: 'Número',      field: 'numero',       align: 'left',  sortable: true },
  { name: 'solicitante',  label: 'Solicitante',  field: 'solicitante_nome', align: 'left', sortable: false },
  { name: 'valor_final',  label: 'Valor',        field: 'valor_final',  align: 'right', sortable: false },
  { name: 'status',       label: 'Status',       field: 'status',       align: 'center', sortable: false },
  { name: 'data_cadastro', label: 'Data',        field: 'data_cadastro', align: 'left', sortable: true },
  { name: 'acoes',        label: '',             field: 'acoes',        align: 'right' },
]

const opcoesStatus = [
  { label: 'Aberto',       value: 'aberto' },
  { label: 'Pago',         value: 'pago' },
  { label: 'Pago parcial', value: 'pago_parcial' },
  { label: 'Isento',       value: 'isento' },
  { label: 'Cancelado',    value: 'cancelado' },
]

const corStatus = (status) => ({
  aberto:      'blue',
  pago:        'positive',
  pago_parcial: 'orange',
  isento:      'grey',
  cancelado:   'negative',
}[status] ?? 'grey')

const labelStatus = (status) => ({
  aberto:      'Aberto',
  pago:        'Pago',
  pago_parcial: 'Pago Parcial',
  isento:      'Isento',
  cancelado:   'Cancelado',
}[status] ?? status)

const formatarDinheiro = (valor) =>
  Number(valor ?? 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })

const formatarData = (data) => {
  if (!data) return '-'
  return new Date(data).toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' })
}

let timer = null
const buscar = () => {
  clearTimeout(timer)
  timer = setTimeout(() => carregarLista(), 400)
}

const carregarLista = async () => {
  const params = {
    page:     paginacao.value.page,
    por_pagina: paginacao.value.rowsPerPage,
  }
  if (filtros.numero)      params.numero      = filtros.numero
  if (filtros.solicitante) params.solicitante = filtros.solicitante
  if (filtros.status)      params.status      = filtros.status
  if (filtros.data_inicio) params.data_inicio = filtros.data_inicio
  if (filtros.data_fim)    params.data_fim    = filtros.data_fim

  const dados = await store.listar(params)
  paginacao.value.rowsNumber = dados?.total ?? 0
}

const onRequest = ({ pagination }) => {
  paginacao.value.page        = pagination.page
  paginacao.value.rowsPerPage = pagination.rowsPerPage
  carregarLista()
}

const abrirCriar  = () => { modalCriar.value = true }
const onCriado    = () => { modalCriar.value = false; carregarLista() }

const abrirDetalhe = (row) => {
  router.push({ name: 'protocolo.geral', params: { id: row.id } })
}

onMounted(carregarLista)
</script>
