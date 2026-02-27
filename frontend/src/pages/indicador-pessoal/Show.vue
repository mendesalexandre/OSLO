<template>
  <modal titulo="Cadastrar novo Indicador" v-model="model" tamanho="lg">
    <!-- Dados Pessoais -->
    <q-card flat bordered class="q-mb-sm">
      <q-card-section>
        <div class="row q-col-gutter-sm">
          <div class="col-md-1 col-sm-12 col-xs-12">
            <v-label label="ID" />
            <q-input v-model="indicadorPessoal.id" outlined dense disabled readonly />
          </div>

          <div class="col-md-8 col-sm-12 col-xs-12">
            <v-label label="Nome" obrigatorio />
            <q-input v-model="indicadorPessoal.nome" outlined dense placeholder="Nome completo" />
          </div>

          <div class="col-md-3 col-sm-12 col-xs-12">
            <v-input-check v-model="indicadorPessoal.is_ativo" label="Ativo?" />
          </div>

          <div class="col-md-3 col-sm-12 col-xs-12">
            <v-label label="Tipo Pessoa" obrigatorio />
            <q-select v-model="indicadorPessoal.tipo_pessoa_id" outlined dense :options="tiposPessoa" option-value="id"
              :option-label="(tipo) => `${tipo.nome}`" emit-value map-options placeholder="Selecione o tipo" />
          </div>

          <div class="col-md-6 col-sm-12 col-xs-12">
            <v-label label="CPF/CNPJ" obrigatorio />
            <q-input v-model="indicadorPessoal.cpf_cnpj" :mask="mascaraCpfCnpj" unmasked-value outlined dense
              @blur="validarCpfCnpj" :error="erroCpfCnpj" :error-message="mensagemErroCpfCnpj" hide-bottom-space
              placeholder="Digite o CPF ou CNPJ" />
          </div>

          <div class="col-md-3 col-sm-12 col-xs-12">
            <v-label label="Data de Nascimento" />
            <q-input v-model="indicadorPessoal.data_nascimento" type="date" outlined dense />
          </div>

          <div class="col-md-12 col-sm-12 col-xs-12">
            <v-label label="E-mail" />
            <q-input v-model="indicadorPessoal.email" outlined dense type="email" placeholder="exemplo@email.com" />
          </div>

          <!-- RG -->
          <div class="col-md-4 col-sm-12 col-xs-12">
            <v-label label="RG" />
            <q-input v-model="indicadorPessoal.rg_ie" outlined dense placeholder="Número do RG" />
          </div>

          <div class="col-md-3 col-sm-12 col-xs-12">
            <v-label label="Órgão Expedidor RG" />
            <q-input v-model="indicadorPessoal.rg_orgao_expedidor" outlined dense placeholder="Ex: SSP" />
          </div>

          <div class="col-md-2 col-sm-12 col-xs-12">
            <v-label label="UF Emissão RG" />
            <q-input v-model="indicadorPessoal.rg_uf_emissao" outlined dense maxlength="2" placeholder="SP" />
          </div>

          <div class="col-md-3 col-sm-12 col-xs-12">
            <v-label label="Data Emissão RG" />
            <q-input type="date" v-model="indicadorPessoal.rg_data_emissao" outlined dense />
          </div>

          <!-- CNH -->
          <div class="col-md-3 col-sm-12 col-xs-12">
            <v-label label="CNH" />
            <q-input v-model="indicadorPessoal.cnh" outlined dense placeholder="Número da CNH" />
          </div>

          <div class="col-md-3 col-sm-12 col-xs-12">
            <v-label label="Órgão Expedidor CNH" />
            <q-input v-model="indicadorPessoal.cnh_orgao_expedidor" outlined dense placeholder="Ex: DETRAN/SP" />
          </div>

          <div class="col-md-2 col-sm-12 col-xs-12">
            <v-label label="Categoria CNH" />
            <q-input v-model="indicadorPessoal.cnh_categoria" outlined dense placeholder="Ex: AB" />
          </div>

          <div class="col-md-2 col-sm-12 col-xs-12">
            <v-label label="Data Emissão CNH" />
            <q-input type="date" v-model="indicadorPessoal.cnh_data_emissao" outlined dense />
          </div>

          <div class="col-md-2 col-sm-12 col-xs-12">
            <v-label label="Validade CNH" />
            <q-input type="date" v-model="indicadorPessoal.cnh_data_validade" outlined dense />
          </div>

          <!-- Passaporte -->
          <div class="col-md-3 col-sm-12 col-xs-12">
            <v-label label="Passaporte" />
            <q-input v-model="indicadorPessoal.passaporte" outlined dense placeholder="Número do passaporte" />
          </div>

          <div class="col-md-3 col-sm-12 col-xs-12">
            <v-label label="Órgão Expedidor" />
            <q-input v-model="indicadorPessoal.passaporte_orgao_expedidor" outlined dense
              placeholder="Ex: Polícia Federal" />
          </div>

          <div class="col-md-2 col-sm-12 col-xs-12">
            <v-label label="País Emissão" />
            <q-input v-model="indicadorPessoal.passaporte_pais_emissao" outlined dense maxlength="3"
              placeholder="BRA" />
          </div>

          <div class="col-md-2 col-sm-12 col-xs-12">
            <v-label label="Data Emissão" />
            <q-input type="date" v-model="indicadorPessoal.passaporte_data_emissao" outlined dense />
          </div>

          <div class="col-md-2 col-sm-12 col-xs-12">
            <v-label label="Validade" />
            <q-input type="date" v-model="indicadorPessoal.passaporte_data_validade" outlined dense />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- SISCOAF -->
    <q-card flat bordered class="q-mb-sm" v-if="!verificarTipoPessoa">
      <q-card-section>
        <div class="titulo">Informações SISCOAF</div>
      </q-card-section>
      <q-separator />
      <q-card-section>
        <div class="row q-col-gutter-md">
          <div class="col-md-4 col-sm-6 col-xs-12">
            <v-input-check v-model="indicadorPessoal.pessoa_falecida" label="Pessoa Falecida" />
          </div>

          <div class="col-md-4 col-sm-6 col-xs-12">
            <v-input-check v-model="indicadorPessoal.menor_idade" label="Menor de Idade" />
          </div>

          <div class="col-md-4 col-sm-6 col-xs-12">
            <v-input-check v-model="indicadorPessoal.incapaz" label="Incapaz" />
          </div>

          <div class="col-md-4 col-sm-6 col-xs-12">
            <v-input-check v-model="indicadorPessoal.pessoa_politicamente_exposta"
              label="Pessoa Politicamente Exposta" />
          </div>

          <div class="col-md-4 col-sm-6 col-xs-12">
            <v-input-check v-model="indicadorPessoal.servidor_publico" label="Servidor Público" />
          </div>

          <div class="col-md-4 col-sm-6 col-xs-12">
            <v-input-check v-model="indicadorPessoal.pessoa_obrigada" label="Pessoa Obrigada" />
          </div>

          <!-- Campos PEP -->
          <template v-if="indicadorPessoal.pessoa_politicamente_exposta">
            <div class="col-12">
              <q-separator class="q-my-md" />
              <div class="text-subtitle2 q-mb-md">Detalhes PEP</div>
            </div>

            <div class="col-md-6 col-sm-12 col-xs-12">
              <v-label label="Nível da Função" />
              <q-input v-model="indicadorPessoal.pep_nivel_funcao" outlined dense placeholder="Ex: Federal, Estadual" />
            </div>

            <div class="col-md-6 col-sm-12 col-xs-12">
              <v-label label="Sigla da Função" />
              <q-input v-model="indicadorPessoal.pep_sigla_funcao" outlined dense placeholder="Ex: GOV, SEN" />
            </div>

            <div class="col-md-12 col-sm-12 col-xs-12">
              <v-label label="Nome do Órgão" />
              <q-input v-model="indicadorPessoal.pep_nome_orgao" outlined dense placeholder="Nome do órgão público" />
            </div>

            <div class="col-md-6 col-sm-12 col-xs-12">
              <v-label label="Data Início Exercício" />
              <q-input type="date" v-model="indicadorPessoal.pep_data_inicio_exercicio" outlined dense />
            </div>

            <div class="col-md-6 col-sm-12 col-xs-12">
              <v-label label="Data Fim Exercício" />
              <q-input type="date" v-model="indicadorPessoal.pep_data_fim_exercicio" outlined dense />
            </div>

            <div class="col-md-6 col-sm-12 col-xs-12">
              <v-label label="Data Carência" />
              <q-input type="date" v-model="indicadorPessoal.pep_data_carencia" outlined dense />
            </div>

            <div class="col-md-6 col-sm-12 col-xs-12">
              <v-label label="Data Atualização" />
              <q-input type="date" v-model="indicadorPessoal.pep_data_atualizacao" outlined dense />
            </div>
          </template>
        </div>
      </q-card-section>
    </q-card>

    <!-- Dados de Contato -->
    <q-card flat bordered class="q-mb-sm">
      <q-card-section>
        <div class="titulo">Dados de Contato</div>
      </q-card-section>
      <q-separator />
      <q-card-section>
        <div class="row q-col-gutter-sm">
          <div class="col-md-6 col-sm-12 col-xs-12">
            <v-label label="Telefone" />
            <q-input v-model="indicadorPessoal.telefone" mask="(##) ####-####" unmasked-value outlined dense
              placeholder="(11) 1234-5678" />
          </div>

          <div class="col-md-6 col-sm-12 col-xs-12">
            <v-label label="Celular" />
            <q-input v-model="indicadorPessoal.celular" mask="(##) #####-####" unmasked-value outlined dense
              placeholder="(11) 99999-9999" />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Estado Civil -->
    <q-card flat bordered class="q-mb-sm" v-if="!verificarTipoPessoa">
      <q-card-section>
        <div class="titulo">Estado Civil</div>
      </q-card-section>
      <q-separator />
      <q-card-section>
        <div class="row q-col-gutter-sm">
          <div class="col-md-6 col-sm-12 col-xs-12">
            <v-label label="Estado Civil" />
            <q-select v-model="indicadorPessoal.estado_civil_id" outlined dense :options="estadosCivis"
              option-value="id"
              :option-label="(estado) => typeof estado === 'object' ? `${estado.id} - ${estado.nome}` : null" emit-value
              map-options placeholder="Selecione o estado civil" @update:model-value="verificarEstadoCivil" />
          </div>

          <div class="col-md-6 col-sm-12 col-xs-12">
            <v-label label="Regime de Casamento" />
            <q-select v-model="indicadorPessoal.regime_casamento_id" outlined dense :options="regimesCasamento"
              option-value="id"
              :option-label="(regime) => typeof regime === 'object' ? `${regime.id} - ${regime.nome}` : ''"
              :disable="campoRegimeCasamento" emit-value map-options placeholder="Selecione o regime" />
          </div>

          <div class="col-md-6 col-sm-12 col-xs-12">
            <v-label label="Data de Casamento" />
            <q-input v-model="indicadorPessoal.data_casamento" type="date" :disable="campoDataCasamento" outlined dense>
              <template v-slot:append>
                <q-icon :name="campoDataCasamento ? 'lock' : 'favorite'" :color="campoDataCasamento ? 'grey' : 'red'" />
              </template>
            </q-input>
          </div>

          <div class="col-md-6 col-sm-12 col-xs-12">
            <v-label label="Data de Divórcio" />
            <q-input v-model="indicadorPessoal.data_divorcio" type="date" :disable="campoDataDivorcio" outlined dense>
              <template v-slot:append>
                <q-icon :name="campoDataDivorcio ? 'lock' : 'gavel'" :color="campoDataDivorcio ? 'grey' : 'blue'" />
              </template>
            </q-input>
          </div>

          <div class="col-md-1 col-sm-12 col-xs-12">
            <v-label label="Código" />
            <q-input v-model="indicadorPessoal.conjuge_id" :disable="campoRegimeCasamento" outlined dense
              type="number" />
          </div>

          <div class="col-md-11 col-sm-12 col-xs-12">
            <v-label label="Nome do Cônjuge" />
            <q-input v-model="indicadorPessoal.conjuge_nome" :disable="campoRegimeCasamento" outlined dense
              placeholder="Nome completo do cônjuge" />
          </div>

          <div class="col-md-8 col-sm-12 col-xs-12">
            <v-label label="Registro Civil do Casamento" />
            <q-select v-model="indicadorPessoal.conjuge_registro_civil_id" outlined dense
              :disable="campoRegimeCasamento" emit-value map-options placeholder="Selecione o registro">
              <template v-slot:append>
                <q-btn color="primary" icon="add" @click="abrirModalRegistro" flat round size="sm" />
              </template>
            </q-select>
          </div>

          <div class="col-md-2 col-sm-12 col-xs-12">
            <v-label label="Livro" />
            <q-input v-model="indicadorPessoal.conjuge_livro" outlined dense :disable="campoRegimeCasamento" />
          </div>

          <div class="col-md-2 col-sm-12 col-xs-12">
            <v-label label="Folhas" />
            <q-input v-model="indicadorPessoal.conjuge_folhas" outlined dense :disable="campoRegimeCasamento" />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Filiação -->
    <q-card flat bordered class="q-mb-sm" v-if="!verificarTipoPessoa">
      <q-card-section>
        <div class="titulo">Dados sobre Filiação</div>
      </q-card-section>
      <q-separator />
      <q-card-section>
        <div class="row q-col-gutter-sm">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <v-label label="Nome da Mãe" />
            <q-input v-model="indicadorPessoal.nome_mae" outlined dense placeholder="Nome completo da mãe" />
          </div>

          <div class="col-md-12 col-sm-12 col-xs-12">
            <v-input-check v-model="indicadorPessoal.pai_desconhecido" label="Pai desconhecido" />
          </div>

          <div class="col-md-12 col-sm-12 col-xs-12">
            <v-label label="Nome do Pai" />
            <q-input v-model="indicadorPessoal.nome_pai" :disable="indicadorPessoal.pai_desconhecido" outlined dense
              placeholder="Nome completo do pai" />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Profissão -->
    <q-card flat bordered class="q-mb-sm" v-if="isPessoaFisica">
      <q-card-section>
        <div class="titulo">Dados sobre Profissão</div>
      </q-card-section>
      <q-separator />
      <q-card-section>
        <div class="row q-col-gutter-sm">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <v-label label="Profissão" />
            <q-select v-model="indicadorPessoal.profissao_id" outlined dense :options="profissoes" option-value="id"
              option-label="nome" emit-value map-options placeholder="Selecione a profissão" />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Endereço -->
    <q-card flat bordered class="q-mb-sm">
      <q-card-section>
        <div class="titulo">Endereço</div>
      </q-card-section>
      <q-separator />
      <q-card-section>
        <div class="row q-col-gutter-sm">
          <div class="col-md-2 col-sm-12 col-xs-12">
            <v-label label="CEP" />
            <q-input v-model="indicadorPessoal.endereco_cep" mask="#####-###" unmasked-value @blur="consultarCep"
              outlined dense placeholder="00000-000" />
          </div>

          <div class="col-md-8 col-sm-12 col-xs-12">
            <v-label label="Endereço" />
            <q-input v-model="indicadorPessoal.endereco_logradouro" outlined dense placeholder="Rua, Avenida, etc." />
          </div>

          <div class="col-md-2 col-sm-12 col-xs-12">
            <v-label label="Número" />
            <q-input v-model="indicadorPessoal.endereco_numero" outlined dense placeholder="Nº" />
          </div>

          <div class="col-md-6 col-sm-12 col-xs-12">
            <v-label label="Bairro" />
            <q-input v-model="indicadorPessoal.endereco_bairro" outlined dense placeholder="Nome do bairro" />
          </div>

          <div class="col-md-6 col-sm-12 col-xs-12">
            <v-label label="Complemento" />
            <q-input v-model="indicadorPessoal.endereco_complemento" outlined dense placeholder="Apto, Bloco, etc." />
          </div>

          <div class="col-md-4 col-sm-12 col-xs-12">
            <v-label label="Estado" />
            <q-select v-model="indicadorPessoal.endereco_estado_id" outlined dense :options="estados" option-value="id"
              :option-label="(estado) => `${estado.ibge_codigo} - ${estado.nome}`" emit-value map-options
              @update:model-value="getCidadesPorEstado($event)" placeholder="Selecione o estado" />
          </div>

          <div class="col-md-4 col-sm-12 col-xs-12">
            <v-label label="Cidade" />
            <q-select v-model="indicadorPessoal.endereco_cidade_id" outlined dense emit-value map-options
              :options="cidades" option-value="id" :option-label="(cidade) => `${cidade.nome}`"
              placeholder="Selecione a cidade" />
          </div>

          <div class="col-md-4 col-sm-12 col-xs-12">
            <v-label label="País" />
            <q-select v-model="indicadorPessoal.endereco_pais_id" outlined dense emit-value map-options
              :options="paises" option-value="id" option-label="nome" placeholder="Selecione o país" />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <template v-slot:rodape>
      <q-card-section class="flex justify-end">
        <div class="q-gutter-sm">
          <q-btn label="Cancelar" color="secondary" @click="cancelar" outline icon="close" />
          <q-btn label="Salvar" color="primary" icon="check" @click="salvar" :loading="salvando" />
        </div>
      </q-card-section>
    </template>
  </modal>
