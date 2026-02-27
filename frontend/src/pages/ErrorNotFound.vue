<template>
  <div class="fullscreen flex flex-center" style="background: var(--background)">
    <div class="text-center">
      <div style="font-size: 8rem; font-weight: 700; color: var(--border-color); line-height: 1">404</div>
      <div style="color: var(--text-secondary); font-size: var(--font-size-lg); margin-top: 8px">
        Página não encontrada.
      </div>
      <q-btn
        class="q-mt-xl"
        color="primary"
        unelevated
        no-caps
        to="/"
        label="Voltar para a página inicial"
      />
    </div>
  </div>
</template>

<script setup>
defineOptions({ name: "ErrorNotFound" });
</script>
