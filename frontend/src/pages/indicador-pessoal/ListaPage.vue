<template>
  <q-page padding>
    <div class="row items-center q-mb-md">
      <div class="text-h5 col">Indicador Pessoal</div>
      <q-btn
        color="primary"
        icon="add"
        label="Novo"
        unelevated
        :to="{ name: 'indicador-pessoal.novo' }"
      />
    </div>

    <!-- Filtros -->
    <q-card flat bordered class="q-mb-md">
      <q-card-section>
        <div class="row q-gutter-sm">
          <q-input
            v-model="filtros.busca"
            dense
            outlined
            placeholder="Buscar por nome ou CPF/CNPJ..."
            class="col"
            clearable
            @update:model-value="buscar"
          >
            <template #prepend>
              <q-icon name="search" />
            </template>
          </q-input>
          <q-select
            v-model="filtros.tipo_pessoa"
            dense
            outlined
            clearable
            :options="tiposPessoa"
            option-label="label"
            option-value="value"
            emit-value
            map-options
            label="Tipo"
            style="min-width: 120px"
            @update:model-value="buscar"
          />
        </div>
      </q-card-section>
    </q-card>

    <!-- Tabela -->
    <q-card flat bordered>
      <q-table
        :rows="indicadorPessoalStore.lista"
        :columns="colunas"
        :loading="indicadorPessoalStore.carregando"
        :rows-per-page-options="[15, 30, 50]"
        row-key="id"
        flat
        @request="onRequest"
      >
        <template #body-cell-tipo_pessoa="props">
          <q-td :props="props">
            <q-badge
              :color="props.row.tipo_pessoa === 'F' ? 'blue-6' : 'orange-8'"
              :label="props.row.tipo_pessoa === 'F' ? 'PF' : 'PJ'"
            />
          </q-td>
        </template>

        <template #body-cell-cpf_cnpj="props">
          <q-td :props="props">
            {{ formatarDoc(props.row.cpf_cnpj) }}
          </q-td>
        </template>

        <template #body-cell-indisponibilidades="props">
          <q-td :props="props" class="text-center">
            <BadgeIndisponibilidade :count="props.row.indisponibilidades_count ?? 0" />
          </q-td>
        </template>

        <template #body-cell-acoes="props">
          <q-td :props="props" class="text-right">
            <q-btn
              flat
              round
              dense
              icon="edit"
              color="primary"
              :to="{ name: 'indicador-pessoal.editar', params: { id: props.row.id } }"
            >
              <q-tooltip>Editar</q-tooltip>
            </q-btn>
            <q-btn
              flat
              round
              dense
              icon="history"
              color="grey-7"
              :to="{ name: 'indicador-pessoal.versoes', params: { cpfCnpj: props.row.cpf_cnpj } }"
            >
              <q-tooltip>Histórico de versões</q-tooltip>
            </q-btn>
            <q-btn
              flat
              round
              dense
              icon="delete"
              color="negative"
              @click="confirmarExclusao(props.row)"
            >
              <q-tooltip>Excluir</q-tooltip>
            </q-btn>
          </q-td>
        </template>
      </q-table>
    </q-card>
  </q-page>
</template>

<script setup>
import { onMounted, reactive } from 'vue'
import { useQuasar } from 'quasar'
import { useIndicadorPessoalStore } from 'src/stores/indicador-pessoal'
import BadgeIndisponibilidade from 'src/components/indicador-pessoal/BadgeIndisponibilidade.vue'

const $q = useQuasar()
const indicadorPessoalStore = useIndicadorPessoalStore()

const filtros = reactive({ busca: '', tipo_pessoa: null })

const tiposPessoa = [
  { label: 'Pessoa Física', value: 'F' },
  { label: 'Pessoa Jurídica', value: 'J' },
]

const colunas = [
  { name: 'ficha', label: 'Ficha', field: 'ficha', align: 'left', sortable: true },
  { name: 'nome', label: 'Nome', field: 'nome', align: 'left', sortable: true },
  { name: 'cpf_cnpj', label: 'CPF/CNPJ', field: 'cpf_cnpj', align: 'left' },
  { name: 'tipo_pessoa', label: 'Tipo', field: 'tipo_pessoa', align: 'center' },
  {
    name: 'estado_civil',
    label: 'Estado Civil',
    field: (row) => row.estado_civil?.descricao ?? '—',
    align: 'left',
  },
  { name: 'cidade', label: 'Cidade', field: (row) => row.cidade ?? '—', align: 'left' },
  { name: 'indisponibilidades', label: 'Indisp.', field: 'indisponibilidades_count', align: 'center' },
  { name: 'acoes', label: '', field: 'acoes', align: 'right' },
]

onMounted(() => buscar())

function buscar() {
  const params = {}
  if (filtros.busca) params.busca = filtros.busca
  if (filtros.tipo_pessoa) params.tipo_pessoa = filtros.tipo_pessoa
  indicadorPessoalStore.fetchLista(params)
}

function onRequest({ pagination }) {
  const params = { page: pagination.page }
  if (filtros.busca) params.busca = filtros.busca
  if (filtros.tipo_pessoa) params.tipo_pessoa = filtros.tipo_pessoa
  indicadorPessoalStore.fetchLista(params)
}

function formatarDoc(doc) {
  if (!doc) return ''
  const d = doc.replace(/\D/g, '')
  if (d.length === 11) return d.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4')
  return d.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5')
}

function confirmarExclusao(registro) {
  $q.dialog({
    title: 'Confirmar exclusão',
    message: `Excluir "${registro.nome}"?`,
    cancel: true,
    persistent: true,
  }).onOk(async () => {
    await indicadorPessoalStore.excluir(registro.id)
    buscar()
    $q.notify({ type: 'positive', message: 'Registro excluído com sucesso.' })
  })
}
</script>
