<script setup lang="ts">
import type { BookChapter } from '@/types'
import { computed } from 'vue'

const props = defineProps<{
  chapters?: BookChapter[]
  currentPageId?: number
  progress: number
  bookTitle?: string
  bookAuthor?: string
}>()

const currentChapterNumber = computed(() => {
  const current = (props.chapters ?? []).find((chapter) =>
    chapter.pageIds.includes(props.currentPageId ?? 0),
  )
  return current?.number ?? 1
})

const isChapterRead = (chapterNumber: number): boolean => {
  return chapterNumber < currentChapterNumber.value
}

defineEmits<{
  close: []
  'go-to-chapter': [id: number]
}>()
</script>

<template>
  <div class="toc-panel" :class="{ open: true }">
    <div class="toc-panel__header">
      <h3>Table of Contents</h3>
      <button class="toc-panel__close" @click="$emit('close')">×</button>
    </div>
    <div class="toc-panel__book">
      <div class="toc-mini-cover">🐉</div>
      <div>
        <strong>{{ bookTitle }}</strong>
        <span>{{ bookAuthor }}</span>
      </div>
    </div>
    <nav class="toc-nav">
      <a
        v-for="chapter in chapters ?? []"
        :key="chapter.id"
        href="#"
        class="toc-nav__item"
        :class="{
          'toc-nav__item--active': currentChapterNumber === chapter.number,
          'toc-nav__item--read': isChapterRead(chapter.number),
        }"
        @click.prevent="$emit('go-to-chapter', chapter.id)"
      >
        <span class="toc-nav__num">{{ chapter.number }}</span>
        <span class="toc-nav__name">{{ chapter.title }}</span>
        <span v-if="isChapterRead(chapter.number)" class="toc-nav__check">✓</span>
        <span v-else-if="currentChapterNumber === chapter.number" class="toc-nav__dot"></span>
      </a>
    </nav>
    <div class="toc-panel__footer">
      <div class="toc-progress">
        <div class="toc-progress__bar">
          <div class="toc-progress__fill" :style="{ width: `${progress}%` }"></div>
        </div>
        <span>
          {{ Math.round(progress) }}% completed · Chapter {{ currentChapterNumber }} of
          {{ chapters?.length ?? 0 }}
        </span>
      </div>
    </div>
  </div>
</template>
