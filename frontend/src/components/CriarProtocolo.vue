<template>
  <modal v-model="model" :titulo="titulo" tamanho="lg" cor-titulo-cabecalho="modal-titulo" @close="onCancelar">
    <!-- Seção Principal -->
    <q-card-section class="q-pa-none">
      <div class="corpo-secao">
        <l-icon name="file-plus" :size="16" class="q-mr-sm" />
        <span class="titulo-secao">Informações do Protocolo</span>
      </div>
    </q-card-section>

    <q-card-section>
      <!-- Tipo de Protocolo -->
      <div class="col-12 grupo-tipo-protocolo">
        <v-label label="Tipo de Protocolo" obrigatorio />
        <q-btn-toggle
          v-model="getOpcaoSelecionada"
          :options="[
            { label: 'Normal',           value: 'NORMAL' },
            { label: 'Orçamento',        value: 'ORCAMENTO' },
            { label: 'Processo Interno', value: 'PROCESSO_INTERNO' },
            { label: 'Exame e Cálculo',  value: 'EXAME_CALCULO' },
          ]"
          spread
          unelevated
          class="tipo-protocolo"
        />
      </div>
    </q-card-section>

    <q-card-section>
      <div class="row q-col-gutter-md">
        <!-- Origem da Solicitação -->
        <div class="col-12">
          <v-label label="Origem da Solicitação" obrigatorio />
          <q-select
            v-model="protocolo.meio_solicitacao_id"
            :options="meiosSolicitacao"
            option-value="id"
            option-label="nome"
            outlined
            dense
            emit-value
            map-options
            placeholder="Selecione a origem"
          >
            <template v-slot:selected-item="scope">
              <q-item v-bind="scope.itemProps">
                <q-item-section>
                  <q-item-label>
                    <q-chip icon-remove="none">{{ scope.opt.nome }}</q-chip>
                  </q-item-label>
                </q-item-section>
              </q-item>
            </template>
            <template v-slot:option="scope">
              <q-item v-bind="scope.itemProps">
                <q-item-section>
                  <q-item-label>{{ scope.opt.nome }}</q-item-label>
                </q-item-section>
              </q-item>
            </template>
          </q-select>
        </div>

        <!-- Natureza Formal do Título -->
        <div class="col-12">
          <v-label label="Natureza Formal do Título" obrigatorio />
          <q-select
            ref="selectNatureza"
            v-model="naturezaSelecionada"
            :options="opcoesNatureza"
            option-value="id"
            option-label="nome"
            use-input
            outlined
            dense
            input-debounce="300"
            @filter="filtrarNatureza"
            :loading="carregandoNatureza"
            hide-dropdown-icon
            :placeholder="naturezaSelecionada ? '' : 'Digite para buscar a natureza...'"
            clearable
            @update:model-value="removerFoco"
          >
            <template v-slot:selected-item="scope">
              <q-item v-bind="scope.itemProps">
                <q-item-section>
                  <q-item-label>
                    <q-chip icon-remove="none">{{ scope.opt.nome }}</q-chip>
                  </q-item-label>
                </q-item-section>
              </q-item>
            </template>
            <template v-slot:option="scope">
              <q-item v-bind="scope.itemProps">
                <q-item-section>
                  <q-item-label>{{ scope.opt.nome }}</q-item-label>
                </q-item-section>
              </q-item>
            </template>
            <template v-slot:no-option>
              <q-item>
                <q-item-section class="text-center">
                  <div class="text-grey-5 q-py-md">
                    <l-icon name="search" :size="32" />
                    <div class="q-mt-sm">Nenhuma natureza encontrada</div>
                    <div class="text-caption">Digite pelo menos 2 caracteres</div>
                  </div>
                </q-item-section>
              </q-item>
            </template>
          </q-select>
        </div>

        <!-- Estado -->
        <div class="col-md-4 col-12">
          <v-label label="Estado" obrigatorio />
          <q-select
            v-model="protocolo.estado_id"
            :options="estadoStore.estados"
            option-value="id"
            option-label="nome"
            outlined
            dense
            emit-value
            map-options
            placeholder="Selecione o estado"
          >
            <template v-slot:selected-item="scope">
              <q-item v-bind="scope.itemProps">
                <q-item-section>
                  <q-item-label>
                    <q-chip icon-remove="none">{{ scope.opt.nome }}</q-chip>
                  </q-item-label>
                </q-item-section>
              </q-item>
            </template>
            <template v-slot:option="scope">
              <q-item v-bind="scope.itemProps">
                <q-item-section>
                  <q-item-label>{{ scope.opt.nome }}</q-item-label>
                  <q-item-label caption>{{ scope.opt.sigla }}</q-item-label>
                </q-item-section>
              </q-item>
            </template>
            <template v-slot:no-option>
              <q-item>
                <q-item-section class="text-center text-grey">
                  <l-icon name="alert-circle" :size="32" class="q-mb-sm" />
                  <div>Nenhum estado encontrado</div>
                </q-item-section>
              </q-item>
            </template>
          </q-select>
        </div>

        <!-- Solicitante -->
        <div class="col-12">
          <v-label label="Solicitante" obrigatorio />
          <div class="row q-gutter-sm">
            <div class="col">
              <q-select
                v-model="solicitanteSelecionado"
                :options="opcoesCliente"
                option-value="id"
                option-label="nome"
                use-input
                outlined
                dense
                input-debounce="300"
                @filter="filtrarCliente"
                :loading="carregandoCliente"
                hide-dropdown-icon
                placeholder="Digite o nome ou CPF/CNPJ para buscar..."
              >
                <template v-slot:selected-item="scope">
                  <q-item v-bind="scope.itemProps">
                    <q-item-section avatar>
                      <q-avatar size="24px" color="blue-grey-6" text-color="white">
                        {{ scope.opt.nome.charAt(0).toUpperCase() }}
                      </q-avatar>
                    </q-item-section>
                    <q-item-section>
                      <q-item-label class="text-weight-medium text-primary">{{ scope.opt.nome }}</q-item-label>
                      <q-item-label caption class="text-grey-6">{{ scope.opt.cpf_cnpj }}</q-item-label>
                    </q-item-section>
                  </q-item>
                </template>
                <template v-slot:option="scope">
                  <q-item v-bind="scope.itemProps">
                    <q-item-section>
                      <q-item-label class="text-weight-medium">{{ scope.opt.nome }}</q-item-label>
                      <q-item-label caption class="text-grey-6">{{ scope.opt.cpf_cnpj }}</q-item-label>
                    </q-item-section>
                  </q-item>
                </template>
                <template v-slot:no-option>
                  <q-item>
                    <q-item-section class="text-center">
                      <div class="text-grey-5 q-py-md">
                        <l-icon name="search" :size="32" />
                        <div class="q-mt-sm">Nenhum indicador encontrado</div>
                        <div class="text-caption">Digite pelo menos 2 caracteres</div>
                      </div>
                    </q-item-section>
                  </q-item>
                </template>
              </q-select>
            </div>
            <div class="col-auto">
              <q-btn color="grey-5" outline size="8px" class="full-height" @click="abrirNovoIndicador">
                <l-icon name="user-plus" :size="16" />
                <q-tooltip>Adicionar novo indicador pessoal</q-tooltip>
              </q-btn>
            </div>
          </div>
        </div>

        <!-- Interessado -->
        <div class="col-12">
          <v-label label="Interessado" obrigatorio />
          <div class="row q-gutter-sm">
            <div class="col">
              <q-select
                v-model="interessadoSelecionado"
                :options="opcoesCliente"
                option-value="id"
                option-label="nome"
                use-input
                outlined
                dense
                input-debounce="300"
                @filter="filtrarCliente"
                :loading="carregandoCliente"
                hide-dropdown-icon
                placeholder="Digite o nome ou CPF/CNPJ para buscar..."
              >
                <template v-slot:prepend>
                  <l-icon name="user" :size="14" />
                </template>
                <template v-slot:selected-item="scope">
                  <q-item v-bind="scope.itemProps">
                    <q-item-section avatar>
                      <q-avatar size="24px" color="blue-grey-6" text-color="white">
                        {{ scope.opt.nome.charAt(0).toUpperCase() }}
                      </q-avatar>
                    </q-item-section>
                    <q-item-section>
                      <q-item-label class="text-weight-medium text-primary">{{ scope.opt.nome }}</q-item-label>
                      <q-item-label caption class="text-grey-6">{{ scope.opt.cpf_cnpj }}</q-item-label>
                    </q-item-section>
                  </q-item>
                </template>
                <template v-slot:option="scope">
                  <q-item v-bind="scope.itemProps">
                    <q-item-section>
                      <q-item-label class="text-weight-medium">{{ scope.opt.nome }}</q-item-label>
                      <q-item-label caption class="text-grey-6">{{ scope.opt.cpf_cnpj }}</q-item-label>
                    </q-item-section>
                  </q-item>
                </template>
                <template v-slot:no-option>
                  <q-item>
                    <q-item-section class="text-center">
                      <div class="text-grey-5 q-py-md">
                        <l-icon name="search" :size="32" />
                        <div class="q-mt-sm">Nenhum indicador encontrado</div>
                        <div class="text-caption">Digite pelo menos 2 caracteres</div>
                      </div>
                    </q-item-section>
                  </q-item>
                </template>
              </q-select>
            </div>
            <div class="col-auto">
              <q-btn color="grey-5" outline size="8px" class="full-height" @click="abrirNovoIndicador">
                <l-icon name="user-plus" :size="16" />
                <q-tooltip>Adicionar novo indicador pessoal</q-tooltip>
              </q-btn>
            </div>
          </div>
        </div>
      </div>
    </q-card-section>

    <template v-slot:rodape>
      <q-card-section class="bg-grey-3">
        <div class="flex justify-between">
          <q-btn label="Cancelar" color="negative" outline @click="cancelar" :disable="salvando" />
          <q-btn label="Salvar" color="primary" outline @click="salvar" :loading="salvando">
            <template v-slot:loading>
              <q-spinner class="q-mr-sm" />Salvando...
            </template>
          </q-btn>
        </div>
      </q-card-section>
    </template>
  </modal>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { useEstadoStore } from 'src/stores/estado'
