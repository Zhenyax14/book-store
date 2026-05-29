import { http, buildQuery } from './client'
import type { Subscription, PaginatedResponse, SubscriptionIndexParams } from '@/types'

export const subscriptionsApi = {
  index: (params: SubscriptionIndexParams = {}) =>
    http.get<PaginatedResponse<Subscription>>(
      `/admin/subscriptions${buildQuery(params as Record<string, unknown>)}`,
    ),

  show: (id: number) => http.get<Subscription>(`/admin/subscriptions/${id}`),
}
