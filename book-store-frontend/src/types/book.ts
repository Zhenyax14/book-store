import type { Money } from './common'

export type BookStatus = 'draft' | 'published' | 'archived'
export type AccessType = 'subscription' | 'purchase' | 'free'
export type PopularityPeriod = 'day' | 'week' | 'month'

export interface FileLink {
  mime_type: string
  url: string
  label: string
}

export interface Book {
  id: number
  title: string
  slug: string
  description: string | null
  isbn: string | null
  language: string
  publisher: string | null
  published_year: number | null
  pages_count: number
  cover_url: string | null
  file_links: FileLink[]
  access_type: AccessType
  price: Money
  status: BookStatus
  is_free: boolean
  published_at: string | null
}

export interface BookSearchResult {
  id: number
  title: string
  slug: string
  description: string | null
  access_type: string
  status: string
  ranking_score: number
}

export interface CreateBookPayload {
  title: string
  description?: string | null
  isbn?: string | null
  language: string
  publisher?: string | null
  published_year?: number | null
  access_type: AccessType
  price: number
  currency: 'USD' | 'EUR'
}

export type UpdateBookPayload = CreateBookPayload

export interface BooksIndexParams {
  status?: BookStatus
  access_type?: AccessType
  language?: string
  per_page?: number
  page?: number
  search?: string
}

export interface BookSearchParams {
  q?: string | null
  status?: BookStatus
  access_type?: AccessType
  language?: string
  limit?: number
  offset?: number
}

export interface PopularBooksParams {
  period?: PopularityPeriod
  per_page?: number
  page?: number
}
