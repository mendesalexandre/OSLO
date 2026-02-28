import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from 'src/boot/axios'

export const useDashboardStore = defineStore('dashboard', () => {
  const resumoFinanceiro = ref({
    total_entrada:        0,
    total_saida:          0,
    saldo:                0,
    transacoes_pendentes: 0,
  })

  const eventosTimeline   = ref([])
  const eventosCalendario = ref([])

  const indicadores = ref({
    transacoes_mes:           0,
    indicadores_ativos:       0,
    indisponibilidades_ativas: 0,
    depositos_judiciais:      0,
  })

  const graficoAtividade = ref([])
  const carregando       = ref(false)

  async function fetchDashboard() {
    carregando.value = true
    try {
      const response = await api.get('/v1/dashboard')
      const data = response.data.dados

      resumoFinanceiro.value  = data.resumo_financeiro
      eventosTimeline.value   = data.eventos_timeline
      eventosCalendario.value = data.eventos_calendario
      indicadores.value       = data.indicadores
      graficoAtividade.value  = data.grafico_atividade
    } finally {
      carregando.value = false
    }
  }

  async function fetchEventosPorMes(ano, mes) {
    try {
      const response = await api.get('/v1/dashboard/eventos', { params: { ano, mes } })
      eventosCalendario.value = response.data.dados.eventos ?? []
      return eventosCalendario.value
    } catch {
      return []
    }
  }

  return {
    resumoFinanceiro,
    eventosTimeline,
    eventosCalendario,
    indicadores,
    graficoAtividade,
    carregando,
    fetchDashboard,
    fetchEventosPorMes,
  }
})
