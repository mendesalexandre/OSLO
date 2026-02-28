import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from 'boot/axios'

const path = '/v1/categorias'

export const useCategoriaStore = defineStore('categoria', () => {
  const categorias  = ref([])
  const carregando  = ref(false)

  async function listar() {
    carregando.value = true
    try {
      const response  = await api.get(path)
      categorias.value = response.data.dados ?? []
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

  return { categorias, carregando, listar, criar, atualizar, excluir }
})
