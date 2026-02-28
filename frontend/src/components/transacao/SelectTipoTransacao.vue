<template>
  <q-select
    v-bind="$attrs"
    :model-value="modelValue"
    :options="opcoes"
    option-value="id"
    option-label="descricao"
    :loading="carregando"
    emit-value
    map-options
    @update:model-value="$emit('update:modelValue', $event)"
  />
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useCatalogoStore } from 'src/stores/catalogo'

defineOptions({ inheritAttrs: false })

const props = defineProps({
  modelValue: { default: null },
  filtroTipo: { type: String, default: null },
})

defineEmits(['update:modelValue'])

const catalogoStore = useCatalogoStore()
const carregando = ref(false)

onMounted(async () => {
  if (!catalogoStore.tiposCarregado) {
    carregando.value = true
    await catalogoStore.fetchTipos()
    carregando.value = false
  }
})

const opcoes = computed(() => {
  if (props.filtroTipo) {
    return catalogoStore.tipos.filter(t => t.tipo === props.filtroTipo)
  }
  return catalogoStore.tipos
})
</script>
