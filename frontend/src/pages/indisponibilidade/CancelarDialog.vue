<template>
  <q-dialog :model-value="modelValue" persistent @update:model-value="$emit('update:modelValue', $event)">
    <q-card style="min-width: 420px">
      <q-card-section class="row items-center q-pb-none">
        <div class="text-h6">Cancelar Indisponibilidade</div>
        <q-space />
        <q-btn icon="close" flat round dense @click="fechar" />
      </q-card-section>

      <q-card-section>
        <div v-if="indisponibilidade" class="text-caption text-grey-7 q-mb-md">
          Protocolo: <strong>{{ indisponibilidade.protocolo_indisponibilidade }}</strong>
        </div>
        <q-form @submit.prevent="confirmar">
          <q-input
            v-model="form.cancelamento_protocolo"
            label="Protocolo de cancelamento *"
            outlined
            dense
            class="q-mb-sm"
            :error="!!erros.cancelamento_protocolo"
            :error-message="erros.cancelamento_protocolo?.[0]"
          />
          <q-input
            v-model.number="form.cancelamento_tipo"
            label="Tipo de cancelamento *"
            type="number"
            outlined
            dense
            class="q-mb-sm"
            :error="!!erros.cancelamento_tipo"
            :error-message="erros.cancelamento_tipo?.[0]"
          />
          <q-input
            v-model="form.cancelamento_data"
            label="Data do cancelamento *"
            outlined
            dense
            type="date"
            class="q-mb-sm"
            :error="!!erros.cancelamento_data"
            :error-message="erros.cancelamento_data?.[0]"
          />
        </q-form>
      </q-card-section>

      <q-card-actions align="right">
        <q-btn flat label="Cancelar" @click="fechar" />
        <q-btn
          unelevated
          color="orange-8"
          label="Confirmar cancelamento"
          :loading="carregando"
          @click="confirmar"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { reactive, ref, watch } from 'vue'
import { useQuasar } from 'quasar'
import { useIndisponibilidadeStore } from 'src/stores/indisponibilidade'

const props = defineProps({
  modelValue: Boolean,
  indisponibilidade: Object,
})

const emit = defineEmits(['update:modelValue', 'cancelada'])

const $q = useQuasar()
const store = useIndisponibilidadeStore()
const carregando = ref(false)
const erros = ref({})

const form = reactive({
  cancelamento_protocolo: '',
  cancelamento_tipo: null,
  cancelamento_data: '',
})

watch(() => props.modelValue, (val) => {
  if (val) {
    form.cancelamento_protocolo = ''
    form.cancelamento_tipo = null
    form.cancelamento_data = ''
    erros.value = {}
  }
})

async function confirmar() {
  erros.value = {}
  carregando.value = true
  try {
    await store.cancelar(props.indisponibilidade.id, { ...form })
    $q.notify({ type: 'positive', message: 'Indisponibilidade cancelada com sucesso.' })
    emit('cancelada')
    fechar()
  } catch (e) {
    erros.value = e?.response?.data?.erros ?? {}
    $q.notify({ type: 'negative', message: e?.response?.data?.mensagem ?? 'Erro ao cancelar.' })
  } finally {
    carregando.value = false
  }
}

function fechar() {
  emit('update:modelValue', false)
}
</script>
