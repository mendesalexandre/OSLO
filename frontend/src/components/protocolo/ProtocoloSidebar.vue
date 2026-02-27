<template>
  <div class="protocolo-sidebar">
    <!-- ETAPA ATUAL -->
    <div v-if="protocolo.etapa_info" class="sidebar-secao">
      <div class="sidebar-secao__titulo">ETAPA ATUAL</div>

      <div class="etapa-atual">
        <div class="etapa-atual__badge" :style="{ background: protocolo.etapa_info.cor || '#6B7280' }">
          {{ protocolo.etapa_info.nome || protocolo.etapa }}
        </div>
        <div class="etapa-atual__info">
          <div v-if="protocolo.etapa_usuario" class="etapa-atual__atendente">
            <q-icon name="eva-person-outline" size="14px" />
            {{ protocolo.etapa_usuario.nome }}
          </div>
          <div v-if="protocolo.etapa_info.desde" class="etapa-atual__desde">
            Desde {{ protocolo.etapa_info.desde }}
          </div>
        </div>
      </div>
    </div>

    <!-- AÇÕES DE FLUXO -->
    <div v-if="(protocolo.transicoes_permitidas && protocolo.transicoes_permitidas.length > 0) || protocolo.pode_voltar"
      class="sidebar-secao">
      <div class="sidebar-secao__titulo">AÇÕES</div>

      <div class="etapa-acoes">
        <!-- Botões de transição permitida -->
        <button v-for="etapaId in protocolo.transicoes_permitidas" :key="etapaId" class="etapa-acao-btn"
          :class="etapaClasse(etapaId)" @click="confirmarMudancaEtapa(etapaId)">
          <span class="etapa-acao-btn__label">
            {{ etapaAcaoLabel(etapaId) }}
          </span>
          <span class="etapa-acao-btn__nome">
            {{ etapaNome(etapaId) }}
          </span>
        </button>

        <!-- Voltar Etapa -->
        <button v-if="protocolo.pode_voltar" class="etapa-acao-btn etapa-acao-btn--neutro"
          @click="confirmarVoltarEtapa">
          <span class="etapa-acao-btn__nome">Voltar Etapa</span>
        </button>
      </div>
    </div>

    <!-- SOLICITANTE -->
    <div class="sidebar-secao">
      <div class="sidebar-secao__titulo">SOLICITANTE</div>

      <div class="sidebar-info">
        <div class="sidebar-info__nome">{{ protocolo.solicitante_nome }}</div>
        <div v-if="protocolo.solicitante_cpf_cnpj" class="sidebar-info__texto">
          {{ protocolo.solicitante_cpf_cnpj }}
        </div>
        <div v-if="protocolo.solicitante_telefone" class="sidebar-info__texto">
          {{ protocolo.solicitante_telefone }}
        </div>
        <div v-if="protocolo.solicitante_email" class="sidebar-info__texto">
          {{ protocolo.solicitante_email }}
        </div>
      </div>
    </div>

    <!-- DETALHES -->
    <div class="sidebar-secao">
      <div class="sidebar-secao__titulo">DETALHES</div>

      <div class="sidebar-detalhes">
        <div class="sidebar-detalhes__item">
          <span class="sidebar-detalhes__label">Matrícula</span>
          <span class="sidebar-detalhes__valor">{{ protocolo.matricula || '-' }}</span>
        </div>
        <div class="sidebar-detalhes__item">
          <span class="sidebar-detalhes__label">Atendente</span>
          <span class="sidebar-detalhes__valor">{{ protocolo.atendente?.nome || '-' }}</span>
        </div>
        <div class="sidebar-detalhes__item">
          <span class="sidebar-detalhes__label">Data</span>
          <span class="sidebar-detalhes__valor">{{ formatarData(protocolo.data_cadastro) }}</span>
        </div>
      </div>
    </div>

    <!-- HISTÓRICO -->
    <div v-if="protocolo.andamentos && protocolo.andamentos.length > 0" class="sidebar-secao">
      <div class="sidebar-secao__titulo">HISTÓRICO</div>

      <div class="sidebar-historico">
        <div v-for="(andamento, idx) in protocolo.andamentos.slice(0, 5)" :key="idx" class="sidebar-historico__item">
          <div class="sidebar-historico__bullet" :style="{ background: andamento.cor_acao }"></div>
          <div class="sidebar-historico__conteudo">
            <div class="sidebar-historico__acao">
              {{ andamento.acao_formatada }} para {{ andamento.etapa_nova?.nome }}
            </div>
            <div class="sidebar-historico__meta">
              {{ formatarDataHora(andamento.data_cadastro) }} • {{ andamento.usuario?.nome }}
            </div>
            <div v-if="andamento.observacao" class="sidebar-historico__obs">
              "{{ andamento.observacao }}"
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useProtocoloStore } from 'src/stores/protocolo'
import { useQuasar } from 'quasar'
import { onMounted, ref } from 'vue'

