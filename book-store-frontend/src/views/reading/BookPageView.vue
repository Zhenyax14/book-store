<script setup lang="ts">
import { computed, inject, onMounted, unref } from 'vue'
import type { FontFamily, ReadingSettings } from '@/types'
import { useReadingStore } from '@/stores/reading'
import { useRoute } from 'vue-router'

const settings = inject<ReadingSettings>('readingSettings')
const reading = useReadingStore()
const route = useRoute()
const bookId = computed(() => Number(route.params.bookId))

const props = defineProps<{
  pageId: number
}>()

const currentPageHtml = computed(() => reading.currentPage?.page.content ?? 'Loading...')

const FONT_MAP = {
  Lora: "'Lora', serif",
  'Playfair Display': "'Playfair Display', serif",
  Georgia: 'Georgia, serif',
} as const

const getFontFamily = (fontKey: FontFamily | undefined): string => {
  return FONT_MAP[fontKey ?? 'Lora']
}

const contentStyle = computed(() => ({
  fontSize: unref(settings?.fontSize) ? `${unref(settings?.fontSize) ?? 18}px` : '18px',
  lineHeight: unref(settings?.lineHeight) ?? 1.8,
  fontFamily: getFontFamily(unref(settings?.fontFamily)),
}))

onMounted(async () => {
  await reading.fetchPage(bookId.value, props.pageId)
})
</script>

<template>
  <article class="chapter-content" :style="contentStyle" ref="contentRef">
    <div v-if="currentPageHtml" v-html="currentPageHtml"></div>
  </article>
</template>

<style></style>
