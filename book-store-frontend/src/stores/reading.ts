import { ref } from 'vue'
import { defineStore } from 'pinia'
import { readingApi } from '@/api/reading'
import type {
  ReadingEntry,
  ReadingProgress,
  BookPage,
  ReadingStatus,
  PaginationMeta,
  SaveProgressPayload,
  ReadingSettings,
  ReadingBook,
  BookChapter,
} from '@/types'

export const useReadingStore = defineStore('reading', () => {
  const list = ref<ReadingEntry[]>([])
  const listMeta = ref<PaginationMeta | null>(null)
  const currentProgress = ref<ReadingProgress | null>(null)
  const currentPage = ref<BookPage | null>(null)
  const currentChapter = ref<BookChapter | null>(null)
  const currentBook = ref<ReadingBook | null>(null)
  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const currentSettings = ref<ReadingSettings | null>(null);

  async function fetchList(params: { status?: ReadingStatus; page?: number } = {}): Promise<void> {
    isLoading.value = true
    error.value = null
    try {
      const response = await readingApi.list(params)
      list.value = response.data
      listMeta.value = response.meta
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Failed to fetch reading list'
    } finally {
      isLoading.value = false
    }
  }

  async function addToList(bookId: number): Promise<ReadingEntry | null> {
    try {
      const entry = await readingApi.addToList(bookId)
      list.value.push(entry)
      return entry
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Failed to add to list'
      return null
    }
  }

  async function removeFromList(bookId: number): Promise<void> {
    await readingApi.removeFromList(bookId)
    list.value = list.value.filter((e) => e.book_id !== bookId)
  }

  async function fetchProgress(bookId: number): Promise<void> {
    isLoading.value = true
    try {
      currentProgress.value = await readingApi.getProgress(bookId)
    } finally {
      isLoading.value = false
    }
  }

  async function saveProgress(bookId: number, payload: SaveProgressPayload): Promise<void> {
    await readingApi.saveProgress(bookId, payload)
  }

  async function fetchPage(bookId: number, pageId: number): Promise<void> {
    isLoading.value = true
    try {
      currentPage.value = await readingApi.getPage(bookId, pageId)
    } finally {
      isLoading.value = false
    }
  }

  async function fetchChapter(bookId: number, chapterId: number): Promise<void> {
    isLoading.value = true
    try {
      currentChapter.value = await readingApi.getChapter(bookId, chapterId)
    } finally {
      isLoading.value = false
    }
  }

  async function fetchBook(bookId: number): Promise<void> {
    isLoading.value = true
    try {
      currentBook.value = await readingApi.getBook(bookId)
    } finally {
      isLoading.value = false
    }
  }

  async function fetchSettings(): Promise<void> {
    isLoading.value = true
    try {
      currentSettings.value = await readingApi.settings()
    } finally {
      isLoading.value = false
    }
  }

  async function updateSettings(): Promise<void> {
    if (!currentSettings.value) return

    isLoading.value = true

    try {
      const payload = {
        theme: currentSettings.value.theme,
        fontSize: currentSettings.value.fontSize,
        fontFamily: currentSettings.value.fontFamily,
        lineHeight: currentSettings.value.lineHeight,
        pageWidth: currentSettings.value.pageWidth,
        paginationMode: currentSettings.value.paginationMode,
        wordsPerPage: currentSettings.value.wordsPerPage,
      }

      const updated = await readingApi.updateSettings(payload)

      Object.assign(currentSettings.value, updated)
    } finally {
      isLoading.value = false
    }
  }

  return {
    list,
    listMeta,
    currentProgress,
    currentPage,
    isLoading,
    error,
    fetchList,
    addToList,
    removeFromList,
    fetchProgress,
    saveProgress,
    fetchBook,
    currentBook,
    fetchChapter,
    currentChapter,
    fetchPage,
    fetchSettings,
    updateSettings,
    currentSettings,
  }
})
