<template>
  <q-page padding>
    <!-- Header -->
    <div class="row items-center q-mb-lg">
      <div class="col">
        <div class="oslo-page-title">Permissões</div>
        <div class="oslo-page-subtitle">Visualize todas as permissões disponíveis no sistema</div>
      </div>
    </div>

    <!-- Busca -->
    <q-card flat bordered class="q-mb-md">
      <q-card-section class="q-py-sm">
        <div class="row q-col-gutter-md items-end">
          <div class="col-12 col-md-5">
            <v-label label="Buscar" />
            <q-input
              v-model="busca"
              outlined dense clearable
              placeholder="Nome, descrição ou módulo..."
              debounce="300"
              @update:model-value="filtrar"
            >
              <template #prepend><l-icon name="search" :size="16" /></template>
            </q-input>
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Lista agrupada por módulo -->
    <div v-if="permissaoStore.carregando" class="text-center q-pa-xl">
      <q-spinner size="48px" />
    </div>

    <template v-else>
      <div v-for="modulo in modulosFiltrados" :key="modulo.modulo" class="q-mb-md">
        <q-expansion-item
          :label="modulo.modulo"
          :caption="`${modulo.permissoes.length} permissão(ões)`"
          default-opened
          header-class="bg-grey-2 rounded-borders"
          expand-separator
        >
          <q-card flat bordered>
            <q-list separator>
              <q-item v-for="perm in modulo.permissoes" :key="perm.id" dense>
                <q-item-section>
                  <q-item-label class="text-weight-medium">{{ perm.descricao || perm.nome }}</q-item-label>
                  <q-item-label caption>
                    <code class="codigo-chip">{{ perm.nome }}</code>
                  </q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-card>
        </q-expansion-item>
      </div>

      <div v-if="modulosFiltrados.length === 0" class="text-center text-grey-5 q-pa-xl">
        Nenhuma permissão encontrada
      </div>
    </template>
  </q-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { usePermissaoStore } from 'src/stores/permissao'

defineOptions({ name: 'PermissoesPage' })

const permissaoStore = usePermissaoStore()
const busca = ref('')

async function filtrar() {
  if (busca.value) {
    await permissaoStore.listar({ busca: busca.value })
  } else {
    await permissaoStore.listarAgrupada()
  }
}

const modulosFiltrados = computed(() => {
  if (busca.value && permissaoStore.lista.length > 0) {
    const agrupado = {}
    permissaoStore.lista.forEach((p) => {
      if (!agrupado[p.modulo]) agrupado[p.modulo] = { modulo: p.modulo, permissoes: [] }
      agrupado[p.modulo].permissoes.push(p)
    })
    return Object.values(agrupado)
  }
  return permissaoStore.agrupada
})

onMounted(async () => {
  await permissaoStore.listarAgrupada()
})
</script>

<style scoped>
.codigo-chip {
  background: var(--bg-subtle);
  color: var(--text-secondary);
  padding: 1px 6px;
  border-radius: 4px;
  font-size: 11px;
  font-family: monospace;
}
</style>
