import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { api } from 'boot/axios'

const path = '/v1/protocolo'

export const useProtocoloStore = defineStore('protocolo', () => {
  const protocolos  = ref([])
  const protocolo   = ref(null)
  const carregando  = ref(false)
  const paginacao   = ref({ page: 1, rowsPerPage: 15, rowsNumber: 0 })
  const filtros     = ref({
    numero:      '',
    solicitante: '',
    status:      '',
    data_inicio: '',
    data_fim:    '',
  })

  // Getters derivados do protocolo atual
  const valorTotal    = computed(() => parseFloat(protocolo.value?.valor_total  ?? 0))
  const valorDesconto = computed(() => parseFloat(protocolo.value?.valor_desconto ?? 0))
  const valorIsento   = computed(() => parseFloat(protocolo.value?.valor_isento ?? 0))
  const valorFinal    = computed(() => parseFloat(protocolo.value?.valor_final  ?? 0))
  const valorPago     = computed(() => parseFloat(protocolo.value?.valor_pago   ?? 0))
  const valorRestante = computed(() => parseFloat(protocolo.value?.valor_restante ?? 0))

  const itens      = computed(() => protocolo.value?.itens      ?? [])
  const pagamentos = computed(() => protocolo.value?.pagamentos ?? [])
  const isencoes   = computed(() => protocolo.value?.isencoes   ?? [])
  const andamentos = computed(() => protocolo.value?.andamentos ?? [])

  const estaPago       = computed(() => protocolo.value?.status === 'pago')
  const estaPagoParcial = computed(() => protocolo.value?.status === 'pago_parcial')
  const estaCancelado  = computed(() => protocolo.value?.status === 'cancelado')

  // Actions
  async function listar(params = {}) {
    carregando.value = true
    try {
      const response = await api.get(path, { params })
      const dados    = response.data.dados
      protocolos.value = dados.data
      paginacao.value  = {
        page:        dados.current_page,
        rowsPerPage: dados.per_page,
        rowsNumber:  dados.total,
      }
      return dados
    } finally {
      carregando.value = false
    }
  }

  async function carregar(id) {
    carregando.value = true
    try {
      const response   = await api.get(`${path}/${id}`)
      protocolo.value  = response.data.dados
      return response.data.dados
    } finally {
      carregando.value = false
    }
  }

  async function criar(dados) {
    const response  = await api.post(path, dados)
    const criado    = response.data.dados
    protocolos.value.unshift(criado)
    return criado
  }

  async function atualizar(id, dados) {
    const response  = await api.put(`${path}/${id}`, dados)
    protocolo.value = response.data.dados
    return response.data.dados
  }

  async function cancelar(id, motivo) {
    const response = await api.post(`${path}/${id}/cancelar`, { motivo })
    if (protocolo.value?.id === parseInt(id)) {
      protocolo.value.status = 'cancelado'
    }
    return response.data
  }

  async function recalcular(id) {
    const response  = await api.post(`${path}/${id}/recalcular`)
    protocolo.value = response.data.dados
    return response.data.dados
  }

  async function adicionarItem(id, dados) {
    const response = await api.post(`${path}/${id}/item`, dados)
    if (protocolo.value) await carregar(id)
    return response.data.dados
  }

  async function removerItem(protocoloId, itemId) {
    await api.delete(`${path}/${protocoloId}/item/${itemId}`)
    if (protocolo.value) await carregar(protocoloId)
  }

  async function registrarPagamento(id, dados) {
    const response = await api.post(`${path}/${id}/pagamento`, dados)
    if (protocolo.value) await carregar(id)
    return response.data.dados
  }

  async function estornarPagamento(protocoloId, pagId, motivo) {
    await api.post(`${path}/${protocoloId}/pagamento/${pagId}/estornar`, { motivo })
    if (protocolo.value) await carregar(protocoloId)
  }

  async function excluirPagamento(protocoloId, pagId) {
    await api.delete(`${path}/${protocoloId}/pagamento/${pagId}`)
    if (protocolo.value) await carregar(protocoloId)
  }

  async function registrarIsencao(id, dados) {
    const response = await api.post(`${path}/${id}/isencao`, dados)
    if (protocolo.value) await carregar(id)
    return response.data.dados
  }

  function setFiltro(campo, valor) {
    filtros.value[campo] = valor
  }

  function limparFiltros() {
    filtros.value = { numero: '', solicitante: '', status: '', data_inicio: '', data_fim: '' }
  }

  function limparProtocolo() {
    protocolo.value = null
  }

  return {
    // State
    protocolos, protocolo, carregando, paginacao, filtros,
    // Getters
    valorTotal, valorDesconto, valorIsento, valorFinal, valorPago, valorRestante,
    itens, pagamentos, isencoes, andamentos,
    estaPago, estaPagoParcial, estaCancelado,
    // Actions
    listar, carregar, criar, atualizar, cancelar, recalcular,
    adicionarItem, removerItem,
    registrarPagamento, estornarPagamento, excluirPagamento,
    registrarIsencao,
    setFiltro, limparFiltros, limparProtocolo,
  }
})
