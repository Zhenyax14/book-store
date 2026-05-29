<script setup lang="ts">
import { onMounted } from 'vue'
import { useBooksStore } from '@/stores/books'
import BookCard from '@/components/book/BookCard.vue'
import AppSpinner from '@/components/ui/AppSpinner.vue'

const books = useBooksStore()
onMounted(() => books.fetchPopular('month'))
</script>

<template>
  <div class="content">
    <section class="section">
      <div class="section__header">
        <h2 class="section__title">POPULAR THIS MONTH</h2>
        <p class="section__subtitle">On this page you can see popular books from our bookstore</p>
        <div class="section__line"></div>
      </div>

      <div class="products-wrapper">
        <TransitionGroup name="book" tag="div" class="products-grid" :css="true">
          <div v-for="book in books.books" :key="book.id" class="product-card">
            <BookCard :book="book" />
          </div>
        </TransitionGroup>

        <AppSpinner v-if="books.isLoading" />
      </div>
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
