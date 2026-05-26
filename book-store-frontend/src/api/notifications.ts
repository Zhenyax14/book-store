import { http, buildQuery } from './client'
import type { NotificationsResponse } from '@/types'

export const notificationsApi = {
  index: (params: { per_page?: number; page?: number } = {}) =>
    http.get<NotificationsResponse>(
      `/notifications${buildQuery(params as Record<string, unknown>)}`,
    ),

  unreadCount: () => http.get<{ count: number }>('/notifications/unread-count'),

  markRead: (id: string) => http.patch<void>(`/notifications/${id}/read`),

  markAllRead: () => http.post<void>('/notifications/read-all'),
}
