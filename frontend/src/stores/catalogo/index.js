import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import { api } from 'src/boot/axios'

export const useCatalogoStore = defineStore('catalogo', () => {
  const tipos   = ref([])
  const bancos  = ref([])
  const tiposCarregado  = ref(false)
  const bancosCarregado = ref(false)

  async function fetchTipos() {
    if (tiposCarregado.value) return
    const response = await api.get('/v1/tipos-transacao')
    tipos.value = response.data.dados ?? []
    tiposCarregado.value = true
  }

  async function fetchBancos() {
    if (bancosCarregado.value) return
    const response = await api.get('/v1/bancos')
    bancos.value = response.data.dados ?? []
    bancosCarregado.value = true
  }

  const motivosPorTipo = computed(() => {
    const mapa = {}
    for (const tipo of tipos.value) {
      mapa[tipo.tipo] = tipo.motivos_transacao ?? []
    }
    return mapa
  })

  function getMotivos(tipo) {
    return motivosPorTipo.value[tipo] ?? []
  }

  return {
    tipos,
    bancos,
    tiposCarregado,
    bancosCarregado,
    fetchTipos,
    fetchBancos,
    motivosPorTipo,
    getMotivos,
  }
})
