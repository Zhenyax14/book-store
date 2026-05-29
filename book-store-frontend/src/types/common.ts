export interface PaginationMeta {
  total: number
  per_page: number
  current_page: number
  total_pages: number
}

export interface PaginatedResponse<T> {
  data: T[]
  meta: PaginationMeta
}

export interface Money {
  currency: string
  amount: number
  formatted: string
}

export interface ApiError {
  message: string
  errors?: Record<string, string[]>
}
