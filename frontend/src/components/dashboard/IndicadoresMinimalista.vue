<template>
  <div class="indicadores-card">
    <h3 class="text-subtitle2 text-weight-bold q-ma-none q-mb-md">
      Indicadores
    </h3>

    <div class="indicador-list">
      <div class="indicador-row">
        <div class="indicador-label">
          <q-icon name="swap_horiz" size="20px" style="color: #667eea" />
          <span>Transações no mês</span>
        </div>
        <div class="indicador-valor">{{ indicadores.transacoes_mes }}</div>
      </div>

      <q-separator class="q-my-md" />

      <div class="indicador-row">
        <div class="indicador-label">
          <q-icon name="person" size="20px" style="color: #4caf50" />
          <span>Indicadores ativos</span>
        </div>
        <div class="indicador-valor">{{ indicadores.indicadores_ativos }}</div>
      </div>

      <q-separator class="q-my-md" />

      <div class="indicador-row">
        <div class="indicador-label">
          <q-icon name="warning" size="20px" style="color: #ff9800" />
          <span>Indisponibilidades</span>
        </div>
        <div class="indicador-valor">{{ indicadores.indisponibilidades_ativas }}</div>
      </div>

      <q-separator class="q-my-md" />

      <div class="indicador-row">
        <div class="indicador-label">
          <q-icon name="gavel" size="20px" style="color: #2196f3" />
          <span>Depósitos Judiciais</span>
        </div>
        <div class="indicador-valor">{{ indicadores.depositos_judiciais }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  indicadores: {
    type: Object,
    default: () => ({
      transacoes_mes:           0,
      indicadores_ativos:       0,
      indisponibilidades_ativas: 0,
      depositos_judiciais:      0,
    }),
  },
})
</script>

<style scoped lang="scss">
.indicadores-card {
  background: white;
  padding: 20px;
  border-radius: 8px;
  border: 1px solid #e0e0e0;

  .indicador-list {
    .indicador-row {
      display: flex;
      align-items: center;
      justify-content: space-between;

      .indicador-label {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
        color: #666;
        font-weight: 500;
      }

      .indicador-valor {
        font-size: 18px;
        font-weight: 600;
        color: #1a1a1a;
      }
    }
  }
}
</style>
