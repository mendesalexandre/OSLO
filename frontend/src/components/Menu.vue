<template>
  <div>
    <!-- Modal do Menu -->
    <q-dialog v-model="model" maximized persistent position="left" allow-focus-outside>
      <q-layout view="lHh Lpr lFf" container class="bg-white text-dark" style="width: 300px">
        <q-header class="bg-white text-dark">
          <q-toolbar>
            <q-btn flat @click="model = false" round dense class="btn-small-icon" color="dark">
              <l-icon name="menu" :size="16" />
            </q-btn>
            <q-toolbar-title class="menu-titulo">Menu</q-toolbar-title>
            <q-btn flat @click="model = false" round dense class="btn-small-icon" color="dark">
              <l-icon name="x" :size="16" />
            </q-btn>
          </q-toolbar>
        </q-header>

        <q-page-container>
          <q-page class="column no-wrap" :style-fn="getPageStyle">
            <q-scroll-area class="col">
              <q-list>
                <!-- SEÇÃO: CADASTRO DE PESSOAS -->
                <q-item-label header class="section-header">
                  <l-icon name="users" :size="14" class="q-mr-sm" />
                  Cadastro de Pessoas
                </q-item-label>

                <q-item clickable v-ripple class="menu-item" @click="openModalConsultarPessoa">
                  <q-item-section avatar>
                    <l-icon name="search" :size="16" />
                  </q-item-section>
                  <q-item-section>Consultar Pessoas</q-item-section>
                </q-item>

                <q-item clickable v-ripple class="menu-item" @click="navigateTo('nova-pessoa')">
                  <q-item-section avatar>
                    <l-icon name="user-plus" :size="16" />
                  </q-item-section>
                  <q-item-section>Nova Pessoa</q-item-section>
                </q-item>

                <q-separator class="q-my-sm" />

                <!-- SEÇÃO: REGISTRO DE IMÓVEIS (RI) -->
                <q-item-label header class="section-header">
                  <l-icon name="home" :size="14" class="q-mr-sm" />
                  Registro de Imóveis (RI)
                </q-item-label>

                <q-item v-for="(item, index) in riItems" :key="index" clickable v-ripple class="menu-item"
                  @click="handleRiItemClick(item)">
                  <q-item-section avatar>
                    <l-icon :name="getRiIcon(item.id)" :size="16" />
                  </q-item-section>
                  <q-item-section>{{ item.nome }}</q-item-section>
                </q-item>

                <q-separator class="q-my-sm" />

                <!-- SEÇÃO: REGISTRO DE TÍTULOS E DOCUMENTOS (RTD) -->
                <q-item-label header class="section-header">
                  <l-icon name="file-text" :size="14" class="q-mr-sm" />
                  RTD
                </q-item-label>

                <q-item v-for="(item, index) in rtdItems" :key="index" clickable v-ripple class="menu-item"
                  @click="navigateTo(`rtd-${index}`)">
                  <q-item-section avatar>
                    <l-icon :name="getRtdIcon(index)" :size="16" />
                  </q-item-section>
                  <q-item-section>{{ item.nome }}</q-item-section>
                </q-item>

                <q-separator class="q-my-sm" />

                <!-- SEÇÃO: FINANCEIRO -->
                <q-item-label header class="section-header">
                  <l-icon name="credit-card" :size="14" class="q-mr-sm" />
                  Financeiro
                </q-item-label>

                <q-item clickable v-ripple class="menu-item" @click="navigateTo('conta-pagar')">
                  <q-item-section avatar>
                    <l-icon name="arrow-up" :size="16" />
                  </q-item-section>
                  <q-item-section>Conta a Pagar</q-item-section>
                </q-item>

                <q-item clickable v-ripple class="menu-item" @click="navigateTo('conta-receber')">
                  <q-item-section avatar>
                    <l-icon name="arrow-down" :size="16" />
                  </q-item-section>
                  <q-item-section>Conta a Receber</q-item-section>
                </q-item>

                <q-item clickable v-ripple class="menu-item" @click="navigateTo('financeiro.caixa.index')">
                  <q-item-section avatar>
                    <l-icon name="archive" :size="16" />
                  </q-item-section>
                  <q-item-section>Caixa</q-item-section>
                </q-item>

                <q-separator class="q-my-sm" />

                <!-- SEÇÃO: RELATÓRIOS -->
                <q-item-label header class="section-header">
                  <l-icon name="bar-chart-2" :size="14" class="q-mr-sm" />
                  Relatórios
                </q-item-label>

                <q-item clickable v-ripple class="menu-item" @click="navigateTo('relatorio-movimento')">
                  <q-item-section avatar>
                    <l-icon name="trending-up" :size="16" />
                  </q-item-section>
                  <q-item-section>Movimento Diário</q-item-section>
                </q-item>

                <q-item clickable v-ripple class="menu-item" @click="navigateTo('relatorio-receita')">
                  <q-item-section avatar>
                    <l-icon name="pie-chart" :size="16" />
                  </q-item-section>
                  <q-item-section>Relatório de Receita</q-item-section>
                </q-item>
              </q-list>
            </q-scroll-area>
          </q-page>
        </q-page-container>
      </q-layout>
    </q-dialog>

  </div>
