<template>
  <div class="protocolo-financeiro-tab">
    <!-- Cards de Resumo -->
    <div class="resumo-cards">
      <div class="resumo-card resumo-card--total">
        <div class="card-label">Total</div>
        <div class="card-valor">{{ formatarDinheiroBrasil(protocolo.valor_total) }}</div>
      </div>

      <div class="resumo-card resumo-card--pago">
        <div class="card-label">Pago</div>
        <div class="card-valor">{{ formatarDinheiroBrasil(protocolo.valor_pago) }}</div>
      </div>

      <div class="resumo-card resumo-card--desconto">
        <div class="card-label">Desconto</div>
        <div class="card-valor">{{ formatarDinheiroBrasil(protocolo.valor_desconto) }}</div>
      </div>

      <div class="resumo-card resumo-card--restante">
        <div class="card-label">Restante</div>
        <div class="card-valor">{{ formatarDinheiroBrasil(valorRestante) }}</div>
      </div>
    </div>

    <!-- Tabela de Pagamentos -->
    <q-card flat bordered class="q-mt-md">
      <q-card-section>
        <div class="row items-center justify-between q-mb-md">
          <div class="text-h6">Pagamentos Recebidos</div>
          <div class="q-gutter-sm">
            <q-btn
              v-if="protocolo.valor_pago > 0"
              color="primary"
              icon="eva-printer-outline"
              :label="labelBotaoRecibo"
              size="sm"
              unelevated
              no-caps
              :loading="gerandoRecibo"
              @click="gerarRecibo"
            />
            <q-btn
              v-if="valorRestante > 0"
              color="green"
              icon="add"
              label="Receber Pagamento"
              size="sm"
              unelevated
              no-caps
              :loading="carregandoDados"
              @click="abrirModalPagamento"
            />
          </div>
        </div>

        <q-table
          :rows="protocolo.pagamentos || []"
          :columns="pagamentosColumns"
          row-key="id"
          flat
          selection="multiple"
          v-model:selected="pagamentosSelecionados"
        >
          <template v-slot:body-cell-valor="props">
            <q-td :props="props">
              <span class="text-weight-bold text-green-8">{{ formatarDinheiroBrasil(props.value) }}</span>
            </q-td>
          </template>

          <template v-slot:body-cell-comprovante="props">
            <q-td :props="props">
              <div v-if="props.row.arquivos && props.row.arquivos.length > 0" class="q-gutter-xs">
                <q-btn
                  v-for="arquivo in props.row.arquivos"
                  :key="arquivo.id"
                  flat
                  dense
                  size="sm"
                  icon="eva-attach-outline"
                  :color="arquivo.ePdf || arquivo.eImagem ? 'primary' : 'grey-6'"
                  @click="baixarArquivo(arquivo)"
                >
                  <q-tooltip>{{ arquivo.nome_original }}</q-tooltip>
                </q-btn>
              </div>
              <span v-else class="text-grey-5">-</span>
            </q-td>
          </template>

          <template v-slot:body-cell-status="props">
            <q-td :props="props">
              <q-chip :color="props.value === 'confirmado' ? 'green' : 'orange'" text-color="white" size="sm">
                {{ props.value }}
              </q-chip>
            </q-td>
          </template>

          <template v-slot:body-cell-actions="props">
            <q-td :props="props">
              <q-btn
                v-if="props.row.status === 'confirmado'"
                flat
                round
                dense
                size="sm"
                icon="eva-printer-outline"
                color="primary"
                @click="gerarReciboIndividual(props.row)"
              >
                <q-tooltip>Gerar Recibo</q-tooltip>
              </q-btn>
              <q-btn v-if="props.row.status === 'confirmado'" flat round dense size="sm" icon="eva-undo-outline" color="orange"
                @click="confirmarEstorno(props.row)">
                <q-tooltip>Estornar</q-tooltip>
              </q-btn>
              <q-btn v-if="isAdmin" flat round dense size="sm" icon="eva-trash-2-outline" color="negative"
                @click="confirmarExclusao(props.row)">
                <q-tooltip>Excluir (Admin)</q-tooltip>
              </q-btn>
            </q-td>
          </template>

          <template v-slot:no-data>
            <div class="full-width row flex-center q-gutter-sm text-grey-6 q-pa-lg">
              <l-icon name="inbox" :size="48" />
              <span>Nenhum pagamento registrado</span>
            </div>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <!-- Modal Receber Pagamento -->
    <q-dialog v-model="modalPagamento">
      <q-card style="min-width: 500px">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">Receber Pagamento</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section>
          <div class="q-gutter-md">
            <div>
              <v-label label="Valor" obrigatorio />
              <q-input v-model.number="formPagamento.valor" type="number" outlined dense prefix="R$"
                min="0.01" step="0.01" placeholder="0,00" />
            </div>

            <div>
              <v-label label="Forma de Pagamento" obrigatorio />
              <q-select
                v-model="formPagamento.forma_pagamento_id"
                :options="formasPagamento"
                option-value="id"
                option-label="nome"
                emit-value
                map-options
                outlined
                dense
                placeholder="Selecione a forma"
                :loading="formaPagamentoStore.carregando"
              >
                <template v-slot:no-option>
                  <q-item>
                    <q-item-section class="text-grey">
                      {{ formaPagamentoStore.carregando ? 'Carregando...' : 'Nenhuma forma encontrada' }}
                    </q-item-section>
                  </q-item>
                </template>
              </q-select>
            </div>

            <div>
              <v-label label="Meio de Pagamento" />
              <q-select
                v-model="formPagamento.meio_pagamento_id"
                :options="meiosPagamento"
                option-value="id"
                option-label="nome"
                emit-value
                map-options
                outlined
                dense
                placeholder="Selecione o meio"
                :loading="meioPagamentoStore.carregando"
                :disable="!formPagamento.forma_pagamento_id"
              >
                <template v-slot:no-option>
                  <q-item>
                    <q-item-section class="text-grey">
                      {{ meioPagamentoStore.carregando ? 'Carregando...' : !formPagamento.forma_pagamento_id ? 'Selecione uma forma primeiro' : 'Nenhum meio encontrado' }}
                    </q-item-section>
                  </q-item>
                </template>
              </q-select>
            </div>

            <div>
              <v-label label="Comprovante (Número/Referência)" />
              <q-input v-model="formPagamento.comprovante" outlined dense placeholder="Número do comprovante" />
            </div>

            <div>
              <v-label label="Anexar Comprovante (Imagem ou PDF)" />
              <q-file
                v-model="formPagamento.arquivo"
                outlined
                dense
                accept="image/*,application/pdf"
                max-file-size="5242880"
                @rejected="onArquivoRejeitado"
                clearable
              >
                <template v-slot:prepend>
                  <q-icon name="attach_file" />
                </template>
                <template v-slot:hint>
                  Formatos aceitos: JPG, PNG, PDF (máx. 5MB)
                </template>
              </q-file>
            </div>

            <div>
              <v-label label="Observação" />
              <q-input v-model="formPagamento.observacao" type="textarea" outlined rows="3"
                placeholder="Observações sobre o pagamento" />
            </div>
          </div>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn label="Cancelar" flat color="grey-7" v-close-popup />
          <q-btn label="Confirmar Pagamento" color="primary" unelevated no-caps :loading="salvandoPagamento"
            :disable="!formPagamento.valor || !formPagamento.forma_pagamento_id" @click="salvarPagamento" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Modal de preview do recibo -->
    <recibo-preview-modal
      v-model="modalReciboPreview"
      :recibo-id="reciboIdSelecionado"
      :protocolo-id="protocolo.id"
    />
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useQuasar, date } from 'quasar'
import { useProtocoloStore } from 'src/stores/protocolo'
import { useFormaPagamentoStore } from 'src/stores/formaPagamento'
import { useMeioPagamentoStore } from 'src/stores/meioPagamento'
import { usePermissao } from 'src/composables/usePermissao'
import { api } from 'src/boot/axios'
import { formatarDinheiroBrasil } from 'src/Utils'
import ReciboPreviewModal from 'src/components/recibo/ReciboPreviewModal.vue'

