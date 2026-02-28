import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from 'src/boot/axios'

const path = '/v1/estados'

export const useEstadoStore = defineStore('estado', () => {
  const estados    = ref([])
  const carregado  = ref(false)

  async function fetchEstados() {
    if (carregado.value) return
    const response = await api.get(path)
    estados.value  = response.data.dados
    carregado.value = true
  }

  return { estados, carregado, fetchEstados }
})
