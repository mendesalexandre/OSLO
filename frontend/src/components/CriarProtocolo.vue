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
        <q-btn-toggle v-model="getOpcaoSelecionada" :options="[
          { label: 'Normal', value: 'NORMAL' },
          { label: 'Orçamento', value: 'ORCAMENTO' },
          { label: 'Processo Interno', value: 'PROCESSO_INTERNO' },
          { label: 'Exame e Cálculo', value: 'EXAME_CALCULO' }
        ]" spread unelevated class="tipo-protocolo" />
      </div>
    </q-card-section>

    <q-card-section>
      <div class="row q-col-gutter-md">
        <!-- Meio de Solicitação -->
        <div class="col-md-12 col-sm-12 col-xs-12">
          <v-label label="Origem da Solicitação" obrigatorio />
          <q-select v-model="protocolo.meio_solicitacao_id" :options="meiosSolicitacao" option-value="id" dense
            option-label="nome" outlined input-debounce="500" placeholder="Selecione o meio de solicitação" emit-value
            map-options>
            <!-- <template v-slot:prepend>
                <q-icon name="fa-duotone fa-envelope" size="14px" />
              </template> -->

            <template v-slot:selected-item="scope">
              <q-item v-bind="scope.itemProps">
                <q-item-section>
                  <q-item-label>
                    <q-chip icon-remove="none">
                      {{ scope.opt.nome }}
                    </q-chip>
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
                <q-item-section class="text-center text-grey">
                  <div>Nenhum meio de solicitação encontrado</div>
                </q-item-section>
              </q-item>
            </template>
          </q-select>
        </div>

        <!-- NATUREZA FORMAL DO TITULO -->
        <div class="col-12">
          <v-label label="Natureza Formal do Título" obrigatorio />
          <div class="row q-gutter-sm">
            <div class="col">
              <q-select ref="selectNatureza" v-model="naturezaSelecionado" :options="options" option-value="id"
                option-label="nome" use-input outlined dense input-debounce="300" @filter="getNaturezaByNome"
                :loading="loading" hide-dropdown-icon
                :placeholder="naturezaSelecionado ? '' : 'digite a natureza para buscar...'" clearable
                @update:model-value="removerFoco">

                <!-- <template v-slot:before>
                    <q-icon name="fa-regular fa-search" size="14px" />
                  </template> -->

                <!-- <template v-slot:selected-item="scope">
                    <q-item v-bind="scope.itemProps">
                      <q-item-section>
                        <q-item-label class="text-weight-medium text-primary">
                          {{ scope.opt.nome }}
                        </q-item-label>
                      </q-item-section>
                    </q-item>
                  </template> -->

                <template v-slot:selected-item="scope">
                  <q-item v-bind="scope.itemProps">
                    <q-item-section>
                      <q-item-label>
                        <q-chip icon-remove="none">
                          <!-- {{ scope.opt.nome }}/{{ scope.opt.sigla }} -->
                          {{ scope.opt.nome }}
                        </q-chip>
                      </q-item-label>
                    </q-item-section>
                  </q-item>
                </template>

                <template v-slot:option="scope">
                  <q-item v-bind="scope.itemProps">
                    <q-item-section>
                      <q-item-label>
                        {{ scope.opt.nome }}
                      </q-item-label>
                    </q-item-section>
                  </q-item>
                </template>

                <template v-slot:no-option>
                  <q-item>
                    <q-item-section class="text-center">
                      <div class="text-grey-5 q-py-md">
                        <l-icon name="search" :size="32" />
                        <div class="q-mt-sm">Nenhuma natureza encontrada</div>
                        <div class="text-caption">
                          Digite pelo menos 2 caracteres
                        </div>
                      </div>
                    </q-item-section>
                  </q-item>
                </template>
              </q-select>
            </div>


            <div class="col-auto">
              <q-btn color="grey-5" outline @click="abrirModalCriarCliente" size="8px"
                class="full-height">
                <l-icon name="user-plus" :size="16" />
                <q-tooltip>Adicionar novo cliente</q-tooltip>
              </q-btn>
            </div>
          </div>
        </div>

        <!-- Serviço Principal e Estado -->
        <div class="col-md-8 col-sm-12">
          <v-label label="Serviço Principal" obrigatorio />

          <q-select v-model="protocolo.natureza_id" :options="servicos" option-value="id" option-label="nome" outlined
            dense input-debounce="500" placeholder="Selecione o serviço principal">
            <template v-slot:selected-item="scope">
              <q-item v-bind="scope.itemProps">
                <q-item-section>
                  <q-item-label>
                    <q-chip icon-remove="none">
                      {{ scope.opt.nome }}
                    </q-chip>
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
                <q-item-section class="text-center text-grey">
                  <l-icon name="alert-circle" :size="32" class="q-mb-sm" />
                  <div>Nenhum serviço encontrado</div>
                </q-item-section>
              </q-item>
            </template>
          </q-select>
        </div>

        <div class="col-md-4 col-sm-12 col-xs-12">
          <v-label label="Estado" obrigatorio />

          <q-select v-model="protocolo.estado_id" :options="estados" option-value="id" option-label="nome" outlined
            dense input-debounce="500" placeholder="Selecione o estado">
            <template v-slot:selected-item="scope">
              <q-item v-bind="scope.itemProps">
                <q-item-section>
                  <q-item-label>
                    <q-chip icon-remove="none">
                      <!-- {{ scope.opt.nome }}/{{ scope.opt.sigla }} -->
                      {{ scope.opt.nome }}
                    </q-chip>
                  </q-item-label>
                </q-item-section>
              </q-item>
            </template>

            <template v-slot:option="scope">
              <q-item v-bind="scope.itemProps">
                <q-item-section>
                  <!-- <q-item-label>{{ scope.opt.nome }}/{{ scope.opt.sigla }}</q-item-label> -->
                  <q-item-label>{{ scope.opt.nome }}</q-item-label>
                  <!-- <q-item-label caption>{{ scope.opt.sigla }}</q-item-label> -->
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
              <q-select v-model="clienteSelecionado" :options="options" option-value="id" option-label="nome" use-input
                outlined dense input-debounce="300" @filter="filterClients" :loading="loading" hide-dropdown-icon
                placeholder="digite o nome ou documento para buscar...">
                <!-- <template v-slot:before>
                    <q-icon name="fa-regular fa-search" size="14px" />
                  </template> -->

                <template v-slot:selected-item="scope">
                  <q-item v-bind="scope.itemProps">
                    <q-item-section avatar>
                      <q-avatar size="24px" color="blue-grey-6" text-color="white">
                        {{ scope.opt.nome.charAt(0).toUpperCase() }}
                      </q-avatar>
                    </q-item-section>
                    <q-item-section>
                      <q-item-label class="text-weight-medium text-primary">
                        {{ scope.opt.nome }}
                      </q-item-label>
                      <q-item-label caption class="text-grey-6">
                        {{ scope.opt.cpf_cnpj }}
                      </q-item-label>
                    </q-item-section>
                  </q-item>
                </template>

                <template v-slot:option="scope">
                  <q-item v-bind="scope.itemProps">
                    <q-item-section>
                      <q-item-label class="text-weight-medium text-primary">
                        {{ scope.opt.nome }}
                      </q-item-label>
                      <q-item-label caption class="text-grey-6">
                        {{ scope.opt.cpf_cnpj }} • {{ scope.opt.tipo || 'Cliente' }}
                      </q-item-label>
                    </q-item-section>
                  </q-item>
                </template>

                <template v-slot:no-option>
                  <q-item>
                    <q-item-section class="text-center">
                      <div class="text-grey-5 q-py-md">
                        <l-icon name="search" :size="32" />
                        <div class="q-mt-sm">Nenhum cliente encontrado</div>
                        <div class="text-caption">
                          Digite pelo menos 2 caracteres
                        </div>
                      </div>
                    </q-item-section>
                  </q-item>
                </template>
              </q-select>
            </div>

            <div class="col-auto">
              <q-btn color="grey-5" outline @click="abrirModalCriarCliente"
                class="full-height" size="8px">
                <l-icon name="user-plus" :size="16" />
                <q-tooltip>Adicionar novo cliente</q-tooltip>
              </q-btn>
            </div>
          </div>
        </div>

        <div class="col-12">
          <v-label label="Interessado" obrigatorio />
          <div class="row q-gutter-sm">
            <div class="col">
              <q-select v-model="clienteSelecionado" :options="options" option-value="id" option-label="nome" use-input
                outlined dense input-debounce="300" @filter="filterClients" :loading="loading" hide-dropdown-icon
                placeholder="digite o nome ou documento para buscar...">
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
                      <q-item-label class="text-weight-medium text-primary">
                        {{ scope.opt.nome }}
                      </q-item-label>
                      <q-item-label caption class="text-grey-6">
                        {{ scope.opt.cpf_cnpj }}
                      </q-item-label>
                    </q-item-section>
                  </q-item>
                </template>

                <template v-slot:option="scope">
                  <q-item v-bind="scope.itemProps">
                    <q-item-section avatar>
                      <q-avatar size="32px" color="blue-grey-6" text-color="white">
                        {{ scope.opt.nome.charAt(0).toUpperCase() }}
                      </q-avatar>
                    </q-item-section>
                    <q-item-section>
                      <q-item-label class="text-weight-medium text-primary">
                        {{ scope.opt.nome }}
                      </q-item-label>
                      <q-item-label caption class="text-grey-6">
                        {{ scope.opt.cpf_cnpj }} • {{ scope.opt.tipo || 'Cliente' }}
                      </q-item-label>
                    </q-item-section>
                  </q-item>
                </template>

                <template v-slot:no-option>
                  <q-item>
                    <q-item-section class="text-center">
                      <div class="text-grey-5 q-py-md">
                        <l-icon name="search" :size="32" />
                        <div class="q-mt-sm">Nenhum cliente encontrado</div>
                        <div class="text-caption">
                          Digite pelo menos 2 caracteres
                        </div>
                      </div>
                    </q-item-section>
                  </q-item>
                </template>
              </q-select>
            </div>

            <div class="col-auto">
              <q-btn color="grey-5" outline @click="abrirModalCriarCliente" size="8px"
                class="full-height">
                <l-icon name="user-plus" :size="16" />
                <q-tooltip>Adicionar novo cliente</q-tooltip>
              </q-btn>
            </div>
          </div>
        </div>

        <!-- Cartório -->
        <div class="col-12">
          <v-label label="Cartório" obrigatorio />

          <q-select v-model="protocolo.cartorio_id" :options="cartorios" option-value="id" option-label="nome" outlined
            dense input-debounce="500" placeholder="Selecione o cartório">

            <template v-slot:selected-item="scope">
              <q-item v-bind="scope.itemProps">
                <q-item-section>
                  <q-item-label>
                    <q-chip icon-remove="none">
                      {{ scope.opt.nome }}
                    </q-chip>
                  </q-item-label>
                </q-item-section>
              </q-item>
            </template>

            <template v-slot:option="scope">
              <q-item v-bind="scope.itemProps">
                <q-item-section avatar>
                  <l-icon name="landmark" :size="18" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>{{ scope.opt.nome }}</q-item-label>
                  <q-item-label caption>{{ scope.opt.endereco || 'Cartório' }}</q-item-label>
                </q-item-section>
              </q-item>
            </template>

            <template v-slot:no-option>
              <q-item>
                <q-item-section class="text-center text-grey">
                  <l-icon name="alert-circle" :size="32" class="q-mb-sm" />
                  <div>Nenhum cartório encontrado</div>
                </q-item-section>
              </q-item>
            </template>
          </q-select>
        </div>
      </div>
    </q-card-section>

    <template v-slot:rodape>
      <q-card-section class="bg-grey-3">
        <div class="flex justify-between">
          <div>
            <q-btn label="Cancelar" color="negative" outline @click="cancelar"
              :disable="salvando" />
          </div>
          <div class="q-mr-md">
            <q-btn label="Salvar" color="primary" @click="salvar" outline
              :loading="salvando">
              <template v-slot:loading>
                <q-spinner class="q-mr-sm" />
                Salvando...
              </template>
            </q-btn>
          </div>
        </div>
      </q-card-section>
    </template>
  </modal>

  <!-- <ProtocoloShow v-model="showModalProtocolo" /> -->
