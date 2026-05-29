<script setup lang="ts">
import { RouterLink } from 'vue-router'
import type { Book } from '@/types'

defineProps<{ book: Book }>()
</script>

<template>
  <div class="product-card__img">
    <div class="book-thumb book-thumb--orange">
      <span>{{ book.title }}</span>
      <small>{{ book.publisher }}</small>
    </div>
    <div class="product-card__overlay">
      <button v-if="!book.is_free" class="product-card__btn product-card__btn--cart">
        🛒 Add Cart
      </button>
      <RouterLink class="product-card__btn product-card__btn--wish" :to="{ name: 'book-detail', params: { id: book.id } }">📖</RouterLink>
    </div>
  </div>
  <div class="product-card__info">
    <h3 class="product-card__title">{{ book.title }}</h3>
    <p class="product-card__author">{{ book.publisher }}</p>
    <div class="product-card__price">
      <span>{{ book.price.formatted }}</span>
    </div>
  </div>
</template>

<style></style>
