<template>
  <q-page padding>
    <div class="text-h5 q-mb-md">Consulta Geral</div>

    <!-- Campo de busca -->
    <q-card flat bordered class="q-mb-md">
      <q-card-section>
        <div class="row q-gutter-sm items-center">
          <q-input
            v-model="termoBusca"
            outlined
            dense
            placeholder="Informe CPF, CNPJ ou Nome..."
            class="col"
            clearable
            @keyup.enter="consultar"
          >
            <template #prepend>
              <q-icon name="search" />
            </template>
          </q-input>
          <q-btn
            unelevated
            color="primary"
            label="Consultar"
            :loading="carregando"
            @click="consultar"
          />
        </div>
      </q-card-section>
    </q-card>

    <!-- Sem resultados -->
    <div v-if="consultado && !carregando && resultados.length === 0" class="text-grey-5 text-center q-py-xl">
      <q-icon name="search_off" size="xl" />
      <div class="q-mt-sm">Nenhum resultado encontrado para "{{ termoBusca }}"</div>
    </div>

    <!-- Resultados -->
    <div v-for="indicador in resultados" :key="indicador.id" class="q-mb-lg">
      <!-- Card do indicador -->
      <q-card flat bordered class="q-mb-sm">
        <q-card-section>
          <div class="row items-center q-mb-sm">
            <div class="col">
              <div class="text-h6">{{ indicador.nome }}</div>
              <div class="text-caption text-grey-6">
                <q-badge
                  :color="indicador.tipo_pessoa === 'F' ? 'blue-6' : 'orange-8'"
                  :label="indicador.tipo_pessoa === 'F' ? 'Pessoa Física' : 'Pessoa Jurídica'"
                  class="q-mr-sm"
                />
                {{ formatarDoc(indicador.cpf_cnpj) }}
                <span v-if="indicador.ficha"> &bull; Ficha: {{ indicador.ficha }}</span>
              </div>
            </div>
            <div class="col-auto row q-gutter-sm">
              <q-btn
                flat
                dense
                icon="edit"
                color="primary"
                :to="{ name: 'indicador-pessoal.editar', params: { id: indicador.id } }"
              >
                <q-tooltip>Editar</q-tooltip>
              </q-btn>
              <q-btn
                flat
                dense
                icon="history"
                color="grey-7"
                :to="{ name: 'indicador-pessoal.versoes', params: { cpfCnpj: indicador.cpf_cnpj } }"
              >
                <q-tooltip>Histórico de versões</q-tooltip>
              </q-btn>
            </div>
          </div>

          <!-- Dados resumidos -->
          <div class="row q-gutter-md text-caption text-grey-8">
            <div v-if="indicador.estado_civil?.descricao">
              <q-icon name="favorite" size="xs" /> {{ indicador.estado_civil.descricao }}
            </div>
            <div v-if="indicador.cidade">
              <q-icon name="place" size="xs" /> {{ indicador.cidade }}<span v-if="indicador.uf"> - {{ indicador.uf }}</span>
            </div>
            <div v-if="indicador.profissao?.descricao">
              <q-icon name="work" size="xs" /> {{ indicador.profissao.descricao }}
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Painel de indisponibilidades -->
      <IndisponibilidadesPanel :cpf-cnpj="indicador.cpf_cnpj" />
    </div>
  </q-page>
</template>

<script setup>
import { ref } from 'vue'
import { useIndicadorPessoalStore } from 'src/stores/indicador-pessoal'
import IndisponibilidadesPanel from 'src/components/indicador-pessoal/IndisponibilidadesPanel.vue'

const indicadorStore = useIndicadorPessoalStore()

const termoBusca = ref('')
const resultados = ref([])
const carregando = ref(false)
const consultado = ref(false)

async function consultar() {
  const termo = termoBusca.value?.trim()
  if (!termo) return

  carregando.value = true
  consultado.value = false
  try {
    resultados.value = await indicadorStore.buscar(termo)
  } finally {
    carregando.value = false
    consultado.value = true
  }
}

function formatarDoc(doc) {
  if (!doc) return ''
  const d = doc.replace(/\D/g, '')
  if (d.length === 11) return d.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4')
  return d.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5')
}
</script>
