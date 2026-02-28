<template>
  <q-page padding>
    <!-- Header -->
    <div class="row items-center q-mb-lg">
      <div class="col">
        <div class="oslo-page-title">Formas de Pagamento</div>
        <div class="oslo-page-subtitle">Gerencie as formas de pagamento aceitas</div>
      </div>
      <div class="col-auto">
        <q-btn
          v-permissao="'FORMA_PAGAMENTO_CRIAR'"
          unelevated color="primary" no-caps
          @click="abrirModal()"
        >
          <l-icon name="plus" :size="16" class="q-mr-sm" />
          Nova Forma
        </q-btn>
      </div>
    </div>

    <!-- Tabela -->
    <q-card flat bordered>
      <q-table
        :rows="formaPagamentoStore.formas"
        :columns="columns"
        row-key="id"
        :loading="formaPagamentoStore.carregando"
        flat
        hide-bottom
        no-data-label="Nenhuma forma de pagamento cadastrada"
      >
        <template #body-cell-is_ativo="props">
          <q-td :props="props">
            <q-badge
              :color="props.value ? 'positive' : 'grey'"
              :label="props.value ? 'Ativa' : 'Inativa'"
            />
          </q-td>
        </template>

        <template #body-cell-acoes="props">
          <q-td :props="props">
            <q-btn
              v-permissao="'FORMA_PAGAMENTO_EDITAR'"
              flat dense round color="primary" size="sm"
              @click="abrirModal(props.row)"
            >
              <l-icon name="pen" :size="14" />
              <q-tooltip>Editar</q-tooltip>
            </q-btn>
            <q-btn
              v-permissao="'FORMA_PAGAMENTO_EXCLUIR'"
              flat dense round color="negative" size="sm"
              @click="confirmarExcluir(props.row)"
            >
              <l-icon name="trash-2" :size="14" />
              <q-tooltip>Excluir</q-tooltip>
            </q-btn>
          </q-td>
        </template>
      </q-table>
    </q-card>

    <!-- Modal -->
    <ModalFormaPagamento
      v-model="modalAberto"
      :forma="formaSelecionada"
      @salvo="formaPagamentoStore.listar(true)"
    />
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { useFormaPagamentoStore } from 'src/stores/formaPagamento'
import ModalFormaPagamento from 'src/components/administracao/ModalFormaPagamento.vue'

defineOptions({ name: 'FormaPagamentoPage' })

const $q = useQuasar()
const formaPagamentoStore = useFormaPagamentoStore()

const modalAberto     = ref(false)
const formaSelecionada = ref(null)

const columns = [
  { name: 'id',        label: 'ID',        field: 'id',        align: 'left', sortable: true, style: 'width: 60px' },
  { name: 'nome',      label: 'Nome',      field: 'nome',      align: 'left', sortable: true },
  { name: 'descricao', label: 'Descrição', field: 'descricao', align: 'left', format: (v) => v || '-' },
  { name: 'is_ativo',  label: 'Status',    field: 'is_ativo',  align: 'center', style: 'width: 100px' },
  { name: 'acoes',     label: 'Ações',     field: 'acoes',     align: 'center', style: 'width: 100px' },
]

function abrirModal(forma = null) {
  formaSelecionada.value = forma
  modalAberto.value = true
}

function confirmarExcluir(forma) {
  $q.dialog({
    title:   'Excluir forma de pagamento',
    message: `Deseja excluir "<strong>${forma.nome}</strong>"?`,
    html:    true,
    cancel:  { label: 'Cancelar', flat: true, color: 'grey-7' },
    ok:      { label: 'Excluir',  unelevated: true, color: 'negative', noCaps: true },
  }).onOk(async () => {
    try {
      await formaPagamentoStore.excluir(forma.id)
      $q.notify({ type: 'positive', message: 'Forma de pagamento excluída', position: 'top' })
    } catch (e) {
      $q.notify({
        type:    'negative',
        message: e.response?.data?.mensagem ?? 'Erro ao excluir',
        position: 'top',
      })
    }
  })
}

onMounted(() => formaPagamentoStore.listar())
</script>
