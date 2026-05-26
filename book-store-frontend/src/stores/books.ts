import { ref } from 'vue'
import { defineStore } from 'pinia'
import { booksApi } from '@/api/books'
import type { Book, PaginationMeta, BooksIndexParams, BookSearchParams } from '@/types'

export const useBooksStore = defineStore('books', () => {
  const books = ref<Book[]>([])
  const currentBook = ref<Book | null>(null)
  const meta = ref<PaginationMeta | null>(null)
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  async function fetchBooks(params: BooksIndexParams = {}): Promise<void> {
    isLoading.value = true
    error.value = null
    try {
      const response = await booksApi.index(params)
      books.value = response.data
      meta.value = response.meta
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Failed to fetch books'
    } finally {
      isLoading.value = false
    }
  }

  async function fetchBook(id: number): Promise<void> {
    isLoading.value = true
    error.value = null
    try {
      currentBook.value = await booksApi.show(id)
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Failed to fetch book'
    } finally {
      isLoading.value = false
    }
  }

  async function searchBooks(params: BookSearchParams) {
    isLoading.value = true
    error.value = null
    try {
      return await booksApi.search(params)
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Search failed'
      return null
    } finally {
      isLoading.value = false
    }
  }

  async function fetchPopular(period: 'day' | 'week' | 'month' = 'week') {
    isLoading.value = true
    error.value = null
    try {
      const response = await booksApi.popular({ period })
      books.value = response.data
      meta.value = response.meta
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Failed to fetch popular books'
    } finally {
      isLoading.value = false
    }
  }

  return {
    books,
    currentBook,
    meta,
    isLoading,
    error,
    fetchBooks,
    fetchBook,
    searchBooks,
    fetchPopular,
  }
})
