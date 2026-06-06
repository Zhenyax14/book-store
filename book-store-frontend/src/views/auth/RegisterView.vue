<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { HttpError } from '@/api/client'

const auth = useAuthStore()
const router = useRouter()

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirm = ref('')
const acceptTerms = ref(false)
const showPassword = ref(false)
const isLoading = ref(false)
const showSuccess = ref(false)
const fieldErrors = ref<Record<string, string[]>>({})

function clearError(field: string): void {
  delete fieldErrors.value[field]
}

const passwordScore = computed((): number => {
  const pwd = password.value
  if (!pwd) return 0
  let score = 0
  if (pwd.length >= 8) score++
  if (pwd.length >= 12) score++
  if (/[A-Z]/.test(pwd) && /[a-z]/.test(pwd)) score++
  if (/[0-9]/.test(pwd)) score++
  if (/[^A-Za-z0-9]/.test(pwd)) score++
  return Math.min(score, 4)
})

const strengthLabel = computed((): string => {
  return ['', 'Débil', 'Regular', 'Buena', 'Fuerte'][passwordScore.value] ?? ''
})

const strengthClass = computed((): string => {
  return ['', 'weak', 'fair', 'good', 'strong'][passwordScore.value] ?? ''
})

const strengthColor = computed((): string => {
  return ['', '#D94040', '#F59E0B', '#3B82F6', '#3ECF8E'][passwordScore.value] ?? ''
})

function barClass(index: number): string {
  return passwordScore.value >= index ? strengthClass.value : ''
}

async function handleSubmit(): Promise<void> {
  fieldErrors.value = {}

  if (!acceptTerms.value) {
    fieldErrors.value = { terms: ['Debes aceptar los términos de uso'] }
    return
  }
  if (password.value !== passwordConfirm.value) {
    fieldErrors.value = { password_confirmation: ['Las contraseñas no coinciden'] }
    return
  }

  isLoading.value = true
  try {
    await auth.register({
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirm.value,
    })
    showSuccess.value = true
    setTimeout(() => router.push({ name: 'home' }), 2400)
  } catch (e) {
    if (e instanceof HttpError) {
      if (e.body.errors) {
        fieldErrors.value = e.body.errors
      } else if (e.body.message) {
        fieldErrors.value = { email: [e.body.message] }
      }
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <!-- Success state -->
  <div v-if="showSuccess" class="auth-success show">
    <div class="auth-success__icon">
      <svg
        width="32"
        height="32"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2.5"
      >
        <polyline points="20 6 9 17 4 12" />
      </svg>
    </div>
    <h2 class="auth-success__title">¡Bienvenido a BookShop!</h2>
    <p class="auth-success__sub">Tu cuenta ha sido creada. Redirigiendo...</p>
    <div class="auth-success__progress" />
  </div>

  <!-- Register form -->
  <form v-else class="auth-form" novalidate @submit.prevent="handleSubmit">
    <div class="auth-form__header">
      <h1 class="auth-form__title">Crear cuenta</h1>
      <p class="auth-form__sub">Empieza a leer hoy mismo</p>
    </div>

    <!-- Name row -->
    <div class="form-group">
      <label class="form-label" for="reg-name">Nombre</label>
      <div class="form-input-wrap">
        <svg
          class="form-input-icon"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
          <circle cx="12" cy="7" r="4" />
        </svg>
        <input
          id="reg-name"
          v-model="name"
          type="text"
          class="form-input"
          :class="{ error: fieldErrors.name, valid: !fieldErrors.name && name }"
          placeholder="Tu nombre"
          autocomplete="given-name"
          required
          @input="clearError('name')"
        />
      </div>
      <span class="form-error">{{ fieldErrors.name?.[0] }}</span>
    </div>

    <!-- Email -->
    <div class="form-group">
      <label class="form-label" for="reg-email">Correo electrónico</label>
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
          id="reg-email"
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
      <label class="form-label" for="reg-pass">Contraseña</label>
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
          id="reg-pass"
          v-model="password"
          :type="showPassword ? 'text' : 'password'"
          class="form-input"
          :class="{ error: fieldErrors.password }"
          placeholder="Mínimo 8 caracteres"
          autocomplete="new-password"
          required
          @input="clearError('password')"
        />
        <button
          type="button"
          class="form-eye"
          :style="showPassword ? 'color: #E8A020' : ''"
          tabindex="-1"
          @click="showPassword = !showPassword"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
            <circle cx="12" cy="12" r="3" />
          </svg>
        </button>
      </div>
      <span class="form-error">{{ fieldErrors.password?.[0] }}</span>

      <!-- Password strength -->
      <div v-if="password" class="pass-strength">
        <div class="pass-strength__bars">
          <div v-for="i in 4" :key="i" class="pass-strength__bar" :class="barClass(i)" />
        </div>
        <span class="pass-strength__label" :style="{ color: strengthColor }">
          {{ strengthLabel }}
        </span>
      </div>
    </div>

    <!-- Confirm password -->
    <div class="form-group">
      <label class="form-label" for="reg-pass2">Confirmar contraseña</label>
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
          id="reg-pass2"
          v-model="passwordConfirm"
          type="password"
          class="form-input"
          :class="{ error: fieldErrors.password_confirmation }"
          placeholder="Repite tu contraseña"
          autocomplete="new-password"
          @input="clearError('password_confirmation')"
        />
      </div>
      <span class="form-error">{{ fieldErrors.password_confirmation?.[0] }}</span>
    </div>

    <!-- Terms -->
    <label class="form-check terms-check">
      <input v-model="acceptTerms" type="checkbox" />
      <span class="form-check__box" />
      <span>
        Acepto los <a href="#" class="form-link">Términos de uso</a> y la
        <a href="#" class="form-link">Política de privacidad</a>
      </span>
    </label>
    <span v-if="fieldErrors.terms" class="form-error terms-error">
      {{ fieldErrors.terms[0] }}
    </span>

    <!-- Submit -->
    <button type="submit" class="btn-auth" :class="{ loading: isLoading }" :disabled="isLoading">
      <span class="btn-auth__text">Crear cuenta gratis</span>
      <span class="btn-auth__loader" aria-hidden="true" />
    </button>

    <p class="auth-switch">
      ¿Ya tienes cuenta?
      <RouterLink :to="{ name: 'login' }" class="auth-switch__btn">Inicia sesión</RouterLink>
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

.terms-check {
  margin-bottom: 8px;
}
.terms-error {
  margin-bottom: 16px;
}
</style>
