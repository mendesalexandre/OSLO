<template>
  <modal
    v-model="model"
    :titulo="produto ? 'Editar Produto' : 'Novo Produto'"
    :subtitulo="produto ? produto.nome : 'Preencha os dados do produto ou serviço'"
    tamanho="md"
    @close="fechar"
  >

    <!-- SEÇÃO: Dados do Produto -->
    <div class="produto-section-header">
      <l-icon name="package-open" :size="14" class="q-mr-xs" />
      <span class="produto-section-title">Dados do Produto</span>
    </div>
    <div class="row q-col-gutter-md q-mb-md q-mt-none">
      <div class="col-12 col-sm-4">
        <v-label label="Código" required />
        <q-input
          v-model="form.codigo"
          outlined dense
          :error="!!erros.codigo"
          :error-message="erros.codigo"
        />
      </div>
      <div class="col-12 col-sm-5">
        <v-label label="Tipo" required />
        <q-select
          v-model="form.tipo"
          :options="tiposProduto"
          option-label="label" option-value="value"
          outlined dense emit-value map-options
          :disable="!!produto"
          :error="!!erros.tipo"
          :error-message="erros.tipo"
        />
      </div>
      <div class="col-12 col-sm-3 flex items-end q-pb-xs">
        <q-toggle v-model="form.is_ativo" label="Ativo" color="positive" />
      </div>
      <div class="col-12">
        <v-label label="Nome" required />
        <q-input
          v-model="form.nome"
          outlined dense
          :error="!!erros.nome"
          :error-message="erros.nome"
        />
      </div>
      <div class="col-12">
        <v-label label="Descrição" />
        <q-input v-model="form.descricao" outlined dense type="textarea" rows="2" autogrow />
      </div>
    </div>

    <!-- SEÇÃO: Referência Tabela de Custa (somente TABELA_CUSTA) -->
    <template v-if="form.tipo === 'TABELA_CUSTA'">
      <div class="produto-section-header">
        <l-icon name="table-2" :size="14" class="q-mr-xs" />
        <span class="produto-section-title">Referência Tabela de Custa</span>
      </div>
      <div class="row q-col-gutter-md q-mb-md q-mt-none">
        <div class="col-12">
          <v-label label="Ato da Tabela de Custa" required />
          <q-select
            v-model="form.tabela_custa_ato_id"
            :options="atosFiltrados"
            option-label="nome" option-value="id"
            outlined dense emit-value map-options
            use-input input-debounce="300"
            :loading="buscandoAtos"
            :error="!!erros.tabela_custa_ato_id"
            :error-message="erros.tabela_custa_ato_id"
            @filter="filtrarAtos"
          >
            <template #option="scope">
              <q-item v-bind="scope.itemProps">
                <q-item-section>
                  <q-item-label>{{ scope.opt.nome }}</q-item-label>
                  <q-item-label caption>Código {{ scope.opt.codigo_ato }}</q-item-label>
                </q-item-section>
              </q-item>
            </template>
            <template #selected-item="scope">
              <span>{{ scope.opt.nome ?? atoSelecionadoNome }}</span>
            </template>
          </q-select>
        </div>
      </div>
    </template>

    <!-- SEÇÃO: Valores (somente PROPRIO) -->
    <template v-if="form.tipo === 'PROPRIO'">
      <div class="produto-section-header">
        <l-icon name="circle-dollar-sign" :size="14" class="q-mr-xs" />
        <span class="produto-section-title">Valores</span>
      </div>
      <div class="row q-col-gutter-md q-mb-md q-mt-none">
        <div class="col-12 col-sm-6">
          <v-label label="Tipo de cálculo" required />
          <q-select
            v-model="form.tipo_calculo"
            :options="tiposCalculo"
            option-label="label" option-value="value"
            outlined dense emit-value map-options
            :error="!!erros.tipo_calculo"
            :error-message="erros.tipo_calculo"
          />
        </div>
        <div class="col-12 col-sm-6">
          <v-label label="Valor" />
          <q-input
            v-model.number="form.valor"
            type="number" min="0" step="0.01"
            outlined dense prefix="R$"
            :disable="form.tipo_calculo === 'GRATUITO'"
            :hint="form.tipo_calculo === 'GRATUITO' ? 'Gratuito — valor fixado em 0' : ''"
          />
        </div>
      </div>
    </template>

    <!-- SEÇÃO: Configurações -->
    <div class="produto-section-header">
      <l-icon name="sliders-horizontal" :size="14" class="q-mr-xs" />
      <span class="produto-section-title">Configurações</span>
    </div>
    <div class="row q-col-gutter-md q-mb-md q-mt-none">
      <div class="col-12 col-sm-6">
        <q-toggle v-model="form.permite_base_calculo" label="Permite base de cálculo" color="primary" />
        <div class="text-caption text-grey-6">Habilita campo de valor de negócio na calculadora</div>
      </div>
      <div class="col-12">
        <v-label label="Observação" />
        <q-input v-model="form.observacao" outlined dense type="textarea" rows="2" autogrow />
      </div>
    </div>

    <!-- Rodapé -->
    <template #rodape>
      <q-btn flat no-caps label="Cancelar" color="grey-7" @click="fechar" />
      <q-btn
        unelevated no-caps
        color="primary"
        :loading="salvando"
        @click="salvar"
      >
        <l-icon name="save" :size="16" class="q-mr-sm" />
        Salvar
      </q-btn>
    </template>

  </modal>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import { useQuasar } from 'quasar'
