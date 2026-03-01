import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from 'src/boot/axios'

const path = '/v1/usuarios-permissoes'

export const useUsuarioPermissaoStore = defineStore('usuario-permissao', () => {
  const lista      = ref([])
  const atual      = ref(null)
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
    carregando.value = true
    try {
      const response = await api.get(`${path}/${id}`)
      atual.value = response.data.dados
      return response.data.dados
    } finally {
      carregando.value = false
    }
  }

  async function sincronizarGrupos(id, grupoIds) {
    const response = await api.post(`${path}/${id}/grupos`, {
      grupo_ids: grupoIds,
    })
    return response.data.dados
  }

  async function definirPermissao(id, permissaoId, tipo) {
    const response = await api.post(`${path}/${id}/permissao`, {
      permissao_id: permissaoId,
      tipo,
    })
    return response.data.dados
  }

  async function buscarEfetivas(id) {
    const response = await api.get(`${path}/${id}/efetivas`)
    return response.data.dados
  }

  return {
    lista, atual, carregando,
    listar, buscarPorId, sincronizarGrupos, definirPermissao, buscarEfetivas,
  }
})
