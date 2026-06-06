<script setup lang="ts">
import { computed, inject, onMounted, unref } from 'vue'
import { useRoute } from 'vue-router'
import { useReadingStore } from '@/stores/reading'

import ReaderChapterHeader from '@/components/reading/ReaderChapterHeader.vue'
import ReaderChapterNav from '@/components/reading/ReaderChapterNav.vue'

import type { ReadingSettings } from '@/types'
import router from '@/router'

const settings = inject<ReadingSettings>('readingSettings')
const reading = useReadingStore()

const route = useRoute()
const bookId = computed(() => Number(route.params.bookId))
const pageId = computed(() => Number(route.params.pageId))
const currentBook = computed(() => reading.currentBook)

const props = defineProps<{
  chapterId: number
}>()

const currentChapter = computed(() => reading.currentChapter)

onMounted(async () => {
  await reading.fetchChapter(bookId.value, props.chapterId)
})

const sortedChapters = computed(() => {
  return [...(currentBook.value?.chapters ?? [])].sort((a, b) => a.number - b.number)
})

const currentChapterIndex = computed(() => {
  return sortedChapters.value.findIndex((c) => c.id === props.chapterId)
})

const currentPageIndex = computed(() => {
  const pages = currentChapter.value?.pageIds ?? []
  return pages.indexOf(pageId.value)
})

const pages = computed(() => {
  return currentChapter.value?.pageIds ?? []
})

const canGoToNextChapter = computed(() => {
  return (
    currentChapterIndex.value !== -1 && currentChapterIndex.value < sortedChapters.value.length - 1
  )
})

const canGoToPrevChapter = computed(() => {
  return currentChapterIndex.value > 0
})

const totalPages = computed(() =>
  sortedChapters.value.reduce((acc, c) => acc + (c.pageIds?.length ?? 0), 0),
)

const getChapterOffset = (chapterId: number): number => {
  let offset = 0
  for (const chapter of sortedChapters.value) {
    if (chapter.id === chapterId) break
    offset += chapter.pageIds?.length ?? 0
  }
  return offset
}

const globalPageOffset = computed(() => getChapterOffset(props.chapterId))

const saveAndNavigate = async (
  chapterId: number,
  targetPageId: number,
  chapterPageIndex: number,
  offsetOverride?: number,
) => {
  const offset = offsetOverride !== undefined ? offsetOverride : globalPageOffset.value

  await reading.saveProgress(bookId.value, {
    chapter_id: chapterId,
    page_id: targetPageId,
    global_page_number: offset + chapterPageIndex + 1,
    scroll_position: 0,
    total_pages: totalPages.value,
    book_title: currentBook.value?.title ?? '',
  })

  router.push({
    name: 'read-page',
    params: { bookId: bookId.value, chapterId, pageId: targetPageId },
  })
}

const prevPage = () => {
  const prevPageId = pages.value[currentPageIndex.value - 1]
  if (!prevPageId) return
  saveAndNavigate(props.chapterId, prevPageId, currentPageIndex.value - 1)
}

const nextPage = () => {
  const nextPageId = pages.value[currentPageIndex.value + 1]
  if (!nextPageId) return
  saveAndNavigate(props.chapterId, nextPageId, currentPageIndex.value + 1)
}

const nextChapter = () => {
  const chapter = sortedChapters.value[currentChapterIndex.value + 1]
  if (!chapter) return
  const firstPageId = chapter.pageIds?.[0]
  if (!firstPageId) return
  saveAndNavigate(chapter.id, firstPageId, 0, getChapterOffset(chapter.id))
}

const prevChapter = () => {
  const chapter = sortedChapters.value[currentChapterIndex.value - 1]
  if (!chapter) return
  const lastPageId = chapter.pageIds?.[chapter.pageIds.length - 1]
  if (!lastPageId) return
  saveAndNavigate(
    chapter.id,
    lastPageId,
    (chapter.pageIds?.length ?? 1) - 1,
    getChapterOffset(chapter.id),
  )
}
</script>

<template>
  <main class="reader" :style="{ 'max-width': `${unref(settings?.pageWidth) ?? 70}%` }">
    <ReaderChapterHeader
      :chapter-number="currentChapter?.number ?? 1"
      :chapter-title="currentChapter?.title ?? ''"
    />

    <RouterView :key="pageId" />
    <ReaderChapterNav
      :total-pages="currentChapter?.pageIds.length"
      :current-page="currentPageIndex + 1"
      :can-go-next-chapter="canGoToNextChapter"
      :can-go-prev-chapter="canGoToPrevChapter"
      @prev="prevPage"
      @next="nextPage"
      @prev-chapter="prevChapter"
      @next-chapter="nextChapter"
    />
  </main>
</template>

<style></style>