</template>

<script setup>
import { computed, onMounted, ref, watch } from "vue";
import { storeToRefs } from "pinia";
import { useIndicadorPessoal } from "src/stores/indicador-pessoal";
import { useCidadeStore } from "src/stores/cidade";
import { useEstadoStore } from "src/stores/estado";
import getCep from "src/Utils";
import { useQuasar } from "quasar";

const model = defineModel({ default: false });
const $q = useQuasar();

// Store
const indicadorPessoalStore = useIndicadorPessoal();
const { indicadorPessoal } = storeToRefs(indicadorPessoalStore);

// Estados e Cidades
const estadoStore = useEstadoStore();
const { estados } = storeToRefs(estadoStore);

const cidadeStore = useCidadeStore();
const { cidades } = storeToRefs(cidadeStore);

// Estados de controle
const salvando = ref(false);
const campoDataDivorcio = ref(true);
const campoRegimeCasamento = ref(true);
const campoDataCasamento = ref(true);

// Validação CPF/CNPJ
const erroCpfCnpj = ref(false);
const mensagemErroCpfCnpj = ref("");

// Options
const tiposPessoa = ref([
  { id: 'PESSOA_FISICA', nome: 'Pessoa física' },
  { id: 'PESSOA_JURIDICA', nome: 'Pessoa jurídica' }
]);

