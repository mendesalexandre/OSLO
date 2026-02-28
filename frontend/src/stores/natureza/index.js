import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from 'src/boot/axios'

const path = '/v1/naturezas'

export const useNaturezaStore = defineStore('natureza', () => {
  const naturezas = ref([])

  /**
   * Busca naturezas por nome (mínimo 2 caracteres).
   * Retorna o array para uso direto em q-select @filter.
   */
  async function fetchNaturezas(nome = '') {
    const params = nome.length >= 2 ? { nome } : {}
    const response = await api.get(path, { params })
    naturezas.value = response.data.dados
    return response.data.dados
  }

  return { naturezas, fetchNaturezas }
})
