import { boot } from 'quasar/wrappers'
import axios from 'axios'
import { Notify } from 'quasar'

// API_URL deve ser a URL absoluta do backend incluindo /api
// Ex: http://localhost:8000/api (dev) | https://api.sistemaoslo.com.br/api (prod)
const api = axios.create({
  baseURL: process.env.API_URL || '',
  withCredentials: true,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
})

export default boot(({ app, router }) => {
  api.interceptors.response.use(
    (response) => response,
    async (error) => {
      const status = error.response?.status
      const url = error.config?.url ?? ''

      if (status === 401 && !url.includes('/auth/login')) {
        const { useAuthStore } = await import('src/stores/auth')
        const authStore = useAuthStore()
        authStore.limpar()

        if (router.currentRoute.value.name !== 'login') {
          router.push({ name: 'login' })
        }
      }

      if (status === 422) {
        const mensagem = error.response?.data?.mensagem ?? 'Erro de validação'
        Notify.create({ type: 'negative', message: mensagem, position: 'top' })
      }

      return Promise.reject(error)
    },
  )

  app.config.globalProperties.$api = api
})

export { api }