const estadosCivis = ref([]);
const regimesCasamento = ref([]);
const profissoes = ref([]);
const paises = ref([]);

// Computed
const mascaraCpfCnpj = computed(() => indicadorPessoalStore.mascaraCpfCnpj);

const verificarTipoPessoa = computed(() => {
  return indicadorPessoal.value.tipo_pessoa_id === 'PESSOA_JURIDICA';
});

const isPessoaFisica = computed(() => {
  return indicadorPessoal.value.tipo_pessoa_id === 'PESSOA_FISICA';
});

const isPessoaJuridica = computed(() => {
  return indicadorPessoal.value.tipo_pessoa_id === 'PESSOA_JURIDICA';
});

// Métodos
const consultarCep = async () => {
  if (!indicadorPessoal.value.endereco_cep) return;

  $q.loading.show({ message: 'Consultando CEP...' });

  try {
    const response = await getCep(indicadorPessoal.value.endereco_cep);

    if (response.erro === "true" || response.erro === true) {
      $q.notify({
        type: "negative",
        message: "CEP não encontrado. Verifique o número digitado.",
        position: "top",
        timeout: 3000,
      });
      return;
    }

    // Preenche os dados do endereço
    indicadorPessoal.value.endereco_logradouro = response.logradouro || '';
    indicadorPessoal.value.endereco_bairro = response.bairro || '';
    indicadorPessoal.value.endereco_complemento = response.complemento || '';

    // Busca a cidade pelo código IBGE
    if (response.ibge) {
      try {
        const cidadeResponse = await cidadeStore.getCidadeCodigoIbge({
          codigoIbge: response.ibge
        });

        if (cidadeResponse?.data.success) {
          indicadorPessoal.value.endereco_estado_id = cidadeResponse.data.data.estado_id;
          await cidadeStore.getCidadesEstadoId(indicadorPessoal.value.endereco_estado_id);
          indicadorPessoal.value.endereco_cidade_id = cidadeResponse.data.data.id;
        }
      } catch (error) {
        console.error("Erro ao buscar cidade pelo IBGE:", error);
      }
    }

    $q.notify({
      type: "positive",
      message: "CEP consultado com sucesso!",
      position: "top",
      timeout: 2000,
    });
  } catch (error) {
    console.error("Erro ao consultar CEP:", error);
    $q.notify({
      type: "negative",
      message: "Erro ao consultar CEP. Tente novamente.",
      position: "top",
      timeout: 3000,
    });
  } finally {
    $q.loading.hide();
  }
};

