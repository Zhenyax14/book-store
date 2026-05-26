<script setup lang="ts">
import { ref, computed, provide, onMounted } from 'vue'
import type { Theme, FontFamily, LineHeight, PaginationMode } from '@/types'
import { RouterView, useRoute } from 'vue-router'
import { useReadingStore } from '@/stores/reading'
import { useToastStore } from '@/stores'

import ReaderHeader from '@/components/reading/ReaderHeader.vue'
import ReaderSettingsPanel from '@/components/reading/ReaderSettingsPanel.vue'
import ReaderTocPanel from '@/components/reading/ReaderTocPanel.vue'
import ReaderSearchPanel from '@/components/reading/ReaderSearchPanel.vue'
import router from '@/router'

const route = useRoute()

const props = defineProps<{
  bookId: number
}>()

const pageId = computed(() => Number(route.params.pageId))
const reading = useReadingStore()
const toast = useToastStore()

const isLoading = ref(true)

const currentProgress = computed(() => reading.currentProgress)
const currentBook = computed(() => reading.currentBook)

const currentTheme = computed({
  get: () => reading.currentSettings?.theme ?? 'light',
  set: (value: Theme) => {
    reading.currentSettings!.theme = value
  },
})

const fontSize = computed({
  get: () => reading.currentSettings?.fontSize ?? 16,
  set: (value: number) => {
    reading.currentSettings!.fontSize = value
  },
})

const currentFont = computed({
  get: () => reading.currentSettings?.fontFamily ?? 'Georgia',
  set: (value: FontFamily) => {
    reading.currentSettings!.fontFamily = value
  },
})

const lineHeight = computed({
  get: () => reading.currentSettings?.lineHeight ?? '1.8',
  set: (value: LineHeight) => {
    reading.currentSettings!.lineHeight = value
  },
})

const pageWidth = computed({
  get: () => reading.currentSettings?.pageWidth ?? 70,
  set: (value: number) => {
    reading.currentSettings!.pageWidth = value
  },
})

const paginationMode = computed({
  get: () => reading.currentSettings?.paginationMode ?? 'page',
  set: (value: PaginationMode) => {
    reading.currentSettings!.paginationMode = value
  },
})

const wordsPerPage = computed({
  get: () => reading.currentSettings?.wordsPerPage ?? 300,
  set: (value: number) => {
    reading.currentSettings!.wordsPerPage = value
  },
})

const showSettings = ref<boolean>(false)
const showToc = ref<boolean>(false)
const showSearch = ref<boolean>(false)
const isBookmarked = computed(() => currentBook.value?.bookmark !== null)
const searchQuery = ref<string>('')

const safeProgress = computed(() =>
  Math.max(0, Math.min(100, currentProgress.value?.progress.percentage ?? 0)),
)
const showOverlay = computed(() => showSettings.value || showToc.value || showSearch.value)

const toggleSettings = () => {
  showSettings.value = !showSettings.value
}
const toggleToc = () => {
  showToc.value = !showToc.value
}
const toggleSearch = () => {
  showSearch.value = !showSearch.value
}

const closeAllPanels = () => {
  showSettings.value = false
  showToc.value = false
  showSearch.value = false
}

const toggleBookmark = () => {
  console.log('Bookmarking/unbookmarking book')
}

const goToChapter = (chapterId: number) => {
  const chapter = currentBook.value?.chapters.find((c) => c.id === chapterId)

  const firstPageId = chapter?.pageIds?.[0] ?? null

  router.push({
    name: 'read-page',
    params: {
      bookId: props.bookId,
      chapterId: chapterId,
      pageId: firstPageId,
    },
  })
  closeAllPanels()
}

const performSearch = () => {
  if (searchQuery.value.trim()) {
    console.log(`Searching for: "${searchQuery.value}"`)
  }
}

provide('readingSettings', {
  theme: currentTheme,
  fontSize: fontSize,
  fontFamily: currentFont,
  lineHeight: lineHeight,
  pageWidth: pageWidth,
  paginationMode: paginationMode,
})

onMounted(async () => {
  try {
    await reading.fetchSettings()
    await reading.fetchProgress(props.bookId)
    await reading.fetchBook(props.bookId)
  } catch {
    toast.error('Error', 'Failed to fetch reader details.')
  } finally {
    isLoading.value = false
  }
})

async function saveSettings() {
  if (!reading.currentSettings) return
  await reading.updateSettings()
  toggleSettings()
}
</script>

<template>
  <div class="reader-layout" :data-theme="currentTheme">
    <!-- Header -->
    <ReaderHeader
      :book-title="currentBook?.title ?? ''"
      :book-author="currentBook?.publisher ?? 'Unknown Author'"
      :progress="safeProgress"
      :is-bookmarked="isBookmarked"
      @toggle-settings="toggleSettings"
      @toggle-toc="toggleToc"
      @toggle-search="toggleSearch"
      @toggle-bookmark="toggleBookmark"
    />

    <!-- Panels -->
    <ReaderSettingsPanel
      v-if="showSettings"
      v-model:theme="currentTheme"
      v-model:font-size="fontSize"
      v-model:font-family="currentFont"
      v-model:line-height="lineHeight"
      v-model:page-width="pageWidth"
      v-model:pagination-mode="paginationMode"
      v-model:words-per-page="wordsPerPage"
      :is-loading="isLoading"
      @save="saveSettings"
      @close="toggleSettings"
    />

    <ReaderTocPanel
      v-if="showToc"
      :chapters="currentBook?.chapters"
      :current-page-id="pageId ?? null"
      :progress="safeProgress"
      :book-title="currentBook?.title ?? ''"
      :book-author="currentBook?.publisher ?? 'Unknown Author'"
      @close="toggleToc"
      @go-to-chapter="goToChapter"
    />

    <ReaderSearchPanel
      v-if="showSearch"
      v-model:query="searchQuery"
      @close="toggleSearch"
      @search="performSearch"
    />

    <RouterView :key="pageId" />

    <div class="overlay" :class="{ visible: showOverlay }" @click="closeAllPanels" />
  </div>
</template>

<style>
@import '@/assets/reader.scss';

.reader-layout {
  position: relative;
  min-height: 100vh;
  background: var(--bg);
}
</style>
