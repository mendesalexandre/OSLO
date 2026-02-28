<template>
  <div class="calendario-card">
    <div class="calendario-header">
      <button class="btn-nav" @click="mesAnterior">
        <q-icon name="chevron_left" />
      </button>
      <div class="calendario-titulo text-weight-bold">
        {{ mesFormatado }} {{ ano }}
      </div>
      <button class="btn-nav" @click="proximoMes">
        <q-icon name="chevron_right" />
      </button>
    </div>

    <div class="calendario-semana">
      <div v-for="d in diasSemana" :key="d" class="dia-semana">{{ d }}</div>
    </div>

    <div class="calendario-dias">
      <div
        v-for="dia in diasMes"
        :key="dia.data"
        class="dia-cell"
        :class="{
          'dia-outro-mes': !dia.ehMesAtual,
          'dia-com-evento': dia.count > 0,
          'dia-hoje': dia.ehHoje,
        }"
      >
        <div class="dia-numero">{{ dia.dia }}</div>
        <div v-if="dia.count > 0" class="dia-evento">
          <span class="evento-badge">{{ dia.count }}</span>
        </div>
      </div>
    </div>

    <div class="calendario-legenda q-mt-md">
      <div class="legenda-item">
        <span class="legenda-box" style="background: #4caf50"></span>
        <span class="text-caption">Entradas</span>
      </div>
      <div class="legenda-item">
        <span class="legenda-box" style="background: #f44336"></span>
        <span class="text-caption">Saídas</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import dayjs from 'dayjs'
import 'dayjs/locale/pt-br'

dayjs.locale('pt-br')

const props = defineProps({
  eventos: { type: Array, default: () => [] },
})

const emit = defineEmits(['change'])

const ano = ref(dayjs().year())
const mes = ref(dayjs().month() + 1)

const diasSemana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab']

const mesFormatado = computed(() =>
  dayjs().month(mes.value - 1).format('MMMM')
)

const diasMes = computed(() => {
  const primeiroDia = dayjs(`${ano.value}-${String(mes.value).padStart(2, '0')}-01`)
  const ultimoDia   = primeiroDia.endOf('month')
  const diasAntes   = primeiroDia.day()
  const arr = []

  for (let i = diasAntes - 1; i >= 0; i--) {
    const d = primeiroDia.subtract(i + 1, 'day')
    arr.push({ dia: d.date(), data: d.format('YYYY-MM-DD'), ehMesAtual: false, count: 0 })
  }

  for (let i = 1; i <= ultimoDia.date(); i++) {
    const d      = primeiroDia.date(i)
    const evento = props.eventos?.find(e => e.dia === i)
    arr.push({
      dia:       i,
      data:      d.format('YYYY-MM-DD'),
      ehMesAtual:true,
      ehHoje:    d.isSame(dayjs(), 'day'),
      count:     evento?.count  ?? 0,
      entrada:   evento?.entrada ?? 0,
      saida:     evento?.saida   ?? 0,
    })
  }

  const faltam = 42 - arr.length
  for (let i = 1; i <= faltam; i++) {
    const d = ultimoDia.add(i, 'day')
    arr.push({ dia: d.date(), data: d.format('YYYY-MM-DD'), ehMesAtual: false, count: 0 })
  }

  return arr
})

function mesAnterior() {
  mes.value--
  if (mes.value < 1) { mes.value = 12; ano.value-- }
  emit('change', { ano: ano.value, mes: mes.value })
}

function proximoMes() {
  mes.value++
  if (mes.value > 12) { mes.value = 1; ano.value++ }
  emit('change', { ano: ano.value, mes: mes.value })
}
</script>

<style scoped lang="scss">
.calendario-card {
  background: white;
  padding: 20px;
  border-radius: 8px;
  border: 1px solid #e0e0e0;

  .calendario-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;

    .btn-nav {
      background: none;
      border: none;
      padding: 4px 8px;
      cursor: pointer;
      border-radius: 4px;
      transition: background 0.2s ease;
      &:hover { background: #f5f5f5; }
    }

    .calendario-titulo {
      font-size: 16px;
      text-transform: capitalize;
      color: #1a1a1a;
    }
  }

  .calendario-semana {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
    margin-bottom: 8px;

    .dia-semana {
      text-align: center;
      font-size: 12px;
      font-weight: 600;
      color: #999;
      padding: 8px 0;
    }
  }

  .calendario-dias {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;

    .dia-cell {
      aspect-ratio: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      border-radius: 6px;
      cursor: default;
      position: relative;
      transition: all 0.2s ease;
      font-size: 13px;
      font-weight: 500;

      .dia-numero { color: #1a1a1a; }

      .dia-evento {
        position: absolute;
        top: 2px;
        right: 2px;

        .evento-badge {
          display: inline-flex;
          width: 18px;
          height: 18px;
          background: #667eea;
          color: white;
          border-radius: 50%;
          font-size: 10px;
          align-items: center;
          justify-content: center;
          font-weight: 600;
        }
      }

      &.dia-outro-mes { color: #ccc; .dia-numero { color: #ccc; } }
      &.dia-com-evento { background: #f0f0f0; &:hover { background: #e8e8e8; } }
      &.dia-hoje { background: #667eea; .dia-numero { color: white; } }
      &:hover:not(.dia-hoje) { background: #f5f5f5; }
    }
  }

  .calendario-legenda {
    display: flex;
    gap: 16px;
    justify-content: center;

    .legenda-item {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 12px;
      color: #666;

      .legenda-box {
        width: 12px;
        height: 12px;
        border-radius: 2px;
      }
    }
  }
}
</style>
