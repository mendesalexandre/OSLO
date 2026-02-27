<template>
  <q-select
    v-bind="$attrs"
    :model-value="modelValue"
    :options="opcoes"
    :loading="buscando"
    option-label="nome"
    option-value="id"
    emit-value
    map-options
    use-input
    input-debounce="300"
    @filter="filtrar"
    @update:model-value="$emit('update:modelValue', $event)"
  >
    <template #option="scope">
      <q-item v-bind="scope.itemProps">
        <q-item-section>
          <q-item-label>{{ scope.opt.nome }}</q-item-label>
          <q-item-label caption>
            {{ formatarDoc(scope.opt.cpf_cnpj) }} —
            {{ scope.opt.tipo_pessoa === 'F' ? 'PF' : 'PJ' }}
          </q-item-label>
        </q-item-section>
      </q-item>
    </template>
    <template #no-option>
      <q-item>
        <q-item-section class="text-grey">Nenhum resultado</q-item-section>
      </q-item>
    </template>
  </q-select>
</template>

<script setup>
import { ref } from 'vue'
import { useIndicadorPessoalStore } from 'src/stores/indicador-pessoal'

defineOptions({ inheritAttrs: false })

defineProps({
  modelValue: { default: null },
})

defineEmits(['update:modelValue'])

const indicadorPessoalStore = useIndicadorPessoalStore()
const opcoes = ref([])
const buscando = ref(false)

async function filtrar(termo, update, abort) {
  if (!termo || termo.length < 2) {
    abort()
    return
  }
  buscando.value = true
  const resultados = await indicadorPessoalStore.buscar(termo)
  buscando.value = false
  update(() => {
    opcoes.value = resultados
  })
}

function formatarDoc(doc) {
  if (!doc) return ''
  const d = doc.replace(/\D/g, '')
  if (d.length === 11) {
    return d.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4')
  }
  return d.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5')
}
</script>