</template>

<script setup>
import { ref } from "vue";
import { useQuasar } from "quasar";
import { useRouter } from "vue-router";

defineOptions({
  name: "MenuNavegacao",
});

const $q = useQuasar();
const router = useRouter();

const model = defineModel({ default: false });
const showConsultarPessoaModal = ref(false);
const showSeloDigitalModal = ref(false);

function getPageStyle(offset, height) {
  return {
    height: `${height - offset}px`,
  };
}

const riItems = [
  {
    id: "PRENOTACAO",
    nome: "Livro 1 - Protocolo",
    rota: "centrais.onr.eprotocolo.index",
  },
  {
    id: "MATRICULA",
    nome: "Livro 2 - Matrículas",
    rota: "centrais.onr.eprotocolo.index",
  },
  {
    id: "REGISTRO_AUXILIAR",
    nome: "Livro 3 - Registro Auxiliar",
    rota: "centrais.onr.eprotocolo.index",
  },
  {
    id: "INDICADOR_REAL",
    nome: "Livro 4 - Indicador Real",
    rota: "centrais.onr.eprotocolo.index",
  },
  {
    id: "OFICIO",
    nome: "Ofício",
    rota: "oficio.index",
  },
  {
    id: "CONTRADITORIO",
    nome: "Contraditório",
    rota: "contraditorio.index",
  },
  {
    id: "NOTA_DEVOLUTIVA",
    nome: "Nota Devolutiva",
    rota: "nota-devolutiva.index",
  },
  {
    id: "INTIMACAO",
    nome: "Intimação",
    rota: "intimacao.index",
  },
  {
    id: "ONR_EPROTOCOLO",
    nome: "ONR - E-Protocolo",
    rota: "centrais.onr.eprotocolo.index",
  },
  {
    id: "ONR_ECERTIDAO",
    nome: "ONR - E-Certidão",
    rota: "centrais.onr.ecertidao.index",
  },
  {
    id: "ONR_EINTIMACAO",
    nome: "ONR - E-Intimação",
    rota: "centrais.onr.intimacao.index",
  },
  {
    id: "ONR_COMUNICACAO_PREFEITURA",
    nome: "ONR - Comunicação Prefeitura",
    rota: "onr.comunicacao.prefeitura.index",
  },
  {
    id: "ONR_VISUALIZACAO_MATRICULA",
    nome: "ONR - Visualização de Matrícula",
    rota: "centrais.onr.visualizacao-matricula.index",
  },
  {
    id: "SELO_DIGITAL",
    nome: "Selo Digital",
    rota: "selo-digital.index",
  },

  {
    id: "TRANSFERENCIA_MATRICULA",
    nome: "Transferência de Matrícula",
    rota: "imoveis.tranferencia-imoveis.index",
  },

  {
    id: "DECLARACAO_IMOBILIARIA",
    nome: "DOI - Declaração de Operações Imobiliárias",
    rota: "doi.index",
  },
];

