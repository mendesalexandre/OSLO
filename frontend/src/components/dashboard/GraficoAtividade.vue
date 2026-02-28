<template>
  <div class="grafico-card">
    <h3 class="text-subtitle2 text-weight-bold q-ma-none q-mb-md">
      Atividade do Mês
    </h3>

    <div v-if="dados && dados.length > 0" class="grafico-barras">
      <div v-for="item in dados" :key="item.data" class="barra-item">
        <div class="barra-wrapper">
          <div
            class="barra"
            :style="{ height: `${(item.valor / maxValor) * 100}%` }"
          ></div>
        </div>
        <div class="barra-label text-caption">{{ item.data }}</div>
      </div>
    </div>

    <div v-else class="text-center text-grey-6 q-py-lg text-caption">
      Sem atividade registrada no mês
    </div>

    <div class="grafico-info text-caption q-mt-md">
      <span>Total: {{ dados?.length ?? 0 }} dias com transações</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  dados: { type: Array, default: () => [] },
})

const maxValor = computed(() => {
  if (!props.dados || props.dados.length === 0) return 1
  return Math.max(...props.dados.map(d => d.valor))
})
</script>

<style scoped lang="scss">
.grafico-card {
  background: white;
  padding: 20px;
  border-radius: 8px;
  border: 1px solid #e0e0e0;

  .grafico-barras {
    display: flex;
    gap: 6px;
    align-items: flex-end;
    height: 120px;
    overflow-x: auto;
    padding-bottom: 8px;

    .barra-item {
      flex: 0 0 calc(100% / 8);
      min-width: 24px;
      display: flex;
      flex-direction: column;
      align-items: center;
      height: 100%;
      gap: 4px;

      .barra-wrapper {
        width: 100%;
        flex: 1;
        background: #f5f5f5;
        border-radius: 3px;
        overflow: hidden;
        display: flex;
        align-items: flex-end;

        .barra {
          width: 100%;
          background: #667eea;
          transition: all 0.2s ease;
          min-height: 4px;

          &:hover { background: #764ba2; }
        }
      }

      .barra-label {
        color: #999;
        font-size: 10px;
        white-space: nowrap;
      }
    }
  }

  .grafico-info { color: #999; }
}
</style>
