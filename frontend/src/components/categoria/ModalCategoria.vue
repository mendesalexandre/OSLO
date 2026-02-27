<template>
  <modal
    v-model="model"
    :titulo="categoria ? 'Editar Categoria' : (isPaiFixo ? 'Nova Subcategoria' : 'Nova Categoria')"
    tamanho="sm"
    @close="model = false"
  >
    <div class="q-gutter-md">

      <!-- Se tem pai fixo, exibe só como informação -->
      <div v-if="isPaiFixo" class="q-pa-sm rounded-borders" style="background: var(--surface); border: 1px solid var(--border-color)">
        <div class="text-caption" style="color: var(--text-secondary)">Subcategoria de</div>
        <div class="text-body2 text-weight-medium row items-center q-gutter-xs">
          <l-icon v-if="categoriaPai?.icone" :name="categoriaPai.icone" :size="16" />
          <span>{{ categoriaPai?.nome }}</span>
        </div>
      </div>

      <!-- Categoria Pai (quando não é fixo e está criando) -->
      <div v-if="!isPaiFixo && !categoria">
        <v-label label="Categoria Pai" />
        <q-select
          v-model="form.categoria_pai_id"
          :options="categoriasRaiz"
          option-value="id"
          option-label="nome"
          emit-value map-options
          outlined dense clearable
          placeholder="Deixe vazio para criar categoria raiz"
        >
          <template #prepend><l-icon name="folder" :size="14" /></template>
          <template #option="scope">
            <q-item v-bind="scope.itemProps">
              <q-item-section avatar>
                <l-icon v-if="scope.opt.icone" :name="scope.opt.icone" :size="16" />
                <l-icon v-else name="folder" :size="16" />
              </q-item-section>
              <q-item-section>{{ scope.opt.nome }}</q-item-section>
            </q-item>
          </template>
        </q-select>
      </div>

      <!-- Nome -->
      <div>
        <v-label label="Nome *" />
        <q-input
          v-model="form.nome"
          outlined dense
          :rules="[v => !!v || 'Obrigatório']"
        />
      </div>

      <!-- Descrição -->
      <div>
        <v-label label="Descrição" />
        <q-input v-model="form.descricao" outlined dense type="textarea" rows="2" autogrow />
      </div>

      <!-- Tipo -->
      <div>
        <v-label label="Tipo *" />
        <div class="tipo-wrapper">
          <button
            v-for="op in tiposOpcoes"
            :key="op.value"
            type="button"
            class="tipo-btn"
            :class="[`tipo-btn--${op.value.toLowerCase()}`, { 'tipo-btn--ativo': form.tipo === op.value }]"
            @click="form.tipo = op.value"
          >
            <l-icon :name="op.icon" :size="15" class="q-mr-xs" />
            {{ op.label }}
          </button>
        </div>
      </div>

      <!-- Ícone -->
      <div>
        <v-label label="Ícone (Lucide)" />
        <q-input
          v-model="form.icone"
          outlined dense
          placeholder="ex: heart, home, car, shopping-cart..."
        >
          <template #prepend>
            <l-icon v-if="form.icone" :name="form.icone" :size="16" />
            <l-icon v-else name="image" :size="16" />
          </template>
        </q-input>
      </div>

      <!-- Paleta de cores -->
      <div>
        <v-label label="Cor" />
        <div class="paleta-wrapper">
          <button
            v-for="cor in paleta"
            :key="cor.hex"
            type="button"
            class="paleta-circulo"
            :class="{ 'paleta-circulo--ativo': form.cor === cor.hex }"
            :style="{ backgroundColor: cor.hex }"
            :title="cor.nome"
            @click="form.cor = cor.hex"
          >
            <l-icon v-if="form.cor === cor.hex" name="check" :size="14" style="color: white" />
          </button>

          <!-- Nenhuma cor -->
          <button
            type="button"
            class="paleta-circulo paleta-circulo--vazio"
            :class="{ 'paleta-circulo--ativo': !form.cor }"
            title="Sem cor"
            @click="form.cor = ''"
          >
            <l-icon v-if="!form.cor" name="check" :size="14" style="color: #999" />
          </button>
        </div>
      </div>

      <!-- Preview -->
      <div v-if="form.icone || form.cor" class="row items-center q-gutter-sm q-pa-sm rounded-borders" style="background: var(--surface)">
        <div
          class="preview-icone"
          :style="{ backgroundColor: form.cor || '#e0e0e0' }"
        >
          <l-icon v-if="form.icone" :name="form.icone" :size="22" style="color: white" />
        </div>
        <div>
          <div class="text-body2 text-weight-medium">{{ form.nome || 'Preview' }}</div>
          <div class="text-caption" style="color: var(--text-secondary)">{{ form.cor || 'Sem cor' }}</div>
        </div>
      </div>

      <q-toggle v-model="form.is_ativo" label="Ativa" />
    </div>

    <template #rodape>
      <div class="row justify-end q-gutter-sm">
        <q-btn flat no-caps label="Cancelar" @click="model = false" />
        <q-btn unelevated color="primary" no-caps :loading="salvando" @click="salvar">
          <l-icon name="save" :size="16" class="q-mr-sm" />
          Salvar
        </q-btn>
      </div>
    </template>
  </modal>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useQuasar } from 'quasar'
