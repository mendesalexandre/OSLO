import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from 'src/boot/axios'

const path = '/v1/permissoes'

export const usePermissaoStore = defineStore('permissao', () => {
  const lista      = ref([])
  const agrupada   = ref([])   // [{ modulo, permissoes }]
  const modulos    = ref([])
  const carregando = ref(false)
  const carregado  = ref(false)

  async function listar(params = {}) {
    carregando.value = true
    try {
      const response = await api.get(path, { params })
      lista.value = response.data.dados ?? []
    } finally {
      carregando.value = false
    }
  }

  async function listarAgrupada() {
    if (carregado.value) return

    carregando.value = true
    try {
      const response = await api.get(path, { params: { agrupado: 1 } })
      agrupada.value = response.data.dados ?? []
      carregado.value = true
    } finally {
      carregando.value = false
    }
  }

  return {
    lista, agrupada, modulos, carregando, carregado,
    listar, listarAgrupada,
  }
})
