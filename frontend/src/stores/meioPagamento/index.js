import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { api } from 'boot/axios'

export const useMeioPagamentoStore = defineStore('meioPagamento', () => {
  const meios     = ref([])
  const carregado = ref(false)

  const meiosAtivos = computed(() => meios.value.filter((m) => m.is_ativo !== false))

  function porFormaPagamento(formaId) {
    return meiosAtivos.value.filter((m) => m.forma_pagamento_id === formaId)
  }

  async function listar() {
    if (carregado.value) return
    const response = await api.get('/v1/meios-pagamento')
    meios.value    = response.data.dados ?? []
    carregado.value = true
  }

  return { meios, meiosAtivos, carregado, listar, porFormaPagamento }
})
