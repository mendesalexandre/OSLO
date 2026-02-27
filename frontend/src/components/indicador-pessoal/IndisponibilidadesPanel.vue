<template>
  <q-card flat bordered>
    <q-card-section>
      <div class="row items-center q-mb-md">
        <div class="text-subtitle1 col">
          Indisponibilidades
          <q-badge
            v-if="indisponibilidades.length > 0"
            color="negative"
            :label="indisponibilidades.length"
            class="q-ml-sm"
          />
          <q-badge
            v-else
            color="positive"
            label="Livre"
            class="q-ml-sm"
          />
        </div>
        <q-btn
          v-if="carregando"
          flat
          dense
          round
          icon="hourglass_empty"
          disable
        />
      </div>

      <q-inner-loading :showing="carregando" />

      <div v-if="!carregando && indisponibilidades.length === 0" class="text-grey-5 text-center q-py-sm">
        Nenhuma indisponibilidade ativa
      </div>

      <q-list v-if="!carregando && indisponibilidades.length > 0" separator>
        <q-item
          v-for="ind in indisponibilidades"
          :key="ind.id"
          clickable
          :to="{ name: 'indisponibilidades.editar', params: { id: ind.id } }"
        >
          <q-item-section>
            <q-item-label>
              <span class="text-weight-medium">{{ ind.protocolo_indisponibilidade }}</span>
              <q-chip
                dense
                size="sm"
                :color="corStatus(ind.status)"
                text-color="white"
                :label="labelStatus(ind.status)"
                class="q-ml-sm"
              />
              <q-icon
                v-if="ind.ordem_prioritaria"
                name="priority_high"
                color="negative"
                size="xs"
                class="q-ml-xs"
              >
                <q-tooltip>Ordem prioritária</q-tooltip>
              </q-icon>
            </q-item-label>
            <q-item-label caption>
              <span v-if="ind.numero_processo">Processo: {{ ind.numero_processo }} &bull; </span>
              <span v-if="ind.forum_vara">{{ ind.forum_vara }}</span>
            </q-item-label>
          </q-item-section>
          <q-item-section side>
            <q-item-label caption>{{ formatarData(ind.data_pedido) }}</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-card-section>
  </q-card>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useIndisponibilidadeStore } from 'src/stores/indisponibilidade'

const props = defineProps({
  cpfCnpj: {
    type: String,
    required: true,
  },
})

const store = useIndisponibilidadeStore()
const indisponibilidades = ref([])
const carregando = ref(false)

async function carregar() {
  if (!props.cpfCnpj) return
  carregando.value = true
  try {
    indisponibilidades.value = await store.fetchPorCpfCnpj(props.cpfCnpj)
  } finally {
    carregando.value = false
  }
}

watch(() => props.cpfCnpj, carregar)
onMounted(carregar)

function corStatus(status) {
  const mapa = { pendente: 'orange-7', cumprida: 'positive', cancelada: 'grey-6', em_analise: 'blue-6' }
  return mapa[status] ?? 'grey-5'
}

function labelStatus(status) {
  const mapa = { pendente: 'Pendente', cumprida: 'Cumprida', cancelada: 'Cancelada', em_analise: 'Em análise' }
  return mapa[status] ?? status
}

function formatarData(data) {
  if (!data) return ''
  return new Date(data).toLocaleDateString('pt-BR')
}
</script>
