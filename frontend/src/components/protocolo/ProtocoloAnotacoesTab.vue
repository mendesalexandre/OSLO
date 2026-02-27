<template>
  <div class="protocolo-anotacoes-tab">
    <!-- Card de nova anotação -->
    <q-card flat bordered class="q-mb-md">
      <q-card-section>
        <div class="text-subtitle2 q-mb-md">Nova Anotação</div>
        <q-input
          v-model="novaAnotacao"
          type="textarea"
          outlined
          rows="3"
          placeholder="Digite sua anotação aqui..."
          :loading="salvando"
        />
        <div class="row justify-end q-mt-sm">
          <q-btn
            label="Adicionar Anotação"
            color="primary"
            unelevated
            no-caps
            size="sm"
            :loading="salvando"
            :disable="!novaAnotacao.trim()"
            @click="adicionarAnotacao"
          />
        </div>
      </q-card-section>
    </q-card>

    <!-- Lista de anotações -->
    <q-card flat bordered>
      <q-card-section>
        <div class="text-h6 q-mb-md">Histórico de Anotações</div>

        <q-timeline v-if="anotacoes.length > 0" color="primary" layout="comfortable">
          <q-timeline-entry
            v-for="anotacao in anotacoes"
            :key="anotacao.id"
            :subtitle="formatarData(anotacao.data)"
          >
            <template v-slot:title>
              <div class="row items-center justify-between">
                <div class="text-weight-medium">
                  {{ anotacao.usuario?.nome || 'Usuário' }}
                  <q-chip
                    v-if="anotacao.etapa"
                    :style="{ backgroundColor: anotacao.etapa.cor }"
                    text-color="white"
                    size="sm"
                    dense
                    class="q-ml-sm"
                  >
                    {{ anotacao.etapa.nome }}
                  </q-chip>
                </div>
                <div v-if="podeEditar(anotacao)" class="q-gutter-xs">
                  <q-btn
                    flat
                    dense
                    round
                    size="sm"
                    icon="eva-edit-2-outline"
                    color="primary"
                    @click="editarAnotacao(anotacao)"
                  >
                    <q-tooltip>Editar</q-tooltip>
                  </q-btn>
                  <q-btn
                    flat
                    dense
                    round
                    size="sm"
                    icon="eva-trash-2-outline"
                    color="negative"
                    @click="confirmarExclusao(anotacao)"
                  >
                    <q-tooltip>Excluir</q-tooltip>
                  </q-btn>
                </div>
              </div>
            </template>

            <div class="anotacao-conteudo">
              {{ anotacao.anotacao }}
            </div>

            <div v-if="anotacao.data_alteracao" class="text-caption text-grey-6 q-mt-xs">
              Editado em {{ formatarData(anotacao.data_alteracao) }}
            </div>
          </q-timeline-entry>
        </q-timeline>

        <div v-else class="text-center q-pa-lg text-grey-6">
          <l-icon name="message-square" :size="48" class="q-mb-md" />
          <div>Nenhuma anotação registrada</div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Modal de edição -->
    <q-dialog v-model="modalEdicao">
      <q-card style="min-width: 500px">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">Editar Anotação</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section>
          <q-input
            v-model="anotacaoEditando.anotacao"
            type="textarea"
            outlined
            rows="5"
            placeholder="Digite a anotação..."
          />
        </q-card-section>

        <q-card-actions align="right">
          <q-btn label="Cancelar" flat color="grey-7" v-close-popup />
          <q-btn
            label="Salvar"
            color="primary"
            unelevated
            no-caps
            :loading="salvandoEdicao"
            :disable="!anotacaoEditando.anotacao?.trim()"
            @click="salvarEdicao"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useQuasar, date } from 'quasar'
import { api } from 'src/boot/axios'
import { useAuthStore } from 'src/stores/auth'

defineOptions({ name: 'ProtocoloAnotacoesTab' })

const props = defineProps({
  protocolo: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['atualizar'])

const $q = useQuasar()
const authStore = useAuthStore()

const anotacoes = ref([])
const novaAnotacao = ref('')
const salvando = ref(false)
const modalEdicao = ref(false)
const salvandoEdicao = ref(false)
const anotacaoEditando = ref({})

onMounted(() => {
  carregarAnotacoes()
})

async function carregarAnotacoes() {
  try {
    const { data } = await api.get(`/protocolo/${props.protocolo.id}/anotacao`)
    if (data.sucesso) {
      anotacoes.value = data.dados
    }
  } catch (error) {
    console.error('Erro ao carregar anotações:', error)
  }
}

async function adicionarAnotacao() {
  if (!novaAnotacao.value.trim()) return

  salvando.value = true
  try {
    const { data } = await api.post(`/protocolo/${props.protocolo.id}/anotacao`, {
      anotacao: novaAnotacao.value,
      etapa_id: props.protocolo.etapa_id,
    })

    if (data.sucesso) {
      $q.notify({
        type: 'positive',
        message: 'Anotação adicionada com sucesso',
        position: 'top',
      })
      novaAnotacao.value = ''
      await carregarAnotacoes()
      emit('atualizar')
    }
  } catch (error) {
    $q.notify({
      type: 'negative',
      message: error.response?.data?.mensagem || 'Erro ao adicionar anotação',
      position: 'top',
    })
  } finally {
    salvando.value = false
  }
}

function editarAnotacao(anotacao) {
  anotacaoEditando.value = { ...anotacao }
  modalEdicao.value = true
}

async function salvarEdicao() {
  salvandoEdicao.value = true
  try {
    const { data } = await api.put(
      `/protocolo/${props.protocolo.id}/anotacao/${anotacaoEditando.value.id}`,
      {
        anotacao: anotacaoEditando.value.anotacao,
      }
    )

    if (data.sucesso) {
      $q.notify({
        type: 'positive',
        message: 'Anotação atualizada com sucesso',
        position: 'top',
      })
      modalEdicao.value = false
      await carregarAnotacoes()
    }
  } catch (error) {
    $q.notify({
      type: 'negative',
      message: error.response?.data?.mensagem || 'Erro ao atualizar anotação',
      position: 'top',
    })
  } finally {
    salvandoEdicao.value = false
  }
}

function confirmarExclusao(anotacao) {
  $q.dialog({
    title: 'Confirmar Exclusão',
    message: 'Deseja excluir esta anotação?',
    cancel: true,
    persistent: true,
  }).onOk(async () => {
    await excluirAnotacao(anotacao)
  })
}

async function excluirAnotacao(anotacao) {
  try {
    const { data } = await api.delete(
      `/protocolo/${props.protocolo.id}/anotacao/${anotacao.id}`
    )

    if (data.sucesso) {
      $q.notify({
        type: 'positive',
        message: 'Anotação excluída com sucesso',
        position: 'top',
      })
      await carregarAnotacoes()
    }
  } catch (error) {
    $q.notify({
      type: 'negative',
      message: error.response?.data?.mensagem || 'Erro ao excluir anotação',
      position: 'top',
    })
  }
}

function podeEditar(anotacao) {
  return anotacao.usuario_id === authStore.user?.id
}

function formatarData(data) {
  if (!data) return ''
  return date.formatDate(data, 'DD/MM/YYYY HH:mm')
}
</script>

<style lang="scss" scoped>
.protocolo-anotacoes-tab {
  padding: 24px;
}

.anotacao-conteudo {
  white-space: pre-wrap;
  word-break: break-word;
  line-height: 1.6;
}
</style>
