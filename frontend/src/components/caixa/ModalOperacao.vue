<template>
  <modal
    v-model="model"
    :titulo="tituloModal"
    tamanho="sm"
    @close="model = false"
  >
    <!-- Seletor de tipo (quando nenhum tipo é pré-selecionado) -->
    <div v-if="!tipoFixo" class="row q-col-gutter-sm q-mb-md">
      <div class="col-4" v-for="op in tiposOperacao" :key="op.value">
        <q-btn
          unelevated no-caps class="full-width"
          :color="form.tipo === op.value ? op.cor : 'grey-3'"
          :text-color="form.tipo === op.value ? 'white' : 'grey-8'"
          @click="form.tipo = op.value"
        >
          <l-icon :name="op.icon" :size="16" class="q-mr-xs" />
          {{ op.label }}
        </q-btn>
      </div>
    </div>

    <div class="q-gutter-md">
      <!-- Caixa origem -->
      <div>
        <v-label label="Caixa *" />
        <q-select
          v-model="form.caixa_id"
          :options="caixasAtivos"
          option-value="id"
          option-label="nome"
          emit-value map-options
          outlined dense
          :rules="[v => !!v || 'Obrigatório']"
        >
          <template #prepend><l-icon name="wallet" :size="14" /></template>
        </q-select>
      </div>

      <!-- Caixa destino (só para transferência) -->
      <div v-if="form.tipo === 'TRANSFERENCIA'">
        <v-label label="Caixa Destino *" />
        <q-select
          v-model="form.caixa_destino_id"
          :options="caixasDestino"
          option-value="id"
          option-label="nome"
          emit-value map-options
          outlined dense
          :rules="[v => !!v || 'Obrigatório']"
        >
          <template #prepend><l-icon name="wallet" :size="14" /></template>
        </q-select>
      </div>

      <div>
        <v-label label="Valor *" />
        <v-money v-model.number="form.valor" outlined dense />
      </div>

      <div>
        <v-label label="Descrição *" />
        <q-input
          v-model="form.descricao"
          outlined dense
          :rules="[v => !!v || 'Obrigatório']"
        />
      </div>

      <div>
        <v-label label="Observação" />
        <q-input v-model="form.observacao" outlined dense type="textarea" rows="2" autogrow />
      </div>
    </div>

    <template #rodape>
      <div class="row justify-end q-gutter-sm">
        <q-btn flat no-caps label="Cancelar" @click="model = false" />
        <q-btn unelevated no-caps :color="corBotao" :loading="salvando" @click="salvar">
          <l-icon :name="iconBotao" :size="16" class="q-mr-sm" />
          {{ tituloModal }}
        </q-btn>
      </div>
    </template>
  </modal>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useQuasar } from 'quasar'
import { useCaixaOperacaoStore } from 'src/stores/caixaOperacao'
import { useCaixaStore } from 'src/stores/caixa'

defineOptions({ name: 'ModalOperacao' })

const props = defineProps({
  // Se informado, fixa o tipo (SANGRIA | REFORCO | TRANSFERENCIA)
  tipo: { type: String, default: null },
  // Se informado, pré-seleciona o caixa de origem
  caixaId: { type: Number, default: null },
})

const emit = defineEmits(['salvo'])
const model = defineModel({ default: false })

const $q               = useQuasar()
const caixaOperacaoStore = useCaixaOperacaoStore()
const caixaStore       = useCaixaStore()
const salvando         = ref(false)

const tiposOperacao = [
  { value: 'SANGRIA',       label: 'Sangria',       cor: 'negative', icon: 'arrow-down-circle' },
  { value: 'REFORCO',       label: 'Reforço',       cor: 'positive', icon: 'arrow-up-circle'   },
  { value: 'TRANSFERENCIA', label: 'Transferência',  cor: 'primary',  icon: 'arrow-left-right'  },
]

const tipoFixo = computed(() => !!props.tipo)

const form = ref({
  tipo:             props.tipo ?? 'SANGRIA',
  caixa_id:         null,
  caixa_destino_id: null,
  valor:            0,
  descricao:        '',
  observacao:       '',
})

const caixasAtivos = computed(() =>
  caixaStore.caixas.filter(c => c.is_ativo)
)

const caixasDestino = computed(() =>
  caixaStore.caixas.filter(c => c.is_ativo && c.id !== form.value.caixa_id)
)

const tipoAtual = computed(() =>
  tiposOperacao.find(t => t.value === form.value.tipo) ?? tiposOperacao[0]
)

const tituloModal = computed(() => tipoAtual.value.label)
const corBotao    = computed(() => tipoAtual.value.cor)
const iconBotao   = computed(() => tipoAtual.value.icon)

watch(model, async (aberto) => {
  if (aberto) {
    await caixaStore.listar()
    form.value = {
      tipo:             props.tipo ?? 'SANGRIA',
      caixa_id:         props.caixaId ?? null,
      caixa_destino_id: null,
      valor:            0,
      descricao:        '',
      observacao:       '',
    }
  }
})

const salvar = async () => {
  if (!form.value.caixa_id || !form.value.valor || !form.value.descricao) {
    $q.notify({ type: 'warning', message: 'Preencha todos os campos obrigatórios', position: 'top' })
    return
  }
  salvando.value = true
  try {
    if (form.value.tipo === 'SANGRIA') {
      await caixaOperacaoStore.sangria({
        caixa_id:   form.value.caixa_id,
        valor:      form.value.valor,
        descricao:  form.value.descricao,
        observacao: form.value.observacao || null,
      })
    } else if (form.value.tipo === 'REFORCO') {
      await caixaOperacaoStore.reforco({
        caixa_id:   form.value.caixa_id,
        valor:      form.value.valor,
        descricao:  form.value.descricao,
        observacao: form.value.observacao || null,
      })
    } else {
      if (!form.value.caixa_destino_id) {
        $q.notify({ type: 'warning', message: 'Selecione o caixa destino', position: 'top' })
        salvando.value = false
        return
      }
      await caixaOperacaoStore.transferir({
        caixa_origem_id:  form.value.caixa_id,
        caixa_destino_id: form.value.caixa_destino_id,
        valor:            form.value.valor,
        descricao:        form.value.descricao,
        observacao:       form.value.observacao || null,
      })
    }
    $q.notify({ type: 'positive', message: `${tituloModal.value} realizada com sucesso`, position: 'top' })
    model.value = false
    emit('salvo')
  } catch (e) {
    $q.notify({
      type: 'negative',
      message: `Erro ao realizar ${tituloModal.value.toLowerCase()}`,
      caption: e.response?.data?.error ?? e.response?.data?.mensagem,
    })
  } finally {
    salvando.value = false
  }
}
</script>