defineOptions({ name: 'ProtocoloFinanceiroTab' })

const props = defineProps({
  protocolo: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['atualizar'])

const $q = useQuasar()
const protocoloStore = useProtocoloStore()
const formaPagamentoStore = useFormaPagamentoStore()
const meioPagamentoStore = useMeioPagamentoStore()
const { isAdmin } = usePermissao()

const modalPagamento = ref(false)
const salvandoPagamento = ref(false)
const carregandoDados = ref(false)
const gerandoRecibo = ref(false)
const modalReciboPreview = ref(false)
const reciboIdSelecionado = ref(null)
const pagamentosSelecionados = ref([])

const formPagamento = reactive({
  valor: 0,
  forma_pagamento_id: null,
  meio_pagamento_id: null,
  comprovante: '',
  observacao: '',
  arquivo: null,
})

const valorRestante = computed(() => {
  return (props.protocolo.valor_final || 0) - (props.protocolo.valor_pago || 0)
})

const labelBotaoRecibo = computed(() => {
  if (pagamentosSelecionados.value.length > 0) {
    return `Gerar Recibo (${pagamentosSelecionados.value.length})`
  }
  return 'Gerar Recibo (Todos)'
})

// Computed para as listas de formas e meios de pagamento
const formasPagamento = computed(() => formaPagamentoStore.formasAtivas)
const meiosPagamento = computed(() => {
  // Filtra meios de pagamento pela forma selecionada
  if (formPagamento.forma_pagamento_id) {
    return meioPagamentoStore.porFormaPagamento(formPagamento.forma_pagamento_id)
  }
  return meioPagamentoStore.meiosAtivos
})

const pagamentosColumns = [
  {
    name: 'data_pagamento',
    label: 'Data',
    field: 'data_pagamento',
    align: 'left',
    format: (val) => date.formatDate(val, 'DD/MM/YYYY HH:mm'),
  },
  {
    name: 'forma',
    label: 'Forma',
    field: (row) => row.formaPagamento?.nome || '-',
    align: 'left',
  },
  {
    name: 'meio',
    label: 'Meio',
    field: (row) => row.meioPagamento?.nome || '-',
    align: 'left',
  },
  {
    name: 'valor',
    label: 'Valor',
    field: 'valor',
    align: 'right',
  },
  {
    name: 'comprovante',
    label: 'Comprovante',
    field: 'arquivos',
    align: 'center',
  },
  {
    name: 'status',
    label: 'Status',
    field: 'status',
    align: 'center',
  },
  {
    name: 'actions',
    label: 'Ações',
    field: 'actions',
    align: 'center',
  },
]

async function abrirModalPagamento() {
  carregandoDados.value = true

  // Limpar formulário
  formPagamento.valor = valorRestante.value
  formPagamento.forma_pagamento_id = null
  formPagamento.meio_pagamento_id = null
  formPagamento.comprovante = ''
  formPagamento.observacao = ''
  formPagamento.arquivo = null

  try {
    // Carregar formas e meios de pagamento das stores
    await Promise.all([
      formaPagamentoStore.listar(),
      meioPagamentoStore.listar()
    ])

    // Só abre o modal depois de carregar os dados
    modalPagamento.value = true
  } catch (error) {
    $q.notify({
      type: 'negative',
      message: 'Erro ao carregar dados de pagamento',
      position: 'top',
    })
  } finally {
    carregandoDados.value = false
  }
}

function onArquivoRejeitado(rejectedEntries) {
  const rejection = rejectedEntries[0]
  if (rejection.failedPropValidation === 'max-file-size') {
    $q.notify({
      type: 'negative',
      message: 'Arquivo muito grande. Tamanho máximo: 5MB',
      position: 'top',
    })
  } else if (rejection.failedPropValidation === 'accept') {
    $q.notify({
      type: 'negative',
      message: 'Formato de arquivo não aceito. Use JPG, PNG ou PDF',
      position: 'top',
    })
  }
}

// Watch para resetar meio de pagamento quando forma mudar
watch(() => formPagamento.forma_pagamento_id, (novaForma, formaAnterior) => {
  // Se mudou a forma de pagamento, limpa o meio selecionado
  if (novaForma !== formaAnterior && formaAnterior !== undefined) {
    formPagamento.meio_pagamento_id = null
  }
})

// Pré-carregar formas e meios de pagamento ao montar o componente
onMounted(async () => {
  // Carrega em background para não bloquear a UI
  try {
    await Promise.all([
      formaPagamentoStore.listar(),
      meioPagamentoStore.listar()
    ])
  } catch (error) {
    console.error('Erro ao pré-carregar dados de pagamento:', error)
  }
})

async function salvarPagamento() {
  salvandoPagamento.value = true
  try {
    // Preparar FormData para enviar arquivo
    const formData = new FormData()
    formData.append('valor', formPagamento.valor)
    formData.append('forma_pagamento_id', formPagamento.forma_pagamento_id)
    formData.append('data_pagamento', new Date().toISOString())

    if (formPagamento.meio_pagamento_id) {
      formData.append('meio_pagamento_id', formPagamento.meio_pagamento_id)
    }
    if (formPagamento.comprovante) {
      formData.append('comprovante', formPagamento.comprovante)
    }
    if (formPagamento.observacao) {
      formData.append('observacao', formPagamento.observacao)
    }
    if (formPagamento.arquivo) {
      formData.append('arquivo', formPagamento.arquivo)
    }

    const { data } = await api.post(`/protocolo/${props.protocolo.id}/pagamento`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })

    if (data.sucesso) {
      $q.notify({
        type: 'positive',
        message: 'Pagamento registrado com sucesso',
        position: 'top',
      })
      modalPagamento.value = false
      emit('atualizar')
    }
  } catch (error) {
    $q.notify({
      type: 'negative',
      message: error.response?.data?.mensagem || 'Erro ao registrar pagamento',
      position: 'top',
    })
  } finally {
    salvandoPagamento.value = false
  }
}

