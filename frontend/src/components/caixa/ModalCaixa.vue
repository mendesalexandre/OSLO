<template>
  <modal
    v-model="model"
    :titulo="caixa ? 'Editar Caixa' : 'Novo Caixa'"
    tamanho="sm"
    @close="model = false"
  >
    <div class="q-gutter-md">
      <q-input
        v-model="form.nome"
        outlined dense
        label="Nome *"
        :rules="[v => !!v || 'Campo obrigatório']"
      />

      <q-input
        v-model="form.descricao"
        outlined dense
        label="Descrição"
        type="textarea"
        rows="2"
        autogrow
      />

      <q-input
        v-model.number="form.saldo_inicial"
        outlined dense
        label="Saldo Inicial"
        type="number"
        step="0.01"
        prefix="R$"
        :disable="!!caixa"
        :hint="caixa ? 'Saldo inicial não pode ser alterado após criação' : ''"
      />

      <div class="row q-col-gutter-md">
        <div class="col-6">
          <q-toggle v-model="form.is_ativo" label="Ativo" />
        </div>
        <div class="col-6">
          <q-toggle v-model="form.requer_abertura" label="Requer abertura diária" />
        </div>
      </div>
    </div>

    <template #rodape>
      <div class="row justify-end q-gutter-sm">
        <q-btn flat no-caps label="Cancelar" @click="model = false" />
        <q-btn
          unelevated color="primary" no-caps
          :loading="salvando"
          @click="salvar"
        >
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
import { useCaixaStore } from 'src/stores/caixa'

defineOptions({ name: 'ModalCaixa' })

const props = defineProps({
  caixa: { type: Object, default: null },
})

const emit = defineEmits(['salvo'])

const model = defineModel({ default: false })

const $q         = useQuasar()
const caixaStore = useCaixaStore()
const salvando   = ref(false)

const formVazio = () => ({
  nome:            '',
  descricao:       '',
  saldo_inicial:   0,
  is_ativo:        true,
  requer_abertura: false,
})

const form = ref(formVazio())

watch(model, (aberto) => {
  if (aberto) {
    form.value = props.caixa
      ? {
          nome:            props.caixa.nome,
          descricao:       props.caixa.descricao ?? '',
          saldo_inicial:   props.caixa.saldo_inicial,
          is_ativo:        props.caixa.is_ativo,
          requer_abertura: props.caixa.requer_abertura,
        }
      : formVazio()
  }
})

const salvar = async () => {
  salvando.value = true
  try {
    if (props.caixa) {
      await caixaStore.atualizar(props.caixa.id, form.value)
      $q.notify({ type: 'positive', message: 'Caixa atualizado com sucesso', position: 'top' })
    } else {
      await caixaStore.criar(form.value)
      $q.notify({ type: 'positive', message: 'Caixa criado com sucesso', position: 'top' })
    }
    model.value = false
    emit('salvo')
  } catch (e) {
    $q.notify({
      type: 'negative',
      message: 'Erro ao salvar caixa',
      caption: e.response?.data?.mensagem ?? e.response?.data?.message,
    })
  } finally {
    salvando.value = false
  }
}
</script>
