<template>
  <q-page padding>
    <div class="row items-center q-mb-md">
      <q-btn flat dense round icon="arrow_back" :to="{ name: 'indisponibilidades.lista' }" class="q-mr-sm" />
      <div class="text-h5">{{ isEdicao ? 'Editar Indisponibilidade' : 'Nova Indisponibilidade' }}</div>
    </div>

    <q-form @submit.prevent="salvar">
      <!-- Dados principais -->
      <q-card flat bordered class="q-mb-md">
        <q-card-section>
          <div class="text-subtitle1 q-mb-md">Dados Principais</div>
          <div class="row q-gutter-md">
            <q-input
              v-model="form.protocolo_indisponibilidade"
              label="Protocolo *"
              outlined
              dense
              class="col-3"
              :disable="isEdicao"
              :error="!!erros.protocolo_indisponibilidade"
              :error-message="erros.protocolo_indisponibilidade?.[0]"
            />
            <q-input
              v-model="form.numero_processo"
              label="Nº Processo"
              outlined
              dense
              class="col-4"
            />
            <q-select
              v-model="form.status"
              label="Status *"
              :options="opcoesStatus"
              option-label="label"
              option-value="value"
              emit-value
              map-options
              outlined
              dense
              class="col-2"
              :error="!!erros.status"
              :error-message="erros.status?.[0]"
            />
            <q-select
              v-model="form.tipo"
              label="Tipo"
              :options="opcoesTipo"
              option-label="label"
              option-value="value"
              emit-value
              map-options
              outlined
              dense
              clearable
              class="col-2"
            />
          </div>
          <div class="row q-gutter-md q-mt-sm">
            <q-input v-model="form.usuario" label="Usuário" outlined dense class="col-3" />
            <q-input v-model="form.forum_vara" label="Fórum/Vara" outlined dense class="col-4" />
            <q-input v-model="form.nome_instituicao" label="Instituição" outlined dense class="col" />
          </div>
          <div class="row q-gutter-md q-mt-sm">
            <q-input v-model="form.email" label="E-mail" type="email" outlined dense class="col-3" />
            <q-input v-model="form.telefone" label="Telefone" outlined dense class="col-2" />
            <q-input v-model="form.data_pedido" label="Data do Pedido" type="datetime-local" outlined dense class="col-3" />
            <div class="col-auto column justify-center">
              <q-toggle v-model="form.ordem_prioritaria" label="Ordem prioritária" />
              <q-toggle v-model="form.segredo_justica" label="Segredo de Justiça" />
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Partes -->
      <q-card flat bordered class="q-mb-md">
        <q-card-section>
          <div class="row items-center q-mb-md">
            <div class="text-subtitle1 col">Partes</div>
            <q-btn
              flat
              dense
              icon="add"
              label="Adicionar parte"
              color="primary"
              @click="adicionarParte"
            />
          </div>

          <div
            v-for="(parte, idx) in form.partes"
            :key="idx"
            class="q-mb-md"
          >
            <q-card flat bordered>
              <q-card-section>
                <div class="row items-center q-mb-sm">
                  <div class="text-caption text-grey-7 col">Parte {{ idx + 1 }}</div>
                  <q-btn flat round dense icon="remove_circle" color="negative" @click="removerParte(idx)">
                    <q-tooltip>Remover parte</q-tooltip>
                  </q-btn>
                </div>
                <div class="row q-gutter-md">
                  <q-input
                    v-model="parte.cpf_cnpj"
                    label="CPF/CNPJ *"
                    outlined
                    dense
                    class="col-3"
                    :mask="parte.cpf_cnpj?.replace(/\D/g, '').length <= 11 ? '###.###.###-##' : '##.###.###/####-##'"
                    fill-mask
                    @blur="buscarNomeParte(idx)"
                  />
                  <q-input
                    v-model="parte.nome_razao"
                    label="Nome / Razão Social *"
                    outlined
                    dense
                    class="col"
                  />
                </div>

                <!-- Matrículas da parte -->
                <div class="q-mt-sm">
                  <div class="row items-center q-mb-xs">
                    <div class="text-caption text-grey-6 col">Matrículas</div>
                    <q-btn flat dense icon="add" size="sm" label="Matrícula" @click="adicionarMatricula(idx)" />
                  </div>
                  <div v-for="(mat, mIdx) in parte.matriculas" :key="mIdx" class="row q-gutter-sm q-mb-xs">
                    <q-input
                      v-model="mat.matricula"
                      outlined
                      dense
                      class="col"
                      placeholder="Nº da matrícula"
                    />
                    <q-btn flat round dense icon="close" size="sm" color="negative" @click="removerMatricula(idx, mIdx)" />
                  </div>
                </div>
              </q-card-section>
            </q-card>
          </div>

          <div v-if="!form.partes.length" class="text-grey-5 text-center q-py-sm">
            Nenhuma parte cadastrada
          </div>
        </q-card-section>
      </q-card>

      <!-- Ações -->
      <div class="row justify-end q-gutter-sm">
        <q-btn flat label="Cancelar" :to="{ name: 'indisponibilidades.lista' }" />
        <q-btn
          unelevated
          color="primary"
          :label="isEdicao ? 'Salvar alterações' : 'Cadastrar'"
          type="submit"
          :loading="carregando"
        />
      </div>
    </q-form>
  </q-page>