function confirmarEstorno(pagamento) {
  $q.dialog({
    title: 'Confirmar Estorno',
    message: `Deseja estornar o pagamento de ${formatarDinheiroBrasil(pagamento.valor)}?`,
    prompt: {
      model: '',
      type: 'text',
      label: 'Motivo do estorno *',
      isValid: (val) => val && val.length > 3,
    },
    cancel: true,
    persistent: true,
  }).onOk(async (motivo) => {
    await estornarPagamento(pagamento, motivo)
  })
}

async function estornarPagamento(pagamento, motivo) {
  try {
    const resultado = await protocoloStore.estornarPagamento(props.protocolo.id, pagamento.id, motivo)

    if (resultado.sucesso) {
      $q.notify({
        type: 'positive',
        message: 'Pagamento estornado com sucesso',
        position: 'top',
      })
      emit('atualizar')
    }
  } catch (error) {
    $q.notify({
      type: 'negative',
      message: error.response?.data?.mensagem || 'Erro ao estornar pagamento',
      position: 'top',
    })
  }
}

async function gerarRecibo() {
  if (props.protocolo.valor_pago <= 0) {
    $q.notify({
      type: 'warning',
      message: 'Não há pagamentos confirmados para gerar recibo',
      position: 'top',
    })
    return
  }

  gerandoRecibo.value = true
  try {
    // Se houver pagamentos selecionados, usa apenas eles
    const payload = {}
    if (pagamentosSelecionados.value.length > 0) {
      payload.pagamento_ids = pagamentosSelecionados.value.map(p => p.id)
    }

    const { data } = await api.post(`/protocolo/${props.protocolo.id}/recibo`, payload)

    if (data.sucesso) {
      const reciboJaExistia = data.dados.ja_existia

      $q.notify({
        type: 'positive',
        message: reciboJaExistia ? 'Recibo já existe para estes pagamentos' : 'Recibo gerado com sucesso',
        position: 'top',
      })

      // Limpar seleção
      pagamentosSelecionados.value = []
      emit('atualizar')

      // Perguntar se deseja visualizar
      $q.dialog({
        title: reciboJaExistia ? 'Recibo Encontrado' : 'Recibo Gerado',
        message: 'Deseja visualizar o recibo agora?',
        cancel: {
          label: 'Não',
          flat: true,
        },
        ok: {
          label: 'Sim, visualizar',
          color: 'primary',
        },
        persistent: false,
      }).onOk(() => {
        // Abrir modal de preview
        reciboIdSelecionado.value = data.dados.id
        modalReciboPreview.value = true
      })
    }
  } catch (error) {
    $q.notify({
      type: 'negative',
      message: error.response?.data?.mensagem || 'Erro ao gerar recibo',
      position: 'top',
    })
  } finally {
    gerandoRecibo.value = false
  }
}

