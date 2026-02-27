<template>
  <div class="login-container">
    <q-card class="login-card" flat bordered>
      <q-card-section class="text-center q-pb-sm">
        <div class="text-h5 text-weight-bold text-grey-9">OSLO</div>
        <div class="text-body2 text-grey-6">Sistema de Gestão Cartorária</div>
      </q-card-section>

      <q-card-section>
        <q-form @submit.prevent="handleLogin" class="q-gutter-sm">
          <q-input
            v-model="form.email"
            label="E-mail"
            type="email"
            outlined
            dense
            autocomplete="email"
            :error="!!erros.email"
            :error-message="erros.email"
            :disable="carregando"
          >
            <template #prepend>
              <q-icon name="mail" />
            </template>
          </q-input>

          <q-input
            v-model="form.senha"
            label="Senha"
            :type="mostrarSenha ? 'text' : 'password'"
            outlined
            dense
            autocomplete="current-password"
            :error="!!erros.senha"
            :error-message="erros.senha"
            :disable="carregando"
          >
            <template #prepend>
              <q-icon name="lock" />
            </template>
            <template #append>
              <q-icon
                :name="mostrarSenha ? 'visibility_off' : 'visibility'"
                class="cursor-pointer"
                @click="mostrarSenha = !mostrarSenha"
              />
            </template>
          </q-input>

          <div v-if="erroGeral" class="text-negative text-caption q-mt-xs">
            {{ erroGeral }}
          </div>

          <q-btn
            type="submit"
            label="Entrar"
            color="primary"
            class="full-width q-mt-sm"
            :loading="carregando"
            unelevated
          />
        </q-form>
      </q-card-section>
    </q-card>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from 'src/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({ email: '', senha: '' })
const erros = reactive({ email: '', senha: '' })
const erroGeral = ref('')
const carregando = ref(false)
const mostrarSenha = ref(false)

function limparErros() {
  erros.email = ''
  erros.senha = ''
  erroGeral.value = ''
}

function validar() {
  let valido = true

  if (!form.email) {
    erros.email = 'O e-mail é obrigatório.'
    valido = false
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    erros.email = 'Informe um e-mail válido.'
    valido = false
  }

  if (!form.senha) {
    erros.senha = 'A senha é obrigatória.'
    valido = false
  }

  return valido
}

async function handleLogin() {
  limparErros()

  if (!validar()) return

  carregando.value = true

  try {
    await authStore.login(form.email, form.senha)
    router.push({ name: 'home' })
  } catch (error) {
    const status = error.response?.status
    const dados = error.response?.data

    if (status === 422 && dados?.erros) {
      if (dados.erros.email) erros.email = dados.erros.email[0]
      if (dados.erros.senha) erros.senha = dados.erros.senha[0]
    } else if (status === 401) {
      erroGeral.value = 'E-mail ou senha incorretos.'
    } else {
      erroGeral.value = 'Erro ao realizar login. Tente novamente.'
    }
  } finally {
    carregando.value = false
  }
}
</script>

<style scoped>
.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f5f5f5;
  padding: 16px;
}

.login-card {
  width: 100%;
  max-width: 400px;
}
</style>
