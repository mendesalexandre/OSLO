import { defineStore } from 'pinia'
import { reactive, ref } from 'vue'
import { api } from 'src/boot/axios'

const tabelas = [
  'estado-civil',
  'regime-bem',
  'nacionalidade',
  'capacidade-civil',
  'profissao',
  'tipo-empresa',
  'porte-empresa',
]

export const useAuxiliaresStore = defineStore('auxiliares', () => {
  const dados = reactive(Object.fromEntries(tabelas.map((t) => [t, []])))
  const carregado = ref(false)
  const carregando = ref(false)

  async function fetchAuxiliares() {
    if (carregado.value || carregando.value) return

    carregando.value = true

    await Promise.all(
      tabelas.map(async (tabela) => {
        const response = await api.get(`/v1/auxiliares/${tabela}`)
        dados[tabela] = response.data.dados ?? []
      })
    )

    carregado.value = true
    carregando.value = false
  }

  function obter(tabela) {
    return dados[tabela] ?? []
  }

  return {
    dados,
    carregado,
    carregando,
    fetchAuxiliares,
    obter,
  }
})
