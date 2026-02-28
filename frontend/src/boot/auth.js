import { boot } from "quasar/wrappers";
import { api } from "src/boot/axios";
import { useAuthStore } from "src/stores/auth";
import { useAuxiliaresStore } from "src/stores/auxiliares";

export default boot(async ({ router }) => {
  const authStore = useAuthStore();
  const auxiliaresStore = useAuxiliaresStore();

  // Garante que o X-CSRF-TOKEN esteja sempre presente, inclusive após refresh de página.
  // Necessário pois o token fica apenas em memória no axios e é perdido ao recarregar.
  try {
    const { data: csrf } = await api.get("/v1/csrf-token");
    api.defaults.headers.common["X-CSRF-TOKEN"] = csrf.token;
  } catch {
    // silencioso — backend pode estar offline
  }

  // Tenta carregar o usuário da sessão ativa (cookie HttpOnly)
  try {
    await authStore.buscarMe();
    // Pré-carrega tabelas auxiliares em background se já autenticado
    auxiliaresStore.fetchAuxiliares();
  } catch {
    // Não autenticado — sem ação necessária
  }

  router.beforeEach((to, from, next) => {
    const isPublico = to.meta?.publico === true;

    if (isPublico) {
      if (authStore.autenticado && to.name === "login") {
        return next({ name: "home" });
      }
      return next();
    }

    if (!authStore.autenticado) {
      return next({ name: "login" });
    }

    // Carrega auxiliares se ainda não carregadas (ex: após login)
    if (!auxiliaresStore.carregado) {
      auxiliaresStore.fetchAuxiliares();
    }

    next();
  });
});
