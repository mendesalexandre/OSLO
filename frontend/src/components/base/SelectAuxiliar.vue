<template>
  <q-select
    v-bind="$attrs"
    :model-value="modelValue"
    :options="opcoes"
    :option-label="optionLabel"
    :option-value="optionValue"
    :loading="auxiliaresStore.carregando"
    emit-value
    map-options
    @update:model-value="$emit('update:modelValue', $event)"
  />
</template>

<script setup>
import { computed } from 'vue'
import { useAuxiliaresStore } from 'src/stores/auxiliares'

defineOptions({ inheritAttrs: false })

const props = defineProps({
  modelValue: {
    default: null,
  },
  tabela: {
    type: String,
    required: true,
  },
  optionLabel: {
    type: String,
    default: 'descricao',
  },
  optionValue: {
    type: String,
    default: 'id',
  },
})

defineEmits(['update:modelValue'])

const auxiliaresStore = useAuxiliaresStore()
const opcoes = computed(() => auxiliaresStore.obter(props.tabela))
</script>