import { useCategoriaStore } from 'src/stores/categoria'

defineOptions({ name: 'ModalCategoria' })

const props = defineProps({
  categoria:    { type: Object, default: null },
  categoriaPai: { type: Object, default: null },
})

const emit  = defineEmits(['salvo'])
const model = defineModel({ default: false })

const $q             = useQuasar()
const categoriaStore = useCategoriaStore()
const salvando       = ref(false)

// ── Paleta ────────────────────────────────────────────────────────────────────
const paleta = [
  { hex: '#E53935', nome: 'Vermelho'    },
  { hex: '#F97316', nome: 'Laranja'     },
  { hex: '#F59E0B', nome: 'Âmbar'       },
  { hex: '#EAB308', nome: 'Amarelo'     },
  { hex: '#84CC16', nome: 'Lima'        },
  { hex: '#22C55E', nome: 'Verde'       },
  { hex: '#10B981', nome: 'Esmeralda'   },
  { hex: '#14B8A6', nome: 'Teal'        },
  { hex: '#06B6D4', nome: 'Ciano'       },
  { hex: '#3B82F6', nome: 'Azul'        },
  { hex: '#6366F1', nome: 'Índigo'      },
  { hex: '#8B5CF6', nome: 'Violeta'     },
  { hex: '#A855F7', nome: 'Roxo'        },
  { hex: '#EC4899', nome: 'Rosa'        },
  { hex: '#F43F5E', nome: 'Rosa Forte'  },
  { hex: '#64748B', nome: 'Cinza Azul'  },
  { hex: '#6B7280', nome: 'Cinza'       },
  { hex: '#78716C', nome: 'Pedra'       },
  { hex: '#92400E', nome: 'Marrom'      },
  { hex: '#1E293B', nome: 'Escuro'      },
]

// ── Opções ────────────────────────────────────────────────────────────────────
const tiposOpcoes = [
  { label: 'Receita', value: 'RECEITA', icon: 'trending-up'   },
  { label: 'Despesa', value: 'DESPESA', icon: 'trending-down' },
]

const categoriasRaiz = computed(() =>
  categoriaStore.categorias.filter(c => !c.categoria_pai_id)
)

const isPaiFixo = computed(() => !!props.categoriaPai)

// ── Form ──────────────────────────────────────────────────────────────────────
const formVazio = () => ({
  nome:             '',
  descricao:        '',
  tipo:             'DESPESA',
  icone:            '',
  cor:              '',
  is_ativo:         true,
  categoria_pai_id: props.categoriaPai?.id ?? null,
})

const form = ref(formVazio())

watch(model, (aberto) => {
  if (aberto) {
    form.value = props.categoria
      ? {
          nome:             props.categoria.nome,
          descricao:        props.categoria.descricao ?? '',
          tipo:             props.categoria.tipo,
          icone:            props.categoria.icone ?? '',
          cor:              props.categoria.cor ?? '',
          is_ativo:         props.categoria.is_ativo,
          categoria_pai_id: props.categoria.categoria_pai_id ?? null,
        }
      : formVazio()
  }
})

// ── Salvar ────────────────────────────────────────────────────────────────────
const salvar = async () => {
  salvando.value = true
  try {
    const payload = {
      ...form.value,
      icone: form.value.icone || null,
      cor:   form.value.cor   || null,
    }
    if (props.categoria) {
      await categoriaStore.atualizar(props.categoria.id, payload)
      $q.notify({ type: 'positive', message: 'Categoria atualizada com sucesso', position: 'top' })
    } else {
      await categoriaStore.criar(payload)
      $q.notify({ type: 'positive', message: 'Categoria criada com sucesso', position: 'top' })
    }
    model.value = false
    emit('salvo')
  } catch (e) {
    $q.notify({
      type:    'negative',
      message: 'Erro ao salvar categoria',
      caption: e.response?.data?.mensagem ?? e.response?.data?.message,
    })
  } finally {
    salvando.value = false
  }
}
</script>

<style scoped lang="scss">
.tipo-wrapper {
  display: flex;
  gap: 8px;
}

.tipo-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 4px;
  padding: 8px 0;
  border-radius: var(--radius-sm);
  border: 2px solid var(--border-color);
  background: transparent;
  color: var(--text-secondary);
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.15s;

  &:hover {
    background: var(--surface);
  }

  &--receita {
    &.tipo-btn--ativo {
      background: #dcfce7;
      border-color: #22c55e;
      color: #16a34a;
    }
  }

  &--despesa {
    &.tipo-btn--ativo {
      background: #fee2e2;
      border-color: #ef4444;
      color: #dc2626;
    }
  }
}

.paleta-wrapper {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.paleta-circulo {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: 2px solid transparent;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.15s, border-color 0.15s;
  padding: 0;
  outline: none;

  &:hover {
    transform: scale(1.15);
  }

  &--ativo {
    border-color: var(--text-color);
    transform: scale(1.1);
  }

  &--vazio {
    background-color: transparent !important;
    border: 2px dashed #ccc;
  }
}

.preview-icone {
  width: 44px;
  height: 44px;
  border-radius: var(--radius-md);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
</style>
