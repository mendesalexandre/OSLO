<template>
  <q-dialog v-model="model" :persistent="persistente" :maximized="isMaximized" :full-width="fullWidth"
    :full-height="fullHeight" :seamless="seamless" :position="position" :square="square"
    :no-backdrop-dismiss="noBackdropDismiss" allow-focus-outside class="modal-container" transition-duration="100"
    transition-show="jump-down" transition-hide="jump-up">
    <q-card :style="cardStyles" class="modal-cabecalho" :class="[
      cardCompleto ? 'full-height' : '',
      isMaximized ? 'modal-maximized' : `modal-${tamanho}`,
    ]">
      <!-- Cabeçalho -->
      <q-card-section class="row items-center cabecalho-container"
        :class="[classCabecalho, noPaddingCabecalho ? 'no-padding' : '']"
        :style="{ backgroundColor: corCabecalho, ...styleCabecalho }">
        <!-- Botão Voltar -->
        <i v-if="showBackBtn" id="btn-voltar" class="ion-arrow-left-a btn-voltar" @click="$emit('back')"
          :style="{ color: corBotaoVoltar }"></i>

        <div class="titulo-container">
          <div class="titulo-centralizado" :class="[classTitulo, corTituloCabecalho]" :style="styleTitulo">
            {{ titulo }}
          </div>
          <div v-if="subtitulo" class="subtitulo-centralizado" :class="classSubtitulo" :style="styleSubtitulo">
            {{ subtitulo }}
          </div>
        </div>

        <div class="controles-direita">
          <slot name="controles">
            <!-- Botão de maximizar/restaurar -->
            <q-btn v-if="showMaximizeBtn" :icon="internalMaximized ? 'fullscreen_exit' : 'fullscreen'" size="sm" round
              dense flat color="grey-7" :aria-label="internalMaximized ? 'Restaurar' : 'Maximizar'"
              @click="toggleMaximize" />
            <q-btn icon="close" size="sm" round dense flat :color="corBotaoFechar" :aria-label="'Fechar ' + titulo"
              @click="$emit('close')" />
          </slot>
        </div>
      </q-card-section>

      <!-- Separador -->
      <q-separator v-if="separador" />

      <!-- Área de Tabs (se existir slot tabs) -->
      <div v-if="$slots.tabs" class="modal-tabs-container">
        <slot name="tabs"></slot>
      </div>

      <!-- Separador após tabs -->
      <q-separator v-if="$slots.tabs && separador" />

      <!-- Conteúdo -->
      <div class="modal-conteudo" :class="[
        background,
        $slots.tabs ? 'modal-has-tabs' : 'modal-no-tabs',
        isMaximized ? 'conteudo-maximized' : '',
        !$slots.rodape ? 'sem-rodape' : '',
        noPadding ? 'no-padding' : ''
      ]">
        <slot></slot>
      </div>

      <!-- Rodapé -->
      <template v-if="$slots.rodape">
        <q-separator v-if="separador" />
        <div class="modal-rodape">
          <slot name="rodape"></slot>
        </div>
      </template>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { uid } from "quasar";
import { computed, ref, watch } from "vue";

defineOptions({
  name: "Modal",
});

