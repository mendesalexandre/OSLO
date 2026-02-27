<template>
  <label class="v-label">
    <span class="v-label-titulo" v-html="label.toUpperCase()" :title="obrigatorio ? 'Este campo é obrigatório' : ''" />

    <span v-if="obrigatorio" class="text-red-6 q-ml-xs">*</span>

    <q-icon v-if="ajuda" name="help_outline" size="sm" class="q-ml-xs cursor-pointer">
      <q-tooltip>{{ ajuda }}</q-tooltip>
    </q-icon>
  </label>
</template>

<script setup>
defineProps({
  label: {
    type: String,
    required: true,
  },
  obrigatorio: {
    type: Boolean,
    default: false,
  },
  ajuda: {
    type: String,
    default: "",
  },
});
</script>

<style scoped>
.v-label {
  display: block;
  margin-bottom: 6px;
}

.v-label-titulo {
  font-size: 11px;
  font-weight: 600;
  color: #5f6368;
  letter-spacing: 0.5px;
}
</style>