import { useNaturezaStore } from 'src/stores/natureza'
import { useIndicadorPessoalStore } from 'src/stores/indicador-pessoal'
import { useProtocoloStore } from 'src/stores/protocolo'

const $q     = useQuasar()
const model  = defineModel({ default: false })
const emit   = defineEmits(['criado'])

const estadoStore           = useEstadoStore()
const naturezaStore         = useNaturezaStore()
const indicadorPessoalStore = useIndicadorPessoalStore()
const protocoloStore        = useProtocoloStore()

const titulo          = ref('Criar Novo Protocolo')
const salvando        = ref(false)
const getOpcaoSelecionada = ref('NORMAL')
const selectNatureza  = ref(null)

const protocolo = ref({
  meio_solicitacao_id: null,
  estado_id:           null,
  natureza_id:         null,
})

const meiosSolicitacao = [
  { id: 1, nome: 'Balcão de Atendimento' },
  { id: 2, nome: 'Online' },
  { id: 3, nome: 'Telefone' },
  { id: 4, nome: 'E-mail' },
]

// Natureza
const naturezaSelecionada  = ref(null)
const opcoesNatureza       = ref([])
const carregandoNatureza   = ref(false)

const filtrarNatureza = async (val, update, abort) => {
  if (val.length < 2) { abort(); return }
  carregandoNatureza.value = true
  const resultados = await naturezaStore.fetchNaturezas(val)
  update(() => {
    opcoesNatureza.value     = resultados
    carregandoNatureza.value = false
  })
}

