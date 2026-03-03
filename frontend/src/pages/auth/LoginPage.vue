<template>
  <div class="login-page">
    <div class="row no-wrap full-height">
      <!-- Coluna esquerda (60%) — Branding -->
      <div class="col-7 login-left gt-sm">
        <div class="login-left__content">
          <div class="login-left__brand">
            <l-icon name="landmark" :size="40" class="login-left__icon" />
            <div class="login-left__title">OSLO</div>
            <div class="login-left__tagline">Sistema de Gestão Cartorária</div>
          </div>
          <div class="login-left__footer">
            &copy; {{ new Date().getFullYear() }} OSLO
          </div>
        </div>
      </div>

      <!-- Coluna direita (40%) — Formulário -->
      <div class="col login-right">
        <div class="login-form-container">
          <!-- Mobile logo -->
          <div class="login-mobile-brand lt-md">
            <l-icon name="landmark" :size="28" />
            <span class="login-mobile-brand__text">OSLO</span>
          </div>

          <h2 class="login-title">Bem-vindo de volta</h2>
          <p class="login-subtitle">Acesse sua conta</p>

          <q-form class="login-form" @submit.prevent="handleLogin">
            <div class="form-group">
              <label class="form-label">E-mail</label>
              <q-input
                v-model="form.email"
                type="email"
                outlined
                dense
                placeholder="seu@email.com"
                autofocus
                hide-bottom-space
                :error="!!erros.email"
                :error-message="erros.email"
                :disable="carregando"
              />
            </div>

            <div class="form-group">
              <div class="form-label-row">
                <label class="form-label">Senha</label>
                <a class="form-link" @click="onEsqueciSenha">Esqueci minha senha</a>
              </div>
              <q-input
                v-model="form.senha"
                :type="mostrarSenha ? 'text' : 'password'"
                outlined
                dense
                placeholder="Digite sua senha"
                hide-bottom-space
                :error="!!erros.senha"
                :error-message="erros.senha"
                :disable="carregando"
                @keyup.enter="handleLogin"
              >
                <template v-slot:append>
                  <q-icon
                    :name="mostrarSenha ? 'visibility_off' : 'visibility'"
                    class="cursor-pointer form-eye-icon"
                    @click="mostrarSenha = !mostrarSenha"
                    size="20px"
                  />
                </template>
              </q-input>
            </div>

            <div v-if="erroGeral" class="login-erro">
              {{ erroGeral }}
            </div>

            <q-btn
              type="submit"
              class="login-btn"
              unelevated
              no-caps
              :loading="carregando"
              :disable="!form.email || !form.senha"
            >
              <span>Entrar</span>
              <template v-slot:loading>
                <q-spinner class="on-left" size="18px" />
                Entrando...
              </template>
            </q-btn>

            <div class="text-center q-mt-lg">
              <span class="text-grey-7">Não tem conta?</span>
              <router-link to="/cadastro" class="form-link-create q-ml-xs">
                Criar conta grátis
              </router-link>
            </div>
          </q-form>

          <div class="login-footer lt-md">
            &copy; {{ new Date().getFullYear() }} OSLO
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useQuasar } from 'quasar'
import { useRouter } from 'vue-router'
import { useAuthStore } from 'src/stores/auth'

defineOptions({ name: 'LoginPage' })

const $q = useQuasar()
const router = useRouter()
const authStore = useAuthStore()

const carregando = ref(false)
const mostrarSenha = ref(false)

const form = reactive({ email: '', senha: '' })
const erros = reactive({ email: '', senha: '' })
const erroGeral = ref('')

function limparErros() {
  erros.email = ''
  erros.senha = ''
  erroGeral.value = ''
}

