<template>
  <div class="resumo-card">
    <div class="resumo-titulo text-caption text-uppercase">
      {{ titulo }}
    </div>
    <div class="resumo-valor text-h4 text-weight-bold">
      {{ valorFormatado }}
    </div>
    <div class="resumo-bar" :class="`bar-${tipo}`"></div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  titulo: { type: String, required: true },
  valor:  { type: [Number, String], default: 0 },
  tipo: {
    type: String,
    default: 'saldo',
    validator: (v) => ['saldo', 'entrada', 'saida', 'pendente'].includes(v),
  },
  ehNumero: { type: Boolean, default: false },
})

const valorFormatado = computed(() => {
  if (props.ehNumero) return props.valor
  return new Intl.NumberFormat('pt-BR', {
    style:                'currency',
    currency:             'BRL',
    minimumFractionDigits: 0,
  }).format(props.valor)
})
</script>

<style scoped lang="scss">
.resumo-card {
  background: white;
  padding: 20px;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  transition: all 0.2s ease;

  &:hover {
    border-color: #ccc;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  }

  .resumo-titulo {
    color: #999;
    font-size: 11px;
    font-weight: 600;
    margin-bottom: 8px;
    letter-spacing: 0.5px;
  }

  .resumo-valor {
    color: #1a1a1a;
    margin-bottom: 12px;
    line-height: 1.2;
  }

  .resumo-bar {
    height: 3px;
    border-radius: 2px;

    &.bar-saldo   { background: #667eea; }
    &.bar-entrada { background: #4caf50; }
    &.bar-saida   { background: #f44336; }
    &.bar-pendente{ background: #ff9800; }
  }
}
</style>