const removerFoco = () => selectNatureza.value?.blur()

// Cliente (Indicador Pessoal)
const solicitanteSelecionado  = ref(null)
const interessadoSelecionado  = ref(null)
const opcoesCliente           = ref([])
const carregandoCliente       = ref(false)

const filtrarCliente = async (val, update, abort) => {
  if (val.length < 2) { abort(); return }
  carregandoCliente.value = true
  try {
    const resultados = await indicadorPessoalStore.buscar(val)
    update(() => {
      opcoesCliente.value     = resultados
      carregandoCliente.value = false
    })
  } catch {
    update(() => {
      opcoesCliente.value     = []
      carregandoCliente.value = false
    })
  }
}

const abrirNovoIndicador = () => {
  $q.notify({ type: 'info', message: 'Funcionalidade em desenvolvimento.', position: 'top' })
}

// Form
const formValido = () => !!(
  protocolo.value.meio_solicitacao_id &&
  protocolo.value.estado_id &&
  naturezaSelecionada.value &&
  solicitanteSelecionado.value
)

const salvar = async () => {
  if (!formValido()) {
    $q.notify({ type: 'warning', message: 'Preencha todos os campos obrigatórios.', position: 'top-right' })
    return
  }

  salvando.value = true
  try {
    const dados = {
      tipo:                 getOpcaoSelecionada.value,
      meio_solicitacao_id: protocolo.value.meio_solicitacao_id,
      natureza_id:         naturezaSelecionada.value?.id ?? null,
      estado_id:           protocolo.value.estado_id,
      solicitante_nome:    solicitanteSelecionado.value?.nome ?? null,
      solicitante_cpf_cnpj: solicitanteSelecionado.value?.cpf_cnpj ?? null,
    }

    await protocoloStore.criar(dados)

    $q.notify({ type: 'positive', message: 'Protocolo criado com sucesso!', position: 'top-right' })
    model.value = false
    emit('criado')
  } catch (err) {
    const msg = err?.response?.data?.mensagem ?? 'Erro ao criar protocolo.'
    $q.notify({ type: 'negative', message: msg, position: 'top-right' })
  } finally {
    salvando.value = false
  }
}

