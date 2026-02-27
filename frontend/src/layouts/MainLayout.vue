<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated class="bg-primary text-white">
      <q-toolbar>
        <q-btn flat round dense icon="menu" @click="drawerOpen = !drawerOpen" />
        <q-toolbar-title>OSLO</q-toolbar-title>

        <q-btn
          flat
          round
          dense
          icon="logout"
          @click="handleLogout"
          :loading="saindo"
        >
          <q-tooltip>Sair</q-tooltip>
        </q-btn>
      </q-toolbar>
    </q-header>

    <q-drawer v-model="drawerOpen" show-if-above bordered>
      <q-list padding>
        <q-item clickable v-ripple :to="{ name: 'home' }" exact>
          <q-item-section avatar>
            <q-icon name="home" />
          </q-item-section>
          <q-item-section>Início</q-item-section>
        </q-item>
      </q-list>

      <q-separator />

      <q-list padding>
        <q-item dense class="text-caption text-grey-6">
          <q-item-section>
            {{ authStore.usuario?.nome ?? '' }}
          </q-item-section>
        </q-item>
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from 'src/stores/auth'

const router = useRouter()
const authStore = useAuthStore()
const drawerOpen = ref(true)
const saindo = ref(false)

async function handleLogout() {
  saindo.value = true
  try {
    await authStore.logout()
    router.push({ name: 'login' })
  } finally {
    saindo.value = false
  }
}
</script>
