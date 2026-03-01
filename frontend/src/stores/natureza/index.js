import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from 'src/boot/axios'

const path = '/v1/naturezas'

export const useNaturezaStore = defineStore('natureza', () => {
  // lista para autocomplete (apenas ativos, filtrado por nome)
  const naturezas = ref([])

  // lista para gestão CRUD (todos, inclui inativos)
  const lista      = ref([])
  const carregando = ref(false)

  /** Busca naturezas por nome — usado no autocomplete (CriarProtocolo etc.) */
  async function fetchNaturezas(nome = '') {
    const params = nome.length >= 2 ? { nome } : {}
    const response = await api.get(path, { params })
    naturezas.value = response.data.dados
    return response.data.dados
  }

  /** Carrega todas as naturezas para a página de gestão */
  async function listar(filtros = {}) {
    carregando.value = true
    try {
      const response = await api.get(path, { params: { admin: 1, ...filtros } })
      lista.value = response.data.dados ?? []
    } finally {
      carregando.value = false
    }
  }

  async function criar(dados) {
    const response = await api.post(path, dados)
    await listar()
    return response.data.dados
  }

  async function atualizar(id, dados) {
    const response = await api.put(`${path}/${id}`, dados)
    await listar()
    return response.data.dados
  }

  async function excluir(id) {
    await api.delete(`${path}/${id}`)
    await listar()
  }

  return { naturezas, lista, carregando, fetchNaturezas, listar, criar, atualizar, excluir }
})
