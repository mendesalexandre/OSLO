import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { api } from 'src/boot/axios'

const path = '/v1/auth'

export const useAuthStore = defineStore('auth', () => {
  const usuario = ref(null)

  const autenticado = computed(() => usuario.value !== null)
  const permissoes  = computed(() => usuario.value?.permissoes ?? [])
  const grupos      = computed(() => usuario.value?.grupos ?? [])
  const isAdmin     = computed(() => grupos.value.includes('Administrador'))
  const empresa     = computed(() => usuario.value?.empresa ?? null)

  async function login(email, senha) {
    const response = await api.post(`${path}/login`, { email, senha })
    usuario.value = response.data.dados
  }

  async function logout() {
    await api.post(`${path}/logout`)
    usuario.value = null
  }

  async function buscarMe() {
    const response = await api.get(`${path}/me`)
    usuario.value = response.data.dados
  }

  function limpar() {
    usuario.value = null
  }

  return {
    usuario,
    autenticado,
    permissoes,
    grupos,
    isAdmin,
    empresa,
    login,
    logout,
    buscarMe,
    limpar,
  }
})
