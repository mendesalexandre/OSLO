<template>
  <q-page class="protocolo-detalhe-page">
    <!-- Header com abas -->
    <q-header class="bg-white text-dark" bordered>
      <q-toolbar>
        <q-btn flat dense round @click="voltar" class="q-mr-sm">
          <l-icon name="arrow-left" :size="18" />
        </q-btn>

        <div class="text-subtitle1 text-weight-bold text-uppercase">
          {{ titulo }}
        </div>

        <q-space />

        <!-- Abas pill -->
        <q-tabs
          :model-value="abaAtiva"
          mobile-arrows
          align="left"
          no-caps
          active-color="white"
          indicator-color="transparent"
          class="oslo-tabs-pill"
        >
          <q-route-tab
            :to="{ name: 'protocolo.geral', params: { id: $route.params.id } }"
            name="geral"
            label="Informações Gerais"
            class="oslo-tab-pill"
          />
          <q-route-tab
            :to="{ name: 'protocolo.atos', params: { id: $route.params.id } }"
            name="atos"
            class="oslo-tab-pill"
          >
            <div class="oslo-tab-pill__content">
              <span>Atos & Registros</span>
              <q-badge
                v-if="totalAtos > 0"
                :label="totalAtos"
                color="orange-3"
                text-color="orange-9"
                class="q-ml-xs"
              />
            </div>
          </q-route-tab>
          <q-route-tab
            :to="{ name: 'protocolo.financeiro', params: { id: $route.params.id } }"
            name="financeiro"
            class="oslo-tab-pill"
          >
            <div class="oslo-tab-pill__content">
              <span>Financeiro</span>
              <q-badge
                v-if="valorRestante > 0"
                label="!"
                color="red-3"
                text-color="red-9"
                class="q-ml-xs"
              />
            </div>
          </q-route-tab>
          <q-route-tab
            :to="{ name: 'protocolo.anotacoes', params: { id: $route.params.id } }"
            name="anotacoes"
            label="Anotações"
            class="oslo-tab-pill"
          />
        </q-tabs>

        <q-space />

        <!-- Ações do protocolo -->
        <div class="q-gutter-sm flex items-center">
          <q-btn
            v-if="protocolo && protocolo.status !== 'cancelado'"
            flat dense no-caps color="grey-7"
            @click="imprimirProtocolo"
          >
            <l-icon name="printer" :size="16" class="q-mr-xs" />
            Imprimir
          </q-btn>
          <q-btn
            v-if="protocolo && (protocolo.status === 'aberto' || protocolo.status === 'pago_parcial')"
            flat dense no-caps color="negative"
            @click="confirmarCancelamento"
          >
            <l-icon name="x-circle" :size="16" class="q-mr-xs" />
            Cancelar
          </q-btn>
        </div>
      </q-toolbar>

      <!-- Barra de info rápida -->
      <q-toolbar v-if="protocolo" class="bg-grey-1" style="min-height: 36px;">
        <q-chip
          :color="corStatus(protocolo.status)"
          text-color="white"
          size="sm"
          dense
        >
          {{ labelStatus(protocolo.status) }}
        </q-chip>
        <span class="text-caption text-grey-7 q-mx-sm">
          <strong>Solicitante:</strong> {{ protocolo.solicitante_nome || '-' }}
        </span>
        <span v-if="protocolo.solicitante_cpf_cnpj" class="text-caption text-grey-7 q-mx-sm">
          <strong>CPF/CNPJ:</strong> {{ protocolo.solicitante_cpf_cnpj }}
        </span>
        <span class="text-caption text-grey-7 q-mx-sm">
          <strong>Data:</strong> {{ formatarData(protocolo.data_cadastro) }}
        </span>
        <span v-if="protocolo.matricula" class="text-caption text-grey-7 q-mx-sm">
          <strong>Matrícula:</strong> {{ protocolo.matricula }}
        </span>
      </q-toolbar>
    </q-header>

    <!-- Conteúdo com Sidebar -->
    <div class="protocolo-layout">
      <!-- Conteúdo principal -->
      <div class="protocolo-content">
        <router-view
          v-if="protocolo"
          :protocolo="protocolo"
          @atualizar="carregarProtocolo"
        />
      </div>

      <!-- Sidebar direita -->
      <protocolo-sidebar
        v-if="protocolo"
        :protocolo="protocolo"
        class="protocolo-sidebar-container"
      />
    </div>

    <!-- Footer financeiro -->
    <q-footer class="bg-white text-dark" bordered>
      <div class="q-pa-md">
        <div class="row items-center justify-between">
          <div class="col">
            <div class="row items-center q-gutter-md">
              <div class="summary-item">
                <span class="text-caption text-grey-6">Total:</span>
                <span class="text-weight-bold text-grey-8 q-ml-xs">
                  {{ formatarDinheiro(protocolo?.valor_total ?? 0) }}
                </span>
              </div>
              <div v-if="(protocolo?.valor_desconto ?? 0) > 0" class="summary-item">
                <span class="text-caption text-grey-6">Desconto:</span>
                <span class="text-weight-medium text-red-6 q-ml-xs">
                  -{{ formatarDinheiro(protocolo.valor_desconto) }}
                </span>
              </div>
              <div v-if="(protocolo?.valor_pago ?? 0) > 0" class="summary-item">
                <span class="text-caption text-grey-6">Pago:</span>
                <span class="text-weight-medium text-green-6 q-ml-xs">
                  {{ formatarDinheiro(protocolo.valor_pago) }}
                </span>
              </div>
              <div class="summary-item">
                <span class="text-caption text-grey-6">Restante:</span>
                <span class="text-weight-bold text-primary q-ml-xs">
                  {{ formatarDinheiro(valorRestante) }}
                </span>
              </div>
            </div>
          </div>
          <div class="col-auto">
            <q-chip
              v-if="valorRestante > 0"
              label="Pendente"
              color="orange-3"
              text-color="orange-8"
              size="sm"
              outline
            />
            <q-chip
              v-else
              label="Quitado"
              color="green-3"
              text-color="green-8"
              size="sm"
              outline
            />
          </div>
        </div>
      </div>
    </q-footer>

    <!-- Loading -->
    <q-inner-loading :showing="store.carregando && !protocolo">
      <q-spinner-dots color="primary" size="50px" />
    </q-inner-loading>
  </q-page>