const props = defineProps({
  nome: String,
  id: {
    type: String,
    default: `modal-${uid()}`,
  },
  titulo: {
    type: String,
    default: "Título do Modal",
  },
  subtitulo: {
    type: String,
    default: "",
  },
  visivel: {
    type: Boolean,
    default: true,
  },
  persistente: {
    type: Boolean,
    default: true,
  },
  separador: {
    type: Boolean,
    default: true,
  },
  altura: {
    type: [String, Number],
    default: "auto",
  },
  largura: {
    type: [String, Number],
    default: "auto",
  },
  background: {
    type: String,
    default: "",
  },
  corCabecalho: {
    type: String,
    default: "",
  },
  corTituloCabecalho: {
    type: String,
    default: '',
  },
  cardCompleto: {
    type: Boolean,
    default: false,
  },
  mobile: {
    type: Boolean,
    default: false,
  },
  // Prop de tamanho
  tamanho: {
    type: String,
    default: "md",
    validator: (value) => ["xs", "sm", "md", "lg", "xl"].includes(value),
  },


  // ===== NOVAS PROPRIEDADES DO Q-DIALOG =====

  // Maximiza o modal para tela cheia
  maximized: {
    type: Boolean,
    default: false,
  },

  // Modal ocupa 100% da largura disponível
  fullWidth: {
    type: Boolean,
    default: false,
  },

  // Modal ocupa 100% da altura disponível
  fullHeight: {
    type: Boolean,
    default: false,
  },

  // Remove o backdrop/overlay escuro
  seamless: {
    type: Boolean,
    default: false,
  },

  noPadding: {
    type: Boolean,
    default: false,
  },

  noPaddingCabecalho: {
    type: Boolean,
    default: false,
  },

  // Posicionamento: 'standard' | 'top' | 'right' | 'bottom' | 'left'
  position: {
    type: String,
    default: 'standard',
    validator: (value) => ['standard', 'top', 'right', 'bottom', 'left'].includes(value),
  },

  // Remove border-radius do card
  square: {
    type: Boolean,
    default: false,
  },

  // Impede fechar clicando no backdrop (similar ao persistent)
  noBackdropDismiss: {
    type: Boolean,
    default: false,
  },

  // Mostra botão de maximizar/restaurar no cabeçalho
  showMaximizeBtn: {
    type: Boolean,
    default: false,
  },

  // Mostra botão de voltar no cabeçalho
  showBackBtn: {
    type: Boolean,
    default: false,
  },

  // Cor do botão de voltar
  corBotaoVoltar: {
    type: String,
    default: 'white',
  },

  // Cor do botão de fechar
  corBotaoFechar: {
    type: String,
    default: 'dark',
  },

  // Adicione estas props junto com as outras:
  classCabecalho: {
    type: String,
    default: '',
  },
  styleCabecalho: {
    type: Object,
    default: () => ({}),
  },
  classTitulo: {
    type: String,
    default: 'text-h6 text-wrap text-uppercase text-bold',
  },
  styleTitulo: {
    type: Object,
    default: () => ({}),
  },
  classSubtitulo: {
    type: String,
    default: 'text-caption text-grey-7',
  },
  styleSubtitulo: {
    type: Object,
    default: () => ({}),
  },
});

const emit = defineEmits(['close', 'update:maximized', 'back']);
const model = defineModel({ default: false });

// Estado interno para controlar maximização
const internalMaximized = ref(props.maximized);

// Computed que verifica se está maximizado (considerando mobile também)
const isMaximized = computed(() => internalMaximized.value || props.mobile || props.maximized);

// Função para alternar maximização
const toggleMaximize = () => {
  internalMaximized.value = !internalMaximized.value;
  emit('update:maximized', internalMaximized.value);
};

// Watch para sincronizar prop externa com estado interno
watch(() => props.maximized, (newVal) => {
  internalMaximized.value = newVal;
});

// Computed para definir o width do card baseado no tamanho
const cardStyles = computed(() => {
  let styles = {};

  // Se estiver maximizado, não aplicar estilos de dimensão
  if (isMaximized.value) {
    return styles;
  }

  // Se fullWidth ou fullHeight, não aplicar width/height fixos
  if (props.fullWidth) {
    styles.width = '100%';
  }

  if (props.fullHeight) {
    styles.height = '100%';
  }

  // Aplicar altura customizada se definida
  if (props.altura !== 'auto' && !props.fullHeight) {
    styles.height = typeof props.altura === 'number' ? `${props.altura}px` : props.altura;
  }

  // Aplicar largura customizada se definida
  if (props.largura !== 'auto' && !props.fullWidth) {
    styles.width = typeof props.largura === 'number' ? `${props.largura}px` : props.largura;
  }

  // Se cor do cabeçalho foi definida via prop
  if (props.corCabecalho) {
    styles.backgroundColor = props.corCabecalho;
  }

  return styles;
});
</script>

<style lang="scss" scoped>
// Botão de voltar
.btn-voltar {
  color: var(--text-muted);
  top: 18px;
  left: 15px;
  position: absolute;
  font-size: 26px;
  line-height: 0;
  cursor: pointer;

  &:hover {
    color: var(--text-color);
  }
}

