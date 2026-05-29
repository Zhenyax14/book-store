import { buildQuery, http } from './client'
import type { OrderCollectionResponse, OrderResponse, OrderListParams } from '@/types/orders'

export const ordersApi = {
  index: (params: OrderListParams = {}) =>
    http.get<OrderCollectionResponse>(
      `/admin/orders${buildQuery(params as Record<string, unknown>)}`,
    ),

  show: (id: number) => http.get<OrderResponse>(`/admin/orders/${id}`),
}
