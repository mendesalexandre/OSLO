import { boot } from 'quasar/wrappers'
import { useAuthStore } from 'src/stores/auth'
import { useAuxiliaresStore } from 'src/stores/auxiliares'

export default boot(async ({ router }) => {
  const authStore = useAuthStore()
  const auxiliaresStore = useAuxiliaresStore()

  // Tenta carregar o usuário da sessão ativa (cookie HttpOnly)
  try {
    await authStore.buscarMe()
    // Pré-carrega tabelas auxiliares em background se já autenticado
    auxiliaresStore.fetchAuxiliares()
  } catch {
    // Não autenticado — sem ação necessária
  }

  router.beforeEach((to, from, next) => {
    const isPublico = to.meta?.publico === true

    if (isPublico) {
      if (authStore.autenticado && to.name === 'login') {
        return next({ name: 'home' })
      }
      return next()
    }

    if (!authStore.autenticado) {
      return next({ name: 'login' })
    }

    // Carrega auxiliares se ainda não carregadas (ex: após login)
    if (!auxiliaresStore.carregado) {
      auxiliaresStore.fetchAuxiliares()
    }

    next()
  })
})
