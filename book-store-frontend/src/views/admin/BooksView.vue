<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { RouterLink } from 'vue-router'
import { useBooksStore } from '@/stores/books'
import { booksApi } from '@/api/books'
import { useToastStore } from '@/stores/toast'
import AppSpinner from '@/components/ui/AppSpinner.vue'
import AppPagination from '@/components/ui/AppPagination.vue'
import type { BookStatus, AccessType } from '@/types'

const books = useBooksStore()
const toast = useToastStore()

type FilterKey = 'all' | BookStatus | AccessType

const activeFilter = ref<FilterKey>('all')
const searchQuery = ref('')
const currentPage = ref(1)
const PER_PAGE = 10

function load(): void {
  books.fetchBooks({
    status:
      activeFilter.value !== 'all' &&
      ['published', 'draft', 'archived'].includes(activeFilter.value)
        ? (activeFilter.value as BookStatus)
        : undefined,
    access_type:
      activeFilter.value !== 'all' &&
      ['free', 'subscription', 'purchase'].includes(activeFilter.value)
        ? (activeFilter.value as AccessType)
        : undefined,
    search: searchQuery.value.trim() || undefined,
    page: currentPage.value,
    per_page: PER_PAGE,
  })
}

watch([activeFilter, searchQuery], () => {
  currentPage.value = 1
  load()
})

watch(currentPage, load)

onMounted(load)

const subtitle = computed(() => {
  if (books.isLoading) return 'Loading...'
  return `Complete catalogue · ${books.meta?.total ?? books.books.length} books`
})

const FILTERS: { key: FilterKey; label: string }[] = [
  { key: 'all', label: 'All' },
  { key: 'published', label: 'Published' },
  { key: 'draft', label: 'Drafts' },
  { key: 'subscription', label: 'Subscription' },
  { key: 'purchase', label: 'Purchase' },
]

const STATUS_BADGE: Record<BookStatus, string> = {
  published: 'badge-green',
  draft: 'badge-amber',
  archived: 'badge-red',
}

const ACCESS_BADGE: Record<AccessType, string> = {
  free: 'badge-teal',
  subscription: 'badge-purple',
  purchase: 'badge-blue',
}

const BOOK_COLORS = [
  'linear-gradient(160deg,#8B1A1A,#4A0E0E)',
  'linear-gradient(160deg,#1a4a8a,#0d2d5e)',
  'linear-gradient(160deg,#c06010,#8a3a00)',
  'linear-gradient(160deg,#1a6a3a,#0d3d22)',
  'linear-gradient(160deg,#4a1a8a,#2d0d5e)',
  'linear-gradient(160deg,#2a2a3a,#14141e)',
  'linear-gradient(160deg,#1a7a7a,#0d5050)',
  'linear-gradient(160deg,#6a1a4a,#3d0d2a)',
]

const BOOK_ICONS = ['🐉', '📖', '🏜️', '🌬️', '⚔️', '🦋', '💍', '🌫️', '🔭', '🌹']

function coverColor(id: number): string {
  return BOOK_COLORS[id % BOOK_COLORS.length]!
}

function coverIcon(id: number): string {
  return BOOK_ICONS[id % BOOK_ICONS.length]!
}

async function handleDelete(id: number): Promise<void> {
  if (!confirm('Delete this book?')) return
  try {
    await booksApi.destroy(id)
    load()
    toast.success('Eliminated', 'The book was successfully deleted')
  } catch {
    toast.error('Error', 'Could not delete book')
  }
}

async function handlePublish(id: number): Promise<void> {
  if (!confirm('Publish this book?')) return
  try {
    await booksApi.publish(id)
    load()
    toast.success('Published', 'The book is now available')
  } catch {
    toast.error('Error', 'The book could not be published')
  }
}
</script>

