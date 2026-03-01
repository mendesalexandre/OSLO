<template>
  <modal
    v-model="model"
    :titulo="usuario ? `Permissões — ${usuario.nome}` : 'Permissões'"
    tamanho="lg"
  >
    <template #tabs>
      <q-tabs v-model="aba" dense align="left" class="q-px-md">
        <q-tab name="grupos"      label="Grupos"                />
        <q-tab name="individuais" label="Permissões Individuais" />
        <q-tab name="resumo"      label="Resumo"                />
      </q-tabs>
    </template>

    <div v-if="carregando" class="text-center q-pa-xl">
      <q-spinner size="40px" />
    </div>

    <q-tab-panels v-else v-model="aba" animated>

      <!-- Aba: Grupos -->
      <q-tab-panel name="grupos" class="q-pa-none q-pt-md">
        <div v-for="grupo in todosGrupos" :key="grupo.id" class="q-mb-xs">
          <q-checkbox
            v-model="gruposSelecionados"
            :val="grupo.id"
            :label="grupo.nome"
            dense
          >
            <template #default>
              <div class="q-ml-sm">
                <div class="text-weight-medium">{{ grupo.nome }}</div>
                <div v-if="grupo.descricao" class="text-caption text-grey-6">
                  {{ grupo.descricao }}
                </div>
              </div>
            </template>
          </q-checkbox>
        </div>
      </q-tab-panel>

      <!-- Aba: Permissões Individuais -->
      <q-tab-panel name="individuais" class="q-pa-none q-pt-sm">
        <div v-for="modulo in permissaoStore.agrupada" :key="modulo.modulo" class="q-mb-md">
          <div class="text-caption text-weight-bold text-uppercase text-grey-7 q-mb-xs">
            {{ modulo.modulo }}
          </div>

          <div class="q-col-gutter-xs column">
            <div
              v-for="perm in modulo.permissoes"
              :key="perm.id"
              class="row items-center no-wrap"
            >
              <div class="col text-body2">{{ perm.descricao || perm.nome }}</div>

              <q-btn-toggle
                v-model="permissoesIndividuais[perm.id]"
                no-caps dense unelevated
                toggle-color="primary"
                :options="[
                  { label: 'Herdar', value: 'herdar'  },
                  { label: 'Permitir', value: 'permitir' },
                  { label: 'Negar',   value: 'negar'   },
                ]"
                size="xs"
                @update:model-value="(v) => alterarPermissao(perm.id, v)"
              />
            </div>
          </div>
          <q-separator class="q-mt-sm" />
        </div>
      </q-tab-panel>

      <!-- Aba: Resumo -->
      <q-tab-panel name="resumo" class="q-pa-none q-pt-md">
        <div v-if="usuarioDados?.permissoes_efetivas?.length === 0 && !usuarioDados?.is_admin" class="text-grey-5 text-center q-pa-md">
          Nenhuma permissão efetiva
        </div>

        <q-banner v-if="usuarioDados?.is_admin" class="bg-positive text-white q-mb-md rounded-borders">
          <template #avatar>
            <q-icon name="verified_user" />
          </template>
          Administrador — acesso total ao sistema
        </q-banner>

        <div v-for="modulo in resumoAgrupado" :key="modulo.modulo" class="q-mb-md">
          <div class="text-caption text-weight-bold text-uppercase text-grey-7 q-mb-xs">
            {{ modulo.modulo }} ({{ modulo.permissoes.length }})
          </div>
          <div class="row q-col-gutter-xs">
            <div v-for="perm in modulo.permissoes" :key="perm" class="col-12 col-sm-6 col-md-4">
              <q-chip dense color="positive" text-color="white" size="sm" icon="check">
                {{ perm }}
              </q-chip>
            </div>
          </div>
        </div>
      </q-tab-panel>

    </q-tab-panels>

    <template #rodape>
      <div class="row justify-end q-gutter-sm full-width">
        <q-btn flat no-caps label="Fechar" @click="model = false" />
        <q-btn
          v-if="aba !== 'resumo'"
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
import { ref, watch, computed } from 'vue'
import { useQuasar } from 'quasar'
import { useGrupoStore } from 'src/stores/grupo'
import { usePermissaoStore } from 'src/stores/permissao'
import { useUsuarioPermissaoStore } from 'src/stores/usuario-permissao'

