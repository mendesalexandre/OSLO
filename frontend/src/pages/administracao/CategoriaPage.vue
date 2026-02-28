<template>
  <q-page padding>
    <!-- Header -->
    <div class="row items-center q-mb-lg">
      <div class="col">
        <div class="oslo-page-title">Categorias</div>
        <div class="oslo-page-subtitle">Organize as transações por categorias e subcategorias</div>
      </div>
      <div class="col-auto">
        <q-btn
          v-permissao="'CATEGORIA_CRIAR'"
          unelevated color="primary" no-caps
          @click="abrirModalCriar()"
        >
          <l-icon name="plus" :size="16" class="q-mr-sm" />
          Nova Categoria
        </q-btn>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="categoriaStore.carregando" class="row justify-center q-my-xl">
      <q-spinner color="primary" size="50px" />
    </div>

    <!-- Grid de categorias pai -->
    <div v-else class="row q-col-gutter-md">

      <!-- Vazio -->
      <div v-if="categoriaStore.categorias.length === 0" class="col-12">
        <q-card flat bordered>
          <q-card-section class="text-center q-pa-xl">
            <l-icon name="folder-open" :size="64" class="text-grey-5" />
            <div class="text-h6 q-mt-md" style="color: var(--text-secondary)">Nenhuma categoria cadastrada</div>
            <div class="text-caption q-mt-xs" style="color: var(--text-secondary)">Clique em "Nova Categoria" para começar</div>
          </q-card-section>
        </q-card>
      </div>

      <!-- Card por categoria pai -->
      <div
        v-for="cat in categoriaStore.categorias"
        :key="cat.id"
        class="col-12 col-md-6 col-lg-4"
      >
        <q-card flat bordered>

          <!-- Cabeçalho da categoria pai -->
          <q-card-section class="q-pb-sm">
            <div class="row items-center no-wrap">
              <!-- Ícone colorido -->
              <div class="categoria-icone q-mr-md" :style="{ backgroundColor: cat.cor || 'var(--primary)' }">
                <l-icon v-if="cat.icone" :name="cat.icone" :size="20" style="color: white" />
                <l-icon v-else name="folder" :size="20" style="color: white" />
              </div>

              <div class="col">
                <div class="row items-center q-gutter-xs">
                  <span class="text-h6">{{ cat.nome }}</span>
                  <q-badge
                    :color="cat.tipo === 'RECEITA' ? 'positive' : 'negative'"
                    :label="cat.tipo === 'RECEITA' ? 'Receita' : 'Despesa'"
                    size="xs"
                  />
                  <q-badge v-if="!cat.is_ativo" color="grey" label="Inativa" size="xs" />
                </div>
                <div v-if="cat.descricao" class="text-caption q-mt-xs" style="color: var(--text-secondary)">
                  {{ cat.descricao }}
                </div>
              </div>

              <!-- Ações -->
              <div class="row no-wrap q-gutter-xs">
                <q-btn
                  v-permissao="'CATEGORIA_EDITAR'"
                  flat dense round color="grey-7" size="sm"
                  @click="abrirModalEditar(cat)"
                >
                  <l-icon name="pen" :size="14" />
                  <q-tooltip>Editar</q-tooltip>
                </q-btn>
                <q-btn
                  v-permissao="'CATEGORIA_EXCLUIR'"
                  flat dense round color="negative" size="sm"
                  @click="confirmarExcluir(cat)"
                >
                  <l-icon name="trash-2" :size="14" />
                  <q-tooltip>Excluir</q-tooltip>
                </q-btn>
              </div>
            </div>
          </q-card-section>

          <q-separator />

          <!-- Subcategorias -->
          <q-list dense>
            <q-item v-for="sub in cat.subcategorias" :key="sub.id" class="subcategoria-item">
              <q-item-section avatar style="min-width: 36px">
                <div class="subcategoria-icone" :style="{ backgroundColor: sub.cor || cat.cor || 'var(--surface-2)' }">
                  <l-icon v-if="sub.icone" :name="sub.icone" :size="14" style="color: white" />
                  <l-icon v-else name="tag" :size="14" style="color: white" />
                </div>
              </q-item-section>

              <q-item-section>
                <q-item-label>{{ sub.nome }}</q-item-label>
                <q-item-label v-if="sub.descricao" caption>{{ sub.descricao }}</q-item-label>
              </q-item-section>

              <q-item-section side>
                <div class="row no-wrap q-gutter-xs">
                  <q-btn
                    v-permissao="'CATEGORIA_EDITAR'"
                    flat dense round color="grey-7" size="xs"
                    @click="abrirModalEditar(sub)"
                  >
                    <l-icon name="pen" :size="12" />
                  </q-btn>
                  <q-btn
                    v-permissao="'CATEGORIA_EXCLUIR'"
                    flat dense round color="negative" size="xs"
                    @click="confirmarExcluir(sub)"
                  >
                    <l-icon name="trash-2" :size="12" />
                  </q-btn>
                </div>
              </q-item-section>
            </q-item>

            <q-item v-if="!cat.subcategorias?.length" class="text-caption" style="color: var(--text-secondary)">
              <q-item-section>Nenhuma subcategoria</q-item-section>
            </q-item>
          </q-list>

          <q-separator />

          <q-card-actions>
            <q-btn
              v-permissao="'CATEGORIA_CRIAR'"
              flat no-caps dense color="primary"
              @click="abrirModalSubcategoria(cat)"
            >
              <l-icon name="plus" :size="14" class="q-mr-xs" />
              Adicionar Subcategoria
            </q-btn>
          </q-card-actions>

        </q-card>
      </div>

    </div>

    <!-- Modal criar/editar -->
    <ModalCategoria
      v-model="modalAberto"
      :categoria="categoriaSelecionada"
      :categoria-pai="categoriaPaiFixa"
      @salvo="categoriaStore.listar()"
    />
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { useCategoriaStore } from 'src/stores/categoria'
import ModalCategoria from 'src/components/categoria/ModalCategoria.vue'