defineOptions({
  name: 'ProtocoloSidebar',
})

const props = defineProps({
  protocolo: {
    type: Object,
    required: true,
  },
})

const $q = useQuasar()
const protocoloStore = useProtocoloStore()
const etapas = ref([])

onMounted(async () => {
  etapas.value = await protocoloStore.listarEtapas()
})

function etapaNome(etapaId) {
  const etapa = etapas.value.find((e) => e.id === etapaId)
  return etapa?.nome || `Etapa ${etapaId}`
}

function etapaAcaoLabel(etapaId) {
  const nome = etapaNome(etapaId).toLowerCase()
  if (nome.includes('cancelado')) return 'CANCELAR PARA'
  if (nome.includes('exigência') || nome.includes('exigencia')) return 'SUSPENDER PARA'
  if (nome.includes('análise') || nome.includes('analise')) {
    // Se está vindo de exigência, é retomar
    if (props.protocolo.etapa?.nome?.toLowerCase().includes('exigência')) {
      return 'RETOMAR PARA'
    }
  }
  return 'AVANÇAR PARA'
}

function etapaClasse(etapaId) {
  const nome = etapaNome(etapaId).toLowerCase()
  if (nome.includes('cancelado')) return 'etapa-acao-btn--danger'
  if (nome.includes('exigência') || nome.includes('exigencia')) return 'etapa-acao-btn--warning'
  return 'etapa-acao-btn--success'
}

async function confirmarMudancaEtapa(etapaId) {
  const nomeEtapa = etapaNome(etapaId)
  const acaoLabel = etapaAcaoLabel(etapaId)

  $q.dialog({
    title: `${acaoLabel} ${nomeEtapa}`,
    message: 'Deseja prosseguir? Informe uma observação (opcional):',
    prompt: {
      model: '',
      type: 'textarea',
      label: 'Observação',
      outlined: true,
    },
    cancel: true,
    persistent: true,
  }).onOk(async (observacao) => {
    try {
      await protocoloStore.mudarEtapa(props.protocolo.id, etapaId, observacao || null)
      $q.notify({
        color: 'positive',
        message: `Etapa alterada para ${nomeEtapa}`,
        position: 'top-right',
      })
    } catch (error) {
      $q.notify({
        color: 'negative',
        message: error.response?.data?.mensagem || 'Erro ao alterar etapa',
        position: 'top-right',
      })
    }
  })
}

async function confirmarVoltarEtapa() {
  $q.dialog({
    title: 'Voltar Etapa',
    message: 'O protocolo voltará para a etapa anterior. Confirma?',
    prompt: {
      model: '',
      type: 'textarea',
      label: 'Motivo',
      outlined: true,
    },
    cancel: true,
  }).onOk(async (observacao) => {
    try {
      await protocoloStore.voltarEtapa(props.protocolo.id, observacao || null)
      $q.notify({
        color: 'positive',
        message: 'Etapa revertida com sucesso',
        position: 'top-right',
      })
    } catch (error) {
      $q.notify({
        color: 'negative',
        message: error.response?.data?.mensagem || 'Erro ao voltar etapa',
        position: 'top-right',
      })
    }
  })
}

