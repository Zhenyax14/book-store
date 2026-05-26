import { http, buildQuery } from './client'
import type {
  Book,
  BookSearchResult,
  PaginatedResponse,
  BooksIndexParams,
  BookSearchParams,
  PopularBooksParams,
  CreateBookPayload,
  UpdateBookPayload,
} from '@/types'

interface SearchResponse {
  data: BookSearchResult[]
  meta: { total: number; limit: number; offset: number; processing_time_ms: number }
}

export const booksApi = {
  index: (params: BooksIndexParams = {}) =>
    http.get<PaginatedResponse<Book>>(`/books${buildQuery(params as Record<string, unknown>)}`),

  show: (id: number) => http.get<Book>(`/books/${id}`),

  search: (params: BookSearchParams = {}) =>
    http.get<SearchResponse>(`/books/search${buildQuery(params as Record<string, unknown>)}`),

  popular: (params: PopularBooksParams = {}) =>
    http.get<PaginatedResponse<Book>>(
      `/books/popular${buildQuery(params as Record<string, unknown>)}`,
    ),

  // Admin
  create: (payload: CreateBookPayload) => http.post<Book>('/admin/books', payload),

  update: (id: number, payload: UpdateBookPayload) => http.put<Book>(`/admin/books/${id}`, payload),

  publish: (id: number) => http.patch<void>(`/admin/books/${id}/publish`),

  destroy: (id: number) => http.delete<void>(`/admin/books/${id}`),

  uploadCover: (id: number, file: File) => {
    const form = new FormData()
    form.append('cover', file)
    return http.upload<Book>(`/admin/books/${id}/cover`, form)
  },

  uploadFile: (id: number, file: File) => {
    const form = new FormData()
    form.append('book_file', file)
    return http.upload<{ book_id: number; file_path: string; status: string }>(
      `/admin/books/${id}/book-file`,
      form,
    )
  },

  syncTags: (id: number, tagIds: number[]) =>
    http.post<void>(`/admin/books/${id}/tags`, { tag_ids: tagIds }),

  attachTag: (id: number, tagId: number) => http.post<void>(`/admin/books/${id}/tags/${tagId}`),

  detachTag: (id: number, tagId: number) => http.delete<void>(`/admin/books/${id}/tags/${tagId}`),
}
