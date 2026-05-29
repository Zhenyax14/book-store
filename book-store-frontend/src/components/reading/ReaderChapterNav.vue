<script setup lang="ts">
import { computed } from 'vue'

const emit = defineEmits<{
  (e: 'next'): void
  (e: 'nextChapter'): void
  (e: 'prev'): void
  (e: 'prevChapter'): void
}>()

const props = defineProps<{
  currentPage: number
  totalPages?: number
  canGoNextChapter: boolean
  canGoPrevChapter: boolean
}>()

const totalPages = computed(() => props.totalPages ?? 10)

const isFirstPage = computed(() => props.currentPage <= 1)
const isLastPage = computed(() => props.currentPage >= totalPages.value)

const handleNext = () => {
  if (isLastPage.value) {
    emit('nextChapter')
    return
  }
  emit('next')
}

const handlePrev = () => {
  if (isFirstPage.value) {
    emit('prevChapter')
    return
  }
  emit('prev')
}
</script>

<template>
  <div class="chapter-nav">
    <!-- Previous -->
    <button
      class="chapter-nav__btn chapter-nav__btn--prev"
      @click="handlePrev"
      :disabled="isFirstPage && !canGoPrevChapter"
    >
      <svg
        width="20"
        height="20"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
      >
        <polyline points="15 18 9 12 15 6" />
      </svg>
      <div>
        <span class="chapter-nav__label">Previous {{ isFirstPage ? 'Chapter' : 'Page' }}</span>
      </div>
    </button>

    <!-- Center Info -->
    <div class="chapter-nav__center">
      <div class="chapter-nav__dots">
        <span
          v-for="n in totalPages"
          :key="n"
          class="chapter-nav__dot"
          :class="{
            'chapter-nav__dot--done': n < currentPage,
            'chapter-nav__dot--current': n === currentPage,
          }"
        />
      </div>
      <p class="chapter-nav__info">Page {{ currentPage }} of {{ totalPages }}</p>
    </div>

    <!-- Next -->
    <button
      class="chapter-nav__btn chapter-nav__btn--next"
      @click="handleNext"
      :disabled="isLastPage && !canGoNextChapter"
    >
      <div>
        <span class="chapter-nav__label">Next {{ isLastPage ? 'Chapter' : 'Page' }}</span>
      </div>
      <svg
        width="20"
        height="20"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
      >
        <polyline points="9 18 15 12 9 6" />
      </svg>
    </button>
  </div>
</template>