</template>

<script setup>
import { storeToRefs } from "pinia";
import { computed, onMounted, ref, watch } from "vue";
import { useRouter } from "vue-router";
import { useQuasar } from "quasar";
import { useEstadoStore } from "src/stores/estado";
import { useNaturezaStore } from "src/stores/natureza";

const $q = useQuasar();
const model = defineModel({ default: false });
const showModalProtocolo = ref(false);

const estadoStore = useEstadoStore();
const { estado, estados } = storeToRefs(estadoStore);

const titulo = ref("Criar Novo Protocolo");
const salvando = ref(false);
const mostrarSucesso = ref(false);
const protocoloCriado = ref(null);
const observacoes = ref("");

const getOpcaoSelecionada = ref("NORMAL");

// Dados mockados para o exemplo - substitua pelas suas stores/APIs
const protocolo = ref({
  meio_solicitacao_id: 1,
  estado_id: null,
  natureza_id: null,
  cartorio_id: null
});

const meiosSolicitacao = ref([
  { id: 1, nome: 'Balcão de Atendimento' },
  { id: 2, nome: 'Online' },
  { id: 3, nome: 'Telefone' },
  { id: 4, nome: 'E-mail' }
]);

const servicos = ref([
  { id: 1, nome: 'Compra e Venda' },
  { id: 2, nome: 'Cédula de Crédito' },
  { id: 3, nome: 'Abertura de Matrícula' },
]);

