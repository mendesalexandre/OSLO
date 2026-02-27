import { useAuthStore } from "src/stores/auth";

function verificarPermissao(el, binding) {
  const authStore = useAuthStore();

  // Admin sempre vê
  if (authStore.isAdmin) {
    el.style.display = "";
    return;
  }

  const valor = binding.value;
  let temAcesso = false;

  if (Array.isArray(valor)) {
    temAcesso = valor.some((p) => authStore.permissoes.includes(p));
  } else if (typeof valor === "string") {
    temAcesso = authStore.permissoes.includes(valor);
  }

  if (!temAcesso) {
    el.style.display = "none";
  } else {
    el.style.display = "";
  }
}

export const vPermissao = {
  mounted: verificarPermissao,
  updated: verificarPermissao,
};
