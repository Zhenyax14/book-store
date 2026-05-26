import { ref } from 'vue'
import { defineStore } from 'pinia'
import { readersApi } from  '@/api/reader'
import type { PaginationMeta, Reader, ReaderIndexParams } from '@/types'

export const useReadersStore = defineStore('readers', () => {
  const readers = ref<Reader[]>([])
  const currentReader = ref<Reader | null>(null)
  const meta = ref<PaginationMeta | null>(null)
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  async function fetchReaders(params: ReaderIndexParams = {}): Promise<void> {
    isLoading.value = true
    error.value = null
    try {
      const response = await readersApi.index(params)
      readers.value = response.data
      meta.value = response.meta
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Failed to fetch readers'
    } finally {
      isLoading.value = false
    }
  }

  async function fetchReader(id: number): Promise<void> {
    isLoading.value = true
    error.value = null
    try {
      currentReader.value = await readersApi.show(id)
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Failed to fetch reader'
    } finally {
      isLoading.value = false
    }
  }

  return {
    readers,
    currentReader,
    meta,
    isLoading,
    error,
    fetchReaders,
    fetchReader,
  }
})
