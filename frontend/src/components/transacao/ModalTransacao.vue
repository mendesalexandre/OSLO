<template>
  <modal
    v-model="model"
    :titulo="transacao ? 'Editar Transação' : 'Nova Transação'"
    tamanho="sm"
    @close="model = false"
  >
    <div class="q-gutter-md">

      <!-- Indicador Pessoal -->
      <div>
        <v-label label="Indicador Pessoal *" />
        <busca-autocomplete
          v-model="form.indicador_pessoal_id"
          outlined dense
          label="Buscar por nome ou CPF/CNPJ..."
          :rules="[v => !!v || 'Obrigatório']"
        />
      </div>

      <!-- Tipo de Transação -->
      <div>
        <v-label label="Tipo de Transação *" />
        <select-tipo-transacao
          v-model="form.tipo_transacao_id"
          outlined dense
          label="Selecionar tipo..."
          :rules="[v => !!v || 'Obrigatório']"
        />
      </div>

      <!-- Motivo -->
      <div>
        <v-label label="Motivo" />
        <select-motivo-transacao
          v-model="form.motivo_transacao_id"
          :tipo-transacao-id="form.tipo_transacao_id"
          outlined dense
          label="Selecionar motivo..."
        />
      </div>

      <!-- Descrição -->
      <div>
        <v-label label="Descrição *" />
        <q-input
          v-model="form.descricao"
          outlined dense
          :rules="[v => !!v || 'Obrigatório']"
        />
      </div>

      <!-- Valor -->
      <div>
        <v-label label="Valor *" />
        <v-money v-model.number="form.valor" outlined dense />
      </div>

      <!-- Data da Transação -->
      <div>
        <v-label label="Data da Transação *" />
        <v-date v-model="form.data_transacao" outlined dense />
      </div>

      <!-- Observações -->
      <div>
        <v-label label="Observações" />
        <q-input v-model="form.observacoes" outlined dense type="textarea" rows="2" autogrow />
      </div>

    </div>

    <template #rodape>
      <div class="row justify-end q-gutter-sm">
        <q-btn flat no-caps label="Cancelar" @click="model = false" />
        <q-btn unelevated color="primary" no-caps :loading="salvando" @click="salvar">
          <q-icon name="save" size="16px" class="q-mr-sm" />
          Salvar
        </q-btn>
      </div>
    </template>
  </modal>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useQuasar } from 'quasar'
import { useTransacaoStore } from 'src/stores/transacao'
import BuscaAutocomplete from 'src/components/indicador-pessoal/BuscaAutocomplete.vue'
import SelectTipoTransacao from './SelectTipoTransacao.vue'
import SelectMotivoTransacao from './SelectMotivoTransacao.vue'

defineOptions({ name: 'ModalTransacao' })

const props = defineProps({
  transacao: { type: Object, default: null },
})

const emit = defineEmits(['salvo'])
const model = defineModel({ default: false })

const $q             = useQuasar()
const transacaoStore = useTransacaoStore()
const salvando       = ref(false)

const hoje = new Date().toISOString().split('T')[0]

const formVazio = () => ({
  indicador_pessoal_id: null,
  tipo_transacao_id:    null,
  motivo_transacao_id:  null,
  descricao:            '',
  valor:                0,
  data_transacao:       hoje,
  observacoes:          '',
})

const form = ref(formVazio())

watch(model, (aberto) => {
  if (aberto) {
    form.value = props.transacao
      ? {
          indicador_pessoal_id: props.transacao.indicador_pessoal_id,
          tipo_transacao_id:    props.transacao.tipo_transacao_id,
          motivo_transacao_id:  props.transacao.motivo_transacao_id ?? null,
          descricao:            props.transacao.descricao,
          valor:                props.transacao.valor,
          data_transacao:       props.transacao.data_transacao,
          observacoes:          props.transacao.observacoes ?? '',
        }
      : formVazio()
  }
})

const salvar = async () => {
  salvando.value = true
  try {
    if (props.transacao) {
      await transacaoStore.atualizar(props.transacao.id, form.value)
      $q.notify({ type: 'positive', message: 'Transação atualizada com sucesso', position: 'top' })
    } else {
      await transacaoStore.criar(form.value)
      $q.notify({ type: 'positive', message: 'Transação criada com sucesso', position: 'top' })
    }
    model.value = false
    emit('salvo')
  } catch (e) {
    $q.notify({
      type: 'negative',
      message: 'Erro ao salvar transação',
      caption: e.response?.data?.mensagem ?? e.response?.data?.message,
      position: 'top',
    })
  } finally {
    salvando.value = false
  }
}
</script>
