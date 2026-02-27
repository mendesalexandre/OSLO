<template>
  <modal
    v-model="model"
    titulo="Calcular Preço"
    :subtitulo="produto?.nome ?? ''"
    tamanho="sm"
    @close="fechar"
  >

    <div class="row q-col-gutter-md">
      <!-- Base de cálculo (só quando necessário) -->
      <div v-if="precisaBase" class="col-12">
        <v-label label="Base de cálculo (valor do imóvel/negócio)" />
        <q-input
          v-model.number="calcForm.base_calculo"
          type="number" min="0" step="0.01"
          outlined dense prefix="R$"
        />
      </div>

      <div class="col-6">
        <v-label label="Quantidade" />
        <q-input v-model.number="calcForm.quantidade" type="number" min="1" max="99" outlined dense />
      </div>

      <div class="col-6 flex items-end">
        <q-btn
          unelevated color="primary" no-caps
          class="full-width"
          :loading="calculando"
          @click="calcular"
        >
          <l-icon name="calculator" :size="16" class="q-mr-sm" />
          Calcular
        </q-btn>
      </div>
    </div>

    <!-- Resultado: TABELA_CUSTA -->
    <template v-if="resultado && resultado.tipo === 'TABELA_CUSTA'">
      <q-separator class="q-my-md" />

      <div class="oslo-resultado-header text-caption oslo-text-secondary q-mb-sm">RESULTADO</div>

      <div class="row items-center justify-between q-py-xs">
        <span class="text-body2">Emolumento bruto</span>
        <span class="text-body2 text-weight-semibold">{{ formatCurrency(resultado.calculo.emolumento_bruto) }}</span>
      </div>

      <div
        v-for="imp in resultado.calculo.impostos"
        :key="imp.codigo"
        class="row items-center justify-between q-py-xs oslo-imposto-row"
      >
        <div class="row items-center q-gutter-x-xs">
          <l-icon name="minus" :size="10" color="negative" />
          <span class="text-caption">{{ imp.nome }}</span>
          <q-badge
            v-if="imp.percentual"
            color="grey-3" text-color="grey-8"
            :label="`${imp.percentual}%`"
            dense
          />
        </div>
        <span class="text-caption text-negative">- {{ formatCurrency(imp.valor) }}</span>
      </div>

      <q-separator class="q-my-sm" />

      <div class="row items-center justify-between q-py-xs">
        <span class="text-body2 oslo-text-secondary">Emolumento líquido (cartório)</span>
        <span class="text-body2">{{ formatCurrency(resultado.calculo.emolumento_liquido) }}</span>
      </div>

      <div class="row items-center justify-between q-py-xs bg-primary-subtle q-px-sm q-mt-xs rounded-borders">
        <span class="text-body1 text-weight-bold">Total do cliente</span>
        <span class="text-body1 text-weight-bold text-primary">{{ formatCurrency(resultado.calculo.total_cliente) }}</span>
      </div>
    </template>

    <!-- Resultado: PROPRIO -->
    <template v-if="resultado && resultado.tipo === 'PROPRIO'">
      <q-separator class="q-my-md" />

      <div class="oslo-resultado-header text-caption oslo-text-secondary q-mb-sm">RESULTADO</div>

      <div class="row items-center justify-between q-py-xs">
        <span class="text-body2">Valor unitário</span>
        <span class="text-body2 text-weight-semibold">{{ formatCurrency(resultado.calculo.valor_unitario) }}</span>
      </div>

      <div class="row items-center justify-between q-py-xs">
        <span class="text-body2 oslo-text-secondary">Quantidade</span>
        <span class="text-body2">{{ resultado.calculo.quantidade }}</span>
      </div>

      <div class="row items-center justify-between q-py-xs bg-primary-subtle q-px-sm q-mt-xs rounded-borders">
        <span class="text-body1 text-weight-bold">Total</span>
        <span class="text-body1 text-weight-bold text-primary">{{ formatCurrency(resultado.calculo.total_cliente) }}</span>
      </div>
    </template>

    <!-- Rodapé -->
    <template #rodape>
      <q-btn flat no-caps label="Fechar" color="grey-7" @click="fechar" />
    </template>

  </modal>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useQuasar } from 'quasar'
import { useProdutoStore } from 'src/stores/produto'

const props = defineProps({
  produto: { type: Object, default: null },
})

const model        = defineModel({ default: false })
const $q           = useQuasar()
const produtoStore = useProdutoStore()
const calculando   = ref(false)
const resultado    = ref(null)
const calcForm     = ref({ base_calculo: 0, quantidade: 1 })

const precisaBase = computed(() => {
  if (!props.produto) return false
  return props.produto.tipo === 'TABELA_CUSTA' || props.produto.permite_base_calculo
})

// Reset ao abrir
watch(model, (aberto) => {
  if (aberto) {
    resultado.value = null
    calcForm.value  = { base_calculo: 0, quantidade: 1 }
  }
})

// ── API ───────────────────────────────────────────────────────────────────────
const calcular = async () => {
  if (!props.produto) return
  calculando.value = true
  resultado.value  = null
  try {
    resultado.value = await produtoStore.calcular(props.produto.uuid, calcForm.value)
  } catch (e) {
    $q.notify({ type: 'negative', message: 'Erro ao calcular', caption: e.response?.data?.mensagem })
  } finally {
    calculando.value = false
  }
}

// ── Helpers ───────────────────────────────────────────────────────────────────
const formatCurrency = (v) =>
  new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v ?? 0)

const fechar = () => { model.value = false }
</script>

<style lang="scss" scoped>
.oslo-imposto-row {
  border-left: 2px solid #fee2e2;
  padding-left: 8px;
  margin-left: 4px;
  margin-bottom: 2px;
}

.oslo-resultado-header {
  font-weight: 700;
  letter-spacing: 0.05em;
  text-transform: uppercase;
}

.bg-primary-subtle {
  background: rgba(var(--q-primary-rgb, 255, 122, 0), 0.08);
  border-radius: 6px;
}
</style>
