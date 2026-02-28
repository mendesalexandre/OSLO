import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { api } from 'boot/axios'

const path = '/v1/formas-pagamento'

export const useFormaPagamentoStore = defineStore('formaPagamento', () => {
  const formas    = ref([])
  const carregado = ref(false)
  const carregando = ref(false)

  const formasAtivas = computed(() => formas.value.filter((f) => f.is_ativo !== false))

  async function listar(forcar = false) {
    if (carregado.value && !forcar) return
    carregando.value = true
    try {
      const response = await api.get(path)
      formas.value    = response.data.dados ?? []
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

  return { formas, formasAtivas, carregado, carregando, listar, criar, atualizar, excluir }
})
