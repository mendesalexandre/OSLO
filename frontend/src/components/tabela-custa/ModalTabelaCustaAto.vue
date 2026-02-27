<template>
  <modal
    v-model="model"
    titulo="Editar Ato"
    :subtitulo="ato ? `Código ${ato.codigo_ato} — Ano ${ato.ano}` : ''"
    tamanho="md"
    @close="fechar"
  >

    <!-- SEÇÃO: Dados do Ato -->
    <div class="ato-section-header">
      <l-icon name="file-text" :size="14" class="q-mr-xs" />
      <span class="ato-section-title">Dados do Ato</span>
    </div>
    <div class="row q-col-gutter-md q-mb-md q-mt-none">
      <div class="col-12">
        <v-label label="Nome do ato" required />
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
      <div class="col-12 col-sm-6 flex items-center">
        <q-toggle v-model="form.is_ativo" label="Ativo" color="positive" class="q-mt-sm" />
      </div>
    </div>

    <!-- SEÇÃO: Valores -->
    <div class="ato-section-header">
      <l-icon name="circle-dollar-sign" :size="14" class="q-mr-xs" />
      <span class="ato-section-title">Valores</span>
    </div>
    <div class="row q-col-gutter-md q-mb-md q-mt-none">
      <div class="col-12 col-sm-6">
        <v-label label="Valor do serviço" />
        <q-input
          v-model.number="form.valor_servico"
          type="number" min="0" step="0.01"
          outlined dense prefix="R$"
          :disable="form.tipo_calculo === 'GRATUITO'"
          :hint="form.tipo_calculo === 'GRATUITO' ? 'Gratuito — valor fixado em 0' : ''"
        />
      </div>
    </div>

    <!-- SEÇÃO: Faixa Progressiva (condicional) -->
    <template v-if="form.tipo_calculo === 'FAIXA_PROGRESSIVA'">
      <div class="ato-section-header">
        <l-icon name="trending-up" :size="14" class="q-mr-xs" />
        <span class="ato-section-title">Faixa Progressiva</span>
      </div>
      <div class="row q-col-gutter-md q-mb-md q-mt-none">
        <div class="col-12 col-sm-6">
          <v-label label="Início do incremento" />
          <q-input v-model.number="form.valor_inicio_incremento" type="number" min="0" step="0.01" outlined dense prefix="R$" />
        </div>
        <div class="col-12 col-sm-6">
          <v-label label="Tamanho da faixa" />
          <q-input v-model.number="form.valor_faixa" type="number" min="0" step="0.01" outlined dense prefix="R$" />
        </div>
        <div class="col-12 col-sm-6">
          <v-label label="Acréscimo por faixa" />
          <q-input v-model.number="form.valor_acrescimo" type="number" min="0" step="0.01" outlined dense prefix="R$" />
        </div>
        <div class="col-12 col-sm-6">
          <v-label label="Valor máximo" />
          <q-input
            v-model.number="form.valor_maximo"
            type="number" min="0" step="0.01"
            outlined dense prefix="R$"
            hint="Deixe 0 para sem limite"
          />
        </div>
      </div>
    </template>

    <!-- SEÇÃO: Configurações -->
    <div class="ato-section-header">
      <l-icon name="sliders-horizontal" :size="14" class="q-mr-xs" />
      <span class="ato-section-title">Configurações</span>
    </div>
    <div class="row q-col-gutter-md q-mb-md q-mt-none">
      <div class="col-12 col-sm-6">
        <q-toggle v-model="form.permitir_base_calculo" label="Permitir base de cálculo" color="primary" />
        <div class="text-caption text-grey-6">Habilita o campo de valor do negócio na calculadora</div>
      </div>
      <div class="col-12 col-sm-6">
        <q-toggle v-model="form.permitir_desconto" label="Permitir desconto" color="primary" />
        <div class="text-caption text-grey-6">Permite aplicar descontos sobre o emolumento</div>
      </div>
    </div>

    <!-- SEÇÃO: Vigência -->
    <div class="ato-section-header">
      <l-icon name="calendar-range" :size="14" class="q-mr-xs" />
      <span class="ato-section-title">Vigência</span>
    </div>
    <div class="row q-col-gutter-md q-mt-none">
      <div class="col-12 col-sm-6">
        <v-label label="Início da vigência" />
        <q-input v-model="form.vigencia_inicio" outlined dense mask="##/##/####" placeholder="DD/MM/AAAA">
          <template #append>
            <q-icon class="cursor-pointer" size="14px" style="display:flex;align-items:center"><l-icon name="calendar" :size="14" />
              <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                <q-date v-model="form.vigencia_inicio" mask="DD/MM/YYYY" minimal>
                  <div class="row items-center justify-end">
                    <q-btn v-close-popup label="OK" color="primary" flat />
                  </div>
                </q-date>
              </q-popup-proxy>
            </q-icon>
          </template>
        </q-input>
      </div>
      <div class="col-12 col-sm-6">
        <v-label label="Fim da vigência" />
        <q-input v-model="form.vigencia_fim" outlined dense mask="##/##/####" placeholder="DD/MM/AAAA (opcional)">
          <template #append>
            <q-icon class="cursor-pointer" size="14px" style="display:flex;align-items:center"><l-icon name="calendar" :size="14" />
              <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                <q-date v-model="form.vigencia_fim" mask="DD/MM/YYYY" minimal>
                  <div class="row items-center justify-end">
                    <q-btn v-close-popup label="OK" color="primary" flat />
                  </div>
                </q-date>
              </q-popup-proxy>
            </q-icon>
          </template>
        </q-input>
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
import { ref, watch } from 'vue'
import { useQuasar, date } from 'quasar'
import { useTabelaCustaAto } from 'src/stores/tabela-custa-ato'

