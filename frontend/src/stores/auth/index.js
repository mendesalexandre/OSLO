import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'
import { api, apiUrl } from 'src/boot/axios'

const path = '/v1/auth'

export const useAuthStore = defineStore('auth', () => {
  const usuario = ref(null)
  const autenticado = computed(() => usuario.value !== null)

  async function login(email, senha) {
    // Usa axios direto (sem interceptores) para o fluxo de autenticação Sanctum.
    // Com o proxy do dev server, '/sanctum/csrf-cookie' e '/api/...' são same-origin,
    // então os cookies SameSite=Lax são enviados corretamente.
    await axios.get(`${apiUrl}/sanctum/csrf-cookie`, { withCredentials: true })

    const response = await axios.post(
      `${apiUrl}/api/v1/auth/login`,
      { email, senha },
      {
        withCredentials: true,
        headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
      },
    )
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

  async function refresh() {
    const response = await api.post(`${path}/refresh`)
    usuario.value = response.data.dados
  }

  function limpar() {
    usuario.value = null
  }

  return {
    usuario,
    autenticado,
    login,
    logout,
    buscarMe,
    refresh,
    limpar,
  }
})
