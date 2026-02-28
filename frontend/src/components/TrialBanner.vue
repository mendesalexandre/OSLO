<template>
  <q-banner
    v-if="mostrarBanner"
    :class="[
      'trial-banner',
      diasRestantes === 0 ? 'bg-red-1' : diasRestantes <= 3 ? 'bg-orange-1' : 'bg-amber-1'
    ]"
    dense
  >
    <template v-slot:avatar>
      <q-icon
        :name="diasRestantes === 0 ? 'warning' : 'schedule'"
        :color="diasRestantes === 0 ? 'negative' : diasRestantes <= 3 ? 'orange' : 'amber-8'"
      />
    </template>

    <div class="row items-center no-wrap">
      <div class="col">
        <span class="text-weight-medium">
          {{ mensagemTrial }}
        </span>
      </div>

      <div class="col-auto">
        <q-btn
          label="Assinar Agora"
          color="orange"
          unelevated
          no-caps
          size="sm"
          dense
          @click="$router.push('/planos')"
          class="q-ml-md"
        />
      </div>
    </div>
  </q-banner>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from 'stores/auth'

const authStore = useAuthStore()

const empresa = computed(() => authStore.empresa)
const diasRestantes = computed(() => empresa.value?.dias_restantes_trial || 0)
const isTrialAtivo = computed(() => empresa.value?.is_trial_ativo || false)
const isReadOnly = computed(() => empresa.value?.is_read_only || false)

const mostrarBanner = computed(() => {
  if (!empresa.value) return false
  return isTrialAtivo.value || isReadOnly.value
})

const mensagemTrial = computed(() => {
  if (isReadOnly.value) {
    return '⚠️ Seu período de teste expirou. Assine um plano para continuar usando o sistema.'
  }

  if (diasRestantes.value === 0) {
    return '⚠️ Seu teste expira HOJE! Assine agora para não perder acesso.'
  }

  if (diasRestantes.value === 1) {
    return '⏱ Você tem 1 dia restante no período de teste.'
  }

  return `⏱ Você tem ${diasRestantes.value} dias restantes no período de teste.`
})
</script>

<style lang="scss" scoped>
.trial-banner {
  border-bottom: 1px solid rgba(0, 0, 0, 0.12);
}
</style>
