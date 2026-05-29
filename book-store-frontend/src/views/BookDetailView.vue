<script setup lang="ts">
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useBooksStore } from '@/stores/books'
import { useCartStore } from '@/stores/cart'
import { useReadingStore } from '@/stores/reading'
import { useAuthStore } from '@/stores/auth'
import { useToastStore } from '@/stores/toast'
import AppSpinner from '@/components/ui/AppSpinner.vue'

const props = defineProps<{ id: number }>()

const booksStore = useBooksStore()
const cartStore = useCartStore()
const readingStore = useReadingStore()
const auth = useAuthStore()
const toast = useToastStore()
const router = useRouter()

onMounted(() => booksStore.fetchBook(props.id))

async function handleAddToCart(): Promise<void> {
  if (!auth.isAuthenticated) {
    await router.push({ name: 'login' })
    return
  }
  await cartStore.addItem('book', props.id)
  if (!cartStore.error) {
    toast.success('Add to cart', booksStore.currentBook?.title)
  } else {
    toast.error('Fail', cartStore.error)
  }
}

async function handleAddToList(): Promise<void> {
  if (!auth.isAuthenticated) {
    await router.push({ name: 'login' })
    return
  }
  const entry = await readingStore.addToList(props.id)
  if (entry) {
    toast.success('Add to reading list', booksStore.currentBook?.title)
  } else {
    toast.error('Fail', readingStore.error ?? undefined)
  }
}
</script>

<template>
  <div>
    <AppSpinner v-if="booksStore.isLoading" />

    <div v-else-if="booksStore.currentBook" class="book-detail">
      <div class="book-detail__cover">
        <img
          v-if="booksStore.currentBook.cover_url"
          :src="booksStore.currentBook.cover_url"
          :alt="booksStore.currentBook.title"
        />
        <div v-else class="book-detail__cover-placeholder" />
      </div>

      <div class="book-detail__info">
        <h1>{{ booksStore.currentBook.title }}</h1>
        <p v-if="booksStore.currentBook.description" class="book-detail__desc">
          {{ booksStore.currentBook.description }}
        </p>

        <dl class="book-detail__meta">
          <dt>Language</dt>
          <dd>{{ booksStore.currentBook.language.toUpperCase() }}</dd>
          <dt>Pages</dt>
          <dd>{{ booksStore.currentBook.pages_count }}</dd>
          <template v-if="booksStore.currentBook.publisher">
            <dt>Publisher</dt>
            <dd>{{ booksStore.currentBook.publisher }}</dd>
          </template>
        </dl>

        <div class="book-detail__price">
          <span v-if="booksStore.currentBook.is_free" class="tag tag--green">Free</span>
          <span v-else>{{ booksStore.currentBook.price.formatted }}</span>
        </div>

        <div class="book-detail__actions">
          <button
            v-if="!booksStore.currentBook.is_free"
            class="btn btn--primary"
            :disabled="cartStore.isLoading"
            @click="handleAddToCart"
          >
            Add to Cart
          </button>
          <button class="btn btn--secondary" @click="handleAddToList">Add to Reading List</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.book-detail {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 2rem;
}

.book-detail__cover img,
.book-detail__cover-placeholder {
  width: 100%;
  aspect-ratio: 2/3;
  object-fit: cover;
  border-radius: 8px;
  background: #e5e7eb;
}

.book-detail__info {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.book-detail__meta {
  display: grid;
  grid-template-columns: max-content 1fr;
  gap: 0.25rem 1rem;
  font-size: 0.9rem;
}

.book-detail__meta dt {
  color: #6b7280;
}

.book-detail__price {
  font-size: 1.5rem;
  font-weight: 700;
}

.book-detail__actions {
  display: flex;
  gap: 0.75rem;
}

.btn {
  padding: 0.6rem 1.25rem;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 500;
}

.btn--primary {
  background: #4f46e5;
  color: #fff;
}

.btn--secondary {
  background: #f3f4f6;
  color: #111827;
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.tag--green {
  background: #d1fae5;
  color: #065f46;
  padding: 0.2rem 0.6rem;
  border-radius: 9999px;
  font-size: 0.85rem;
}
</style>
