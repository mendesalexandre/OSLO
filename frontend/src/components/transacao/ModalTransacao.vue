<template>
  <modal
    v-model="model"
    :titulo="transacao ? 'Editar Transação' : 'Nova Transação'"
    tamanho="sm"
    @close="model = false"
  >
    <div class="q-gutter-md">

      <!-- Tipo -->
      <div>
        <v-label label="Tipo *" />
        <q-btn-toggle
          v-model="form.tipo"
          :options="tiposOpcoes"
          spread unelevated
          class="tipo-toggle"
        />
      </div>

      <!-- Caixa -->
      <div>
        <v-label label="Caixa *" />
        <q-select
          v-model="form.caixa_id"
          :options="caixaStore.caixas"
          option-value="id"
          option-label="nome"
          emit-value map-options
          outlined dense
          :rules="[v => !!v || 'Obrigatório']"
        >
          <template #prepend><l-icon name="wallet" :size="14" /></template>
        </q-select>
      </div>

      <!-- Natureza -->
      <div>
        <v-label label="Natureza *" />
        <q-select
          v-model="form.natureza"
          :options="naturezasDisponiveis"
          option-value="value"
          option-label="label"
          emit-value map-options
          outlined dense
          :rules="[v => !!v || 'Obrigatório']"
        />
      </div>

      <!-- Descrição -->
      <div>
        <v-label label="Descrição *" />
        <q-input
          v-model="form.descricao"
          outlined dense
          :rules="[v => !!v || 'Obrigatório']"
        />
      </div>

      <!-- Valor -->
      <div>
        <v-label label="Valor *" />
        <v-money v-model.number="form.valor" outlined dense />
      </div>

      <!-- Data vencimento -->
      <div>
        <v-label label="Data de Vencimento *" />
        <v-date v-model="form.data_vencimento" outlined dense />
      </div>

      <!-- Observação -->
      <div>
        <v-label label="Observação" />
        <q-input v-model="form.observacao" outlined dense type="textarea" rows="2" autogrow />
      </div>

    </div>

    <template #rodape>
      <div class="row justify-end q-gutter-sm">
        <q-btn flat no-caps label="Cancelar" @click="model = false" />
        <q-btn unelevated color="primary" no-caps :loading="salvando" @click="salvar">
          <l-icon name="save" :size="16" class="q-mr-sm" />
          Salvar
        </q-btn>
      </div>
    </template>
  </modal>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useQuasar } from 'quasar'
import { useTransacaoStore } from 'src/stores/transacao'
import { useCaixaStore } from 'src/stores/caixa'

defineOptions({ name: 'ModalTransacao' })

const props = defineProps({
  transacao: { type: Object, default: null },
})

const emit = defineEmits(['salvo'])
const model = defineModel({ default: false })

const $q            = useQuasar()
const transacaoStore = useTransacaoStore()
const caixaStore    = useCaixaStore()
const salvando      = ref(false)

const tiposOpcoes = [
  { label: 'Entrada', value: 'ENTRADA', color: 'positive' },
  { label: 'Saída',   value: 'SAIDA',   color: 'negative' },
]

const naturezasPorTipo = {
  ENTRADA: [
    { value: 'CONTA_RECEBER', label: 'Conta a Receber' },
    { value: 'REFORCO',       label: 'Reforço'         },
    { value: 'AJUSTE',        label: 'Ajuste'          },
    { value: 'TRANSFERENCIA', label: 'Transferência'   },
  ],
  SAIDA: [
    { value: 'CONTA_PAGAR',   label: 'Conta a Pagar'  },
    { value: 'SANGRIA',       label: 'Sangria'         },
    { value: 'AJUSTE',        label: 'Ajuste'          },
    { value: 'TRANSFERENCIA', label: 'Transferência'   },
  ],
}

const naturezasDisponiveis = computed(() =>
  naturezasPorTipo[form.value.tipo] ?? naturezasPorTipo.ENTRADA
)

const hoje = new Date().toISOString().split('T')[0]

const formVazio = () => ({
  tipo:            'ENTRADA',
  natureza:        'CONTA_RECEBER',
  caixa_id:        null,
  descricao:       '',
  valor:           0,
  data_vencimento: hoje,
  observacao:      '',
})

const form = ref(formVazio())

// Ajusta natureza quando muda o tipo
watch(() => form.value.tipo, (novoTipo) => {
  const opcoes = naturezasPorTipo[novoTipo]
  if (!opcoes.find(o => o.value === form.value.natureza)) {
    form.value.natureza = opcoes[0].value
  }
})

watch(model, async (aberto) => {
  if (aberto) {
    await caixaStore.listar()
    form.value = props.transacao
      ? {
          tipo:            props.transacao.tipo,
          natureza:        props.transacao.natureza,
          caixa_id:        props.transacao.caixa_id,
          descricao:       props.transacao.descricao,
          valor:           props.transacao.valor,
          data_vencimento: props.transacao.data_vencimento,
          observacao:      props.transacao.observacao ?? '',
        }
      : formVazio()
  }
})

const salvar = async () => {
  salvando.value = true
  try {
    if (props.transacao) {
      await transacaoStore.atualizar(props.transacao.id, form.value)
      $q.notify({ type: 'positive', message: 'Transação atualizada com sucesso', position: 'top' })
    } else {
      await transacaoStore.criar(form.value)
      $q.notify({ type: 'positive', message: 'Transação criada com sucesso', position: 'top' })
    }
    model.value = false
    emit('salvo')
  } catch (e) {
    $q.notify({
      type: 'negative',
      message: 'Erro ao salvar transação',
      caption: e.response?.data?.mensagem ?? e.response?.data?.message,
    })
  } finally {
    salvando.value = false
  }
}
</script>

<style scoped lang="scss">
.tipo-toggle {
  :deep(.q-btn) {
    border: 1px solid var(--border-color);
    border-radius: var(--radius-sm) !important;
  }
}
</style>
