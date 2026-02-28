<template>
  <q-layout view="lHh Lpr lFf">
    <!-- Header -->
    <!-- <q-header class="oslo-header">
      <q-toolbar class="oslo-toolbar">
        <q-btn
          flat
          round
          dense
          icon="menu"
          color="grey-7"
          aria-label="Menu"
          @click="toggleMini"
        />

        <q-breadcrumbs class="oslo-breadcrumb q-ml-md" active-color="grey-8" separator-color="grey-5">
          <q-breadcrumbs-el label="OSLO" icon="fa-duotone fa-building-columns" :to="{ name: 'tarefas' }" />
          <q-breadcrumbs-el v-if="currentPageTitle" :label="currentPageTitle" />
        </q-breadcrumbs>

        <q-space />

        <q-btn flat no-caps dense class="oslo-user-btn">
          <div class="oslo-user-avatar">
            {{ userInitial }}
          </div>
          <span class="oslo-user-name gt-xs">{{ user?.nome || 'Usuário' }}</span>
          <q-icon name="expand_more" size="18px" class="gt-xs" />

          <q-menu anchor="bottom right" self="top right" class="oslo-user-menu">
            <q-list style="min-width: 200px">
              <q-item-label header class="text-weight-medium">
                {{ user?.nome || 'Usuário' }}
                <div class="text-caption text-grey-6">{{ user?.email || '' }}</div>
              </q-item-label>
              <q-separator />
              <q-item clickable v-close-popup @click="verPerfil">
                <q-item-section avatar><q-icon name="fa-duotone fa-user" size="16px" color="grey-7" /></q-item-section>
                <q-item-section>Meu Perfil</q-item-section>
              </q-item>
              <q-item clickable v-close-popup v-permissao="'CONFIGURACAO_LISTAR'" @click="router.push({ name: 'configuracao.index' })">
                <q-item-section avatar><q-icon name="fa-duotone fa-gear" size="16px" color="grey-7" /></q-item-section>
                <q-item-section>Configurações</q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="alterarSenha">
                <q-item-section avatar><q-icon name="fa-duotone fa-lock" size="16px" color="grey-7" /></q-item-section>
                <q-item-section>Alterar Senha</q-item-section>
              </q-item>
              <q-separator />
              <q-item clickable v-close-popup @click="logout">
                <q-item-section avatar><q-icon name="fa-duotone fa-arrow-right-from-bracket" size="16px" color="negative" /></q-item-section>
                <q-item-section class="text-negative">Sair</q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>
      </q-toolbar>
    </q-header> -->

    <!-- Sidebar -->
    <q-drawer v-model="drawerOpen" show-if-above side="left" :mini="miniState" :width="240" :mini-width="60"
      :breakpoint="768" class="oslo-sidebar" :class="{ 'oslo-sidebar--mini': miniState }" no-swipe-open no-swipe-close>
      <!-- Logo -->
      <div class="oslo-sidebar__logo">
        <div class="oslo-sidebar__logo-brand" @click="router.push({ name: 'tarefas' })">
          <l-icon name="landmark" :size="22" />
          <transition name="fade">
            <span v-show="!miniState" class="oslo-sidebar__logo-text">OSLO</span>
          </transition>
        </div>
        <q-btn v-show="!miniState" flat round dense size="sm" color="grey-6" class="oslo-sidebar__toggle"
          @click="toggleMini">
          <l-icon name="panel-left-close" :size="16" />
        </q-btn>
        <q-btn v-show="miniState" flat round dense size="sm" color="grey-6" class="oslo-sidebar__toggle"
          @click="toggleMini">
          <l-icon name="panel-left" :size="16" />
        </q-btn>
      </div>

      <q-separator class="oslo-sidebar__separator" />

      <!-- Informações da Empresa -->
      <div v-if="!miniState && empresa" class="oslo-sidebar__empresa q-px-md q-py-sm">
        <div class="text-caption text-grey-6">Empresa</div>
        <div class="text-body2 text-weight-medium text-truncate">
          {{ empresa.nome_fantasia || empresa.razao_social }}
        </div>
        <div v-if="empresa.is_trial_ativo" class="text-caption text-orange">
          {{ empresa.dias_restantes_trial }} dias restantes
        </div>
        <div v-else-if="empresa.is_read_only" class="text-caption text-negative">
          Trial expirado
        </div>
      </div>

      <q-separator v-if="!miniState && empresa" class="oslo-sidebar__separator" />

      <!-- Menu Items -->
      <q-scroll-area class="oslo-sidebar__scroll">
        <q-list padding>
          <template v-for="(item, index) in menuFiltrado">
            <div v-if="item.separador" :key="'s-' + index" class="oslo-sidebar__section">
              <span v-show="!miniState">{{ item.label }}</span>
              <q-separator v-show="miniState" class="oslo-sidebar__section-line" />
            </div>

            <q-item v-else :key="'i-' + index" clickable v-ripple class="oslo-sidebar__item"
              :class="{ 'oslo-sidebar__item--active': isActiveRoute(item) }" @click="handleMenuClick(item)">
              <q-item-section avatar class="oslo-sidebar__icon">
                <l-icon :name="item.icone" :size="16" />
              </q-item-section>
              <q-item-section v-show="!miniState" class="oslo-sidebar__label">
                {{ item.label || item.tooltip }}
              </q-item-section>

              <q-tooltip v-if="miniState" anchor="center right" self="center left" :offset="[10, 0]"
                class="oslo-tooltip">
                {{ item.tooltip }}
              </q-tooltip>
            </q-item>
          </template>
        </q-list>
      </q-scroll-area>

      <!-- Footer da sidebar -->
      <div class="oslo-sidebar__footer">
        <q-separator class="oslo-sidebar__separator" />

        <!-- Admin link -->
        <q-item clickable v-ripple class="oslo-sidebar__item"
          :class="{ 'oslo-sidebar__item--active': $route.path.startsWith('/administracao') }"
          @click="router.push({ name: 'administracao.index' })">
          <q-item-section avatar class="oslo-sidebar__icon">
            <l-icon name="settings-2" :size="16" />
          </q-item-section>
          <q-item-section v-show="!miniState" class="oslo-sidebar__label">
            Administração
          </q-item-section>
          <q-tooltip v-if="miniState" anchor="center right" self="center left" :offset="[10, 0]" class="oslo-tooltip">
            Administração
          </q-tooltip>
        </q-item>

        <!-- Logout -->
        <q-item clickable v-ripple class="oslo-sidebar__item oslo-sidebar__item--logout" @click="logout">
          <q-item-section avatar class="oslo-sidebar__icon">
            <l-icon name="log-out" :size="16" />
          </q-item-section>
          <q-item-section v-show="!miniState" class="oslo-sidebar__label">
            Sair
          </q-item-section>
          <q-tooltip v-if="miniState" anchor="center right" self="center left" :offset="[10, 0]" class="oslo-tooltip">
            Sair
          </q-tooltip>
        </q-item>
      </div>
    </q-drawer>

    <!-- Content -->
    <q-page-container>
      <!-- Banner de Trial -->
      <trial-banner />

      <router-view />
    </q-page-container>

    <!-- Modais globais -->
    <criar-protocolo v-model="modalCriarProtocolo" />
    <menu-navegacao v-model="mostrarMenuNavegacao" />
  </q-layout>