async function gerarReciboIndividual(pagamento) {
  gerandoRecibo.value = true
  try {
    const payload = {
      pagamento_ids: [pagamento.id]
    }

    const { data } = await api.post(`/protocolo/${props.protocolo.id}/recibo`, payload)

    if (data.sucesso) {
      const reciboJaExistia = data.dados.ja_existia

      $q.notify({
        type: 'positive',
        message: reciboJaExistia ? 'Recibo já existe para este pagamento' : 'Recibo individual gerado com sucesso',
        position: 'top',
      })

      emit('atualizar')

      // Perguntar se deseja visualizar
      $q.dialog({
        title: reciboJaExistia ? 'Recibo Encontrado' : 'Recibo Gerado',
        message: 'Deseja visualizar o recibo agora?',
        cancel: {
          label: 'Não',
          flat: true,
        },
        ok: {
          label: 'Sim, visualizar',
          color: 'primary',
        },
        persistent: false,
      }).onOk(() => {
        // Abrir modal de preview
        reciboIdSelecionado.value = data.dados.id
        modalReciboPreview.value = true
      })
    }
  } catch (error) {
    $q.notify({
      type: 'negative',
      message: error.response?.data?.mensagem || 'Erro ao gerar recibo',
      position: 'top',
    })
  } finally {
    gerandoRecibo.value = false
  }
}

