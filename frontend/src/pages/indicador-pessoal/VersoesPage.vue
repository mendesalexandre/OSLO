<template>
  <q-page padding>
    <div class="row items-center q-mb-md">
      <q-btn flat dense round icon="arrow_back" :to="{ name: 'indicador-pessoal.lista' }" class="q-mr-sm" />
      <div class="text-h5">Histórico de Versões</div>
      <q-chip v-if="versoes.length" class="q-ml-md" color="primary" text-color="white">
        {{ versoes.length }} versão(ões)
      </q-chip>
    </div>

    <q-inner-loading :showing="carregando" />

    <div v-if="!carregando && versoes.length === 0" class="text-center q-mt-xl text-grey">
      Nenhum registro encontrado para este CPF/CNPJ.
    </div>

    <q-timeline color="primary">
      <q-timeline-entry
        v-for="versao in versoes"
        :key="versao.id"
        :title="`Versão ${versao.versao}${versao.is_atual ? ' (atual)' : ''}`"
        :subtitle="formatarData(versao.data_versao)"
        :color="versao.is_atual ? 'positive' : 'grey-5'"
        :icon="versao.is_atual ? 'star' : 'history'"
      >
        <q-card flat :bordered="versao.is_atual" class="q-mb-sm">
          <q-card-section>
            <div class="row q-gutter-md">
              <div class="col-12">
                <span class="text-weight-medium">{{ versao.nome }}</span>
                <q-badge
                  :color="versao.tipo_pessoa === 'F' ? 'blue-6' : 'orange-8'"
                  :label="versao.tipo_pessoa === 'F' ? 'PF' : 'PJ'"
                  class="q-ml-sm"
                />
                <q-badge v-if="versao.is_atual" color="positive" label="Atual" class="q-ml-xs" />
              </div>
              <div v-if="versao.motivo_versao" class="col-12">
                <q-icon name="info" color="grey-6" size="xs" class="q-mr-xs" />
                <span class="text-grey-7">{{ versao.motivo_versao }}</span>
              </div>
              <div class="col-auto text-grey-6 text-caption">
                CPF/CNPJ: {{ formatarDoc(versao.cpf_cnpj) }}
              </div>
              <div v-if="versao.ficha" class="col-auto text-grey-6 text-caption">
                Ficha: {{ versao.ficha }}
              </div>
              <div v-if="versao.estado_civil" class="col-auto text-grey-6 text-caption">
                Estado Civil: {{ versao.estado_civil.descricao }}
              </div>
            </div>
          </q-card-section>
          <q-card-actions v-if="versao.is_atual">
            <q-btn
              flat
              size="sm"
              label="Editar"
              icon="edit"
              color="primary"
              :to="{ name: 'indicador-pessoal.editar', params: { id: versao.id } }"
            />
          </q-card-actions>
        </q-card>
      </q-timeline-entry>
    </q-timeline>
  </q-page>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useIndicadorPessoalStore } from 'src/stores/indicador-pessoal'

const route = useRoute()
const indicadorPessoalStore = useIndicadorPessoalStore()

const versoes = ref([])
const carregando = ref(false)

onMounted(async () => {
  carregando.value = true
  versoes.value = await indicadorPessoalStore.fetchVersoes(route.params.cpfCnpj)
  carregando.value = false
})

function formatarDoc(doc) {
  if (!doc) return ''
  const d = doc.replace(/\D/g, '')
  if (d.length === 11) return d.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4')
  return d.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5')
}

function formatarData(data) {
  if (!data) return ''
  return new Date(data).toLocaleString('pt-BR', { dateStyle: 'short', timeStyle: 'short' })
}
</script>