const getCidadesPorEstado = async (estadoId) => {
  if (!estadoId) return;
  indicadorPessoal.value.endereco_cidade_id = null;
  await cidadeStore.getCidadesEstadoId(estadoId);
};

const validarCpfCnpj = () => {
  // Implementar validação de CPF/CNPJ
  const valor = indicadorPessoal.value.cpf_cnpj;

  if (!valor) {
    erroCpfCnpj.value = true;
    mensagemErroCpfCnpj.value = "Campo obrigatório";
    return false;
  }

  // Aqui você pode adicionar validação real de CPF/CNPJ
  erroCpfCnpj.value = false;
  mensagemErroCpfCnpj.value = "";
  return true;
};

const verificarEstadoCivil = (estadoCivilId) => {
  // Lógica para habilitar/desabilitar campos baseado no estado civil
  // Por exemplo: se casado, habilita campos de cônjuge
  const estadosCasados = [2, 3]; // IDs que representam casado/união estável

  if (estadosCasados.includes(estadoCivilId)) {
    campoRegimeCasamento.value = false;
    campoDataCasamento.value = false;
  } else {
    campoRegimeCasamento.value = true;
    campoDataCasamento.value = true;
  }

  // Se divorciado
  if (estadoCivilId === 4) {
    campoDataDivorcio.value = false;
  } else {
    campoDataDivorcio.value = true;
  }
};