import { api } from 'src/boot/axios'
import { useProdutoStore } from 'src/stores/produto'

const props = defineProps({
  produto: { type: Object, default: null },
})

const emit = defineEmits(['salvo'])

const model        = defineModel({ default: false })
const $q           = useQuasar()
const produtoStore = useProdutoStore()
const salvando     = ref(false)
const erros        = ref({})

// ── Atos (busca para q-select) ────────────────────────────────────────────────
const atosFiltrados  = ref([])
const buscandoAtos   = ref(false)

const atoSelecionadoNome = computed(() => {
  if (!form.value.tabela_custa_ato_id) return ''
  const encontrado = atosFiltrados.value.find(a => a.id === form.value.tabela_custa_ato_id)
  return encontrado?.nome ?? props.produto?.tabela_custa_ato?.nome ?? ''
})

const filtrarAtos = async (val, update, abort) => {
  buscandoAtos.value = true
  try {
    const { data } = await api.get('/tabela-custa-ato', { params: { busca: val, is_ativo: true } })
    update(() => { atosFiltrados.value = data.dados ?? [] })
  } catch {
    abort()
  } finally {
    buscandoAtos.value = false
  }
}

// ── Opções ────────────────────────────────────────────────────────────────────
const tiposProduto = [
  { label: 'Tabela de Custa', value: 'TABELA_CUSTA' },
  { label: 'Serviço Próprio', value: 'PROPRIO' },
]

const tiposCalculo = [
  { label: 'Fixo',       value: 'FIXO'       },
  { label: 'Percentual', value: 'PERCENTUAL'  },
  { label: 'Gratuito',   value: 'GRATUITO'    },
]

// ── Formulário ────────────────────────────────────────────────────────────────
const defaultForm = () => ({
  codigo:              '',
  nome:                '',
  descricao:           '',
  tipo:                'PROPRIO',
  is_ativo:            true,
  tabela_custa_ato_id: null,
  tipo_calculo:        'FIXO',
  valor:               0,
  permite_base_calculo: false,
  observacao:          '',
})

const form = ref(defaultForm())

watch(model, (aberto) => {
  if (aberto) {
    erros.value = {}
    if (props.produto) {
      form.value = {
        codigo:              props.produto.codigo ?? '',
        nome:                props.produto.nome ?? '',
        descricao:           props.produto.descricao ?? '',
        tipo:                props.produto.tipo ?? 'PROPRIO',
        is_ativo:            props.produto.is_ativo ?? true,
        tabela_custa_ato_id: props.produto.tabela_custa_ato_id ?? null,
        tipo_calculo:        props.produto.tipo_calculo ?? 'FIXO',
        valor:               props.produto.valor ?? 0,
        permite_base_calculo: props.produto.permite_base_calculo ?? false,
        observacao:          props.produto.observacao ?? '',
      }
      // Pré-carrega o ato selecionado se existir
      if (props.produto.tabela_custa_ato) {
        atosFiltrados.value = [props.produto.tabela_custa_ato]
      }
    } else {
      form.value      = defaultForm()
      atosFiltrados.value = []
    }
  }
})

// ── API ───────────────────────────────────────────────────────────────────────
const salvar = async () => {
  erros.value    = {}
  salvando.value = true
  try {
    let resultado
    if (props.produto) {
      resultado = await produtoStore.atualizar(props.produto.uuid, form.value)
    } else {
      resultado = await produtoStore.criar(form.value)
    }
    emit('salvo', resultado)
    $q.notify({ type: 'positive', message: 'Produto salvo com sucesso', position: 'top' })
    fechar()
  } catch (e) {
    const apiErros = e.response?.data?.erros ?? {}
    if (Object.keys(apiErros).length) {
      erros.value = Object.fromEntries(
        Object.entries(apiErros).map(([k, v]) => [k, Array.isArray(v) ? v[0] : v])
      )
    } else {
      $q.notify({ type: 'negative', message: 'Erro ao salvar produto', caption: e.response?.data?.mensagem })
    }
  } finally {
    salvando.value = false
  }
}

const fechar = () => { model.value = false }
</script>

<style lang="scss" scoped>
.produto-section-header {
  display: flex;
  align-items: center;
  padding: 8px 12px;
  background: #fafafa;
  border: 1px solid #e0e0e0;
  border-radius: 4px 4px 0 0;
  margin-bottom: 12px;

  .produto-section-title {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #616161;
  }
}
</style>
