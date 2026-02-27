<template>
  <q-page padding>
    <div class="text-h6 text-grey-8 q-mb-md">Início</div>
    <q-card flat bordered>
      <q-card-section>
        <div class="text-body2 text-grey-6">
          Bem-vindo, {{ authStore.usuario?.nome }}!
        </div>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script setup>
import { useAuthStore } from 'src/stores/auth'

const authStore = useAuthStore()
</script>
