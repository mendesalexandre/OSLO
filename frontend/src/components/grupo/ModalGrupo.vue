<template>
  <modal
    v-model="model"
    :titulo="grupo ? 'Editar Grupo' : 'Novo Grupo'"
    tamanho="md"
  >
    <template #tabs>
      <q-tabs v-model="aba" dense align="left" class="q-px-md">
        <q-tab name="dados"      label="Dados"      />
        <q-tab name="permissoes" label="Permissões" :disable="!grupo" />
      </q-tabs>
    </template>

    <!-- Aba Dados -->
    <q-tab-panels v-model="aba" animated>
      <q-tab-panel name="dados" class="q-pa-none q-pt-md">
        <div class="q-gutter-md">
          <div>
            <v-label label="Nome" obrigatorio />
            <q-input
              v-model="form.nome"
              outlined dense
              placeholder="Nome do grupo"
              :error="!!erros.nome"
              :error-message="erros.nome"
            />
          </div>

          <div>
            <v-label label="Descrição" />
            <q-input
              v-model="form.descricao"
              outlined dense
              type="textarea"
              rows="3"
              autogrow
              placeholder="Finalidade do grupo"
            />
          </div>

          <q-toggle v-model="form.is_ativo" label="Ativo" />
        </div>
      </q-tab-panel>

      <!-- Aba Permissões -->
      <q-tab-panel name="permissoes" class="q-pa-none">
        <div v-if="permissaoStore.carregando" class="text-center q-pa-lg">
          <q-spinner size="32px" />
        </div>

        <div v-else class="q-pt-sm">
          <div v-for="modulo in permissaoStore.agrupada" :key="modulo.modulo" class="q-mb-md">
            <div class="row items-center q-mb-xs">
              <span class="text-caption text-weight-bold text-uppercase text-grey-7">
                {{ modulo.modulo }}
              </span>
              <q-space />
              <q-btn
                flat dense no-caps size="sm" color="primary"
                :label="todosSelecionados(modulo) ? 'Desmarcar todos' : 'Selecionar todos'"
                @click="toggleModulo(modulo)"
              />
            </div>

            <div class="row q-col-gutter-xs">
              <div
                v-for="perm in modulo.permissoes"
                :key="perm.id"
                class="col-12 col-sm-6 col-md-4"
              >
                <q-checkbox
                  v-model="permissoesSelecionadas"
                  :val="perm.id"
                  :label="perm.descricao || perm.nome"
                  dense
                />
              </div>
            </div>
            <q-separator class="q-mt-sm" />
          </div>
        </div>
      </q-tab-panel>
    </q-tab-panels>

    <template #rodape>
      <div class="row justify-between items-center full-width">
        <span v-if="aba === 'permissoes'" class="text-caption text-grey-6">
          {{ permissoesSelecionadas.length }} permissão(ões) selecionada(s)
        </span>
        <q-space />
        <div class="row q-gutter-sm">
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
      </div>
    </template>
  </modal>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import { useQuasar } from 'quasar'
import { useGrupoStore } from 'src/stores/grupo'
import { usePermissaoStore } from 'src/stores/permissao'

defineOptions({ name: 'ModalGrupo' })

const props = defineProps({
  grupo: { type: Object, default: null },
})

const emit  = defineEmits(['salvo'])
const model = defineModel({ default: false })

const $q             = useQuasar()
const grupoStore     = useGrupoStore()
const permissaoStore = usePermissaoStore()
const salvando       = ref(false)
const erros          = ref({})
const aba            = ref('dados')

const formVazio = () => ({ nome: '', descricao: '', is_ativo: true })
const form      = ref(formVazio())

const permissoesSelecionadas = ref([])

watch(model, async (aberto) => {
  if (aberto) {
    aba.value   = 'dados'
    erros.value = {}
    form.value  = props.grupo
      ? {
          nome:      props.grupo.nome,
          descricao: props.grupo.descricao ?? '',
          is_ativo:  props.grupo.is_ativo,
        }
      : formVazio()

    // Carregar permissões agrupadas
    await permissaoStore.listarAgrupada()

    // Se editando, carregar permissões atuais do grupo
    if (props.grupo) {
      const dados = await grupoStore.buscarPorId(props.grupo.id)
      permissoesSelecionadas.value = dados.permissoes?.map((p) => p.id) ?? []
    } else {
      permissoesSelecionadas.value = []
    }
  }
})

function todosSelecionados(modulo) {
  return modulo.permissoes.every((p) => permissoesSelecionadas.value.includes(p.id))
}

function toggleModulo(modulo) {
  const ids = modulo.permissoes.map((p) => p.id)
  if (todosSelecionados(modulo)) {
    permissoesSelecionadas.value = permissoesSelecionadas.value.filter((id) => !ids.includes(id))
  } else {
    const novos = ids.filter((id) => !permissoesSelecionadas.value.includes(id))
    permissoesSelecionadas.value = [...permissoesSelecionadas.value, ...novos]
  }
}

const salvar = async () => {
  erros.value = {}

  if (!form.value.nome?.trim()) {
    erros.value.nome = 'O nome é obrigatório'
    aba.value = 'dados'
    return
  }

  salvando.value = true
  try {
    let grupoId

    if (props.grupo) {
      await grupoStore.atualizar(props.grupo.id, form.value)
      grupoId = props.grupo.id
      $q.notify({ type: 'positive', message: 'Grupo atualizado com sucesso', position: 'top' })
    } else {
      const criado = await grupoStore.criar(form.value)
      grupoId = criado.id
      $q.notify({ type: 'positive', message: 'Grupo criado com sucesso', position: 'top' })
    }

    // Sincronizar permissões
    await grupoStore.sincronizarPermissoes(grupoId, permissoesSelecionadas.value)

    model.value = false
    emit('salvo')
  } catch (e) {
    const apiErros = e.response?.data?.erros
    if (apiErros) {
      erros.value = Object.fromEntries(
        Object.entries(apiErros).map(([k, v]) => [k, Array.isArray(v) ? v[0] : v])
      )
      aba.value = 'dados'
    } else {
      $q.notify({ type: 'negative', message: e.response?.data?.mensagem ?? 'Erro ao salvar', position: 'top' })
    }
  } finally {
    salvando.value = false
  }
}
</script>
