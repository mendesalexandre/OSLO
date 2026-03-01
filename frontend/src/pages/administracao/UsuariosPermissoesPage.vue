<template>
  <q-page padding>
    <!-- Header -->
    <div class="row items-center q-mb-lg">
      <div class="col">
        <div class="oslo-page-title">Usuários e Permissões</div>
        <div class="oslo-page-subtitle">Gerencie grupos e permissões individuais de cada usuário</div>
      </div>
    </div>

    <!-- Filtros -->
    <q-card flat bordered class="q-mb-md">
      <q-card-section class="q-py-sm">
        <div class="row q-col-gutter-md items-end">
          <div class="col-12 col-md-5">
            <v-label label="Buscar por nome" />
            <q-input
              v-model="filtroNome"
              outlined dense clearable
              placeholder="Digite para filtrar..."
              debounce="400"
              @update:model-value="listar"
            >
              <template #prepend><l-icon name="search" :size="16" /></template>
            </q-input>
          </div>
          <div class="col-12 col-md-4">
            <v-label label="Buscar por e-mail" />
            <q-input
              v-model="filtroEmail"
              outlined dense clearable
              placeholder="email@exemplo.com"
              debounce="400"
              @update:model-value="listar"
            >
              <template #prepend><l-icon name="mail" :size="16" /></template>
            </q-input>
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Tabela -->
    <q-card flat bordered>
      <q-table
        :rows="store.lista"
        :columns="columns"
        row-key="id"
        :loading="store.carregando"
        flat
        hide-bottom
        no-data-label="Nenhum usuário encontrado"
      >
        <template #body-cell-grupos="props">
          <q-td :props="props">
            <template v-if="props.value?.length">
              <q-badge
                v-for="g in props.value"
                :key="g.id"
                color="primary"
                :label="g.nome"
                class="q-mr-xs"
              />
            </template>
            <span v-else class="text-grey-5">—</span>
          </q-td>
        </template>

        <template #body-cell-is_ativo="props">
          <q-td :props="props">
            <q-badge
              :color="props.value ? 'positive' : 'grey'"
              :label="props.value ? 'Ativo' : 'Inativo'"
            />
          </q-td>
        </template>

        <template #body-cell-acoes="props">
          <q-td :props="props">
            <q-btn
              flat dense round color="primary" size="sm"
              @click="abrirModal(props.row)"
            >
              <l-icon name="shield" :size="14" />
              <q-tooltip>Gerenciar permissões</q-tooltip>
            </q-btn>
          </q-td>
        </template>
      </q-table>
    </q-card>

    <!-- Modal -->
    <ModalUsuarioPermissao
      v-model="modalAberto"
      :usuario="usuarioSelecionado"
      @salvo="listar"
    />
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useUsuarioPermissaoStore } from 'src/stores/usuario-permissao'
import ModalUsuarioPermissao from 'src/components/usuario-permissao/ModalUsuarioPermissao.vue'

defineOptions({ name: 'UsuariosPermissoesPage' })

const store = useUsuarioPermissaoStore()

const modalAberto       = ref(false)
const usuarioSelecionado = ref(null)
const filtroNome        = ref('')
const filtroEmail       = ref('')

const columns = [
  { name: 'id',       label: 'ID',      field: 'id',       align: 'left', sortable: true, style: 'width: 60px' },
  { name: 'nome',     label: 'Nome',    field: 'nome',     align: 'left', sortable: true },
  { name: 'email',    label: 'E-mail',  field: 'email',    align: 'left' },
  { name: 'grupos',   label: 'Grupos',  field: 'grupos',   align: 'left' },
  { name: 'is_ativo', label: 'Status',  field: 'is_ativo', align: 'center', style: 'width: 100px' },
  { name: 'acoes',    label: 'Ações',   field: 'acoes',    align: 'center', style: 'width: 80px' },
]

function listar() {
  const filtros = {}
  if (filtroNome.value)  filtros.nome  = filtroNome.value
  if (filtroEmail.value) filtros.email = filtroEmail.value
  store.listar(filtros)
}

function abrirModal(usuario) {
  usuarioSelecionado.value = usuario
  modalAberto.value = true
}

onMounted(listar)
</script>