const cartorios = ref([
  { id: 1, nome: '1º Cartório de Registro Civil', endereco: 'Centro' },
  { id: 2, nome: '2º Cartório de Registro Civil', endereco: 'Vila Nova' },
  { id: 3, nome: 'Cartório de Notas', endereco: 'Centro' }
]);

const dominioSelecionado = ref(1);

const router = useRouter();

// Validação do formulário
const formValido = computed(() => {
  return !!(
    protocolo.value.meio_solicitacao_id &&
    protocolo.value.estado_id &&
    protocolo.value.natureza_id &&
    protocolo.value.cartorio_id &&
    clienteSelecionado.value
  );
});

const salvar = async () => {
  if (!formValido.value) {
    $q.notify({
      color: "warning",
      message: "Preencha todos os campos obrigatórios",
      position: "top-right",
    });
    return;
  }

  salvando.value = true;

  try {
    // Simular chamada de API
    await new Promise(resolve => setTimeout(resolve, 2000));

    const protocoloCriadoMock = {
      codigo: 'PROT-2025-001',
      uuid: '123e4567-e89b-12d3-a456-426614174000'
    };

    protocoloCriado.value = protocoloCriadoMock;
    model.value = false;

    $q.notify({
      color: "positive",
      message: `Protocolo ${protocoloCriadoMock.codigo} criado! Redirecionando...`,
      timeout: 2000,
      position: "top-right",
    });

    // Redireciona direto para protocolo.geral (comentado para demo)
    // router.push({
    //   name: "protocolo.geral",
    //   params: { id: protocoloCriadoMock.uuid },
    // });

  } catch (error) {
    console.error("Erro ao salvar o protocolo:", error);
    $q.notify({
      color: "negative",
      message: error?.response?.data?.message || "Erro ao criar o protocolo.",
      position: "top-right",
    });
  } finally {
    salvando.value = false;
  }
};

