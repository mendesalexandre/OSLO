<template>
  <q-page class="flex flex-center">
    <div class="text-center" style="max-width: 400px">
      <l-icon name="lock" :size="56" style="color: var(--text-muted)" class="q-mb-md" />
      <h5 style="color: var(--text-color); font-weight: 600" class="q-mb-xs">Acesso Negado</h5>
      <p style="color: var(--text-secondary); font-size: 14px">
        Você não possui permissão para acessar esta página.
        Entre em contato com o administrador caso precise de acesso.
      </p>
      <q-btn flat no-caps color="primary" label="Voltar" icon="arrow_back" @click="$router.back()" class="q-mt-md" />
    </div>
  </q-page>
</template>

<script setup>
defineOptions({ name: "SemPermissaoPage" });
</script>