// Cabeçalho
.cabecalho-container {
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  background: var(--primary-bg);
  padding: 16px 24px;
}

.titulo-container {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
}

.titulo-centralizado {
  text-align: center;
  font-size: var(--font-size-lg) !important;
  font-weight: 600 !important;
  color: var(--text-color) !important;
  text-transform: none !important;
}

.subtitulo-centralizado {
  text-align: center;
  margin-top: 2px;
  font-size: var(--font-size-xs);
  color: var(--text-secondary);
}

.controles-direita {
  position: absolute;
  right: 16px;
  display: flex;
  gap: 4px;
  align-items: center;
}

// Dialog padding
.modal-container :deep(.q-dialog__inner--minimized) {
  padding: 50px 0 !important;
}

.modal-container :deep(.q-dialog__inner--maximized) {
  padding: 0 !important;
}

// Card do modal
.modal-cabecalho {
  position: relative;
  display: flex;
  flex-direction: column;
  border-radius: var(--radius-lg) !important;

  &.modal-maximized {
    width: 100vw !important;
    height: 100vh !important;
    max-width: 100vw !important;
    max-height: 100vh !important;
    border-radius: 0 !important;
    margin: 0 !important;
  }
}

// Tabs
.modal-tabs-container {
  :deep(.q-tabs) {
    background: transparent;

    .q-tab {
      padding: 0 16px;
      min-height: 36px !important;
      border-radius: 0;
      text-transform: none;
      color: var(--text-secondary);
      font-weight: 500;

      &.q-tab--active {
        color: var(--text-color);
        font-weight: 600;
      }

      &:hover {
        color: var(--text-color);
        background-color: rgba(0, 0, 0, 0.03);
      }
    }

    .q-tab__indicator {
      height: 2px;
      background-color: var(--primary);
    }

    .q-tab__label {
      font-weight: inherit;
      text-transform: none;
      font-size: var(--font-size-sm);
      letter-spacing: 0;
      color: inherit;
    }

    .q-ripple {
      display: none;
    }
  }
}

// Conteúdo
.modal-conteudo {
  flex: 1;
  overflow-y: auto;
  padding: 24px !important;
  background-color: var(--background) !important;

  &.no-padding {
    padding: 0 !important;
  }

  &.modal-no-tabs {
    &.sem-rodape {
      max-height: 100vh;
      overflow: hidden;
    }
  }

  &.conteudo-maximized {
    &.modal-no-tabs {
      max-height: calc(100vh - 120px) !important;

      &.sem-rodape {
        max-height: calc(100vh - 64px) !important;
      }
    }

    &.modal-has-tabs {
      max-height: calc(100vh - 160px) !important;

      &.sem-rodape {
        max-height: calc(100vh - 104px) !important;
      }
    }
  }
}

// Rodapé
.modal-rodape {
  background-color: var(--surface) !important;
  padding: 12px 24px !important;
  border-top: 1px solid var(--border-color);
}

// Tamanhos
.modal-xs {
  max-width: 400px !important;
  width: 100% !important;
}

.modal-sm {
  max-width: 500px !important;
  width: 100% !important;
}

.modal-md {
  max-width: 600px !important;
  width: 100% !important;
}

.modal-lg {
  max-width: 900px !important;
  width: 100% !important;
}

.modal-xl {
  max-width: 1200px !important;
  width: 100% !important;
}

// Responsivo
@media (max-width: 768px) {
  .modal-xs,
  .modal-sm,
  .modal-md,
  .modal-lg,
  .modal-xl {
    width: 100% !important;
    max-width: none !important;
    overflow: hidden;
  }

  .modal-conteudo {
    &.modal-no-tabs {
      max-height: calc(100vh - 100px) !important;
    }

    &.modal-has-tabs {
      max-height: calc(100vh - 120px) !important;
    }

    padding: 16px !important;
  }

  .cabecalho-container {
    padding: 12px 16px;
  }

  .modal-rodape {
    padding: 8px 16px !important;
  }
}

@media (max-width: 480px) {
  .modal-xs,
  .modal-sm,
  .modal-md,
  .modal-lg,
  .modal-xl {
    width: 98% !important;
    overflow: hidden;
  }
}
</style>