const props = defineProps({
  ato: { type: Object, default: null },
})

const emit = defineEmits(['salvo'])

const model               = defineModel({ default: false })
const $q                  = useQuasar()
const tabelaCustaAtoStore = useTabelaCustaAto()
const salvando            = ref(false)
const erros    = ref({})

// ── Opções ────────────────────────────────────────────────────────────────────
const tiposCalculo = [
  { label: 'Fixo',              value: 'FIXO'              },
  { label: 'Faixa Progressiva', value: 'FAIXA_PROGRESSIVA' },
  { label: 'Gratuito',          value: 'GRATUITO'          },
]

// ── Formulário ────────────────────────────────────────────────────────────────
const defaultForm = () => ({
  nome:                    '',
  descricao:               '',
  tipo_calculo:            'FIXO',
  is_ativo:                true,
  valor_servico:           0,
  valor_inicio_incremento: 0,
  valor_faixa:             0,
  valor_acrescimo:         0,
  valor_maximo:            0,
  permitir_base_calculo:   false,
  permitir_desconto:       false,
  vigencia_inicio:         '',
  vigencia_fim:            '',
})

const form = ref(defaultForm())

// Popula o form quando o modal abre com um ato
watch(model, (aberto) => {
  if (aberto && props.ato) {
    erros.value = {}
    form.value  = {
      nome:                    props.ato.nome ?? '',
      descricao:               props.ato.descricao ?? '',
      tipo_calculo:            props.ato.tipo_calculo ?? 'FIXO',
      is_ativo:                props.ato.is_ativo ?? true,
      valor_servico:           props.ato.valor_servico ?? 0,
      valor_inicio_incremento: props.ato.valor_inicio_incremento ?? 0,
      valor_faixa:             props.ato.valor_faixa ?? 0,
      valor_acrescimo:         props.ato.valor_acrescimo ?? 0,
      valor_maximo:            props.ato.valor_maximo ?? 0,
      permitir_base_calculo:   props.ato.permitir_base_calculo ?? false,
      permitir_desconto:       props.ato.permitir_desconto ?? false,
      vigencia_inicio:         formatDateInput(props.ato.vigencia_inicio),
      vigencia_fim:            formatDateInput(props.ato.vigencia_fim),
    }
  }
})

// ── API ───────────────────────────────────────────────────────────────────────
const salvar = async () => {
  if (!props.ato) return
  erros.value    = {}
  salvando.value = true
  try {
    const payload = {
      ...form.value,
      vigencia_inicio: parseDateInput(form.value.vigencia_inicio),
      vigencia_fim:    parseDateInput(form.value.vigencia_fim),
    }
    const atualizado = await tabelaCustaAtoStore.atualizar(props.ato.uuid, payload)

    emit('salvo', atualizado)
    $q.notify({ type: 'positive', message: 'Ato atualizado com sucesso', position: 'top' })
    fechar()
  } catch (e) {
    const apiErros = e.response?.data?.erros ?? {}
    if (Object.keys(apiErros).length) {
      erros.value = Object.fromEntries(
        Object.entries(apiErros).map(([k, v]) => [k, Array.isArray(v) ? v[0] : v])
      )
    } else {
      $q.notify({ type: 'negative', message: 'Erro ao salvar ato', caption: e.response?.data?.mensagem })
    }
  } finally {
    salvando.value = false
  }
}

// ── Helpers ───────────────────────────────────────────────────────────────────
const formatDateInput = (d) => d ? date.formatDate(d, 'DD/MM/YYYY') : ''
const parseDateInput  = (d) => {
  if (!d || d.length < 10) return null
  const [dia, mes, ano] = d.split('/')
  return `${ano}-${mes}-${dia}`
}

const fechar = () => { model.value = false }
</script>

<style lang="scss" scoped>
.ato-section-header {
  display: flex;
  align-items: center;
  padding: 8px 12px;
  background: #fafafa;
  border: 1px solid #e0e0e0;
  border-radius: 4px 4px 0 0;
  margin-bottom: 12px;

  .ato-section-title {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #616161;
  }
}
</style>