const rtdItems = [
  { nome: "Livro A - Protocolo" },
  { nome: "Livro B - Registro" },
  { nome: "Livro F - Guarda e Conservação" },
  { nome: "Notificação" },
  { nome: "Certidão" },
];

const getRiIcon = (id) => {
  const icons = {
    PRENOTACAO: "list",
    MATRICULA: "home",
    REGISTRO_AUXILIAR: "book",
    INDICADOR_REAL: "map",
    ONR_EPROTOCOLO: "monitor",
    ONR_EINTIMACAO: "bell",
    ONR_ECERTIDAO: "award",
    OFICIO: "send",
    CONTRADITORIO: "shield",
    NOTA_DEVOLUTIVA: "file-text",
    INTIMACAO: "bell",
    ONR_COMUNICACAO_PREFEITURA: "layers",
    ONR_VISUALIZACAO_MATRICULA: "eye",
    SELO_DIGITAL: "stamp",
    DECLARACAO_IMOBILIARIA: "file-text",
  };
  return icons[id] || "file-text";
};

const getRtdIcon = (index) => {
  const icons = [
    "clipboard",
    "pen-square",
    "archive",
    "bell",
    "award",
  ];
  return icons[index] || "file-text";
};

// Nova função para abrir modal de consultar pessoa
const openModalConsultarPessoa = () => {
  model.value = false; // Fecha o menu
  showConsultarPessoaModal.value = true; // Abre o modal de consultar pessoa
};

// Nova função para abrir modal de selo digital
const openModalSeloDigital = () => {
  model.value = false; // Fecha o menu
  showSeloDigitalModal.value = true;
};


const showBuscarMatriculaModal = ref(false);
// Função para abrir a busca de matrícula
const openModalBuscarMatricula = () => {
  model.value = false; // Fecha o menu
  showBuscarMatriculaModal.value = true;
};


// Função para determinar se é modal ou rota
const handleRiItemClick = (item) => {
  if (item.id === "SELO_DIGITAL") {
    openModalSeloDigital();

  } else if (item.id === "MATRICULA") {
    openModalBuscarMatricula();

  } else {
    navigateTo(item.rota);
  }
};

const navigateTo = (route, options = {}) => {
  const { closeModal = true } = options;

  console.log(`Navegando para: ${route}`);
  router.push({ name: route });

  if (closeModal) {
    model.value = false; // Fecha o modal do menu
  }
};
</script>

<style lang="scss" scoped>
.section-header {
  display: flex;
  align-items: center;
  font-weight: 600;
  color: var(--text-color);
  font-size: var(--font-size-xs);
  text-transform: uppercase;
  letter-spacing: 0.04em;
  padding: 16px 16px 8px 16px;
}

.menu-titulo {
  font-weight: 500;
  color: var(--text-color);
  letter-spacing: 0;
}

.menu-item {
  border-radius: var(--radius-sm);
  margin: 1px 12px;
  transition: all var(--transition);
  min-height: 40px;

  &:hover {
    background-color: var(--bg-subtle);
    transform: translateX(2px);
  }

  .q-item__section--avatar {
    min-width: 40px;
    color: var(--text-secondary);
  }

  .q-item__section--main {
    font-size: var(--font-size-sm);
    color: var(--text-color);
  }
}

.q-separator {
  margin: 8px 16px;
  background-color: var(--border-color);
}

@media (max-width: 480px) {
  :deep(.q-layout-container) {
    .q-layout {
      width: 100vw !important;
    }
  }
}
</style>
