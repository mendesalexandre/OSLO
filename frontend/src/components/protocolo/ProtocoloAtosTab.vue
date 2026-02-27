<template>
  <div class="protocolo-atos-tab">
    <q-card flat bordered>
      <q-card-section>
        <div class="row items-center justify-between q-mb-md">
          <div class="text-h6">Atos do Registro</div>
          <q-btn
            color="primary"
            icon="add"
            label="Adicionar Ato"
            size="sm"
            unelevated
            no-caps
            @click="abrirModal"
          />
        </div>

        <q-table
          :rows="protocolo.itens || []"
          :columns="columns"
          row-key="id"
          flat
          :pagination="{ rowsPerPage: 20 }"
        >
          <template v-slot:body-cell-codigo="props">
            <q-td :props="props">
              <span class="text-weight-medium">{{ props.row.ato?.codigo }}</span>
            </q-td>
          </template>

          <template v-slot:body-cell-descricao="props">
            <q-td :props="props">
              {{ props.row.ato?.nome || props.row.descricao }}
            </q-td>
          </template>

          <template v-slot:body-cell-valor_total="props">
            <q-td :props="props">
              <span class="text-weight-bold">{{ formatarDinheiroBrasil(props.value) }}</span>
            </q-td>
          </template>

          <template v-slot:body-cell-actions="props">
            <q-td :props="props">
              <q-btn
                flat
                round
                dense
                size="sm"
                icon="eva-trash-2-outline"
                color="negative"
                @click="confirmarRemocao(props.row)"
              >
                <q-tooltip>Remover</q-tooltip>
              </q-btn>
            </q-td>
          </template>

          <template v-slot:no-data>
            <div class="full-width row flex-center q-gutter-sm text-grey-6 q-pa-lg">
              <l-icon name="inbox" :size="48" />
              <span>Nenhum ato adicionado</span>
            </div>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <!-- Modal Adicionar Ato -->
    <q-dialog v-model="modalAberto">
      <q-card style="min-width: 500px">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">Adicionar Ato</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section>
          <div class="q-gutter-md">
            <div>
              <v-label label="Ato" obrigatorio />
              <q-select
                v-model="form.ato_id"
                :options="atosDisponiveis"
                option-value="id"
                option-label="nome"
                emit-value
                map-options
                outlined
                dense
                use-input
                input-debounce="300"
                placeholder="Selecione um ato"
                @filter="filtrarAtos"
              >
                <template v-slot:option="scope">
                  <q-item v-bind="scope.itemProps">
                    <q-item-section>
                      <q-item-label>{{ scope.opt.codigo }} - {{ scope.opt.nome }}</q-item-label>
                    </q-item-section>
                  </q-item>
                </template>
              </q-select>
            </div>

            <div>
              <v-label label="Quantidade" />
              <q-input
                v-model.number="form.quantidade"
                type="number"
                outlined
                dense
                min="1"
                placeholder="1"
              />
            </div>

            <div>
              <v-label label="Base de Cálculo" ajuda="Valor usado para calcular os emolumentos (se aplicável)" />
              <q-input
                v-model.number="form.base_calculo"
                type="number"
                outlined
                dense
                min="0"
                step="0.01"
                placeholder="0,00"
              />
            </div>
          </div>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn label="Cancelar" flat color="grey-7" v-close-popup />
          <q-btn
            label="Adicionar"
            color="primary"
            unelevated
            no-caps
            :loading="salvando"
            :disable="!form.ato_id"
            @click="salvar"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useQuasar } from 'quasar'
import { useProtocoloStore } from 'src/stores/protocolo'
import { api } from 'src/boot/axios'
import { formatarDinheiroBrasil } from 'src/Utils'

defineOptions({ name: 'ProtocoloAtosTab' })

const props = defineProps({
  protocolo: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['atualizar'])

const $q = useQuasar()
const protocoloStore = useProtocoloStore()

const modalAberto = ref(false)
const salvando = ref(false)
const atosDisponiveis = ref([])

const form = reactive({
  ato_id: null,
  quantidade: 1,
  base_calculo: null,
})

const columns = [
  {
    name: 'codigo',
    label: 'Código',
    field: 'codigo',
    align: 'left',
  },
  {
    name: 'descricao',
    label: 'Descrição',
    field: 'descricao',
    align: 'left',
  },
  {
    name: 'quantidade',
    label: 'Qtd',
    field: 'quantidade',
    align: 'center',
  },
  {
    name: 'valor_total',
    label: 'Valor Total',
    field: 'valor_total',
    align: 'right',
  },
  {
    name: 'actions',
    label: 'Ações',
    field: 'actions',
    align: 'center',
  },
]

function abrirModal() {
  form.ato_id = null
  form.quantidade = 1
  form.base_calculo = null
  modalAberto.value = true
  carregarAtos()
}

async function carregarAtos(filtro = '') {
  try {
    const { data } = await api.get('/ato', {
      params: { busca: filtro },
    })
    atosDisponiveis.value = data.sucesso ? data.dados : []
  } catch (error) {
    console.error('Erro ao carregar atos:', error)
  }
}

function filtrarAtos(val, update) {
  update(() => {
    carregarAtos(val)
  })
}

async function salvar() {
  salvando.value = true
  try {
    const resultado = await protocoloStore.adicionarItem(props.protocolo.id, form)

    if (resultado.sucesso) {
      $q.notify({
        type: 'positive',
        message: 'Ato adicionado com sucesso',
        position: 'top',
      })
      modalAberto.value = false
      emit('atualizar')
    }
  } catch (error) {
    $q.notify({
      type: 'negative',
      message: error.response?.data?.mensagem || 'Erro ao adicionar ato',
      position: 'top',
    })
  } finally {
    salvando.value = false
  }
}

function confirmarRemocao(item) {
  $q.dialog({
    title: 'Confirmar Remoção',
    message: `Deseja remover o ato "${item.ato?.nome || item.descricao}"?`,
    cancel: true,
    persistent: true,
  }).onOk(async () => {
    await remover(item)
  })
}

async function remover(item) {
  try {
    const resultado = await protocoloStore.removerItem(props.protocolo.id, item.id)

    if (resultado.sucesso) {
      $q.notify({
        type: 'positive',
        message: 'Ato removido com sucesso',
        position: 'top',
      })
      emit('atualizar')
    }
  } catch (error) {
    $q.notify({
      type: 'negative',
      message: error.response?.data?.mensagem || 'Erro ao remover ato',
      position: 'top',
    })
  }
}
</script>

<style lang="scss" scoped>
.protocolo-atos-tab {
  padding: 24px;
}
</style>