</template>

<script setup>
import { ref, computed } from "vue";
import { useQuasar } from "quasar";
import { useRouter, useRoute } from "vue-router";
import CriarProtocolo from "src/components/CriarProtocolo.vue";
import MenuNavegacao from "src/components/Menu.vue";
import TrialBanner from "src/components/TrialBanner.vue";
import { useAuthStore } from "src/stores/auth";
import { useEstadoStore } from "src/stores/estado";
import { usePermissao } from "src/composables/usePermissao";
import { menuItens } from "src/config/menu";

defineOptions({ name: "MainLayout" });

const $q     = useQuasar();
const router = useRouter();
const route  = useRoute();

const authStore   = useAuthStore();
const estadoStore = useEstadoStore();
const { filtrarMenu } = usePermissao();

const usuario  = computed(() => authStore.usuario);
const empresa  = computed(() => authStore.empresa);
const userInitial = computed(() => {
  const nome = usuario.value?.nome || "U";
  return nome.charAt(0).toUpperCase();
});

const currentPageTitle = computed(() => route.meta?.title || "");
const menuFiltrado = computed(() => filtrarMenu(menuItens));

// Sidebar state
const drawerOpen = ref(true);
const miniState  = ref(false);

const toggleMini = () => {
  if ($q.screen.lt.md) {
    drawerOpen.value = !drawerOpen.value;
  } else {
    miniState.value = !miniState.value;
  }
};

