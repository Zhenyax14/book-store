import { http } from './client'
import type { AuthResponse, LoginPayload, RegisterPayload } from '@/types'

export const authApi = {
  login: (payload: LoginPayload) => http.post<AuthResponse>('/auth/login', payload),

  register: (payload: RegisterPayload) => http.post<AuthResponse>('/auth/register', payload),

  logout: () => http.post<void>('/auth/logout'),

  adminLogin: (payload: LoginPayload) => http.post<AuthResponse>('/admin/auth/login', payload),

  adminLogout: () => http.post<void>('/admin/auth/logout'),
}
