<script setup lang="ts">
import { ref, watch } from 'vue'
import { RouterLink } from 'vue-router'
import { useBooksStore } from '@/stores/books'
import AppSpinner from '@/components/ui/AppSpinner.vue'
import type { BookSearchResult } from '@/types'

const books = useBooksStore()
const query = ref('')
const results = ref<BookSearchResult[]>([])
let timer: ReturnType<typeof setTimeout>

watch(query, (q) => {
  clearTimeout(timer)
  if (!q.trim()) {
    results.value = []
    return
  }
  timer = setTimeout(async () => {
    const response = await books.searchBooks({ q, limit: 20, offset: 0 })
    if (response) results.value = response.data
  }, 350)
})
</script>

<template>
  <div>
    <h1 class="page-title">Search</h1>

    <input
      v-model="query"
      class="search-input"
      type="search"
      placeholder="Search by title, author…"
      autofocus
    />

    <AppSpinner v-if="books.isLoading" />

    <ul v-else-if="results.length" class="search-results">
      <li v-for="book in results" :key="book.id" class="search-result">
        <RouterLink :to="{ name: 'book-detail', params: { id: book.id } }">
          <strong v-html="book.title" />
          <span class="search-result__meta">{{ book.status }} · {{ book.access_type }}</span>
        </RouterLink>
      </li>
    </ul>

    <p v-else-if="query && !books.isLoading" class="empty">No results found.</p>
  </div>
</template>

<style scoped>
.page-title {
  font-size: 1.75rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
}
:deep(mark) {
  background: transparent;
  color: #f97316;
  font-weight: 700;
}
.search-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 10px;
  font-size: 1rem;
  margin-bottom: 1.5rem;
  outline: none;
}

.search-input:focus {
  border-color: #4f46e5;
}

.search-results {
  list-style: none;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.search-result a {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 1rem;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  text-decoration: none;
  color: inherit;
}

.search-result__meta {
  font-size: 0.8rem;
  color: #6b7280;
}

.empty {
  color: #6b7280;
}
</style>