</template>

<script setup>
import { onMounted, reactive, ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useIndisponibilidadeStore } from 'src/stores/indisponibilidade'
import { useIndicadorPessoalStore } from 'src/stores/indicador-pessoal'

const route = useRoute()
const router = useRouter()
const $q = useQuasar()
const store = useIndisponibilidadeStore()
const indicadorStore = useIndicadorPessoalStore()

const isEdicao = computed(() => !!route.params.id)
const carregando = ref(false)
const erros = ref({})

const opcoesStatus = [
  { label: 'Pendente', value: 'pendente' },
  { label: 'Cumprida', value: 'cumprida' },
  { label: 'Cancelada', value: 'cancelada' },
  { label: 'Em análise', value: 'em_analise' },
]

const opcoesTipo = [
  { label: 'Judicial', value: 'judicial' },
  { label: 'Extrajudicial', value: 'extrajudicial' },
]

const form = reactive({
  protocolo_indisponibilidade: '',
  numero_processo: '',
  usuario: '',
  status: 'pendente',
  tipo: null,
  forum_vara: '',
  nome_instituicao: '',
  email: '',
  telefone: '',
  data_pedido: null,
  ordem_prioritaria: false,
  segredo_justica: false,
  partes: [],
})

onMounted(async () => {
  if (isEdicao.value) {
    const dados = await store.fetchById(route.params.id)
    Object.assign(form, {
      protocolo_indisponibilidade: dados.protocolo_indisponibilidade,
      numero_processo: dados.numero_processo ?? '',
      usuario: dados.usuario ?? '',
      status: dados.status,
      tipo: dados.tipo ?? null,
      forum_vara: dados.forum_vara ?? '',
      nome_instituicao: dados.nome_instituicao ?? '',
      email: dados.email ?? '',
      telefone: dados.telefone ?? '',
      data_pedido: dados.data_pedido ? dados.data_pedido.slice(0, 16) : null,
      ordem_prioritaria: dados.ordem_prioritaria ?? false,
      segredo_justica: dados.segredo_justica ?? false,
      partes: (dados.partes ?? []).map(p => ({
        cpf_cnpj: p.cpf_cnpj,
        nome_razao: p.nome_razao,
        matriculas: (p.matriculas ?? []).map(m => ({ matricula: m.matricula })),
      })),
    })
  }
})

function adicionarParte() {
  form.partes.push({ cpf_cnpj: '', nome_razao: '', matriculas: [] })
}

function removerParte(idx) {
  form.partes.splice(idx, 1)
}

function adicionarMatricula(parteIdx) {
  form.partes[parteIdx].matriculas.push({ matricula: '' })
}

function removerMatricula(parteIdx, matIdx) {
  form.partes[parteIdx].matriculas.splice(matIdx, 1)
}

async function buscarNomeParte(idx) {
  const cpf = form.partes[idx].cpf_cnpj?.replace(/\D/g, '')
  if (!cpf || cpf.length < 11) return
  if (form.partes[idx].nome_razao) return

  try {
    const resultados = await indicadorStore.buscar(cpf)
    if (resultados.length > 0) {
      form.partes[idx].nome_razao = resultados[0].nome
    }
  } catch {
    // silencioso — usuário pode preencher manualmente
  }
}

async function salvar() {
  erros.value = {}
  carregando.value = true
  try {
    if (isEdicao.value) {
      await store.atualizar(route.params.id, { ...form })
    } else {
      await store.criar({ ...form })
    }
    $q.notify({ type: 'positive', message: isEdicao.value ? 'Atualizado com sucesso.' : 'Cadastrado com sucesso.' })
    router.push({ name: 'indisponibilidades.lista' })
  } catch (e) {
    erros.value = e?.response?.data?.erros ?? {}
    $q.notify({ type: 'negative', message: e?.response?.data?.mensagem ?? 'Erro ao salvar.' })
  } finally {
    carregando.value = false
  }
}
</script>