function formatarData(data) {
  if (!data) return '-'
  const d = new Date(data)
  return d.toLocaleDateString('pt-BR')
}

function formatarDataHora(data) {
  if (!data) return '-'
  const d = new Date(data)
  return d.toLocaleString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>

<style lang="scss" scoped>
.protocolo-sidebar {
  background: #FFFFFF;
  border-left: 1px solid #E8EAED;
  min-height: 100vh;
  max-width: 320px;
}

// Seção da sidebar
.sidebar-secao {
  padding: 16px 20px;
  border-bottom: 1px solid #F1F3F4;

  &__titulo {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #9AA0A6;
    margin-bottom: 12px;
  }
}

// Etapa atual
.etapa-atual {
  &__badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    color: #FFFFFF;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 8px;
  }

  &__info {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }

  &__atendente,
  &__desde {
    font-size: 12px;
    color: #5F6368;
    display: flex;
    align-items: center;
    gap: 4px;
  }
}

// Botões de ação de etapa
.etapa-acoes {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.etapa-acao-btn {
  display: block;
  width: 100%;
  padding: 10px 14px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  text-align: left;
  transition: all 0.15s;

  &__label {
    display: block;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 2px;
  }

  &__nome {
    display: block;
    font-size: 14px;
    font-weight: 500;
  }

  // Avançar — verde claro
  &--success {
    background: #F0FDF4;
    color: #166534;

    .etapa-acao-btn__label {
      color: #15803D;
    }

    &:hover {
      background: #DCFCE7;
    }
  }

  // Suspender — amarelo claro
  &--warning {
    background: #FFFBEB;
    color: #92400E;

    .etapa-acao-btn__label {
      color: #B45309;
    }

    &:hover {
      background: #FEF3C7;
    }
  }

  // Cancelar — vermelho claro
  &--danger {
    background: #FEF2F2;
    color: #991B1B;

    .etapa-acao-btn__label {
      color: #DC2626;
    }

    &:hover {
      background: #FEE2E2;
    }
  }

  // Neutro (Voltar Etapa)
  &--neutro {
    background: #F5F5F5;
    color: #5F6368;

    &:hover {
      background: #EEEEEE;
    }
  }
}

// Info do solicitante
.sidebar-info {
  &__nome {
    font-size: 14px;
    font-weight: 600;
    color: #202124;
    margin-bottom: 4px;
  }

  &__texto {
    font-size: 13px;
    color: #5F6368;
    margin-bottom: 2px;
  }
}

// Detalhes
.sidebar-detalhes {
  display: flex;
  flex-direction: column;
  gap: 8px;

  &__item {
    display: flex;
    justify-content: space-between;
    gap: 8px;
  }

  &__label {
    font-size: 12px;
    color: #5F6368;
  }

  &__valor {
    font-size: 12px;
    font-weight: 500;
    color: #202124;
  }
}

// Histórico
.sidebar-historico {
  display: flex;
  flex-direction: column;
  gap: 12px;

  &__item {
    display: flex;
    gap: 8px;
  }

  &__bullet {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-top: 6px;
    flex-shrink: 0;
  }

  &__conteudo {
    flex: 1;
  }

  &__acao {
    font-size: 13px;
    font-weight: 500;
    color: #202124;
    margin-bottom: 2px;
  }

  &__meta {
    font-size: 11px;
    color: #5F6368;
    margin-bottom: 4px;
  }

  &__obs {
    font-size: 12px;
    color: #5F6368;
    font-style: italic;
  }
}
</style>