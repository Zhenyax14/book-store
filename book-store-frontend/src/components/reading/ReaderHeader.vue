<script setup lang="ts">
import ReaderProgress from './ReaderProgress.vue'

defineProps<{
  bookTitle: string
  bookAuthor: string
  progress: number
  isBookmarked?: boolean
}>()

defineEmits<{
  'toggle-settings': []
  'toggle-toc': []
  'toggle-search': []
  'toggle-bookmark': []
  seek: [percent: number]
}>()
</script>

<template>
  <header class="toolbar">
    <div class="toolbar__left">
      <router-link :to="{ name: 'reading-list' }" class="toolbar__back" title="Back to Library">
        <svg
          width="18"
          height="18"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <polyline points="15 18 9 12 15 6" />
        </svg>
        <span>Library</span>
      </router-link>
      <div class="toolbar__divider"></div>
      <div class="toolbar__book-info">
        <span class="toolbar__title">{{ bookTitle }}</span>
        <span class="toolbar__author">{{ bookAuthor }}</span>
      </div>
    </div>

    <div class="toolbar__center">
      <ReaderProgress :progress="progress" />
    </div>

    <div class="toolbar__right">
      <button
        class="toolbar__btn"
        :class="{ active: isBookmarked }"
        @click="$emit('toggle-bookmark')"
        title="Add Bookmark"
      >
        <svg
          width="16"
          height="16"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z" />
        </svg>
      </button>
      <button class="toolbar__btn" @click="$emit('toggle-toc')" title="Table of Contents">
        <svg
          width="16"
          height="16"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <line x1="8" y1="6" x2="21" y2="6" />
          <line x1="8" y1="12" x2="21" y2="12" />
          <line x1="8" y1="18" x2="21" y2="18" />
        </svg>
      </button>
      <button class="toolbar__btn" @click="$emit('toggle-search')" title="Search in Book">
        <svg
          width="16"
          height="16"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <circle cx="11" cy="11" r="8" />
          <line x1="21" y1="21" x2="16.65" y2="16.65" />
        </svg>
      </button>
      <div class="toolbar__divider"></div>
      <button class="toolbar__btn" @click="$emit('toggle-settings')" title="Reading Settings">
        <svg
          width="16"
          height="16"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <circle cx="12" cy="12" r="3" />
          <path d="M19.07 4.93A10 10 0 0 0 4.93 19.07M19.07 4.93L12 12" />
        </svg>
      </button>
    </div>
  </header>
</template>

<style></style>
