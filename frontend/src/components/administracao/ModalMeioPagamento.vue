<template>
  <modal
    v-model="model"
    :titulo="meio ? 'Editar Meio de Pagamento' : 'Novo Meio de Pagamento'"
    tamanho="sm"
  >
    <div class="q-gutter-md">
      <div>
        <v-label label="Nome" obrigatorio />
        <q-input
          v-model="form.nome"
          outlined dense
          placeholder="Ex: Visa Crédito, PIX, Dinheiro"
          :error="!!erros.nome"
          :error-message="erros.nome"
        />
      </div>

      <div>
        <v-label label="Forma de Pagamento" obrigatorio />
        <q-select
          v-model="form.forma_pagamento_id"
          :options="formaPagamentoStore.formasAtivas"
          option-value="id"
          option-label="nome"
          emit-value map-options
          outlined dense clearable
          placeholder="Selecione a forma"
          :error="!!erros.forma_pagamento_id"
          :error-message="erros.forma_pagamento_id"
        />
      </div>

      <div class="row q-col-gutter-md">
        <div class="col-6">
          <v-label label="Taxa Percentual (%)" />
          <q-input
            v-model.number="form.taxa_percentual"
            type="number"
            outlined dense
            min="0" step="0.01"
            suffix="%"
          />
        </div>
        <div class="col-6">
          <v-label label="Taxa Fixa (R$)" />
          <q-input
            v-model.number="form.taxa_fixa"
            type="number"
            outlined dense
            min="0" step="0.01"
            prefix="R$"
          />
        </div>
      </div>

      <div>
        <v-label label="Prazo de Compensação (dias)" />
        <q-input
          v-model.number="form.prazo_compensacao"
          type="number"
          outlined dense
          min="0"
          suffix="dias"
        />
      </div>

      <div>
        <v-label label="Identificador" />
        <q-input
          v-model="form.identificador"
          outlined dense
          placeholder="Ex: pix, credit_card, boleto"
        />
      </div>

      <div>
        <v-label label="Descrição" />
        <q-input
          v-model="form.descricao"
          type="textarea"
          outlined dense
          rows="2"
          autogrow
        />
      </div>

      <q-toggle v-model="form.is_ativo" label="Ativo" />
    </div>

    <template #rodape>
      <div class="row justify-end q-gutter-sm">
        <q-btn flat no-caps label="Cancelar" @click="model = false" />
        <q-btn unelevated color="primary" no-caps :loading="salvando" @click="salvar">
          <l-icon name="save" :size="16" class="q-mr-sm" />
          Salvar
        </q-btn>
      </div>
    </template>
  </modal>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useQuasar } from 'quasar'
import { useFormaPagamentoStore } from 'src/stores/formaPagamento'
import { useMeioPagamentoStore } from 'src/stores/meioPagamento'

defineOptions({ name: 'ModalMeioPagamento' })

const props = defineProps({
  meio: { type: Object, default: null },
})

const emit  = defineEmits(['salvo'])
const model = defineModel({ default: false })

const $q                 = useQuasar()
const formaPagamentoStore = useFormaPagamentoStore()
const meioPagamentoStore  = useMeioPagamentoStore()
const salvando           = ref(false)
const erros              = ref({})

const formVazio = () => ({
  nome:               '',
  forma_pagamento_id: null,
  descricao:          '',
  identificador:      '',
  taxa_percentual:    0,
  taxa_fixa:          0,
  prazo_compensacao:  0,
  is_ativo:           true,
})

const form = ref(formVazio())

watch(model, (aberto) => {
  if (aberto) {
    erros.value = {}
    form.value  = props.meio
      ? {
          nome:               props.meio.nome,
          forma_pagamento_id: props.meio.forma_pagamento_id,
          descricao:          props.meio.descricao ?? '',
          identificador:      props.meio.identificador ?? '',
          taxa_percentual:    Number(props.meio.taxa_percentual) || 0,
          taxa_fixa:          Number(props.meio.taxa_fixa) || 0,
          prazo_compensacao:  props.meio.prazo_compensacao || 0,
          is_ativo:           props.meio.is_ativo,
        }
      : formVazio()
    formaPagamentoStore.listar()
  }
})

const salvar = async () => {
  erros.value = {}

  if (!form.value.nome?.trim()) {
    erros.value.nome = 'O nome é obrigatório'
    return
  }
  if (!form.value.forma_pagamento_id) {
    erros.value.forma_pagamento_id = 'A forma de pagamento é obrigatória'
    return
  }

  salvando.value = true
  try {
    const payload = {
      nome:               form.value.nome.trim(),
      forma_pagamento_id: form.value.forma_pagamento_id,
      descricao:          form.value.descricao || null,
      identificador:      form.value.identificador || null,
      taxa_percentual:    form.value.taxa_percentual || 0,
      taxa_fixa:          form.value.taxa_fixa || 0,
      prazo_compensacao:  form.value.prazo_compensacao || 0,
      is_ativo:           form.value.is_ativo,
    }

    if (props.meio) {
      await meioPagamentoStore.atualizar(props.meio.id, payload)
      $q.notify({ type: 'positive', message: 'Meio de pagamento atualizado', position: 'top' })
    } else {
      await meioPagamentoStore.criar(payload)
      $q.notify({ type: 'positive', message: 'Meio de pagamento criado', position: 'top' })
    }

    model.value = false
    emit('salvo')
  } catch (e) {
    const apiErros = e.response?.data?.erros
    if (apiErros) {
      erros.value = Object.fromEntries(Object.entries(apiErros).map(([k, v]) => [k, Array.isArray(v) ? v[0] : v]))
    } else {
      $q.notify({ type: 'negative', message: e.response?.data?.mensagem ?? 'Erro ao salvar', position: 'top' })
    }
  } finally {
    salvando.value = false
  }
}
</script>