function baixarArquivo(arquivo) {
  // Abrir o arquivo em nova aba (download ou visualização)
  const url = `${api.defaults.baseURL}/arquivo/${arquivo.uuid}/download`
  const token = localStorage.getItem('access_token')

  // Criar link temporário com token na URL para autenticação
  window.open(`${url}?token=${token}`, '_blank')
}

function confirmarExclusao(pagamento) {
  $q.dialog({
    title: 'Confirmar Exclusão',
    message: `ATENÇÃO: Esta ação é irreversível! Deseja excluir permanentemente o pagamento de ${formatarDinheiroBrasil(pagamento.valor)}?`,
    prompt: {
      model: '',
      type: 'text',
      label: 'Digite "EXCLUIR" para confirmar *',
      isValid: (val) => val === 'EXCLUIR',
    },
    cancel: true,
    persistent: true,
    color: 'negative',
  }).onOk(async () => {
    await excluirPagamento(pagamento)
  })
}

async function excluirPagamento(pagamento) {
  try {
    const { data } = await api.delete(`/protocolo/${props.protocolo.id}/pagamento/${pagamento.id}`)

    if (data.sucesso) {
      $q.notify({
        type: 'positive',
        message: 'Pagamento excluído com sucesso',
        position: 'top',
      })
      emit('atualizar')
    }
  } catch (error) {
    $q.notify({
      type: 'negative',
      message: error.response?.data?.mensagem || 'Erro ao excluir pagamento',
      position: 'top',
    })
  }
}
</script>

<style lang="scss" scoped>
.protocolo-financeiro-tab {
  padding: 24px;
}

.resumo-cards {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;

  @media (max-width: 1024px) {
    grid-template-columns: repeat(2, 1fr);
  }

  @media (max-width: 480px) {
    grid-template-columns: 1fr;
  }
}

.resumo-card {
  padding: 20px;
  border-radius: var(--radius-md);
  border: 1px solid var(--border-color);

  &--total {
    background: #F8F9FA;
  }

  &--pago {
    background: #F0FDF4;
  }

  &--desconto {
    background: #FEF3C7;
  }

  &--restante {
    background: #FFF7F0;
  }

  .card-label {
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
    margin-bottom: 8px;
  }

  .card-valor {
    font-size: var(--font-size-xl);
    font-weight: 700;

    .resumo-card--total & {
      color: #202124;
    }

    .resumo-card--pago & {
      color: #059669;
    }

    .resumo-card--desconto & {
      color: #D97706;
    }

    .resumo-card--restante & {
      color: var(--primary);
    }
  }
}
</style>