import { buildQuery, http } from './client'
import type { Reader, ReaderIndexParams, PaginatedResponse } from '@/types'

export const readersApi = {
  index: (params: ReaderIndexParams = {}) =>
    http.get<PaginatedResponse<Reader>>(
      `/admin/readers${buildQuery(params as Record<string, unknown>)}`,
    ),

  show: (id: number) => http.get<Reader>(`/admin/readers/${id}`),
}
