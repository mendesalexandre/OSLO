<template>
  <q-badge
    :color="count > 0 ? 'negative' : 'positive'"
    :label="count > 0 ? count : 'Livre'"
    class="q-px-sm"
  />
</template>

<script setup>
defineProps({
  count: {
    type: Number,
    default: 0,
  },
})
</script>
