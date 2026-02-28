import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from 'src/boot/axios'

const path = '/v1/transacoes'

export const useTransacaoStore = defineStore('transacao', () => {
  const lista      = ref([])
  const paginacao  = ref(null)
  const atual      = ref(null)
  const auditorias = ref([])
  const carregando = ref(false)
  const filtros    = ref({})

  async function fetchLista(params = {}) {
    carregando.value = true
    const response = await api.get(path, { params })
    paginacao.value = response.data.dados
    lista.value = response.data.dados.data ?? []
    carregando.value = false
    return lista.value
  }

  async function fetchById(id) {
    const response = await api.get(`${path}/${id}`)
    atual.value = response.data.dados
    auditorias.value = atual.value?.auditorias ?? []
    return atual.value
  }

  async function criar(dados) {
    const response = await api.post(path, dados)
    return response.data.dados
  }

  async function atualizar(id, dados) {
    const response = await api.put(`${path}/${id}`, dados)
    return response.data.dados
  }

  async function confirmar(id) {
    const response = await api.post(`${path}/${id}/confirmar`)
    return response.data.dados
  }

  async function excluir(id) {
    await api.delete(`${path}/${id}`)
  }

  async function fetchResumo(indicadorId) {
    const response = await api.get(`${path}/resumo`, {
      params: { indicador_pessoal_id: indicadorId },
    })
    return response.data.dados
  }

  return {
    lista,
    paginacao,
    atual,
    auditorias,
    carregando,
    filtros,
    fetchLista,
    fetchById,
    criar,
    atualizar,
    confirmar,
    excluir,
    fetchResumo,
  }
})