const cancelar = () => {
  model.value = false
  resetarForm()
}

const onCancelar = () => {
  resetarForm()
}

const resetarForm = () => {
  protocolo.value            = { meio_solicitacao_id: null, estado_id: null, natureza_id: null }
  naturezaSelecionada.value  = null
  solicitanteSelecionado.value = null
  interessadoSelecionado.value = null
  opcoesNatureza.value       = []
  opcoesCliente.value        = []
  getOpcaoSelecionada.value  = 'NORMAL'
}

onMounted(() => estadoStore.fetchEstados())

watch(model, (val) => { if (val) resetarForm() })
</script>

<style lang="scss" scoped>
.corpo-secao {
  display: flex;
  align-items: center;
  padding: 12px 16px;
  background: var(--bg-subtle);

  .titulo-secao {
    font-size: var(--font-size-sm);
    font-weight: 500;
    color: var(--text-color);
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  :deep(svg) {
    color: var(--text-secondary);
  }
}

.grupo-tipo-protocolo {
  :deep(.q-btn) {
    color: var(--text-color);
    border-radius: var(--radius-sm) !important;
    border: 1px solid var(--border-color) !important;

    &.bg-primary {
      border-color: $primary !important;
    }

    &[aria-pressed="true"] {
      border-color: $primary !important;
    }
  }
}

:deep(.q-btn-group) {
  column-gap: 4px !important;
}

:deep(.q-field__focusable-action) {
  font-size: 20px !important;
}
</style>