const cancelar = () => {
  model.value = false;
  resetarForm();
};

const resetarForm = () => {
  protocolo.value.meio_solicitacao_id = null;
  protocolo.value.estado_id = null;
  protocolo.value.natureza_id = null;
  protocolo.value.cartorio_id = null;
  clienteSelecionado.value = null;
  observacoes.value = "";
  getOpcaoSelecionada.value = "NORMAL";
  options.value = [];
};

const criarNovo = () => {
  mostrarSucesso.value = false;
  resetarForm();
  model.value = true;
};

const onCancelar = () => {
  model.value = !model.value;
}

// FILTRO DO CLIENTE
const clienteSelecionado = ref(null);
const naturezaSelecionado = ref(null);
const options = ref([]);
const loading = ref(false);


// Refs
const selectNatureza = ref(null);
// Método para remover foco
const removerFoco = () => {
  if (selectNatureza.value) {
    selectNatureza.value.blur();
  }
};

const filterClients = (val, update) => {
  if (val.length < 2) {
    update(() => {
      options.value = [];
    });
    return;
  }

  loading.value = true;

  // Simular busca com delay
  setTimeout(() => {
    update(() => {
      const needle = val.toLowerCase();
      options.value = clientesMock.filter(
        cliente => cliente.nome.toLowerCase().includes(needle) ||
          cliente.cpf_cnpj.includes(needle)
      );
    });
    loading.value = false;
  }, 500);
};


const naturezaStore = useNaturezaStore();
const getNaturezaByNome = async (val, update, abort) => {
  // Se o texto for muito curto, não busca
  if (val.length < 2) {
    abort();
    return;
  }

  loading.value = true;

  // Chama a action da store passando o filtro
  const resultados = await naturezaStore.fetchNaturezas(val);

  // Atualiza as opções do select
  update(() => {
    options.value = resultados;
    loading.value = false;
  });
};


const abrirModalCriarCliente = () => {
  showModalProtocolo.value = !showModalProtocolo.value;
  model.value = !model.value;
};

// Watch para resetar form quando modal abre
watch(model, (newVal) => {
  if (newVal) {
    resetarForm();
  }
});
</script>

<style lang="scss" scoped>
.corpo-secao {
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

    .q-btn__content {
      padding: 0;
      margin: 0;
    }

    .q-icon {
      font-size: 16px;
    }

    &.q-btn--outline {
      border: 1px solid var(--border-color);
      background-color: white;
    }

    &.bg-primary {
      border-color: $primary !important;
    }

    &[aria-pressed="true"] {
      border-color: $primary !important;
    }

    .q-focus-helper {
      background-color: currentColor;
      opacity: 0;
      border-radius: inherit;
    }

    &:hover .q-focus-helper {
      opacity: 0.1;
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