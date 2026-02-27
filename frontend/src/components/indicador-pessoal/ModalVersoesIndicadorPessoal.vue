<template>
  <modal
    titulo="Histórico de Versões"
    v-model="model"
    tamanho="lg"
    show-maximize-btn
  >
    <q-inner-loading :showing="carregando" />

    <div v-if="!carregando && versoes.length === 0" class="text-center q-py-xl text-grey-5">
      <q-icon name="history" size="xl" />
      <div class="q-mt-sm">Nenhum registro encontrado.</div>
    </div>

    <q-timeline v-if="!carregando && versoes.length" color="primary">
      <q-timeline-entry
        v-for="versao in versoes"
        :key="versao.id"
        :title="`Versão ${versao.versao}${versao.is_atual ? ' — atual' : ''}`"
        :subtitle="formatarData(versao.data_versao)"
        :color="versao.is_atual ? 'positive' : 'grey-4'"
        :icon="versao.is_atual ? 'star' : 'history'"
      >
        <q-card flat :bordered="versao.is_atual">
          <q-card-section>
            <div class="row q-col-gutter-sm items-center">
              <div class="col-12">
                <span class="text-weight-medium">{{ versao.nome }}</span>
                <q-badge
                  :color="versao.tipo_pessoa === 'F' ? 'blue-6' : 'orange-8'"
                  :label="versao.tipo_pessoa === 'F' ? 'PF' : 'PJ'"
                  class="q-ml-sm"
                />
                <q-badge v-if="versao.is_atual" color="positive" label="Atual" class="q-ml-xs" />
              </div>

              <div v-if="versao.motivo_versao" class="col-12 text-grey-7 text-caption">
                <q-icon name="info" size="xs" class="q-mr-xs" />
                {{ versao.motivo_versao }}
              </div>

              <div class="col-auto text-grey-6 text-caption">
                {{ formatarDoc(versao.cpf_cnpj) }}
              </div>
              <div v-if="versao.ficha" class="col-auto text-grey-6 text-caption">
                Ficha: {{ versao.ficha }}
              </div>
              <div v-if="versao.estado_civil" class="col-auto text-grey-6 text-caption">
                {{ versao.estado_civil.descricao }}
              </div>
            </div>
          </q-card-section>

          <q-card-actions>
            <!-- Editar: apenas a versão atual -->
            <q-btn
              v-if="versao.is_atual"
              flat
              dense
              size="sm"
              icon="edit"
              label="Editar"
              color="primary"
              @click="editarAtual(versao.id)"
            />
          </q-card-actions>
        </q-card>
      </q-timeline-entry>
    </q-timeline>

    <!-- Modal de edição aninhado -->
    <ModalIndicadorPessoal
      v-model="modalEdicaoAberto"
      :id="idEdicao"
      @salvo="aoSalvar"
    />
  </modal>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useQuasar } from 'quasar'
import { useIndicadorPessoalStore } from 'src/stores/indicador-pessoal'
import ModalIndicadorPessoal from './ModalIndicadorPessoal.vue'

const props = defineProps({
  cpfCnpj: {
    type: String,
    default: null,
  },
})

const emit = defineEmits(['atualizado'])

const model = defineModel({ default: false })

const $q = useQuasar()
const store = useIndicadorPessoalStore()

const versoes = ref([])
const carregando = ref(false)
const modalEdicaoAberto = ref(false)
const idEdicao = ref(null)

watch(model, async (aberto) => {
  if (aberto && props.cpfCnpj) {
    await carregar()
  }
})

async function carregar() {
  carregando.value = true
  versoes.value = await store.fetchVersoes(props.cpfCnpj)
  carregando.value = false
}

function editarAtual(id) {
  idEdicao.value = id
  modalEdicaoAberto.value = true
}

function aoSalvar() {
  carregar()
  emit('atualizado')
}

function formatarDoc(doc) {
  if (!doc) return ''
  const d = doc.replace(/\D/g, '')
  if (d.length === 11) return d.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4')
  return d.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5')
}

function formatarData(data) {
  if (!data) return ''
  return new Date(data).toLocaleString('pt-BR', { dateStyle: 'short', timeStyle: 'short' })
}
</script>
