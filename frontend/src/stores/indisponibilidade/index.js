import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from 'src/boot/axios'

const path = '/v1/indisponibilidades'

export const useIndisponibilidadeStore = defineStore('indisponibilidade', () => {
  const lista = ref([])
  const paginacao = ref(null)
  const atual = ref(null)
  const carregando = ref(false)

  async function fetchLista(params = {}) {
    carregando.value = true
    const response = await api.get(path, { params })
    paginacao.value = response.data.dados
    lista.value = response.data.dados.data ?? []
    carregando.value = false
  }

  async function fetchById(id) {
    const response = await api.get(`${path}/${id}`)
    atual.value = response.data.dados
    return atual.value
  }

  async function fetchPorCpfCnpj(cpfCnpj) {
    const response = await api.get(`${path}/cpf-cnpj/${cpfCnpj}`)
    return response.data.dados
  }

  async function criar(dados) {
    const response = await api.post(path, dados)
    return response.data.dados
  }

  async function atualizar(id, dados) {
    const response = await api.put(`${path}/${id}`, dados)
    return response.data.dados
  }

  async function cancelar(id, dados) {
    const response = await api.post(`${path}/${id}/cancelar`, dados)
    return response.data.dados
  }

  async function excluir(id) {
    await api.delete(`${path}/${id}`)
  }

  return {
    lista,
    paginacao,
    atual,
    carregando,
    fetchLista,
    fetchById,
    fetchPorCpfCnpj,
    criar,
    atualizar,
    cancelar,
    excluir,
  }
})