const abrirModalRegistro = () => {
  // Implementar abertura de modal de registro civil
  $q.notify({
    type: "info",
    message: "Funcionalidade de registro civil em desenvolvimento",
    position: "top",
    timeout: 2000,
  });
};

const validarFormulario = () => {
  const erros = [];

  // Validações obrigatórias
  if (!indicadorPessoal.value.nome) {
    erros.push("Nome é obrigatório");
  }

  if (!indicadorPessoal.value.tipo_pessoa_id) {
    erros.push("Tipo de pessoa é obrigatório");
  }

  if (!indicadorPessoal.value.cpf_cnpj) {
    erros.push("CPF/CNPJ é obrigatório");
  }

  if (erros.length > 0) {
    $q.notify({
      type: "negative",
      message: erros.join(", "),
      position: "top",
      timeout: 3000,
      multiLine: true,
    });
    return false;
  }

  return validarCpfCnpj();
};

const salvar = async () => {
  if (!validarFormulario()) {
    return;
  }

  salvando.value = true;
  $q.loading.show({ message: 'Salvando indicador pessoal...' });

  try {
    // Prepara os dados para envio
    const dados = {
      nome: indicadorPessoal.value.nome,
      tipo_pessoa_id: indicadorPessoal.value.tipo_pessoa_id,
      cpf_cnpj: indicadorPessoal.value.cpf_cnpj,
      is_ativo: indicadorPessoal.value.is_ativo,

      // Documentos
      rg_ie: indicadorPessoal.value.rg_ie,
      rg_orgao_expedidor: indicadorPessoal.value.rg_orgao_expedidor,
      rg_uf_emissao: indicadorPessoal.value.rg_uf_emissao,
      rg_data_emissao: indicadorPessoal.value.rg_data_emissao,

      cnh: indicadorPessoal.value.cnh,
      cnh_orgao_expedidor: indicadorPessoal.value.cnh_orgao_expedidor,
      cnh_categoria: indicadorPessoal.value.cnh_categoria,
      cnh_data_emissao: indicadorPessoal.value.cnh_data_emissao,
      cnh_data_validade: indicadorPessoal.value.cnh_data_validade,

      passaporte: indicadorPessoal.value.passaporte,
      passaporte_orgao_expedidor: indicadorPessoal.value.passaporte_orgao_expedidor,
      passaporte_pais_emissao: indicadorPessoal.value.passaporte_pais_emissao,
      passaporte_data_emissao: indicadorPessoal.value.passaporte_data_emissao,
      passaporte_data_validade: indicadorPessoal.value.passaporte_data_validade,

      // Dados pessoais
      email: indicadorPessoal.value.email,
      data_nascimento: indicadorPessoal.value.data_nascimento,

      // Contato
      telefone: indicadorPessoal.value.telefone,
      celular: indicadorPessoal.value.celular,

      // Estado civil (apenas para pessoa física)
      ...(isPessoaFisica.value && {
        estado_civil_id: indicadorPessoal.value.estado_civil_id,
        regime_casamento_id: indicadorPessoal.value.regime_casamento_id,
        data_casamento: indicadorPessoal.value.data_casamento,
        data_divorcio: indicadorPessoal.value.data_divorcio,
        conjuge_id: indicadorPessoal.value.conjuge_id,
      }),

      // SISCOAF/PEP (apenas para pessoa física)
      ...(isPessoaFisica.value && {
        pessoa_falecida: indicadorPessoal.value.pessoa_falecida,
        menor_idade: indicadorPessoal.value.menor_idade,
        incapaz: indicadorPessoal.value.incapaz,
        pessoa_politicamente_exposta: indicadorPessoal.value.pessoa_politicamente_exposta,
        servidor_publico: indicadorPessoal.value.servidor_publico,
        pessoa_obrigada: indicadorPessoal.value.pessoa_obrigada,

        ...(indicadorPessoal.value.pessoa_politicamente_exposta && {
          pep_nivel_funcao: indicadorPessoal.value.pep_nivel_funcao,
          pep_sigla_funcao: indicadorPessoal.value.pep_sigla_funcao,
          pep_nome_orgao: indicadorPessoal.value.pep_nome_orgao,
          pep_data_inicio_exercicio: indicadorPessoal.value.pep_data_inicio_exercicio,
          pep_data_fim_exercicio: indicadorPessoal.value.pep_data_fim_exercicio,
          pep_data_carencia: indicadorPessoal.value.pep_data_carencia,
          pep_data_atualizacao: indicadorPessoal.value.pep_data_atualizacao,
        }),
      }),

      // Filiação (apenas para pessoa física)
      ...(isPessoaFisica.value && {
        nome_mae: indicadorPessoal.value.nome_mae,
        nome_pai: indicadorPessoal.value.pai_desconhecido ? null : indicadorPessoal.value.nome_pai,
      }),

      // Profissão (apenas para pessoa física)
      ...(isPessoaFisica.value && {
        profissao_id: indicadorPessoal.value.profissao_id,
      }),

      // Endereço
      endereco_cep: indicadorPessoal.value.endereco_cep,
      endereco_logradouro: indicadorPessoal.value.endereco_logradouro,
      endereco_numero: indicadorPessoal.value.endereco_numero,
      endereco_bairro: indicadorPessoal.value.endereco_bairro,
      endereco_complemento: indicadorPessoal.value.endereco_complemento,
      endereco_estado_id: indicadorPessoal.value.endereco_estado_id,
      endereco_cidade_id: indicadorPessoal.value.endereco_cidade_id,
      endereco_pais_id: indicadorPessoal.value.endereco_pais_id,
    };

    // Remove campos undefined ou null
    Object.keys(dados).forEach(key => {
      if (dados[key] === undefined || dados[key] === null || dados[key] === '') {
        delete dados[key];
      }
    });

    const response = await indicadorPessoalStore.salvar(dados);

    if (response?.data?.success) {
      $q.notify({
        type: "positive",
        message: "Indicador pessoal cadastrado com sucesso!",
        position: "top",
        timeout: 2000,
      });

      model.value = false;
      indicadorPessoalStore.resetarFormulario();
    } else {
      throw new Error(response?.data?.message || "Erro ao salvar");
    }
  } catch (error) {
    console.error("Erro ao salvar indicador:", error);

    const mensagemErro = error?.response?.data?.message
      || error?.message
      || "Erro ao salvar indicador pessoal";

    $q.notify({
      type: "negative",
      message: mensagemErro,
      position: "top",
      timeout: 3000,
    });
  } finally {
    salvando.value = false;
    $q.loading.hide();
  }
};