async function handleLogin() {
  limparErros()

  if (!form.email || !form.senha) {
    $q.notify({
      color: 'negative',
      message: 'Preencha todos os campos',
      position: 'top',
    })
    return
  }

  carregando.value = true

  try {
    await authStore.login(form.email, form.senha)

    $q.notify({
      color: 'positive',
      message: 'Login realizado com sucesso!',
      position: 'top',
    })

    router.push({ name: 'dashboard' })
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

function onEsqueciSenha() {
  $q.notify({
    color: 'info',
    message: 'Entre em contato com o administrador',
    position: 'top',
  })
}
</script>

<style lang="scss" scoped>
.login-page {
  height: 100vh;
  width: 100vw;
  overflow: hidden;
}

.full-height {
  height: 100vh;
}

// --- Left Side ---
.login-left {
  height: 100vh;
  background: #1A1A1A;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.login-left__content {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  padding: 3rem;
}

.login-left__brand {
  text-align: center;
}

.login-left__icon {
  color: #ffffff;
  margin-bottom: 1rem;
}

.login-left__title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #FFFFFF;
  letter-spacing: 0.15em;
  margin-bottom: 0.5rem;
}

.login-left__tagline {
  font-size: 1rem;
  color: #9AA0A6;
  font-weight: 400;
}

.login-left__footer {
  position: absolute;
  bottom: 2rem;
  font-size: 0.75rem;
  color: #5F6368;
}

// --- Right Side ---
.login-right {
  height: 100vh;
  background: #FFFFFF;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  overflow-y: auto;
}

.login-form-container {
  width: 100%;
  max-width: 380px;
}

.login-mobile-brand {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 2rem;
  justify-content: center;

  &__text {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary);
    letter-spacing: 0.1em;
  }
}

.login-title {
  font-size: var(--font-size-2xl);
  font-weight: 700;
  color: var(--text-color);
  margin: 0 0 4px 0;
}

.login-subtitle {
  font-size: var(--font-size-sm);
  color: var(--text-muted);
  margin: 0 0 2rem 0;
}

// --- Form ---
.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.form-label {
  font-size: var(--font-size-sm);
  font-weight: 500;
  color: var(--text-secondary);
}

.form-label-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.form-link {
  font-size: var(--font-size-xs);
  color: var(--text-muted);
  cursor: pointer;
  transition: color var(--transition);
  text-decoration: none;

  &:hover {
    color: var(--primary);
  }
}

.form-link-create {
  color: var(--primary);
  font-weight: 500;
  text-decoration: none;
  transition: color var(--transition);

  &:hover {
    color: var(--primary-light);
  }
}

.form-eye-icon {
  color: var(--text-muted);
  transition: color var(--transition);

  &:hover {
    color: var(--text-secondary);
  }
}

.login-erro {
  color: var(--danger);
  font-size: var(--font-size-sm);
  padding: 8px 12px;
  background: var(--danger-bg);
  border-radius: var(--radius-sm);
  border: 1px solid rgba(220, 38, 38, 0.2);
}

:deep(.q-field--outlined .q-field__control) {
  height: 44px;
}

:deep(.q-field--outlined.q-field--focused .q-field__control) {
  &:before {
    border-color: var(--primary) !important;
  }

  &:after {
    border-width: 1px !important;
    box-shadow: 0 0 0 3px rgba(9, 9, 11, 0.10);
  }
}

// --- Login Button ---
.login-btn {
  background: var(--primary) !important;
  color: white;
  font-weight: 600;
  height: 44px;
  font-size: var(--font-size-base);
  width: 100%;
  margin-top: 0.5rem;
  transition: background var(--transition);

  &:hover {
    background: var(--primary-dark) !important;
  }

  &:disabled {
    background: var(--border-color) !important;
    color: var(--text-muted);
  }
}

.login-footer {
  text-align: center;
  font-size: var(--font-size-xs);
  color: var(--text-muted);
  margin-top: 2rem;
}

// --- Responsive ---
@media (max-width: 1024px) {
  .login-left {
    display: none;
  }
}

@media (max-width: 768px) {
  .login-right {
    padding: 1.5rem;
  }

  .login-form-container {
    max-width: 100%;
  }

  .login-title {
    font-size: var(--font-size-xl);
  }
}

@media (max-width: 480px) {
  .login-right {
    padding: 1rem;
  }
}
</style>
