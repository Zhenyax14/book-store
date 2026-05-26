<script setup lang="ts">
import { ref } from 'vue'
import { useRouter, useRoute, RouterLink } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { HttpError } from '@/api/client'

const auth = useAuthStore()
const router = useRouter()
const route = useRoute()

const email = ref('')
const password = ref('')
const rememberMe = ref(false)
const showPassword = ref(false)
const isLoading = ref(false)
const fieldErrors = ref<Record<string, string[]>>({})

function togglePassword(): void {
  showPassword.value = !showPassword.value
}

function clearError(field: string): void {
  delete fieldErrors.value[field]
}

async function handleSubmit(): Promise<void> {
  fieldErrors.value = {}
  isLoading.value = true
  try {
    await auth.login({ email: email.value, password: password.value })
    const redirect = (route.query.redirect as string) ?? '/'
    if (redirect === '/') {
      window.location.href = '/'
    } else {
      await router.push(redirect)
    }
  } catch (e) {
    if (e instanceof HttpError && e.body.errors) {
      fieldErrors.value = e.body.errors
    } else if (e instanceof HttpError) {
      fieldErrors.value = { password: [e.body.message] }
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <form class="auth-form" novalidate @submit.prevent="handleSubmit">
    <div class="auth-form__header">
      <h1 class="auth-form__title">Bienvenido de vuelta</h1>
      <p class="auth-form__sub">Accede a tu biblioteca personal</p>
    </div>

    <!-- Email -->
    <div class="form-group">
      <label class="form-label" for="login-email">Correo electrónico</label>
      <div class="form-input-wrap">
        <svg
          class="form-input-icon"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
          <polyline points="22,6 12,13 2,6" />
        </svg>
        <input
          id="login-email"
          v-model="email"
          type="email"
          class="form-input"
          :class="{ error: fieldErrors.email, valid: !fieldErrors.email && email }"
          placeholder="tu@email.com"
          autocomplete="email"
          required
          @input="clearError('email')"
        />
      </div>
      <span class="form-error">{{ fieldErrors.email?.[0] }}</span>
    </div>

    <!-- Password -->
    <div class="form-group">
      <div class="form-label-row">
        <label class="form-label" for="login-pass">Contraseña</label>
<!--        <a href="#" class="form-forgot">¿Olvidaste tu contraseña?</a>-->
      </div>
      <div class="form-input-wrap">
        <svg
          class="form-input-icon"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <rect x="3" y="11" width="18" height="11" rx="2" />
          <path d="M7 11V7a5 5 0 0 1 10 0v4" />
        </svg>
        <input
          id="login-pass"
          v-model="password"
          :type="showPassword ? 'text' : 'password'"
          class="form-input"
          :class="{ error: fieldErrors.password }"
          placeholder="••••••••"
          autocomplete="current-password"
          required
          @input="clearError('password')"
        />
        <button
          type="button"
          class="form-eye"
          :style="showPassword ? 'color: #E8A020' : ''"
          tabindex="-1"
          @click="togglePassword"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
            <circle cx="12" cy="12" r="3" />
          </svg>
        </button>
      </div>
      <span class="form-error">{{ fieldErrors.password?.[0] }}</span>
    </div>

    <!-- Remember me -->
    <div class="form-check-row">
      <label class="form-check">
        <input v-model="rememberMe" type="checkbox" />
        <span class="form-check__box" />
        <span>Mantener sesión iniciada</span>
      </label>
    </div>

    <!-- Submit -->
    <button type="submit" class="btn-auth" :class="{ loading: isLoading }" :disabled="isLoading">
      <span class="btn-auth__text">Iniciar sesión</span>
      <span class="btn-auth__loader" aria-hidden="true" />
    </button>

    <p class="auth-switch">
      ¿No tienes cuenta?
      <RouterLink :to="{ name: 'register' }" class="auth-switch__btn">Regístrate gratis</RouterLink>
    </p>
  </form>
</template>

<style scoped>
@import '@/assets/auth-form.scss';

.auth-form {
  display: flex;
  flex-direction: column;
  animation: fadeUp 0.3s ease both;
}

@keyframes fadeUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