<template>
  <div class="page">
    <div class="page-header">
      <div>
        <h1 class="page-header__title">Book Management</h1>
        <p class="page-header__sub">{{ subtitle }}</p>
      </div>
      <div class="page-header__actions">
        <RouterLink :to="{ name: 'admin-book-create' }" class="btn-primary">
          <svg
            width="14"
            height="14"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
          >
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
          </svg>
          Create book
        </RouterLink>
      </div>
    </div>

    <div class="table-wrap">
      <div class="table-filters">
        <div class="filter-tabs">
          <button
            v-for="f in FILTERS"
            :key="f.key"
            class="filter-tab"
            :class="{ active: activeFilter === f.key }"
            @click="activeFilter = f.key"
          >
            {{ f.label }}
          </button>
        </div>
        <div class="filter-search">
          <svg
            width="13"
            height="13"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <circle cx="11" cy="11" r="8" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
          </svg>
          <input v-model="searchQuery" type="text" placeholder="Find Title, ISBN..." />
        </div>
      </div>
      <div class="table-body">
        <div v-if="books.isLoading" class="table-overlay">
          <div class="table-loader__box">
            <AppSpinner />
            <span class="table-loader__info">Loading books...</span>
          </div>
        </div>

        <table class="table" :class="{ 'loading': books.isLoading }">
          <thead>
            <tr>
              <th style="width: 36%">Book</th>
              <th style="width: 14%">ISBN</th>
              <th style="width: 12%">Access</th>
              <th style="width: 11%">Price</th>
              <th style="width: 12%">Status</th>
              <th style="width: 9%">Year</th>
              <th style="width: 130px">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="book in books.books" :key="book.id">
              <td>
                <div style="display: flex; align-items: center; gap: 12px">
                  <div class="book-row-cover" :style="{ background: coverColor(book.id) }">
                    <template v-if="book.cover_url">
                      <img
                        :src="book.cover_url"
                        :alt="book.title"
                        style="width: 100%; height: 100%; object-fit: cover; border-radius: inherit"
                      />
                    </template>
                    <span v-else class="book-row-cover__icon">{{ coverIcon(book.id) }}</span>
                  </div>

                  <div class="book-row-info">
                    <span class="book-row-info__title">{{ book.title }}</span>
                    <span class="book-row-info__meta">
                      {{ book.publisher ?? '—' }} · {{ book.language.toUpperCase() }}
                    </span>
                  </div>
                </div>
              </td>

              <td class="td-mono">{{ book.isbn ?? '—' }}</td>

              <td>
                <span class="badge" :class="ACCESS_BADGE[book.access_type]">
                  {{ book.access_type }}
                </span>
              </td>

              <td>
                <span v-if="book.is_free" style="color: var(--text-muted); font-size: 0.82rem"
                  >Free</span
                >
                <span v-else style="font-weight: 500">{{ book.price.formatted }}</span>
              </td>

              <td>
                <span class="badge" :class="STATUS_BADGE[book.status]">
                  {{ book.status }}
                </span>
              </td>

              <td class="td-muted">{{ book.published_year ?? '—' }}</td>

              <td>
                <div class="row-actions">
                  <RouterLink
                    :to="{ name: 'admin-book-edit', params: { id: book.id } }"
                    class="btn-icon"
                    title="Edit"
                  >
                    <svg
                      width="13"
                      height="13"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                    >
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                  </RouterLink>

                  <button
                    v-if="book.status !== 'published'"
                    class="btn-icon"
                    title="Publish"
                    @click="handlePublish(book.id)"
                  >
                    <svg
                      width="13"
                      height="13"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                    >
                      <polyline points="9 11 12 14 22 4" />
                      <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                    </svg>
                  </button>

                  <button
                    class="btn-icon btn-icon--danger"
                    title="Delete"
                    @click="handleDelete(book.id)"
                  >
                    <svg
                      width="13"
                      height="13"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                    >
                      <polyline points="3 6 5 6 21 6" />
                      <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                      <path d="M10 11v6M14 11v6" />
                      <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>

            <tr v-if="!books.books.length && !books.isLoading">
              <td
                colspan="7"
                style="text-align: center; color: var(--text-muted); padding: 80px 20px"
              >
                No books found
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="table-footer">
        <span class="table-footer__info">
          {{ books.meta?.total ?? books.books.length }} result{{
            (books.meta?.total ?? books.books.length) !== 1 ? 's' : ''
          }}
        </span>

        <AppPagination
          v-if="books.meta && books.meta.total_pages > 1"
          :current="books.meta.current_page"
          :total="books.meta.total_pages"
          @change="currentPage = $event"
        />
      </div>
    </div>
  </div>
</template>

<style>
.row-actions {
  display: flex;
  gap: 4px;
  align-items: center;
}

.row-actions .btn-icon--danger:hover {
  border-color: var(--red);
  color: var(--red);
}
</style>
