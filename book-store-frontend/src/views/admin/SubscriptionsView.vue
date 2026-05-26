<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useSubscriptionsStore } from '@/stores/subscriptions'
import AppSpinner from '@/components/ui/AppSpinner.vue'
import AppPagination from '@/components/ui/AppPagination.vue'

import { getSubscriptionBadge, getSubscriptionLabel, type SubscriptionStatus } from '@/types'

const subscriptions = useSubscriptionsStore()

type FilterKey = 'all' | SubscriptionStatus

const activeFilter = ref<FilterKey>('all')
const searchQuery = ref('')
const currentPage = ref(1)
const PER_PAGE = 10

function load(): void {
  const filter = getSubscriptionFilter()

  subscriptions.fetchSubscriptions({
    status: filter,
    search: searchQuery.value.trim() || undefined,
    page: currentPage.value,
    per_page: PER_PAGE,
  })
}

function getSubscriptionFilter(): SubscriptionStatus | undefined {
  const filterValue = activeFilter.value

  if (filterValue === 'all') return undefined

  if (['active', 'expired', 'canceled'].includes(filterValue)) {
    return filterValue as SubscriptionStatus
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
  if (subscriptions.isLoading) return 'Loading...'
  return `Complete list · ${subscriptions.meta?.total ?? subscriptions.subscriptions.length} subscriptions`
})

const getSubscriptionBadgeClass = (status: SubscriptionStatus) => getSubscriptionBadge(status)
const getSubscriptionLabelText = (status: SubscriptionStatus) => getSubscriptionLabel(status)

const FILTERS: { key: FilterKey; label: string }[] = [
  { key: 'all', label: 'All' },
  { key: 'active', label: 'Active' },
  { key: 'cancelled', label: 'Canceled' },
  { key: 'expired', label: 'Expired' },
]
</script>

<template>
  <div class="page">
    <div class="page-header">
      <div>
        <h1 class="page-header__title">Subscription Management</h1>
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
          <input v-model="searchQuery" type="text" placeholder="Find Stripe ID..." />
        </div>
      </div>
      <div class="table-body">
        <div v-if="subscriptions.isLoading" class="table-overlay">
          <div class="table-loader__box">
            <AppSpinner />
            <span class="table-loader__info">Loading subscriptions...</span>
          </div>
        </div>

        <table class="table" :class="{ loading: subscriptions.isLoading }">
          <thead>
            <tr>
              <th style="width: 40%">Stripe ID</th>
              <th style="width: 20%">Status</th>
              <th style="width: 20%">Started At</th>
              <th style="width: 20%">Expires At</th>
              <th style="width: 130px">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="subscription in subscriptions.subscriptions" :key="subscription.id">
              <td>
                <div style="display: flex; align-items: center; gap: 12px">
                  <div class="book-row-info">
                    <span class="book-row-info__title">{{
                      subscription.stripe_subscription_id
                    }}</span>
                  </div>
                </div>
              </td>

              <td>
                <span class="badge" :class="getSubscriptionBadgeClass(subscription.status)">
                  {{ getSubscriptionLabelText(subscription.status) }}
                </span>
              </td>

              <td>
                {{
                  new Date(subscription.started_at).toLocaleDateString('en-GB', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                  })
                }}
              </td>

              <td>
                {{
                  new Date(subscription.expires_at).toLocaleDateString('en-GB', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                  })
                }}
              </td>

              <td>
                <div class="row-actions">
                  <RouterLink
                    :to="{ name: 'admin-subscriptions-show', params: { id: subscription.id } }"
                    class="btn-icon"
                    title="Show"
                  >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                      <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                  </RouterLink>
                  <RouterLink
                    :to="{ name: 'admin-readers-show', params: { id: subscription.user_id } }"
                    class="btn-icon"
                    title="Reader"
                  >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="12" cy="9" r="3.5"></circle>
                      <path d="M5 21v-2a4 4 0 0 1 4-4h6a4 4 0 0 1 4 4v2"></path>
                    </svg>
                  </RouterLink>
                </div>
              </td>
            </tr>

            <tr v-if="!subscriptions.subscriptions.length && !subscriptions.isLoading">
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
          {{ subscriptions.meta?.total ?? subscriptions.subscriptions.length }} result{{
            (subscriptions.meta?.total ?? subscriptions.subscriptions.length) !== 1 ? 's' : ''
          }}
        </span>

        <AppPagination
          v-if="subscriptions.meta && subscriptions.meta.total_pages > 1"
          :current="subscriptions.meta.current_page"
          :total="subscriptions.meta.total_pages"
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
