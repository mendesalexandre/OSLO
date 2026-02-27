import { computed } from "vue";
import { useAuthStore } from "src/stores/auth";

export function usePermissao() {
  const authStore = useAuthStore();

  const isAdmin = computed(() => authStore.isAdmin);

  function temPermissao(permissao) {
    if (authStore.isAdmin) return true;
    return authStore.permissoes.includes(permissao);
  }

  function temAlgumaPermissao(permissoes) {
    if (authStore.isAdmin) return true;
    return permissoes.some((p) => authStore.permissoes.includes(p));
  }

  function temTodasPermissoes(permissoes) {
    if (authStore.isAdmin) return true;
    return permissoes.every((p) => authStore.permissoes.includes(p));
  }

  function temAcessoModulo(modulo) {
    if (authStore.isAdmin) return true;
    return Object.keys(authStore.modulos).includes(modulo);
  }

  function filtrarMenu(itens) {
    return itens.filter((item) => {
      // Admin vê tudo
      if (authStore.isAdmin) return true;

      // Sem restrição de permissão
      if (!item.permissao && !item.modulo) return true;

      // Checa permissão específica
      if (item.permissao && !temAlgumaPermissao(Array.isArray(item.permissao) ? item.permissao : [item.permissao])) {
        return false;
      }

      // Checa módulo
      if (item.modulo && !temAcessoModulo(item.modulo)) {
        return false;
      }

      return true;
    }).map((item) => {
      // Filtra filhos recursivamente
      if (item.filhos && item.filhos.length > 0) {
        return { ...item, filhos: filtrarMenu(item.filhos) };
      }
      return item;
    });
  }

  return {
    isAdmin,
    temPermissao,
    temAlgumaPermissao,
    temTodasPermissoes,
    temAcessoModulo,
    filtrarMenu,
  };
}
