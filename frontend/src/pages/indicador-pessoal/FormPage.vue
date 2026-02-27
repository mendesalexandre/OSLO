<template>
  <q-page padding>
    <div class="row items-center q-mb-md">
      <q-btn flat dense round icon="arrow_back" :to="{ name: 'indicador-pessoal.lista' }" class="q-mr-sm" />
      <div class="text-h5">{{ isEdicao ? 'Editar Indicador Pessoal' : 'Novo Indicador Pessoal' }}</div>
    </div>

    <q-form @submit.prevent="salvar">
      <!-- Tipo de pessoa -->
      <q-card flat bordered class="q-mb-md">
        <q-card-section>
          <div class="text-subtitle1 q-mb-sm">Tipo de Pessoa</div>
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
            :disable="isEdicao"
          />
        </q-card-section>
      </q-card>

      <!-- Dados principais -->
      <q-card flat bordered class="q-mb-md">
        <q-card-section>
          <div class="text-subtitle1 q-mb-md">Identificação</div>
          <div class="row q-gutter-md">
            <q-input
              v-model="form.ficha"
              label="Ficha"
              outlined
              dense
              class="col-2"
            />
            <q-input
              v-model="form.cpf_cnpj"
              :label="form.tipo_pessoa === 'F' ? 'CPF' : 'CNPJ'"
              :mask="form.tipo_pessoa === 'F' ? '###.###.###-##' : '##.###.###/####-##'"
              fill-mask
              outlined
              dense
              class="col-3"
              :error="!!erros.cpf_cnpj"
              :error-message="erros.cpf_cnpj?.[0]"
            />
            <q-input
              v-model="form.nome"
              :label="form.tipo_pessoa === 'F' ? 'Nome Completo' : 'Razão Social'"
              outlined
              dense
              class="col"
              :error="!!erros.nome"
              :error-message="erros.nome?.[0]"
            />
            <q-input
              v-if="form.tipo_pessoa === 'J'"
              v-model="form.nome_fantasia"
              label="Nome Fantasia"
              outlined
              dense
              class="col"
            />
          </div>
        </q-card-section>
      </q-card>

      <!-- Dados PF -->
      <q-card v-if="form.tipo_pessoa === 'F'" flat bordered class="q-mb-md">
        <q-card-section>
          <div class="text-subtitle1 q-mb-md">Dados Pessoais</div>
          <div class="row q-gutter-md">
            <q-input v-model="form.rg" label="RG" outlined dense class="col-2" />
            <q-input v-model="form.orgao_expedidor" label="Órgão Exp." outlined dense class="col-2" />
            <q-input v-model="form.data_expedicao_rg" label="Data Expedição" type="date" outlined dense class="col-2" />
            <q-input v-model="form.data_nascimento" label="Nascimento" type="date" outlined dense class="col-2" />
            <q-input v-model="form.data_obito" label="Óbito" type="date" outlined dense class="col-2" />
            <q-select
              v-model="form.sexo"
              label="Sexo"
              :options="[{ label: 'Masculino', value: 'M' }, { label: 'Feminino', value: 'F' }, { label: 'Outro', value: 'O' }]"
              option-label="label"
              option-value="value"
              emit-value
              map-options
              outlined
              dense
              clearable
              class="col-2"
            />
            <q-input v-model="form.nome_pai" label="Nome do Pai" outlined dense class="col" />
            <q-input v-model="form.nome_mae" label="Nome da Mãe" outlined dense class="col" />
          </div>

          <div class="text-subtitle2 q-mt-md q-mb-sm">Estado Civil</div>
          <div class="row q-gutter-md">
            <SelectAuxiliar v-model="form.estado_civil_id" tabela="estado-civil" label="Estado Civil" outlined dense class="col-3" />
            <SelectAuxiliar
              v-if="isCasado"
              v-model="form.regime_bem_id"
              tabela="regime-bem"
              label="Regime de Bens"
              outlined
              dense
              class="col-3"
              :error="!!erros.regime_bem_id"
              :error-message="erros.regime_bem_id?.[0]"
            />
            <q-input v-if="isCasado" v-model="form.data_casamento" label="Data do Casamento" type="date" outlined dense class="col-2" />
            <q-toggle v-if="isCasado" v-model="form.anterior_lei_6515" label="Anterior à Lei 6.515/77" class="col-auto" />
          </div>
          <div v-if="isCasado" class="row q-gutter-md q-mt-sm">
            <BuscaAutocomplete
              v-model="form.conjuge_id"
              label="Cônjuge"
              outlined
              dense
              class="col-4"
              :error="!!erros.conjuge_id"
              :error-message="erros.conjuge_id?.[0]"
            />
          </div>

          <div class="text-subtitle2 q-mt-md q-mb-sm">Capacidade Civil</div>
          <div class="row q-gutter-md">
            <SelectAuxiliar v-model="form.capacidade_civil_id" tabela="capacidade-civil" label="Capacidade Civil" outlined dense class="col-3" />
            <q-input
              v-if="isIncapaz"
              v-model="form.representante_legal"
              label="Representante Legal"
              outlined
              dense
              class="col-4"
              :error="!!erros.representante_legal"
              :error-message="erros.representante_legal?.[0]"
            />
          </div>

          <div class="text-subtitle2 q-mt-md q-mb-sm">Naturalidade / Profissão</div>
          <div class="row q-gutter-md">
            <SelectAuxiliar v-model="form.nacionalidade_id" tabela="nacionalidade" label="Nacionalidade" outlined dense class="col-3" />
            <q-input v-model="form.naturalidade" label="Naturalidade" outlined dense class="col-3" />
            <SelectAuxiliar v-model="form.profissao_id" tabela="profissao" label="Profissão" outlined dense class="col-3" />
          </div>
        </q-card-section>
      </q-card>

      <!-- Dados PJ -->
      <q-card v-if="form.tipo_pessoa === 'J'" flat bordered class="q-mb-md">
        <q-card-section>
          <div class="text-subtitle1 q-mb-md">Dados da Empresa</div>
          <div class="row q-gutter-md">
            <q-input v-model="form.data_abertura" label="Data de Abertura" type="date" outlined dense class="col-2" />
            <q-input v-model="form.data_encerramento" label="Data de Encerramento" type="date" outlined dense class="col-2" />
            <q-input v-model="form.sede" label="Sede" outlined dense class="col" />
            <SelectAuxiliar v-model="form.tipo_empresa_id" tabela="tipo-empresa" label="Tipo de Empresa" outlined dense class="col-2" />
            <SelectAuxiliar v-model="form.porte_empresa_id" tabela="porte-empresa" label="Porte" outlined dense class="col-2" />
            <q-input v-model="form.inscricao_estadual" label="Inscrição Estadual" outlined dense class="col-2" />
            <q-input v-model="form.inscricao_municipal" label="Inscrição Municipal" outlined dense class="col-2" />
            <q-input v-model="form.objeto_social" label="Objeto Social" outlined dense type="textarea" rows="3" class="col-12" />
          </div>

          <!-- Sócios -->
          <div class="text-subtitle2 q-mt-md q-mb-sm">
            Sócios
            <q-btn flat dense round icon="add" color="primary" @click="adicionarSocio" />
          </div>
          <div v-for="(socio, idx) in form.socios" :key="idx" class="row q-gutter-md q-mb-sm items-center">
            <BuscaAutocomplete v-model="socio.socio_id" label="Sócio" outlined dense class="col-4" />
            <q-input v-model="socio.participacao_percentual" label="% Participação" type="number" outlined dense class="col-1" />
            <q-input v-model="socio.cargo" label="Cargo" outlined dense class="col-2" />
            <q-input v-model="socio.data_entrada" label="Entrada" type="date" outlined dense class="col-2" />
            <q-input v-model="socio.data_saida" label="Saída" type="date" outlined dense class="col-2" />
            <q-btn flat round dense icon="delete" color="negative" @click="removerSocio(idx)" />
          </div>
        </q-card-section>
      </q-card>

      <!-- COAF -->
      <q-card flat bordered class="q-mb-md">
        <q-card-section>
          <div class="text-subtitle1 q-mb-md">Compliance / COAF</div>
          <div class="row q-gutter-md items-center">
            <q-toggle v-model="form.pessoa_politicamente_exposta" label="Pessoa Politicamente Exposta" />
            <q-toggle v-model="form.servidor_publico" label="Servidor Público" />
            <q-input v-if="form.servidor_publico || form.pessoa_politicamente_exposta" v-model="form.cargo_funcao" label="Cargo/Função" outlined dense class="col-3" />
            <q-input v-if="form.servidor_publico || form.pessoa_politicamente_exposta" v-model="form.orgao_entidade" label="Órgão/Entidade" outlined dense class="col-3" />
          </div>
        </q-card-section>
      </q-card>

      <!-- Endereço -->
      <q-card flat bordered class="q-mb-md">
        <q-card-section>
          <div class="text-subtitle1 q-mb-md">Endereço</div>
          <div class="row q-gutter-md">
            <q-input
              v-model="form.cep"
              label="CEP"
              mask="#####-###"
              fill-mask
              outlined
              dense
              class="col-2"
              @update:model-value="buscarCep"
            />
            <q-input v-model="form.logradouro" label="Logradouro" outlined dense class="col-4" />
            <q-input v-model="form.numero" label="Número" outlined dense class="col-1" />
            <q-input v-model="form.complemento" label="Complemento" outlined dense class="col-2" />
            <q-input v-model="form.bairro" label="Bairro" outlined dense class="col-3" />
            <q-input v-model="form.cidade" label="Cidade" outlined dense class="col-3" />
            <q-input v-model="form.uf" label="UF" outlined dense class="col-1" maxlength="2" />
            <q-input v-model="form.pais" label="País" outlined dense class="col-2" />
          </div>
        </q-card-section>
      </q-card>

      <!-- Observações -->
      <q-card flat bordered class="q-mb-md">
        <q-card-section>
          <q-input v-model="form.observacoes" label="Observações" outlined type="textarea" rows="3" />
        </q-card-section>
      </q-card>

      <!-- Indisponibilidades (somente edição) -->
      <IndisponibilidadesPanel
        v-if="isEdicao && form.cpf_cnpj"
        :cpf-cnpj="form.cpf_cnpj"
        class="q-mb-md"
      />

      <!-- Motivo versão (somente edição) -->
      <q-card v-if="isEdicao" flat bordered class="q-mb-md bg-amber-1">
        <q-card-section>
          <div class="text-subtitle1 q-mb-sm">Motivo da Alteração</div>
          <q-input
            v-model="form.motivo_versao"
            label="Informe o motivo desta alteração *"
            outlined
            type="textarea"
            rows="2"
            :error="!!erros.motivo_versao"
            :error-message="erros.motivo_versao?.[0]"
          />
        </q-card-section>
      </q-card>

      <!-- Ações -->
      <div class="row justify-end q-gutter-sm">
        <q-btn flat label="Cancelar" :to="{ name: 'indicador-pessoal.lista' }" />
        <q-btn
          type="submit"
          :label="isEdicao ? 'Salvar nova versão' : 'Cadastrar'"
          color="primary"
          unelevated
          :loading="salvando"
        />
      </div>
    </q-form>
  </q-page>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useIndicadorPessoalStore } from 'src/stores/indicador-pessoal'
