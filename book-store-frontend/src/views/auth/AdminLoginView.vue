<script setup lang="ts">
import { ref } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { HttpError } from '@/api/client'

const auth = useAuthStore()
const router = useRouter()

const email = ref('')
const password = ref('')
const showPassword = ref(false)
const isLoading = ref(false)
const fieldErrors = ref<Record<string, string[]>>({})
const globalError = ref('')

function clearError(field: string): void {
  delete fieldErrors.value[field]
  globalError.value = ''
}

async function handleSubmit(): Promise<void> {
  fieldErrors.value = {}
  globalError.value = ''
  isLoading.value = true
  try {
    await auth.adminLogin({ email: email.value, password: password.value })
    await router.push({ name: 'admin-dashboard' })
  } catch (e) {
    if (e instanceof HttpError && e.body.errors) {
      fieldErrors.value = e.body.errors
    } else if (e instanceof HttpError) {
      globalError.value = e.body.message
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="admin-login-page">
    <!-- Header -->
    <header class="al-header">
      <RouterLink to="/" class="al-logo">
        <span class="al-logo__book">Book</span><span class="al-logo__shop">shop</span>
      </RouterLink>
      <span class="al-header__badge">Panel de administración</span>
    </header>

    <!-- Card -->
    <main class="al-main">
      <div class="al-card">
        <!-- Icon -->
        <div class="al-card__icon">
          <svg
            width="22"
            height="22"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <rect x="3" y="11" width="18" height="11" rx="2" />
            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
          </svg>
        </div>

        <div class="al-card__header">
          <h1 class="al-card__title">Acceso restringido</h1>
          <p class="al-card__sub">Solo para administradores</p>
        </div>

        <!-- Global error -->
        <div v-if="globalError" class="al-error-box">
          <svg
            width="16"
            height="16"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="12" />
            <line x1="12" y1="16" x2="12.01" y2="16" />
          </svg>
          {{ globalError }}
        </div>

        <form novalidate @submit.prevent="handleSubmit">
          <!-- Email -->
          <div class="al-field">
            <label class="al-label" for="admin-email">Correo electrónico</label>
            <div class="al-input-wrap">
              <svg
                class="al-input-icon"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path
                  d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"
                />
                <polyline points="22,6 12,13 2,6" />
              </svg>
              <input
                id="admin-email"
                v-model="email"
                type="email"
                class="al-input"
                :class="{ error: fieldErrors.email }"
                placeholder="admin@bookshop.com"
                autocomplete="email"
                required
                @input="clearError('email')"
              />
            </div>
            <span v-if="fieldErrors.email" class="al-field-error">{{ fieldErrors.email[0] }}</span>
          </div>

          <!-- Password -->
          <div class="al-field">
            <label class="al-label" for="admin-pass">Contraseña</label>
            <div class="al-input-wrap">
              <svg
                class="al-input-icon"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <rect x="3" y="11" width="18" height="11" rx="2" />
                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
              </svg>
              <input
                id="admin-pass"
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                class="al-input"
                :class="{ error: fieldErrors.password }"
                placeholder="••••••••"
                autocomplete="current-password"
                required
                @input="clearError('password')"
              />
              <button
                type="button"
                class="al-eye"
                :class="{ active: showPassword }"
                tabindex="-1"
                @click="showPassword = !showPassword"
              >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
              </button>
            </div>
            <span v-if="fieldErrors.password" class="al-field-error">{{
              fieldErrors.password[0]
            }}</span>
          </div>

          <!-- Submit -->
          <button
            type="submit"
            class="al-btn"
            :class="{ loading: isLoading }"
            :disabled="isLoading"
          >
            <span class="al-btn__text">Iniciar sesión</span>
            <span class="al-btn__loader" aria-hidden="true" />
          </button>
        </form>

        <!-- Back link -->
        <RouterLink :to="{ name: 'home' }" class="al-back">
          <svg
            width="14"
            height="14"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <polyline points="15 18 9 12 15 6" />
          </svg>
          Volver a la tienda
        </RouterLink>
      </div>
    </main>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=Barlow:wght@400;500;600&family=Barlow+Condensed:wght@600;700&display=swap');

.admin-login-page {
  --amber: #e8a020;
  --dark: #111827;
  --text: #1f2937;
  --muted: #6b7280;
  --light: #9ca3af;
  --border: #e5e7eb;
  --bg: #f3f4f6;
  --white: #ffffff;
  --red: #dc2626;
  --radius: 8px;

  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: var(--bg);
  font-family: 'Barlow', sans-serif;
  animation: pageIn 0.3s ease both;
}
@keyframes pageIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

/* Header */
.al-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 32px;
  background: var(--dark);
  flex-shrink: 0;
}

.al-logo {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: 1.5rem;
  line-height: 1;
  text-decoration: none;
}
.al-logo__book {
  color: var(--amber);
  font-style: italic;
  font-weight: 700;
}
.al-logo__shop {
  color: #fff;
  font-weight: 400;
}

.al-header__badge {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.4);
  border: 1px solid rgba(255, 255, 255, 0.15);
  padding: 4px 10px;
  border-radius: 20px;
}

/* Main */
.al-main {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 24px;
}

/* Card */
.al-card {
  width: 100%;
  max-width: 400px;
  background: var(--white);
  border-radius: 16px;
  padding: 40px;
  box-shadow:
    0 4px 24px rgba(0, 0, 0, 0.08),
    0 1px 3px rgba(0, 0, 0, 0.05);
  animation: cardIn 0.4s ease both;
}
@keyframes cardIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Icon */
.al-card__icon {
  width: 48px;
  height: 48px;
  background: var(--dark);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--amber);
  margin-bottom: 20px;
}

