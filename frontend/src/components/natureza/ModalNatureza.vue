<template>
  <modal
    v-model="model"
    :titulo="natureza ? 'Editar Natureza' : 'Nova Natureza'"
    tamanho="sm"
  >
    <div class="q-gutter-md">
      <div>
        <v-label label="Nome" obrigatorio />
        <q-input
          v-model="form.nome"
          outlined dense
          placeholder="Nome da natureza"
          :error="!!erros.nome"
          :error-message="erros.nome"
        />
      </div>

      <div>
        <v-label label="Código" />
        <q-input
          v-model="form.codigo"
          outlined dense
          placeholder="Ex: REG-001"
          :error="!!erros.codigo"
          :error-message="erros.codigo"
          hint="Identificador único opcional"
        />
      </div>

      <div>
        <v-label label="Descrição" />
        <q-input
          v-model="form.descricao"
          type="textarea"
          outlined dense
          rows="3"
          autogrow
          placeholder="Descrição da natureza"
        />
      </div>

      <q-toggle v-model="form.is_ativo" label="Ativa" />
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
import { useNaturezaStore } from 'src/stores/natureza'

defineOptions({ name: 'ModalNatureza' })

const props = defineProps({
  natureza: { type: Object, default: null },
})

const emit  = defineEmits(['salvo'])
const model = defineModel({ default: false })

const $q           = useQuasar()
const naturezaStore = useNaturezaStore()
const salvando      = ref(false)
const erros         = ref({})

const formVazio = () => ({ nome: '', codigo: '', descricao: '', is_ativo: true })
const form      = ref(formVazio())

watch(model, (aberto) => {
  if (aberto) {
    erros.value = {}
    form.value  = props.natureza
      ? {
          nome:      props.natureza.nome,
          codigo:    props.natureza.codigo ?? '',
          descricao: props.natureza.descricao ?? '',
          is_ativo:  props.natureza.is_ativo,
        }
      : formVazio()
  }
})

const salvar = async () => {
  erros.value = {}

  if (!form.value.nome?.trim()) {
    erros.value.nome = 'O nome é obrigatório'
    return
  }

  salvando.value = true
  try {
    const payload = {
      nome:      form.value.nome.trim(),
      codigo:    form.value.codigo?.trim() || null,
      descricao: form.value.descricao || null,
      is_ativo:  form.value.is_ativo,
    }

    if (props.natureza) {
      await naturezaStore.atualizar(props.natureza.id, payload)
      $q.notify({ type: 'positive', message: 'Natureza atualizada com sucesso', position: 'top' })
    } else {
      await naturezaStore.criar(payload)
      $q.notify({ type: 'positive', message: 'Natureza criada com sucesso', position: 'top' })
    }

    model.value = false
    emit('salvo')
  } catch (e) {
    const apiErros = e.response?.data?.erros
    if (apiErros) {
      erros.value = Object.fromEntries(
        Object.entries(apiErros).map(([k, v]) => [k, Array.isArray(v) ? v[0] : v])
      )
    } else {
      $q.notify({ type: 'negative', message: e.response?.data?.mensagem ?? 'Erro ao salvar', position: 'top' })
    }
  } finally {
    salvando.value = false
  }
}
</script>
