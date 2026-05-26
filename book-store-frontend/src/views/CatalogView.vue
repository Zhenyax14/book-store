<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import { useBooksStore } from '@/stores/books'
import BookCard from '@/components/book/BookCard.vue'
import AppSpinner from '@/components/ui/AppSpinner.vue'
import AppPagination from '@/components/ui/AppPagination.vue'
import type { BookStatus, AccessType } from '@/types'

const books = useBooksStore()

const status = ref<BookStatus | ''>('')
const accessType = ref<AccessType | ''>('')
const page = ref(1)

function load(): void {
  books.fetchBooks({
    status: status.value || undefined,
    access_type: accessType.value || undefined,
    page: page.value,
    per_page: 20,
  })
}

watch([status, accessType], () => {
  page.value = 1
  load()
})

watch(page, load)
onMounted(load)
</script>

<template>
  <div class="content">
    <section class="section">
      <div class="section__header">
        <h2 class="section__title">FEATURED</h2>
        <p class="section__subtitle">
          A carefully curated selection of the best titles from our bookstore
        </p>
        <div class="section__line"></div>
      </div>

      <div class="filters-bar">
        <div class="filters-right">
          <select class="sort-select" v-model="status">
            <option value="">All statuses</option>
            <option value="published">Published</option>
            <option value="draft">Draft</option>
            <option value="archived">Archived</option>
          </select>

          <select class="sort-select" v-model="accessType">
            <option value="">All access types</option>
            <option value="free">Free</option>
            <option value="purchase">Purchase</option>
            <option value="subscription">Subscription</option>
          </select>
        </div>
      </div>

      <div class="products-wrapper">
        <TransitionGroup name="book" tag="div" class="products-grid" :css="true">
          <div v-for="book in books.books" :key="book.id" class="product-card">
            <BookCard :book="book" />
          </div>
        </TransitionGroup>

        <AppSpinner v-if="books.isLoading" class="products-overlay" />
      </div>

      <AppPagination
        v-if="books.meta"
        :current="books.meta.current_page"
        :total="books.meta.total_pages"
        @change="page = $event"
      />
    </section>
  </div>
</template>

<style>
.book-enter-active,
.book-leave-active,
.book-move {
  transition: all 420ms cubic-bezier(0.34, 1.56, 0.64, 1); /* bouncy, но очень приятно */
}

.book-enter-from,
.book-leave-to {
  opacity: 0;
  transform: translateY(40px) scale(0.92);
}

.book-leave-active {
  position: absolute;
}
</style>
