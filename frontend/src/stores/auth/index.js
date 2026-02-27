import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { api } from 'src/boot/axios'

const path = '/v1/auth'

export const useAuthStore = defineStore('auth', () => {
  const usuario = ref(null)
  const autenticado = computed(() => usuario.value !== null)

  async function login(email, senha) {
    // Inicia a sessão e obtém o token CSRF no corpo da resposta.
    // Necessário para cross-origin (localhost:9000 → localhost:8000): document.cookie
    // não consegue ler cookies de portas diferentes no Chrome, então usamos
    // X-CSRF-TOKEN (plaintext) em vez de X-XSRF-TOKEN (baseado em cookie).
    const { data: csrf } = await api.get('/v1/csrf-token')
    api.defaults.headers.common['X-CSRF-TOKEN'] = csrf.token

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
    login,
    logout,
    buscarMe,
    limpar,
  }
})
