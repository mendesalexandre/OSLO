<template>
  <q-page class="dashboard-minimalista q-pa-md">
    <q-toggle v-model="dashboardStore.mostrarCalendario" label="Mostrar Calendário" />
    <!-- Resumo Financeiro -->
    <div class="resumo-section q-mb-lg">
      <h2 class="text-h5 text-weight-bold q-ma-none q-mb-md">Resumo Financeiro</h2>

      <div class="row q-col-gutter-md">
        <div class="col-12 col-sm-6 col-md-3">
          <resumo-card titulo="Saldo" :valor="dashboardStore.resumoFinanceiro.saldo" tipo="saldo" />
        </div>
        <div class="col-12 col-sm-6 col-md-3">
          <resumo-card titulo="Entradas" :valor="dashboardStore.resumoFinanceiro.total_entrada" tipo="entrada" />
        </div>
        <div class="col-12 col-sm-6 col-md-3">
          <resumo-card titulo="Saídas" :valor="dashboardStore.resumoFinanceiro.total_saida" tipo="saida" />
        </div>
        <div class="col-12 col-sm-6 col-md-3">
          <resumo-card titulo="Pendentes" :valor="dashboardStore.resumoFinanceiro.transacoes_pendentes" tipo="pendente"
            :eh-numero="true" />
        </div>
      </div>
    </div>

    <!-- Ações Rápidas -->
    <div class="shortcuts-section q-mb-lg">
      <h2 class="text-h5 text-weight-bold q-ma-none q-mb-md">Ações Rápidas</h2>

      <div class="row q-col-gutter-md">
        <div class="col-12 col-sm-6 col-md-3">
          <shortcut-button icone="add" titulo="Nova Transação" cor="primary" @click="irPara('/transacoes')" />
        </div>
        <div class="col-12 col-sm-6 col-md-3">
          <shortcut-button icone="person_add" titulo="Novo Indicador" cor="info"
            @click="irPara('/indicador-pessoal')" />
        </div>
        <div class="col-12 col-sm-6 col-md-3">
          <shortcut-button icone="list" titulo="Ver Transações" cor="warning" @click="irPara('/transacoes')" />
        </div>
        <div class="col-12 col-sm-6 col-md-3">
          <shortcut-button icone="search" titulo="Consultar" cor="secondary" @click="irPara('/consulta')" />
        </div>
      </div>
    </div>

    <!-- Conteúdo Principal -->
    <div class="row q-col-gutter-lg">
      <!-- Calendário + Atividade -->
      <div class="col-12 col-lg-6">
        <div class="q-gutter-md">
          <calendario-minimalista :eventos="dashboardStore.eventosCalendario" @change="aoMudarMes" />
          <grafico-atividade :dados="dashboardStore.graficoAtividade" />
        </div>
      </div>

      <!-- Indicadores + Timeline -->
      <div class="col-12 col-lg-6">
        <div class="q-gutter-md">
          <indicadores-minimalista :indicadores="dashboardStore.indicadores" />
          <timeline-eventos :eventos="dashboardStore.eventosTimeline" />
        </div>
      </div>
    </div>

  </q-page>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useDashboardStore } from 'src/stores/dashboard'
import ResumoCard from 'src/components/dashboard/ResumoCard.vue'
import ShortcutButton from 'src/components/dashboard/ShortcutButton.vue'
import CalendarioMinimalista from 'src/components/dashboard/CalendarioMinimalista.vue'
import GraficoAtividade from 'src/components/dashboard/GraficoAtividade.vue'
import IndicadoresMinimalista from 'src/components/dashboard/IndicadoresMinimalista.vue'
import TimelineEventos from 'src/components/dashboard/TimelineEventos.vue'

const router = useRouter()
const dashboardStore = useDashboardStore()

onMounted(() => dashboardStore.fetchDashboard())

function irPara(rota) {
  router.push(rota)
}

async function aoMudarMes({ ano, mes }) {
  await dashboardStore.fetchEventosPorMes(ano, mes)
}
</script>

<style scoped lang="scss">
.dashboard-minimalista {
  background: #fafafa;
  max-width: 1600px;
  margin: 0 auto;

  h2 {
    color: #1a1a1a;
    letter-spacing: -0.5px;
  }

  .resumo-section,
  .shortcuts-section {
    animation: fadeInUp 0.3s ease-out;
  }
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>