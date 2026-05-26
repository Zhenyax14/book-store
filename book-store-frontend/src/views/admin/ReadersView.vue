<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useReadersStore } from '@/stores/reader'
import AppSpinner from '@/components/ui/AppSpinner.vue'
import AppPagination from '@/components/ui/AppPagination.vue'

import {
  type ReaderSubscriptionStatus,
  type HasBooksStatus,
  getReaderSubscriptionBadge,
  getReaderSubscriptionLabel,
  getReaderBooksBadge,
  getReaderBooksLabel,
} from '@/types'

const readers = useReadersStore()

type FilterKey = 'all' | ReaderSubscriptionStatus | HasBooksStatus

const activeFilter = ref<FilterKey>('all')
const searchQuery = ref('')
const currentPage = ref(1)
const PER_PAGE = 10

function load(): void {
  const filter = getReaderFilter()

  readers.fetchReaders({
    filter,
    search: searchQuery.value.trim() || undefined,
    page: currentPage.value,
    per_page: PER_PAGE,
  })
}

function getReaderFilter(): ReaderSubscriptionStatus | HasBooksStatus | undefined {
  const filterValue = activeFilter.value

  if (filterValue === 'all') return undefined

  if (['subscribed', 'not_subscribed'].includes(filterValue)) {
    return filterValue as ReaderSubscriptionStatus
  }

  if (['has_books', 'has_not_books'].includes(filterValue)) {
    return filterValue as HasBooksStatus
  }

  return undefined
}

watch([activeFilter, searchQuery], () => {
  currentPage.value = 1
  load()
})

watch(currentPage, load)

onMounted(load)

const subtitle = computed(() => {
  if (readers.isLoading) return 'Loading...'
  return `Complete list · ${readers.meta?.total ?? readers.readers.length} readers`
})

const getSubscriptionBadgeClass = (hasActive: boolean) => getReaderSubscriptionBadge(hasActive)
const getSubscriptionLabelText = (hasActive: boolean) => getReaderSubscriptionLabel(hasActive)

const getBookBadgeClass = (hasBooks: boolean) => getReaderBooksBadge(hasBooks)
const getBookLabelText = (hasBooks: boolean) => getReaderBooksLabel(hasBooks)

const FILTERS: { key: FilterKey; label: string }[] = [
  { key: 'all', label: 'All' },
  { key: 'subscribed', label: 'Subscribed' },
  { key: 'not_subscribed', label: 'Not subscribed' },
  { key: 'has_books', label: 'Has books' },
  { key: 'has_not_books', label: 'Has not books' },
]
</script>

<template>
  <div class="page">
    <div class="page-header">
      <div>
        <h1 class="page-header__title">Readers Management</h1>
        <p class="page-header__sub">{{ subtitle }}</p>
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
          <input v-model="searchQuery" type="text" placeholder="Find Name, Email..." />
        </div>
      </div>
      <div class="table-body">
        <div v-if="readers.isLoading" class="table-overlay">
          <div class="table-loader__box">
            <AppSpinner />
            <span class="table-loader__info">Loading readers...</span>
          </div>
        </div>

        <table class="table" :class="{ loading: readers.isLoading }">
          <thead>
            <tr>
              <th style="width: 40%">Name</th>
              <th style="width: 30%">Email</th>
              <th style="width: 15%">Subscription Status</th>
              <th style="width: 15%">Orders Info</th>
              <th style="width: 130px">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="reader in readers.readers" :key="reader.id">
              <td>
                <div style="display: flex; align-items: center; gap: 12px">
                  <div class="book-row-info">
                    <span class="book-row-info__title">{{ reader.name }}</span>
                    <span class="book-row-info__meta">
                      {{ reader.email ?? '—' }}
                    </span>
                  </div>
                </div>
              </td>

              <td class="td-mono">{{ reader.email ?? '—' }}</td>

              <td>
                <span
                  class="badge"
                  :class="getSubscriptionBadgeClass(reader.has_active_subscriptions)"
                >
                  {{ getSubscriptionLabelText(reader.has_active_subscriptions) }}
                </span>
              </td>

              <td>
                <span class="badge" :class="getBookBadgeClass(reader.has_books)">
                  {{ getBookLabelText(reader.has_books) }}
                </span>
              </td>

              <td>
                <div class="row-actions">
                  <RouterLink
                    :to="{ name: 'admin-readers-show', params: { id: reader.id } }"
                    class="btn-icon"
                    title="Show"
                  >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                      <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                  </RouterLink>
                </div>
              </td>
            </tr>

            <tr v-if="!readers.readers.length && !readers.isLoading">
              <td
                colspan="7"
                style="text-align: center; color: var(--text-muted); padding: 80px 20px"
              >
                No readers found
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="table-footer">
        <span class="table-footer__info">
          {{ readers.meta?.total ?? readers.readers.length }} result{{
            (readers.meta?.total ?? readers.readers.length) !== 1 ? 's' : ''
          }}
        </span>

        <AppPagination
          v-if="readers.meta && readers.meta.total_pages > 1"
          :current="readers.meta.current_page"
          :total="readers.meta.total_pages"
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
</style>
