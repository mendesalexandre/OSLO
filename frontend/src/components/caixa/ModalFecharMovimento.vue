<template>
  <modal
    v-model="model"
    titulo="Fechar Caixa"
    tamanho="sm"
    @close="model = false"
  >
    <div v-if="movimento" class="q-gutter-md">

      <!-- Resumo do movimento -->
      <q-card flat bordered class="q-pa-sm">
        <div class="row q-col-gutter-sm text-body2">
          <div class="col-6 row justify-between">
            <span style="color: var(--text-secondary)">Caixa:</span>
            <span class="text-weight-medium">{{ movimento.caixa?.nome }}</span>
          </div>
          <div class="col-6 row justify-between">
            <span style="color: var(--text-secondary)">Saldo Inicial:</span>
            <span>{{ formatCurrency(movimento.saldo_inicial_informado) }}</span>
          </div>
          <div class="col-6 row justify-between">
            <span style="color: var(--text-secondary)">Entradas:</span>
            <span class="text-positive">{{ formatCurrency(movimento.total_entradas) }}</span>
          </div>
          <div class="col-6 row justify-between">
            <span style="color: var(--text-secondary)">Saídas:</span>
            <span class="text-negative">{{ formatCurrency(movimento.total_saidas) }}</span>
          </div>
          <div class="col-12">
            <q-separator class="q-my-xs" />
          </div>
          <div class="col-12 row justify-between text-weight-bold">
            <span>Saldo Calculado:</span>
            <span class="text-positive">{{ formatCurrency(saldoCalculado) }}</span>
          </div>
        </div>
      </q-card>

      <div>
        <v-label label="Saldo Final Contado *" />
        <v-money v-model.number="form.saldo_final_informado" outlined dense />
      </div>

      <!-- Diferença -->
      <div v-if="diferenca !== null" class="q-pa-sm rounded-borders" :class="classeDiferenca">
        <div class="text-weight-medium">Diferença: {{ formatCurrency(diferenca) }}</div>
        <div class="text-caption">
          {{ diferenca === 0 ? 'Caixa conferido corretamente!' : diferenca > 0 ? 'Sobra no caixa' : 'Falta no caixa' }}
        </div>
      </div>

      <div>
        <v-label label="Observação" />
        <q-input
          v-model="form.observacao_fechamento"
          outlined dense
          type="textarea"
          rows="2"
          autogrow
        />
      </div>
    </div>

    <template #rodape>
      <div class="row justify-end q-gutter-sm">
        <q-btn flat no-caps label="Cancelar" @click="model = false" />
        <q-btn unelevated color="negative" no-caps :loading="salvando" @click="salvar">
          <l-icon name="lock" :size="16" class="q-mr-sm" />
          Fechar Caixa
        </q-btn>
      </div>
    </template>
  </modal>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useQuasar } from 'quasar'
import { useCaixaMovimentoStore } from 'src/stores/caixaMovimento'

defineOptions({ name: 'ModalFecharMovimento' })

const props = defineProps({
  movimento: { type: Object, default: null },
})

const emit = defineEmits(['fechado'])
const model = defineModel({ default: false })

const $q                  = useQuasar()
const caixaMovimentoStore = useCaixaMovimentoStore()
const salvando            = ref(false)

const form = ref({
  saldo_final_informado:  0,
  observacao_fechamento: '',
})

watch(model, (aberto) => {
  if (aberto) {
    form.value = {
      saldo_final_informado:  props.movimento?.saldo_final_sistema ?? 0,
      observacao_fechamento: '',
    }
  }
})

const saldoCalculado = computed(() => {
  if (!props.movimento) return 0
  const ini = parseFloat(props.movimento.saldo_inicial_informado ?? 0)
  const ent = parseFloat(props.movimento.total_entradas ?? 0)
  const sai = parseFloat(props.movimento.total_saidas ?? 0)
  return ini + ent - sai
})

const diferenca = computed(() => {
  if (form.value.saldo_final_informado === null || form.value.saldo_final_informado === '') return null
  return parseFloat(form.value.saldo_final_informado) - saldoCalculado.value
})

const classeDiferenca = computed(() => {
  if (diferenca.value === null) return ''
  if (Math.abs(diferenca.value) < 0.01) return 'bg-green-1 text-positive'
  return diferenca.value > 0 ? 'bg-orange-1 text-warning' : 'bg-red-1 text-negative'
})

const formatCurrency = (v) =>
  new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v ?? 0)

const salvar = async () => {
  salvando.value = true
  try {
    await caixaMovimentoStore.fechar(props.movimento.id, form.value)
    $q.notify({ type: 'positive', message: 'Caixa fechado com sucesso', position: 'top' })
    model.value = false
    emit('fechado')
  } catch (e) {
    $q.notify({
      type: 'negative',
      message: 'Erro ao fechar caixa',
      caption: e.response?.data?.error ?? e.response?.data?.mensagem,
    })
  } finally {
    salvando.value = false
  }
}
</script>
