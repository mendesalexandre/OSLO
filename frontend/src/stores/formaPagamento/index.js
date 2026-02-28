import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { api } from 'boot/axios'

export const useFormaPagamentoStore = defineStore('formaPagamento', () => {
  const formas    = ref([])
  const carregado = ref(false)

  const formasAtivas = computed(() => formas.value.filter((f) => f.is_ativo !== false))

  async function listar() {
    if (carregado.value) return
    const response = await api.get('/v1/formas-pagamento')
    formas.value   = response.data.dados ?? []
    carregado.value = true
  }

  return { formas, formasAtivas, carregado, listar }
})
