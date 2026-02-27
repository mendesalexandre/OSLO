<template>
  <modal
    v-model="model"
    titulo="Abrir Caixa"
    tamanho="sm"
    @close="model = false"
  >
    <div class="q-gutter-md">
      <div>
        <v-label label="Caixa *" />
        <q-select
          v-model="form.caixa_id"
          :options="caixas"
          option-value="id"
          option-label="nome"
          emit-value map-options
          outlined dense
          :rules="[v => !!v || 'Selecione um caixa']"
        >
          <template #prepend>
            <l-icon name="wallet" :size="14" />
          </template>
        </q-select>
      </div>

      <div>
        <v-label label="Saldo Inicial Informado *" />
        <v-money v-model.number="form.saldo_inicial_informado" outlined dense />
      </div>

      <div>
        <v-label label="Observação" />
        <q-input
          v-model="form.observacao_abertura"
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
        <q-btn unelevated color="positive" no-caps :loading="salvando" @click="salvar">
          <l-icon name="unlock" :size="16" class="q-mr-sm" />
          Abrir Caixa
        </q-btn>
      </div>
    </template>
  </modal>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useQuasar } from 'quasar'
import { useCaixaMovimentoStore } from 'src/stores/caixaMovimento'
import { useCaixaStore } from 'src/stores/caixa'

defineOptions({ name: 'ModalAbrirMovimento' })

const emit = defineEmits(['aberto'])
const model = defineModel({ default: false })

const $q                   = useQuasar()
const caixaMovimentoStore  = useCaixaMovimentoStore()
const caixaStore           = useCaixaStore()
const salvando             = ref(false)

const caixas = ref([])

const form = ref({
  caixa_id:               null,
  saldo_inicial_informado: 0,
  observacao_abertura:    '',
})

watch(model, async (aberto) => {
  if (aberto) {
    form.value = { caixa_id: null, saldo_inicial_informado: 0, observacao_abertura: '' }
    await caixaStore.listar()
    caixas.value = caixaStore.caixas.filter(c => c.is_ativo)
  }
})

const salvar = async () => {
  if (!form.value.caixa_id) {
    $q.notify({ type: 'warning', message: 'Selecione um caixa', position: 'top' })
    return
  }
  salvando.value = true
  try {
    await caixaMovimentoStore.abrir(form.value)
    $q.notify({ type: 'positive', message: 'Caixa aberto com sucesso', position: 'top' })
    model.value = false
    emit('aberto')
  } catch (e) {
    $q.notify({
      type: 'negative',
      message: 'Erro ao abrir caixa',
      caption: e.response?.data?.error ?? e.response?.data?.mensagem,
    })
  } finally {
    salvando.value = false
  }
}
</script>
