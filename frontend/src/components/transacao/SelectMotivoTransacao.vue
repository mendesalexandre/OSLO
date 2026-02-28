<template>
  <q-select
    v-bind="$attrs"
    :model-value="modelValue"
    :options="opcoes"
    option-value="id"
    option-label="descricao"
    :disable="!tipoTransacaoId || opcoes.length === 0"
    :hint="!tipoTransacaoId ? 'Selecione o tipo primeiro' : undefined"
    emit-value
    map-options
    @update:model-value="$emit('update:modelValue', $event)"
  />
</template>

<script setup>
import { computed, watch } from 'vue'
import { useCatalogoStore } from 'src/stores/catalogo'

defineOptions({ inheritAttrs: false })

const props = defineProps({
  modelValue: { default: null },
  tipoTransacaoId: { type: Number, default: null },
})

const emit = defineEmits(['update:modelValue'])

const catalogoStore = useCatalogoStore()

const opcoes = computed(() => {
  if (!props.tipoTransacaoId) return []
  const tipo = catalogoStore.tipos.find(t => t.id === props.tipoTransacaoId)
  return tipo?.motivos_transacao ?? []
})

// Limpar motivo quando tipo muda
watch(() => props.tipoTransacaoId, () => {
  emit('update:modelValue', null)
})
</script>
