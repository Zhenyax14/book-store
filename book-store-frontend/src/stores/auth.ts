import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import { authApi } from '@/api/auth'
import type { User, LoginPayload, RegisterPayload } from '@/types'

const TOKEN_KEY = 'auth_token'
const USER_KEY = 'auth_user'

function loadUser(): User | null {
  try {
    const raw = localStorage.getItem(USER_KEY)
    return raw ? (JSON.parse(raw) as User) : null
  } catch {
    return null
  }
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(loadUser())
  const token = ref<string | null>(localStorage.getItem(TOKEN_KEY))

  const isAuthenticated = computed(() => token.value !== null)
  const isAdmin = computed(() => user.value?.role === 'admin')

  function setAuth(newToken: string, newUser: User): void {
    token.value = newToken
    user.value = newUser
    localStorage.setItem(TOKEN_KEY, newToken)
    localStorage.setItem(USER_KEY, JSON.stringify(newUser))
  }

  function clearAuth(): void {
    token.value = null
    user.value = null
    localStorage.removeItem(TOKEN_KEY)
    localStorage.removeItem(USER_KEY)
  }

  async function login(payload: LoginPayload): Promise<void> {
    const response = await authApi.login(payload)
    setAuth(response.token, response.user)
  }

  async function register(payload: RegisterPayload): Promise<void> {
    const response = await authApi.register(payload)
    setAuth(response.token, response.user)
  }

  async function logout(): Promise<void> {
    await authApi.logout().catch(() => {})
    clearAuth()
  }

  async function adminLogout(): Promise<void> {
    await authApi.adminLogout().catch(() => {})
    clearAuth()
  }

  async function adminLogin(payload: LoginPayload): Promise<void> {
    const response = await authApi.adminLogin(payload)
    setAuth(response.token, response.user)
  }

  return {
    user,
    token,
    isAuthenticated,
    isAdmin,
    login,
    register,
    logout,
    adminLogin,
    adminLogout,
  }
})
