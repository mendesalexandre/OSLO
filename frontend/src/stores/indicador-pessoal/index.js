import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from 'src/boot/axios'

const path = '/v1/indicador-pessoal'

export const useIndicadorPessoalStore = defineStore('indicador-pessoal', () => {
  const lista = ref([])
  const paginacao = ref(null)
  const atual = ref(null)
  const versoes = ref([])
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

  async function fetchVersoes(cpfCnpj) {
    const response = await api.get(`${path}/${cpfCnpj}/versoes`)
    versoes.value = response.data.dados
    return versoes.value
  }

  async function criar(dados) {
    const response = await api.post(path, dados)
    return response.data.dados
  }

  async function atualizar(id, dados) {
    const response = await api.put(`${path}/${id}`, dados)
    return response.data.dados
  }

  async function excluir(id) {
    await api.delete(`${path}/${id}`)
  }

  async function buscar(termo) {
    const response = await api.get(`${path}/busca`, { params: { q: termo } })
    return response.data.dados
  }

  async function duplicar(id, motivo) {
    const response = await api.post(`${path}/${id}/duplicar`, { motivo_versao: motivo })
    return response.data.dados
  }

  return {
    lista,
    paginacao,
    atual,
    versoes,
    carregando,
    fetchLista,
    fetchById,
    fetchVersoes,
    criar,
    atualizar,
    excluir,
    buscar,
    duplicar,
  }
})
