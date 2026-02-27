<template>
  <modal v-model="model" :titulo="`Preview do Recibo ${recibo?.numero || ''}`" tamanho="lg" no-padding
    cor-botao-fechar="white" cor-titulo-cabecalho="text-white" @close="fechar">
    <template #default>
      <div class="preview-container">
        <recibo-preview v-if="recibo && protocolo" :recibo="recibo" :protocolo="protocolo" />
        <div v-else class="flex flex-center q-pa-xl">
          <q-spinner-dots color="primary" size="50px" />
        </div>
      </div>
    </template>

    <template #rodape>
      <div class="row justify-end q-gutter-sm">
        <q-btn label="Fechar" flat color="grey-7" no-caps @click="fechar" />
        <q-btn label="Imprimir" color="primary" unelevated no-caps icon="print" @click="imprimir" />
      </div>
    </template>
  </modal>
</template>

<script setup>
import { ref, watch } from 'vue'
import { api } from 'src/boot/axios'
import { useQuasar } from 'quasar'
import ReciboPreview from './ReciboPreview.vue'

defineOptions({ name: 'ReciboPreviewModal' })

const props = defineProps({
  reciboId: {
    type: Number,
    default: null,
  },
  protocoloId: {
    type: Number,
    default: null,
  },
})

const $q = useQuasar()

const model = defineModel({ default: false })
const recibo = ref(null)
const protocolo = ref(null)

watch(model, (val) => {
  if (val && props.reciboId) {
    carregarDados()
  } else if (!val) {
    // Limpar dados ao fechar
    setTimeout(() => {
      recibo.value = null
      protocolo.value = null
    }, 300)
  }
})

async function carregarDados() {
  try {
    // Buscar dados do recibo
    const reciboResponse = await api.get(`/recibo/${props.reciboId}`)

    if (reciboResponse.data.sucesso) {
      recibo.value = reciboResponse.data.dados

      // Buscar dados completos do protocolo
      const protocoloId = props.protocoloId || recibo.value.protocolo_id
      const protocoloResponse = await api.get(`/protocolo/${protocoloId}`)

      if (protocoloResponse.data.sucesso) {
        protocolo.value = protocoloResponse.data.dados
      }
    }
  } catch (error) {
    console.error('Erro ao carregar dados do recibo:', error)
    $q.notify({
      type: 'negative',
      message: 'Erro ao carregar dados do recibo',
      position: 'top',
    })
    fechar()
  }
}

function fechar() {
  model.value = false
}

function imprimir() {
  window.print()
}
</script>

<style lang="scss" scoped>
.preview-container {
  max-height: 70vh;
  overflow-y: auto;
  background: #f5f5f5;
  padding: 20px;
}

@media print {

  :deep(.modal-cabecalho),
  :deep(.modal-rodape),
  :deep(.q-dialog__backdrop) {
    display: none !important;
  }

  .preview-container {
    height: auto;
    overflow: visible;
    background: white;
    padding: 0;
  }
}
</style>