defineOptions({ name: 'CategoriaPage' })

const $q = useQuasar()
const categoriaStore = useCategoriaStore()

const modalAberto          = ref(false)
const categoriaSelecionada = ref(null)
const categoriaPaiFixa     = ref(null)

function abrirModalCriar() {
  categoriaSelecionada.value = null
  categoriaPaiFixa.value     = null
  modalAberto.value          = true
}

function abrirModalEditar(cat) {
  categoriaSelecionada.value = cat
  categoriaPaiFixa.value     = null
  modalAberto.value          = true
}

function abrirModalSubcategoria(pai) {
  categoriaSelecionada.value = null
  categoriaPaiFixa.value     = pai
  modalAberto.value          = true
}

function confirmarExcluir(cat) {
  $q.dialog({
    title:   'Excluir categoria',
    message: `Deseja excluir a categoria "<strong>${cat.nome}</strong>"?`,
    html:    true,
    cancel:  { label: 'Cancelar', flat: true, color: 'grey-7' },
    ok:      { label: 'Excluir',  unelevated: true, color: 'negative', noCaps: true },
  }).onOk(async () => {
    try {
      await categoriaStore.excluir(cat.id)
      $q.notify({ type: 'positive', message: 'Categoria excluída com sucesso', position: 'top' })
    } catch (e) {
      $q.notify({
        type:    'negative',
        message: e.response?.data?.mensagem ?? 'Erro ao excluir categoria',
        position: 'top',
      })
    }
  })
}

onMounted(() => categoriaStore.listar())
</script>

<style scoped lang="scss">
.categoria-icone {
  width: 44px;
  height: 44px;
  border-radius: var(--radius-md);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.subcategoria-icone {
  width: 28px;
  height: 28px;
  border-radius: var(--radius-sm);
  display: flex;
  align-items: center;
  justify-content: center;
}

.subcategoria-item {
  padding: 6px 16px;
  min-height: unset;

  &:hover { background: var(--surface); }
}
</style>
