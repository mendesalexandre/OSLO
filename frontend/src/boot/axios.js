import { boot } from 'quasar/wrappers'
import axios from 'axios'
import { Notify } from 'quasar'

// URL base do backend sem o prefixo /api
// Se API_URL é relativa (ex: '/api'), apiUrl fica '' (same-origin via proxy)
// Se API_URL é absoluta (ex: 'http://localhost:8000/api'), extrai a origem
const rawApiUrl = process.env.API_URL || '/api'
export const apiUrl = rawApiUrl.startsWith('http') ? rawApiUrl.replace(/\/api\/?$/, '') : ''

const api = axios.create({
  baseURL: process.env.API_URL,   // ex: 'http://localhost:8000/api'
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
    }
  )

  app.config.globalProperties.$api = api
})

export { api }
