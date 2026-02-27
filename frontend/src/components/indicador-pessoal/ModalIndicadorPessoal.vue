<template>
  <modal
    :titulo="readonly ? 'Visualizar Indicador Pessoal' : (id ? 'Editar Indicador Pessoal' : 'Cadastrar Indicador Pessoal')"
    v-model="model"
    tamanho="lg"
  >
    <div :class="{ 'form-readonly': readonly }">

    <!-- Tipo de pessoa -->
    <q-card flat bordered class="q-mb-sm">
      <q-card-section>
        <div class="row q-col-gutter-sm">
          <div class="col-12">
            <q-btn-toggle
              v-model="form.tipo_pessoa"
              spread
              no-caps
              unelevated
              toggle-color="primary"
              :options="[
                { label: 'Pessoa Física', value: 'F' },
                { label: 'Pessoa Jurídica', value: 'J' },
              ]"
              :disable="!!id"
            />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Identificação -->
    <q-card flat bordered class="q-mb-sm">
      <q-card-section>
        <div class="titulo">Identificação</div>
      </q-card-section>
      <q-separator />
      <q-card-section>
        <div class="row q-col-gutter-sm">
          <div class="col-md-2 col-sm-6 col-xs-12">
            <v-label label="Ficha" />
            <q-input v-model="form.ficha" outlined dense />
          </div>
          <div class="col-md-3 col-sm-6 col-xs-12">
            <v-label :label="form.tipo_pessoa === 'F' ? 'CPF' : 'CNPJ'" obrigatorio />
            <q-input
              v-model="form.cpf_cnpj"
              :mask="form.tipo_pessoa === 'F' ? '###.###.###-##' : '##.###.###/####-##'"
              fill-mask
              outlined
              dense
              :error="!!erros.cpf_cnpj"
              :error-message="erros.cpf_cnpj?.[0]"
            />
          </div>
          <div class="col-md-5 col-sm-12 col-xs-12">
            <v-label :label="form.tipo_pessoa === 'F' ? 'Nome Completo' : 'Razão Social'" obrigatorio />
            <q-input
              v-model="form.nome"
              outlined
              dense
              :error="!!erros.nome"
              :error-message="erros.nome?.[0]"
            />
          </div>
          <div v-if="form.tipo_pessoa === 'J'" class="col-md-4 col-sm-12 col-xs-12">
            <v-label label="Nome Fantasia" />
            <q-input v-model="form.nome_fantasia" outlined dense />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Dados PF -->
    <template v-if="form.tipo_pessoa === 'F'">
      <q-card flat bordered class="q-mb-sm">
        <q-card-section>
          <div class="titulo">Dados Pessoais</div>
        </q-card-section>
        <q-separator />
        <q-card-section>
          <div class="row q-col-gutter-sm">
            <div class="col-md-2 col-sm-6 col-xs-12">
              <v-label label="RG" />
              <q-input v-model="form.rg" outlined dense />
            </div>
            <div class="col-md-2 col-sm-6 col-xs-12">
              <v-label label="Órgão Exp." />
              <q-input v-model="form.orgao_expedidor" outlined dense />
            </div>
            <div class="col-md-2 col-sm-6 col-xs-12">
              <v-label label="Data Expedição" />
              <q-input v-model="form.data_expedicao_rg" type="date" outlined dense />
            </div>
            <div class="col-md-2 col-sm-6 col-xs-12">
              <v-label label="Nascimento" />
              <q-input v-model="form.data_nascimento" type="date" outlined dense />
            </div>
            <div class="col-md-2 col-sm-6 col-xs-12">
              <v-label label="Óbito" />
              <q-input v-model="form.data_obito" type="date" outlined dense />
            </div>
            <div class="col-md-2 col-sm-6 col-xs-12">
              <v-label label="Sexo" />
              <q-select
                v-model="form.sexo"
                :options="[{ label: 'Masculino', value: 'M' }, { label: 'Feminino', value: 'F' }, { label: 'Outro', value: 'O' }]"
                option-label="label"
                option-value="value"
                emit-value
                map-options
                outlined
                dense
                clearable
              />
            </div>
            <div class="col-md-6 col-sm-12 col-xs-12">
              <v-label label="Nome da Mãe" />
              <q-input v-model="form.nome_mae" outlined dense />
            </div>
            <div class="col-md-6 col-sm-12 col-xs-12">
              <v-label label="Nome do Pai" />
              <q-input v-model="form.nome_pai" outlined dense />
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Estado Civil -->
      <q-card flat bordered class="q-mb-sm">
        <q-card-section>
          <div class="titulo">Estado Civil</div>
        </q-card-section>
        <q-separator />
        <q-card-section>
          <div class="row q-col-gutter-sm">
            <div class="col-md-3 col-sm-6 col-xs-12">
              <v-label label="Estado Civil" />
              <SelectAuxiliar v-model="form.estado_civil_id" tabela="estado-civil" outlined dense />
            </div>
            <template v-if="isCasado">
              <div class="col-md-3 col-sm-6 col-xs-12">
                <v-label label="Regime de Bens" obrigatorio />
                <SelectAuxiliar
                  v-model="form.regime_bem_id"
                  tabela="regime-bem"
                  outlined
                  dense
                  :error="!!erros.regime_bem_id"
                  :error-message="erros.regime_bem_id?.[0]"
                />
              </div>
              <div class="col-md-2 col-sm-6 col-xs-12">
                <v-label label="Data do Casamento" />
                <q-input v-model="form.data_casamento" type="date" outlined dense />
              </div>
              <div class="col-md-2 col-sm-6 col-xs-12 column justify-end q-pb-xs">
                <q-toggle v-model="form.anterior_lei_6515" label="Ant. Lei 6.515/77" />
              </div>
              <div class="col-md-6 col-sm-12 col-xs-12">
                <v-label label="Cônjuge" obrigatorio />
                <BuscaAutocomplete
                  v-model="form.conjuge_id"
                  outlined
                  dense
                  :error="!!erros.conjuge_id"
                  :error-message="erros.conjuge_id?.[0]"
                />
              </div>
            </template>
          </div>
        </q-card-section>
      </q-card>

      <!-- Capacidade Civil / Naturalidade -->
      <q-card flat bordered class="q-mb-sm">
        <q-card-section>
          <div class="titulo">Capacidade Civil / Naturalidade / Profissão</div>
        </q-card-section>
        <q-separator />
        <q-card-section>
          <div class="row q-col-gutter-sm">
            <div class="col-md-3 col-sm-6 col-xs-12">
              <v-label label="Capacidade Civil" />
              <SelectAuxiliar v-model="form.capacidade_civil_id" tabela="capacidade-civil" outlined dense />
            </div>
            <div v-if="isIncapaz" class="col-md-4 col-sm-6 col-xs-12">
              <v-label label="Representante Legal" obrigatorio />
              <q-input
                v-model="form.representante_legal"
                outlined
                dense
                :error="!!erros.representante_legal"
                :error-message="erros.representante_legal?.[0]"
              />
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
              <v-label label="Nacionalidade" />
              <SelectAuxiliar v-model="form.nacionalidade_id" tabela="nacionalidade" outlined dense />
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
              <v-label label="Naturalidade" />
              <q-input v-model="form.naturalidade" outlined dense />
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
              <v-label label="Profissão" />
              <SelectAuxiliar v-model="form.profissao_id" tabela="profissao" outlined dense />
            </div>
          </div>
        </q-card-section>
      </q-card>
    </template>

    <!-- Dados PJ -->
    <template v-if="form.tipo_pessoa === 'J'">
      <q-card flat bordered class="q-mb-sm">
        <q-card-section>
          <div class="titulo">Dados da Empresa</div>
        </q-card-section>
        <q-separator />
        <q-card-section>
          <div class="row q-col-gutter-sm">
            <div class="col-md-2 col-sm-6 col-xs-12">
              <v-label label="Data de Abertura" />
              <q-input v-model="form.data_abertura" type="date" outlined dense />
            </div>
            <div class="col-md-2 col-sm-6 col-xs-12">
              <v-label label="Data de Encerramento" />
              <q-input v-model="form.data_encerramento" type="date" outlined dense />
            </div>
            <div class="col-md-4 col-sm-12 col-xs-12">
              <v-label label="Sede" />
              <q-input v-model="form.sede" outlined dense />
            </div>
            <div class="col-md-2 col-sm-6 col-xs-12">
              <v-label label="Tipo de Empresa" />
              <SelectAuxiliar v-model="form.tipo_empresa_id" tabela="tipo-empresa" outlined dense />
            </div>
            <div class="col-md-2 col-sm-6 col-xs-12">
              <v-label label="Porte" />
              <SelectAuxiliar v-model="form.porte_empresa_id" tabela="porte-empresa" outlined dense />
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
              <v-label label="Inscrição Estadual" />
              <q-input v-model="form.inscricao_estadual" outlined dense />
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
              <v-label label="Inscrição Municipal" />
              <q-input v-model="form.inscricao_municipal" outlined dense />
            </div>
            <div class="col-12">
              <v-label label="Objeto Social" />
              <q-input v-model="form.objeto_social" outlined type="textarea" rows="2" />
            </div>
          </div>

          <!-- Sócios -->
          <div class="row items-center q-mt-md q-mb-xs">
            <div class="text-subtitle2 col">Sócios</div>
            <q-btn flat dense icon="add" color="primary" label="Adicionar" size="sm" @click="adicionarSocio" />
          </div>
          <div v-for="(socio, idx) in form.socios" :key="idx" class="row q-col-gutter-sm q-mb-xs items-center">
            <div class="col-md-4 col-sm-12 col-xs-12">
              <BuscaAutocomplete v-model="socio.socio_id" outlined dense />
            </div>
            <div class="col-md-2 col-sm-4 col-xs-12">
              <q-input v-model="socio.participacao_percentual" label="% Part." type="number" outlined dense />
            </div>
            <div class="col-md-2 col-sm-4 col-xs-12">
              <q-input v-model="socio.cargo" label="Cargo" outlined dense />
            </div>
            <div class="col-md-2 col-sm-4 col-xs-12">
              <q-input v-model="socio.data_entrada" label="Entrada" type="date" outlined dense />
            </div>
            <div class="col-md-1 col-sm-4 col-xs-12">
              <q-input v-model="socio.data_saida" label="Saída" type="date" outlined dense />
            </div>
            <div class="col-md-1 col-auto">
              <q-btn flat round dense icon="delete" color="negative" @click="removerSocio(idx)" />
            </div>
          </div>
        </q-card-section>
      </q-card>
    </template>

    <!-- Compliance / COAF -->
    <q-card flat bordered class="q-mb-sm">
      <q-card-section>
        <div class="titulo">Compliance / COAF</div>
      </q-card-section>
      <q-separator />
      <q-card-section>
        <div class="row q-col-gutter-sm items-center">
          <div class="col-md-3 col-sm-6 col-xs-12">
            <q-toggle v-model="form.pessoa_politicamente_exposta" label="Pessoa Politicamente Exposta" />
          </div>
          <div class="col-md-3 col-sm-6 col-xs-12">
            <q-toggle v-model="form.servidor_publico" label="Servidor Público" />
          </div>
          <template v-if="form.servidor_publico || form.pessoa_politicamente_exposta">
            <div class="col-md-3 col-sm-6 col-xs-12">
              <v-label label="Cargo/Função" />
              <q-input v-model="form.cargo_funcao" outlined dense />
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
              <v-label label="Órgão/Entidade" />
              <q-input v-model="form.orgao_entidade" outlined dense />
            </div>
          </template>
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
          <div class="col-md-2 col-sm-4 col-xs-12">
            <v-label label="CEP" />
            <q-input
              v-model="form.cep"
              mask="#####-###"
              fill-mask
              outlined
              dense
              @update:model-value="buscarCep"
            />
          </div>
          <div class="col-md-5 col-sm-8 col-xs-12">
            <v-label label="Logradouro" />
            <q-input v-model="form.logradouro" outlined dense />
          </div>
          <div class="col-md-1 col-sm-3 col-xs-12">
            <v-label label="Número" />
            <q-input v-model="form.numero" outlined dense />
          </div>
          <div class="col-md-2 col-sm-5 col-xs-12">
            <v-label label="Complemento" />
            <q-input v-model="form.complemento" outlined dense />
          </div>
          <div class="col-md-2 col-sm-4 col-xs-12">
            <v-label label="Bairro" />
            <q-input v-model="form.bairro" outlined dense />
          </div>
          <div class="col-md-4 col-sm-6 col-xs-12">
            <v-label label="Cidade" />
            <q-input v-model="form.cidade" outlined dense />
          </div>
          <div class="col-md-1 col-sm-2 col-xs-12">
            <v-label label="UF" />
            <q-input v-model="form.uf" outlined dense maxlength="2" />
          </div>
          <div class="col-md-3 col-sm-4 col-xs-12">
            <v-label label="País" />
            <q-input v-model="form.pais" outlined dense />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Observações -->
    <q-card flat bordered class="q-mb-sm">
      <q-card-section>
        <v-label label="Observações" />
        <q-input v-model="form.observacoes" outlined type="textarea" rows="2" />
      </q-card-section>
    </q-card>

    <!-- Indisponibilidades (somente edição) -->
    <IndisponibilidadesPanel
      v-if="id && form.cpf_cnpj"
      :cpf-cnpj="form.cpf_cnpj"
      class="q-mb-sm"
    />

    </div><!-- /form-readonly -->

    <!-- Motivo versão (somente edição, nunca readonly) -->
    <q-card v-if="id && !readonly" flat bordered class="q-mb-sm bg-amber-1">
      <q-card-section>
        <div class="titulo">Motivo da Alteração</div>
      </q-card-section>
      <q-separator />
      <q-card-section>
        <v-label label="Informe o motivo desta alteração" obrigatorio />
        <q-input
          v-model="form.motivo_versao"
          outlined
          type="textarea"
          rows="2"
          :error="!!erros.motivo_versao"
          :error-message="erros.motivo_versao?.[0]"
        />
      </q-card-section>
    </q-card>

    <template v-if="!readonly" v-slot:rodape>
      <q-card-section class="flex justify-end">
        <div class="q-gutter-sm">
          <q-btn label="Cancelar" color="secondary" outline icon="close" @click="cancelar" />
          <q-btn
            :label="id ? 'Salvar nova versão' : 'Cadastrar'"
            color="primary"
            icon="check"
            :loading="salvando"
            @click="salvar"
          />
        </div>
      </q-card-section>
    </template>
  </modal>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue'
