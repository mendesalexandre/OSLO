import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from 'src/boot/axios'

const path = '/v1/grupos'

export const useGrupoStore = defineStore('grupo', () => {
  const lista      = ref([])
  const carregando = ref(false)

  async function listar(filtros = {}) {
    carregando.value = true
    try {
      const response = await api.get(path, { params: filtros })
      lista.value = response.data.dados ?? []
    } finally {
      carregando.value = false
    }
  }

  async function buscarPorId(id) {
    const response = await api.get(`${path}/${id}`)
    return response.data.dados
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

  async function sincronizarPermissoes(id, permissaoIds) {
    const response = await api.post(`${path}/${id}/permissoes`, {
      permissao_ids: permissaoIds,
    })
    return response.data.dados
  }

  return {
    lista, carregando,
    listar, buscarPorId, criar, atualizar, excluir, sincronizarPermissoes,
  }
})
