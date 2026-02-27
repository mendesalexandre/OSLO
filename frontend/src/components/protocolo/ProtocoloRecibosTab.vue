<template>
  <div class="protocolo-recibos-tab">
    <q-card flat bordered>
      <q-card-section>
        <div class="row items-center justify-between q-mb-md">
          <div class="text-h6">Recibos Emitidos</div>
          <q-btn
            v-if="podeGerarRecibo"
            color="primary"
            icon="eva-printer-outline"
            label="Gerar Recibo"
            size="sm"
            unelevated
            no-caps
            :loading="gerando"
            @click="gerarRecibo"
          />
        </div>

        <q-table
          :rows="recibos"
          :columns="columns"
          row-key="id"
          flat
          :loading="carregando"
          :pagination="{ rowsPerPage: 10 }"
        >
          <template v-slot:body-cell-numero="props">
            <q-td :props="props">
              <span class="text-weight-bold text-primary">{{ props.value }}</span>
            </q-td>
          </template>

          <template v-slot:body-cell-valor_pago="props">
            <q-td :props="props">
              <span class="text-weight-bold text-green-8">{{ formatarDinheiroBrasil(props.value) }}</span>
            </q-td>
          </template>

          <template v-slot:body-cell-acoes="props">
            <q-td :props="props">
              <q-btn
                flat
                dense
                round
                size="sm"
                icon="eva-eye-outline"
                color="primary"
                @click="previewRecibo(props.row)"
              >
                <q-tooltip>Preview HTML</q-tooltip>
              </q-btn>
              <q-btn
                flat
                dense
                round
                size="sm"
                icon="eva-download-outline"
                color="green-7"
                @click="downloadPDF(props.row)"
              >
                <q-tooltip>Download PDF</q-tooltip>
              </q-btn>
              <q-btn
                flat
                dense
                round
                size="sm"
                icon="eva-info-outline"
                color="grey-7"
                @click="visualizarRecibo(props.row)"
              >
                <q-tooltip>Ver Detalhes</q-tooltip>
              </q-btn>
            </q-td>
          </template>

          <template v-slot:no-data>
            <div class="full-width row flex-center q-gutter-sm text-grey-6 q-pa-lg">
              <l-icon name="receipt" :size="48" />
              <span>Nenhum recibo emitido</span>
            </div>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <!-- Modal de preview do recibo -->
    <recibo-preview-modal
      v-model="modalPreview"
      :recibo-id="reciboIdSelecionado"
      :protocolo-id="protocolo.id"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useQuasar, date } from 'quasar'
import { api } from 'src/boot/axios'
import { formatarDinheiroBrasil } from 'src/Utils'
import ReciboPreviewModal from 'src/components/recibo/ReciboPreviewModal.vue'

defineOptions({ name: 'ProtocoloRecibosTab' })

const props = defineProps({
  protocolo: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['atualizar'])

const $q = useQuasar()

const recibos = ref([])
const carregando = ref(false)
const gerando = ref(false)
const modalPreview = ref(false)
const reciboIdSelecionado = ref(null)

const columns = [
  {
    name: 'numero',
    label: 'Número',
    field: 'numero',
    align: 'left',
    sortable: true,
  },
  {
    name: 'data_emissao',
    label: 'Data de Emissão',
    field: 'data_emissao',
    align: 'left',
    format: (val) => date.formatDate(val, 'DD/MM/YYYY HH:mm'),
    sortable: true,
  },
  {
    name: 'valor_total',
    label: 'Valor Total',
    field: 'valor_total',
    align: 'right',
  },
  {
    name: 'valor_pago',
    label: 'Valor Pago',
    field: 'valor_pago',
    align: 'right',
  },
  {
    name: 'acoes',
    label: 'Ações',
    field: 'acoes',
    align: 'center',
  },
]

const podeGerarRecibo = computed(() => {
  return props.protocolo.valor_pago > 0 || props.protocolo.valor_isento >= props.protocolo.valor_total
})

onMounted(() => {
  carregarRecibos()
})

async function carregarRecibos() {
  carregando.value = true
  try {
    const { data } = await api.get('/recibo', {
      params: { protocolo_id: props.protocolo.id },
    })

    if (data.sucesso) {
      recibos.value = data.dados?.data || data.dados || []
    }
  } catch (error) {
    console.error('Erro ao carregar recibos:', error)
  } finally {
    carregando.value = false
  }
}

async function gerarRecibo() {
  gerando.value = true
  try {
    const { data } = await api.post(`/protocolo/${props.protocolo.id}/recibo`)

    if (data.sucesso) {
      $q.notify({
        type: 'positive',
        message: 'Recibo gerado com sucesso',
        position: 'top',
      })
      await carregarRecibos()
      emit('atualizar')

      // Perguntar se deseja visualizar
      $q.dialog({
        title: 'Recibo Gerado',
        message: 'Deseja visualizar o recibo agora?',
        cancel: {
          label: 'Não',
          flat: true,
        },
        ok: {
          label: 'Preview HTML',
          color: 'primary',
        },
        options: {
          type: 'radio',
          model: 'preview',
          items: [
            { label: 'Preview HTML', value: 'preview' },
            { label: 'Download PDF', value: 'pdf' }
          ]
        },
        persistent: false,
      }).onOk((tipo) => {
        if (tipo === 'pdf') {
          downloadPDF(data.dados)
        } else {
          previewRecibo(data.dados)
        }
      })
    }
  } catch (error) {
    $q.notify({
      type: 'negative',
      message: error.response?.data?.mensagem || 'Erro ao gerar recibo',
      position: 'top',
    })
  } finally {
    gerando.value = false
  }
}

function previewRecibo(recibo) {
  reciboIdSelecionado.value = recibo.id
  modalPreview.value = true
}

async function downloadPDF() {
  $q.notify({
    type: 'info',
    message: 'Funcionalidade de PDF em desenvolvimento',
    position: 'top',
  })
}

function visualizarRecibo(recibo) {
  previewRecibo(recibo)
}
</script>

<style lang="scss" scoped>
.protocolo-recibos-tab {
  padding: 24px;
}

.info-item {
  padding: 8px 0;
  line-height: 1.6;
}
</style>