const cancelar = () => {
  indicadorPessoalStore.resetarFormulario();
  model.value = false;
};

// Watches
watch(() => indicadorPessoal.value.tipo_pessoa_id, (novoTipo) => {
  if (novoTipo === 'PESSOA_JURIDICA') {
    // Limpa campos que só existem para pessoa física
    indicadorPessoal.value.pessoa_falecida = false;
    indicadorPessoal.value.menor_idade = false;
    indicadorPessoal.value.incapaz = false;
    indicadorPessoal.value.pessoa_politicamente_exposta = false;
    indicadorPessoal.value.servidor_publico = false;
    indicadorPessoal.value.pessoa_obrigada = false;
    indicadorPessoal.value.data_nascimento = null;
    indicadorPessoal.value.estado_civil_id = null;
    indicadorPessoal.value.profissao_id = null;
  }
});

watch(() => indicadorPessoal.value.pai_desconhecido, (novoValor) => {
  if (novoValor) {
    indicadorPessoal.value.nome_pai = null;
  }
});

watch(() => indicadorPessoal.value.pessoa_politicamente_exposta, (novoValor) => {
  if (!novoValor) {
    // Limpa campos PEP quando desmarca
    indicadorPessoal.value.pep_nivel_funcao = null;
    indicadorPessoal.value.pep_sigla_funcao = null;
    indicadorPessoal.value.pep_nome_orgao = null;
    indicadorPessoal.value.pep_data_inicio_exercicio = null;
    indicadorPessoal.value.pep_data_fim_exercicio = null;
    indicadorPessoal.value.pep_data_carencia = null;
    indicadorPessoal.value.pep_data_atualizacao = null;
  }
});

// Lifecycle
onMounted(async () => {
  $q.loading.show({ message: 'Carregando dados...' });

  try {
    // Carrega os dados necessários
    await Promise.all([
      estadoStore.index(),
      // Adicione outras chamadas conforme necessário:
      // carregarEstadosCivis(),
      // carregarRegimesCasamento(),
      // carregarProfissoes(),
      // carregarPaises(),
    ]);
  } catch (error) {
    console.error("Erro ao carregar dados iniciais:", error);
    $q.notify({
      type: "negative",
      message: "Erro ao carregar dados iniciais",
      position: "top",
      timeout: 3000,
    });
  } finally {
    $q.loading.hide();
  }
});
</script>