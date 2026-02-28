import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { api } from 'boot/axios'

const path = '/v1/meios-pagamento'

export const useMeioPagamentoStore = defineStore('meioPagamento', () => {
  const meios     = ref([])
  const carregado = ref(false)
  const carregando = ref(false)

  const meiosAtivos = computed(() => meios.value.filter((m) => m.is_ativo !== false))

  function porFormaPagamento(formaId) {
    return meiosAtivos.value.filter((m) => m.forma_pagamento_id === formaId)
  }

  async function listar(forcar = false) {
    if (carregado.value && !forcar) return
    carregando.value = true
    try {
      const response = await api.get(path)
      meios.value    = response.data.dados ?? []
      carregado.value = true
    } finally {
      carregando.value = false
    }
  }

  async function criar(dados) {
    const response = await api.post(path, dados)
    await listar(true)
    return response.data.dados
  }

  async function atualizar(id, dados) {
    const response = await api.put(`${path}/${id}`, dados)
    await listar(true)
    return response.data.dados
  }

  async function excluir(id) {
    await api.delete(`${path}/${id}`)
    await listar(true)
  }

  return { meios, meiosAtivos, carregado, carregando, listar, criar, atualizar, excluir, porFormaPagamento }
})
