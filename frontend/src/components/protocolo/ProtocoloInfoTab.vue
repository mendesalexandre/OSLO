<template>
  <div class="protocolo-info-tab">
    <!-- Grid de Informações -->
    <div class="oslo-info-grid">
      <div class="oslo-info-item">
        <span class="oslo-info-label">Número</span>
        <span class="oslo-info-valor">{{ protocolo.numero }}</span>
      </div>

      <div class="oslo-info-item">
        <span class="oslo-info-label">Solicitante</span>
        <span class="oslo-info-valor">{{ protocolo.solicitante_nome }}</span>
      </div>

      <div class="oslo-info-item">
        <span class="oslo-info-label">Data de Abertura</span>
        <span class="oslo-info-valor">{{ formatarDataHora(protocolo.data_cadastro) }}</span>
      </div>

      <div class="oslo-info-item">
        <span class="oslo-info-label">CPF/CNPJ</span>
        <span class="oslo-info-valor">{{ protocolo.solicitante_cpf_cnpj || '-' }}</span>
      </div>

      <div class="oslo-info-item">
        <span class="oslo-info-label">Matrícula</span>
        <span class="oslo-info-valor">{{ protocolo.matricula || '-' }}</span>
      </div>

      <div class="oslo-info-item">
        <span class="oslo-info-label">Status</span>
        <span class="oslo-status" :class="'oslo-status--' + protocolo.status">
          {{ getStatusLabel(protocolo.status) }}
        </span>
      </div>

      <div class="oslo-info-item" v-if="protocolo.solicitante_telefone">
        <span class="oslo-info-label">Telefone</span>
        <span class="oslo-info-valor">{{ protocolo.solicitante_telefone }}</span>
      </div>

      <div class="oslo-info-item" v-if="protocolo.solicitante_email">
        <span class="oslo-info-label">E-mail</span>
        <span class="oslo-info-valor">{{ protocolo.solicitante_email }}</span>
      </div>
    </div>

    <!-- Observações -->
    <div class="oslo-info-observacoes">
      <div class="oslo-info-observacoes__header">
        <h3>Observações</h3>
        <q-btn
          v-if="!editandoObservacao"
          flat
          dense
          size="sm"
          icon="edit"
          label="Editar"
          color="primary"
          no-caps
          @click="iniciarEdicao"
        />
      </div>

      <div v-if="!editandoObservacao" class="oslo-info-observacoes__texto">
        {{ protocolo.observacao || 'Nenhuma observação registrada' }}
      </div>

      <div v-else>
        <q-input
          v-model="observacaoTemp"
          type="textarea"
          outlined
          rows="4"
          placeholder="Adicione observações..."
        />
        <div class="row justify-end q-gutter-sm q-mt-sm">
          <q-btn
            label="Cancelar"
            flat
            color="grey-7"
            size="sm"
            no-caps
            @click="cancelarEdicao"
          />
          <q-btn
            label="Salvar"
            unelevated
            color="primary"
            size="sm"
            no-caps
            :loading="salvandoObservacao"
            @click="salvarObservacao"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useQuasar } from 'quasar'
import { useProtocoloStore } from 'src/stores/protocolo'
import { formatarDataHora } from 'src/Utils'

defineOptions({ name: 'ProtocoloInfoTab' })

const props = defineProps({
  protocolo: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['atualizar'])

const $q = useQuasar()
const protocoloStore = useProtocoloStore()

const editandoObservacao = ref(false)
const observacaoTemp = ref('')
const salvandoObservacao = ref(false)

function iniciarEdicao() {
  observacaoTemp.value = props.protocolo.observacao || ''
  editandoObservacao.value = true
}

function cancelarEdicao() {
  editandoObservacao.value = false
  observacaoTemp.value = ''
}

async function salvarObservacao() {
  salvandoObservacao.value = true
  try {
    const resultado = await protocoloStore.atualizar(props.protocolo.id, {
      observacao: observacaoTemp.value,
    })

    if (resultado.sucesso) {
      $q.notify({
        type: 'positive',
        message: 'Observação atualizada com sucesso',
        position: 'top',
      })
      editandoObservacao.value = false
      emit('atualizar')
    }
  } catch (error) {
    $q.notify({
      type: 'negative',
      message: 'Erro ao atualizar observação',
      position: 'top',
    })
  } finally {
    salvandoObservacao.value = false
  }
}

function getStatusColor(status) {
  const cores = {
    aberto: 'blue',
    pago: 'green',
    pago_parcial: 'orange',
    isento: 'grey',
    cancelado: 'red',
  }
  return cores[status] || 'grey'
}

function getStatusLabel(status) {
  const labels = {
    aberto: 'Aberto',
    pago: 'Pago',
    pago_parcial: 'Pago Parcial',
    isento: 'Isento',
    cancelado: 'Cancelado',
  }
  return labels[status] || status
}
</script>

<style lang="scss" scoped>
.protocolo-info-tab {
  padding: 24px;
}

.oslo-info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0;
  background: #FFFFFF;
  border: 1px solid #E8EAED;
  border-radius: 8px;
  overflow: hidden;
}

.oslo-info-item {
  padding: 16px 20px;
  border-bottom: 1px solid #F1F3F4;
  display: flex;
  flex-direction: column;
  gap: 4px;

  // Borda direita nos itens da coluna esquerda
  &:nth-child(odd) {
    border-right: 1px solid #F1F3F4;
  }

  // Remover borda bottom dos últimos 2 itens
  &:nth-last-child(-n+2) {
    border-bottom: none;
  }
}

.oslo-info-label {
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: #9AA0A6;
}

.oslo-info-valor {
  font-size: 14px;
  font-weight: 500;
  color: #202124;
}

.oslo-status {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 14px;
  font-weight: 600;

  &::before {
    content: '●';
    font-size: 12px;
  }

  &--aberto {
    color: #1A73E8;
  }

  &--pago {
    color: #188038;
  }

  &--pago_parcial {
    color: #E37400;
  }

  &--isento {
    color: #5F6368;
  }

  &--cancelado {
    color: #D93025;
  }
}

// Observações
.oslo-info-observacoes {
  margin-top: 20px;
  background: #FFFFFF;
  border: 1px solid #E8EAED;
  border-radius: 8px;
  padding: 20px;

  &__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;

    h3 {
      font-size: 16px;
      font-weight: 600;
      color: #202124;
      margin: 0;
    }
  }

  &__texto {
    font-size: 14px;
    color: #5F6368;
    margin: 0;
    line-height: 1.6;
    white-space: pre-wrap;
    min-height: 40px;
  }
}

// Responsivo
@media (max-width: 640px) {
  .oslo-info-grid {
    grid-template-columns: 1fr;
  }

  .oslo-info-item:nth-child(odd) {
    border-right: none;
  }
}
</style>