const isActiveRoute = (item) => {
  if (!item.rota) return false;
  try {
    const resolved = router.resolve(item.rota);
    return route.path === resolved.path || route.path.startsWith(resolved.path + "/");
  } catch {
    return false;
  }
};

// Modais globais
const modalCriarProtocolo = ref(false);
const mostrarMenuNavegacao = ref(false);

const handleMenuClick = async (item) => {
  if (item.separador) return;

  if (item.acao === "criarProtocolo") {
    try {
      await estadoStore.fetchEstados();
      modalCriarProtocolo.value = true;
    } catch {
      $q.notify({ type: "negative", message: "Erro ao carregar estados.", position: "top" });
    }
  } else if (item.acao === "menuNavegacao") {
    mostrarMenuNavegacao.value = !mostrarMenuNavegacao.value;
  } else if (item.rota) {
    router.push(item.rota);
  }
};

// User actions
const logout = async () => {
  await authStore.logout();
  router.push({ name: "login" });
};

const verPerfil = () => {
  $q.notify({ type: "info", message: "Funcionalidade em desenvolvimento.", position: "top" });
};

const alterarSenha = () => {
  $q.notify({ type: "info", message: "Funcionalidade em desenvolvimento.", position: "top" });
};
</script>

<style lang="scss" scoped>
// --- Header ---
.oslo-header {
  background: #FFFFFF;
  border-bottom: 1px solid var(--border-color);
  box-shadow: none !important;
}

.oslo-toolbar {
  min-height: 56px;
  padding: 0 16px;
}

.oslo-breadcrumb {
  font-size: var(--font-size-sm);

  :deep(.q-breadcrumbs__el) {
    color: var(--text-secondary);
    font-weight: 500;

    &:last-child {
      color: var(--text-color);
    }
  }

  :deep(.q-breadcrumbs__separator) {
    color: var(--text-muted);
  }
}

// --- User Button ---
.oslo-user-btn {
  border-radius: var(--radius-sm);
  padding: 4px 8px;

  &:hover {
    background: var(--bg-subtle);
  }
}

.oslo-user-avatar {
  width: 32px;
  height: 32px;
  border-radius: var(--radius-full);
  background: var(--primary);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: var(--font-size-sm);
  margin-right: 8px;
}

.oslo-user-name {
  font-size: var(--font-size-sm);
  font-weight: 500;
  color: var(--text-color);
  margin-right: 4px;
}

.oslo-user-menu {
  margin-top: 8px;
}

// --- Sidebar (White + Graphite + Orange active) ---
.oslo-sidebar {
  background: var(--sidebar-bg) !important;
  border-right: 1px solid var(--sidebar-border) !important;

  :deep(.q-drawer) {
    background: var(--sidebar-bg);
  }

  :deep(.q-mini-drawer-hide) {
    transition: opacity 150ms ease;
  }
}

.oslo-sidebar__logo {
  height: 56px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 8px 0 12px;
  color: var(--text-color);
}

.oslo-sidebar__logo-brand {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  font-weight: 700;
  font-size: 1.125rem;
  letter-spacing: 0.05em;
  flex: 1;
  min-width: 0;
  transition: opacity var(--transition);

  .q-icon {
    color: var(--primary);
    flex-shrink: 0;
  }

  &:hover {
    opacity: 0.85;
  }
}