import { useQuasar } from 'quasar'
import { useIndicadorPessoalStore } from 'src/stores/indicador-pessoal'
import { useAuxiliaresStore } from 'src/stores/auxiliares'
import SelectAuxiliar from 'src/components/base/SelectAuxiliar.vue'
import BuscaAutocomplete from 'src/components/indicador-pessoal/BuscaAutocomplete.vue'
import IndisponibilidadesPanel from 'src/components/indicador-pessoal/IndisponibilidadesPanel.vue'

const props = defineProps({
  id: {
    type: Number,
    default: null,
  },
  readonly: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['salvo'])

const model = defineModel({ default: false })

const $q = useQuasar()
const store = useIndicadorPessoalStore()
const auxiliaresStore = useAuxiliaresStore()

const salvando = ref(false)
const erros = ref({})

const formVazio = () => ({
  tipo_pessoa: 'F',
  ficha: null,
  cpf_cnpj: '',
  nome: '',
  nome_fantasia: null,
  rg: null,
  orgao_expedidor: null,
  data_expedicao_rg: null,
  data_nascimento: null,
  data_obito: null,
  sexo: null,
  nome_pai: null,
  nome_mae: null,
  estado_civil_id: null,
  regime_bem_id: null,
  data_casamento: null,
  anterior_lei_6515: false,
  conjuge_id: null,
  capacidade_civil_id: null,
  representante_legal: null,
  nacionalidade_id: null,
  naturalidade: null,
  profissao_id: null,
  data_abertura: null,
  data_encerramento: null,
  sede: null,
  objeto_social: null,
  tipo_empresa_id: null,
  porte_empresa_id: null,
  inscricao_estadual: null,
  inscricao_municipal: null,
  pessoa_politicamente_exposta: false,
  servidor_publico: false,
  cargo_funcao: null,
  orgao_entidade: null,
  cep: null,
  logradouro: null,
  numero: null,
  complemento: null,
  bairro: null,
  cidade: null,
  uf: null,
  pais: 'Brasil',
  observacoes: null,
  is_ativo: true,
  motivo_versao: null,
  socios: [],
})

const form = reactive(formVazio())

const isCasado = computed(() => {
  if (!form.estado_civil_id) return false
  const ec = auxiliaresStore.obter('estado-civil').find((e) => e.id === form.estado_civil_id)
  return ec?.descricao === 'Casado(a)'
})

const isIncapaz = computed(() => {
  if (!form.capacidade_civil_id) return false
  const cc = auxiliaresStore.obter('capacidade-civil').find((c) => c.id === form.capacidade_civil_id)
  return ['Relativamente Incapaz (16 a 18 anos)', 'Absolutamente Incapaz'].includes(cc?.descricao)
})

// Carrega dados quando o modal abre em modo edição
watch(model, async (aberto) => {
  if (!aberto) return
  erros.value = {}
  if (props.id) {
    $q.loading.show({ message: 'Carregando...' })
    try {
      const dados = await store.fetchById(props.id)
      Object.assign(form, formVazio(), dados)
      form.motivo_versao = null
    } finally {
      $q.loading.hide()
    }
  } else {
    Object.assign(form, formVazio())
  }
})

function adicionarSocio() {
  form.socios.push({ socio_id: null, participacao_percentual: null, cargo: null, data_entrada: null, data_saida: null })
}

function removerSocio(idx) {
  form.socios.splice(idx, 1)
}

async function buscarCep(cep) {
  const cleaned = (cep || '').replace(/\D/g, '')
  if (cleaned.length !== 8) return
  try {
    const res = await fetch(`https://viacep.com.br/ws/${cleaned}/json/`)
    const data = await res.json()
    if (!data.erro) {
      form.logradouro = data.logradouro
      form.bairro = data.bairro
      form.cidade = data.localidade
      form.uf = data.uf
    }
  } catch {
    // silencioso
  }
}

async function salvar() {
  erros.value = {}
  salvando.value = true
  try {
    const payload = { ...form }
    payload.socios = payload.socios.filter((s) => s.socio_id)

    if (props.id) {
      await store.atualizar(props.id, payload)
      $q.notify({ type: 'positive', message: 'Nova versão criada com sucesso.' })
    } else {
      await store.criar(payload)
      $q.notify({ type: 'positive', message: 'Cadastro realizado com sucesso.' })
    }

    emit('salvo')
    model.value = false
  } catch (e) {
    const data = e.response?.data
    erros.value = data?.erros ?? {}
    $q.notify({ type: 'negative', message: data?.mensagem || 'Erro ao salvar.' })
  } finally {
    salvando.value = false
  }
}

function cancelar() {
  model.value = false
}
</script>

<style scoped>
.form-readonly {
  pointer-events: none;
  opacity: 0.75;
}
</style>