</template>

<script setup>
import { computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { storeToRefs } from 'pinia'
import { useProtocoloStore } from 'src/stores/protocolo'
import ProtocoloSidebar from 'src/components/protocolo/ProtocoloSidebar.vue'

const $q     = useQuasar()
const route  = useRoute()
const router = useRouter()
const store  = useProtocoloStore()

const { protocolo } = storeToRefs(store)

// Aba ativa baseada na rota
const abaAtiva = computed(() => {
  const name = route.name
  if (name === 'protocolo.atos')      return 'atos'
  if (name === 'protocolo.financeiro') return 'financeiro'
  if (name === 'protocolo.anotacoes') return 'anotacoes'
  return 'geral'
})

const titulo = computed(() => {
  if (!protocolo.value?.numero) return 'Protocolo'
  const partes = protocolo.value.numero.split('/')
  return partes.length === 2
    ? `Protocolo RI ${partes[1]}/${partes[0]}`
    : `Protocolo RI ${protocolo.value.numero}`
})

const totalAtos    = computed(() => protocolo.value?.itens?.length ?? 0)
const valorRestante = computed(() => {
  const final = parseFloat(protocolo.value?.valor_final ?? 0)
  const pago  = parseFloat(protocolo.value?.valor_pago  ?? 0)
  return Math.max(0, final - pago)
})

const formatarDinheiro = (v) =>
  Number(v ?? 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })

const formatarData = (data) => {
  if (!data) return '-'
  return new Date(data).toLocaleDateString('pt-BR')
}

const corStatus = (s) => ({
  aberto: 'blue', pago: 'positive', pago_parcial: 'orange', isento: 'grey', cancelado: 'negative',
}[s] ?? 'grey')

const labelStatus = (s) => ({
  aberto: 'Aberto', pago: 'Pago', pago_parcial: 'Pago Parcial', isento: 'Isento', cancelado: 'Cancelado',
}[s] ?? s)

async function carregarProtocolo() {
  try {
    await store.carregar(route.params.id)
  } catch {
    $q.notify({ type: 'negative', message: 'Protocolo não encontrado', position: 'top-right' })
    router.push({ name: 'protocolo.lista' })
  }
}

function voltar() { router.push({ name: 'protocolo.lista' }) }

function imprimirProtocolo() { window.print() }

function confirmarCancelamento() {
  $q.dialog({
    title: 'Confirmar Cancelamento',
    message: 'Deseja cancelar este protocolo? Esta ação não pode ser desfeita.',
    prompt: { model: '', type: 'text', label: 'Motivo do cancelamento *', isValid: (v) => v?.length > 3 },
    cancel: true,
    persistent: true,
  }).onOk(async (motivo) => {
    try {
      await store.cancelar(protocolo.value.id, motivo)
      $q.notify({ type: 'positive', message: 'Protocolo cancelado', position: 'top-right' })
      router.push({ name: 'protocolo.lista' })
    } catch {
      $q.notify({ type: 'negative', message: 'Erro ao cancelar protocolo', position: 'top-right' })
    }
  })
}

onMounted(carregarProtocolo)

watch(() => route.params.id, (id) => { if (id) carregarProtocolo() })
</script>

<style lang="scss" scoped>
.protocolo-detalhe-page {
  display: flex;
  flex-direction: column;
}

.protocolo-layout {
  display: flex;
  flex: 1;
  min-height: calc(100vh - 120px);
}

.protocolo-content {
  flex: 1;
  background: #F8F9FA;
  overflow-y: auto;
}

.protocolo-sidebar-container {
  width: 300px;
  flex-shrink: 0;
  overflow-y: auto;
}

.summary-item {
  display: flex;
  align-items: center;
  white-space: nowrap;
}

// Tabs pill
:deep(.oslo-tabs-pill) {
  .q-tabs__content { gap: 8px; }
  .q-tab__indicator { display: none !important; }
}

:deep(.oslo-tab-pill) {
  border-radius: 20px !important;
  border: 1px solid #E8EAED !important;
  background: transparent !important;
  color: #5F6368 !important;
  font-size: 13px !important;
  font-weight: 500 !important;
  padding: 6px 16px !important;
  min-height: 36px !important;
  text-transform: none !important;
  transition: all 0.15s ease !important;

  .q-tab__content { min-width: auto; padding: 0; }
  .q-tab__label   { color: #5F6368 !important; font-size: 13px !important; font-weight: 500 !important; }

  &:hover:not(.q-tab--active) {
    background: #F5F5F5 !important;
    border-color: #D1D5DB !important;
  }

  &.q-tab--active {
    background: #FF7A00 !important;
    border-color: #FF7A00 !important;
    color: #FFFFFF !important;

    .q-tab__label,
    .oslo-tab-pill__content,
    .oslo-tab-pill__content span { color: #FFFFFF !important; }

    .q-badge { background: rgba(255, 255, 255, 0.25) !important; color: #FFFFFF !important; }
  }
}

.oslo-tab-pill__content { display: flex; align-items: center; gap: 4px; }

@media (max-width: 768px) {
  .protocolo-sidebar-container { display: none; }
}
</style>