.oslo-sidebar__toggle {
  flex-shrink: 0;
}

.oslo-sidebar__logo-text {
  white-space: nowrap;
  overflow: hidden;
}

.oslo-sidebar__separator {
  margin: 0 12px;
  background: var(--border-color);
}

.oslo-sidebar__scroll {
  height: calc(100% - 56px - 1px - 100px);
}

// Section separators
.oslo-sidebar__section {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 20px 16px 6px;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--text-muted);
  white-space: nowrap;
  overflow: hidden;

  &::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border-color);
    min-width: 8px;
  }
}

.oslo-sidebar__section-line {
  margin: 12px 8px;
  background: var(--border-color);
}

// Menu items
.oslo-sidebar__item {
  min-height: 40px;
  border-radius: var(--radius-md);
  margin: 2px 8px;
  padding: 0 12px;
  color: var(--sidebar-text);
  transition: all var(--transition);
  position: relative;

  :deep(.q-item__section--avatar) {
    min-width: unset;
  }

  &:hover {
    background: var(--sidebar-hover);
    color: var(--sidebar-text);
  }

  &--active {
    background: var(--sidebar-active-bg);
    color: var(--sidebar-active);

    .oslo-sidebar__icon {
      color: var(--sidebar-active);
    }

    .oslo-sidebar__label {
      font-weight: 500;
    }
  }

  &--logout:hover {
    color: #DC2626;
  }
}

.oslo-sidebar__icon {
  min-width: 36px !important;
  justify-content: center;
  color: inherit;
}

.oslo-sidebar__label {
  font-size: 14px;
  font-weight: 400;
  white-space: nowrap;
  overflow: hidden;
}

// Mini mode: centralizar ícones
.oslo-sidebar--mini {
  .oslo-sidebar__logo {
    justify-content: center;
    padding: 0;
  }

  .oslo-sidebar__item {
    margin: 2px 4px;
    padding: 0 !important;
    justify-content: center;

    // Forçar centralização do q-item-section
    :deep(.q-item__section) {
      min-width: 0 !important;
      padding: 0 !important;
      margin: 0 auto !important;
    }

    .oslo-sidebar__icon {
      min-width: 0 !important;
      padding: 0 !important;
      margin: 0 auto !important;
      display: flex;
      justify-content: center;
      align-items: center;
      width: 100%;
    }

    // Esconder labels de texto
    .oslo-sidebar__label {
      display: none;
    }
  }

  .oslo-sidebar__section {
    padding: 8px 0;

    // Esconder texto da seção
    span {
      display: none;
    }
  }

  .oslo-sidebar__section-line {
    margin: 8px 8px;
  }

  .oslo-sidebar__separator {
    margin: 0 4px;
  }

  .oslo-sidebar__footer {
    .oslo-sidebar__item {
      margin: 2px 4px;
      padding: 0 !important;
      justify-content: center;

      :deep(.q-item__section) {
        min-width: 0 !important;
        padding: 0 !important;
        margin: 0 auto !important;
      }

      .oslo-sidebar__icon {
        min-width: 0 !important;
        padding: 0 !important;
        margin: 0 auto !important;
      }
    }
  }
}

// Empresa section
.oslo-sidebar__empresa {
  background: rgba(0, 0, 0, 0.03);
  border-radius: 4px;
  margin: 8px;
}

// Footer
.oslo-sidebar__footer {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding-bottom: 8px;
}

// Tooltip
.oslo-tooltip {
  font-size: var(--font-size-xs);
  background: var(--text-color);
  padding: 4px 10px;
}

// Fade transition
.fade-enter-active,
.fade-leave-active {
  transition: opacity 150ms ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

// Responsive
@media (max-width: 768px) {
  .oslo-user-name {
    display: none;
  }

  .oslo-user-avatar {
    margin-right: 0;
  }
}
</style>