.al-card__header {
  margin-bottom: 28px;
}
.al-card__title {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: 1.7rem;
  font-weight: 700;
  color: var(--dark);
  line-height: 1.1;
  margin-bottom: 4px;
}
.al-card__sub {
  font-size: 0.875rem;
  color: var(--muted);
}

/* Error box */
.al-error-box {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: var(--radius);
  padding: 10px 14px;
  font-size: 0.85rem;
  color: var(--red);
  margin-bottom: 20px;
}

/* Fields */
.al-field {
  margin-bottom: 18px;
}

.al-label {
  display: block;
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--text);
  margin-bottom: 6px;
}

.al-input-wrap {
  position: relative;
  display: flex;
  align-items: center;
}

.al-input-icon {
  position: absolute;
  left: 12px;
  width: 16px;
  height: 16px;
  color: var(--light);
  pointer-events: none;
}

.al-input {
  width: 100%;
  background: var(--white);
  border: 1.5px solid var(--border);
  border-radius: var(--radius);
  padding: 11px 40px 11px 38px;
  font-size: 0.9rem;
  font-family: 'Barlow', sans-serif;
  color: var(--text);
  outline: none;
  transition:
    border-color 0.2s,
    box-shadow 0.2s;
}
.al-input::placeholder {
  color: var(--light);
}
.al-input:focus {
  border-color: var(--amber);
  box-shadow: 0 0 0 3px rgba(232, 160, 32, 0.12);
}
.al-input.error {
  border-color: var(--red);
  box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
}

.al-eye {
  position: absolute;
  right: 10px;
  background: none;
  border: none;
  color: var(--light);
  padding: 6px;
  display: flex;
  align-items: center;
  cursor: pointer;
  transition: color 0.2s;
}
.al-eye:hover,
.al-eye.active {
  color: var(--amber);
}
.al-eye svg {
  width: 16px;
  height: 16px;
}

.al-field-error {
  display: block;
  font-size: 0.75rem;
  color: var(--red);
  margin-top: 5px;
}

/* Button */
.al-btn {
  width: 100%;
  background: var(--dark);
  color: var(--white);
  border: none;
  padding: 14px 24px;
  border-radius: var(--radius);
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.95rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  transition: all 0.2s;
  margin-top: 8px;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.al-btn:hover {
  background: #1f2937;
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(17, 24, 39, 0.25);
}
.al-btn:active {
  transform: translateY(0);
}
.al-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.al-btn__loader {
  position: absolute;
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  opacity: 0;
  transition: opacity 0.2s;
}
.al-btn.loading .al-btn__text {
  opacity: 0;
}
.al-btn.loading .al-btn__loader {
  opacity: 1;
}
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Back link */
.al-back {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
  font-size: 0.82rem;
  color: var(--muted);
  text-decoration: none;
  transition: color 0.2s;
}
.al-back:hover {
  color: var(--amber);
}
</style>
