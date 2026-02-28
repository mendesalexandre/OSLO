<template>
  <div class="timeline-card">
    <h3 class="text-subtitle2 text-weight-bold q-ma-none q-mb-md">
      Últimos Eventos
    </h3>

    <div class="timeline-list">
      <template v-for="(evento, idx) in eventos" :key="evento.id">
        <div class="timeline-item">
          <div class="timeline-marker" :style="{ background: evento.cor }">
            <q-icon :name="evento.icone || 'receipt'" size="16px" color="white" />
          </div>

          <div class="timeline-content">
            <div class="timeline-titulo text-weight-bold">
              {{ evento.titulo }}
            </div>
            <div class="timeline-desc text-caption">
              {{ evento.descricao }}
            </div>
            <div class="timeline-meta text-caption text-grey-7">
              {{ formatarData(evento.data) }}
            </div>
            <div v-if="evento.valor" class="timeline-valor text-weight-bold q-mt-xs">
              {{ formatarMoeda(evento.valor) }}
            </div>
          </div>
        </div>

        <q-separator v-if="idx < eventos.length - 1" class="q-my-md" />
      </template>

      <div v-if="!eventos || !eventos.length" class="text-center text-grey-7 q-py-lg">
        Nenhum evento recente
      </div>
    </div>
  </div>
</template>

<script setup>
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import 'dayjs/locale/pt-br'

dayjs.extend(relativeTime)
dayjs.locale('pt-br')

defineProps({
  eventos: { type: Array, default: () => [] },
})

function formatarData(data) {
  return dayjs(data).fromNow()
}

function formatarMoeda(valor) {
  return new Intl.NumberFormat('pt-BR', {
    style:                'currency',
    currency:             'BRL',
    minimumFractionDigits: 0,
  }).format(valor)
}
</script>

<style scoped lang="scss">
.timeline-card {
  background: white;
  padding: 20px;
  border-radius: 8px;
  border: 1px solid #e0e0e0;

  .timeline-list {
    .timeline-item {
      display: flex;
      gap: 16px;

      .timeline-marker {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 2px;
      }

      .timeline-content {
        flex: 1;

        .timeline-titulo {
          font-size: 14px;
          color: #1a1a1a;
          margin-bottom: 4px;
        }
        .timeline-desc { color: #666; margin-bottom: 4px; }
        .timeline-meta { color: #999; }
        .timeline-valor { font-size: 13px; color: #667eea; }
      }
    }
  }
}
</style>