import SelectAuxiliar from 'src/components/base/SelectAuxiliar.vue'
import BuscaAutocomplete from 'src/components/indicador-pessoal/BuscaAutocomplete.vue'
import { useAuxiliaresStore } from 'src/stores/auxiliares'
import IndisponibilidadesPanel from 'src/components/indicador-pessoal/IndisponibilidadesPanel.vue'

const route = useRoute()
const router = useRouter()
const $q = useQuasar()
const indicadorPessoalStore = useIndicadorPessoalStore()
const auxiliaresStore = useAuxiliaresStore()

const isEdicao = computed(() => !!route.params.id)
const salvando = ref(false)
const erros = ref({})

const form = reactive({
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

onMounted(async () => {
  if (isEdicao.value) {
    const dados = await indicadorPessoalStore.fetchById(route.params.id)
    Object.assign(form, dados)
    form.motivo_versao = null
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
    // silently ignore
  }
}

async function salvar() {
  erros.value = {}
  salvando.value = true

  try {
    const payload = { ...form }
    // Limpa socios vazios
    payload.socios = payload.socios.filter((s) => s.socio_id)

    if (isEdicao.value) {
      await indicadorPessoalStore.atualizar(route.params.id, payload)
      $q.notify({ type: 'positive', message: 'Nova versão criada com sucesso.' })
    } else {
      await indicadorPessoalStore.criar(payload)
      $q.notify({ type: 'positive', message: 'Cadastro realizado com sucesso.' })
    }
    router.push({ name: 'indicador-pessoal.lista' })
  } catch (e) {
    const data = e.response?.data
    if (data?.erros) {
      erros.value = data.erros
    }
    $q.notify({ type: 'negative', message: data?.mensagem || 'Erro ao salvar.' })
  } finally {
    salvando.value = false
  }
}
</script>