defineOptions({ name: 'ModalUsuarioPermissao' })

const props = defineProps({
  usuario: { type: Object, default: null },
})

const emit  = defineEmits(['salvo'])
const model = defineModel({ default: false })

const $q                    = useQuasar()
const grupoStore            = useGrupoStore()
const permissaoStore        = usePermissaoStore()
const usuarioPermissaoStore = useUsuarioPermissaoStore()

const carregando = ref(false)
const salvando   = ref(false)
const aba        = ref('grupos')

const todosGrupos         = ref([])
const gruposSelecionados  = ref([])
const permissoesIndividuais = ref({})  // { [permissaoId]: 'herdar'|'permitir'|'negar' }
const usuarioDados        = ref(null)

watch(model, async (aberto) => {
  if (!aberto || !props.usuario) return

  aba.value        = 'grupos'
  carregando.value = true

  try {
    // Carrega grupos e permissões em paralelo
    const [dadosUsuario] = await Promise.all([
      usuarioPermissaoStore.buscarPorId(props.usuario.id),
      grupoStore.listar(),
      permissaoStore.listarAgrupada(),
    ])

    usuarioDados.value    = dadosUsuario
    todosGrupos.value     = grupoStore.lista
    gruposSelecionados.value = dadosUsuario.grupos?.map((g) => g.id) ?? []

    // Montar mapa de permissões individuais
    const mapa = {}
    permissaoStore.agrupada.forEach((m) => {
      m.permissoes.forEach((p) => {
        mapa[p.id] = 'herdar'
      })
    })

    dadosUsuario.permissoes_individuais?.forEach((pi) => {
      mapa[pi.id] = pi.pivot?.tipo ?? 'herdar'
    })

    permissoesIndividuais.value = mapa
  } finally {
    carregando.value = false
  }
})

// Resumo agrupado por módulo
const resumoAgrupado = computed(() => {
  if (!usuarioDados.value?.permissoes_efetivas) return []

  const efetivas = new Set(usuarioDados.value.permissoes_efetivas)

  return permissaoStore.agrupada
    .map((m) => ({
      modulo:     m.modulo,
      permissoes: m.permissoes.filter((p) => efetivas.has(p.nome)).map((p) => p.nome),
    }))
    .filter((m) => m.permissoes.length > 0)
})

async function alterarPermissao(permissaoId, tipo) {
  if (!props.usuario) return
  try {
    await usuarioPermissaoStore.definirPermissao(props.usuario.id, permissaoId, tipo)
    // Atualizar resumo
    usuarioDados.value = await usuarioPermissaoStore.buscarPorId(props.usuario.id)
  } catch {
    $q.notify({ type: 'negative', message: 'Erro ao atualizar permissão', position: 'top' })
  }
}

const salvar = async () => {
  if (!props.usuario) return
  salvando.value = true
  try {
    if (aba.value === 'grupos') {
      await usuarioPermissaoStore.sincronizarGrupos(props.usuario.id, gruposSelecionados.value)
      $q.notify({ type: 'positive', message: 'Grupos atualizados com sucesso', position: 'top' })
      // Atualizar resumo
      usuarioDados.value = await usuarioPermissaoStore.buscarPorId(props.usuario.id)
    }

    emit('salvo')
  } catch (e) {
    $q.notify({
      type:     'negative',
      message:  e.response?.data?.mensagem ?? 'Erro ao salvar',
      position: 'top',
    })
  } finally {
    salvando.value = false
  }
}
</script>
