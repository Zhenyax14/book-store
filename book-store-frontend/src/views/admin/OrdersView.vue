<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useOrdersStore } from '@/stores/orders'
import AppSpinner from '@/components/ui/AppSpinner.vue'
import AppPagination from '@/components/ui/AppPagination.vue'

const orders = useOrdersStore()

const searchQuery = ref('')
const currentPage = ref(1)
const PER_PAGE = 10

function load(): void {
  orders.fetchOrders({
    search: searchQuery.value.trim() || undefined,
    page: currentPage.value,
    per_page: PER_PAGE,
  })
}

watch([searchQuery], () => {
  currentPage.value = 1
  load()
})

watch(currentPage, load)

onMounted(load)

const subtitle = computed(() => {
  if (orders.isLoading) return 'Loading...'
  return `Complete list · ${orders.meta?.total ?? orders.orders.length} orders`
})
const totalResults = computed(() => orders.meta?.total ?? orders.orders.length)
const totalRevenue = computed(() => {
  if (!orders.orders.length) return 0
  return orders.orders.reduce((sum, order) => sum + order.total.amount, 0)
})

const averageOrderValue = computed(() => {
  if (!orders.orders.length) return 0
  return Math.round(totalRevenue.value / orders.orders.length)
})

const currency = computed(() => {
  return orders.orders[0]?.total.currency || 'EUR'
})

const formattedTotalRevenue = computed(() => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: currency.value,
    minimumFractionDigits: 2,
  }).format(totalRevenue.value / 100)
})
</script>

<template>
  <div class="page">
    <div class="page-header">
      <div>
        <h1 class="page-header__title">Orders Management</h1>
        <p class="page-header__sub">{{ subtitle }}</p>
      </div>
    </div>

    <div class="table-wrap">
      <div class="table-filters">
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
          <input v-model="searchQuery" type="text" placeholder="Find by name or email..." />
        </div>
      </div>

      <div class="table-body">
        <div v-if="orders.isLoading" class="table-overlay">
          <div class="table-loader__box">
            <AppSpinner />
            <span class="table-loader__info">Loading orders...</span>
          </div>
        </div>

        <table class="table" :class="{ loading: orders.isLoading }">
          <thead>
            <tr>
              <th style="width: 22%">Customer</th>
              <th style="width: 28%">Email</th>
              <th style="width: 15%">Date</th>
              <th style="width: 12%">Items</th>
              <th style="width: 15%">Total</th>
              <th style="width: 130px">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in orders.orders" :key="order.id">
              <!-- Customer Name -->
              <td>
                <div class="book-row-info">
                  <span class="book-row-info__title">{{ order.user.name }}</span>
                </div>
              </td>

              <!-- Email -->
              <td class="td-mono">{{ order.user.email }}</td>

              <!-- Date -->
              <td>
                {{
                  new Date(order.checked_out_at).toLocaleDateString('en-GB', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                  })
                }}
              </td>

              <!-- Items Count -->
              <td>
                <span class="badge badge-green">
                  {{ order.item_count }} item{{ order.item_count !== 1 ? 's' : '' }}
                </span>
              </td>

              <!-- Total -->
              <td class="td-mono font-medium">
                {{ order.total.formatted }}
              </td>

              <!-- Actions -->
              <td>
                <div class="row-actions">
                  <RouterLink
                    :to="{ name: 'admin-orders-show', params: { id: order.id } }"
                    class="btn-icon"
                    title="View order details"
                  >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                      <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                  </RouterLink>
                </div>
              </td>
            </tr>

            <!-- Empty state -->
            <tr v-if="!orders.orders.length && !orders.isLoading">
              <td colspan="6" class="empty-state">No orders found</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="!orders.isLoading && orders.orders.length" class="table-summary">
        <div class="summary-stats">
          <div class="summary-stat">
            <span class="summary-label">Total Revenue</span>
            <span class="summary-value total">{{ formattedTotalRevenue }}</span>
          </div>
          <div class="summary-stat">
            <span class="summary-label">Average Order Value</span>
            <span class="summary-value">
              {{
                new Intl.NumberFormat('en-US', {
                  style: 'currency',
                  currency: currency,
                }).format(averageOrderValue / 100)
              }}
            </span>
          </div>
          <div class="summary-stat">
            <span class="summary-label">Total Orders</span>
            <span class="summary-value">{{ totalResults }}</span>
          </div>
        </div>
      </div>

      <div class="table-footer">
        <span class="table-footer__info">
          {{ totalResults }} result{{ totalResults !== 1 ? 's' : '' }}
        </span>

        <AppPagination
          v-if="orders.meta && orders.meta.total_pages > 1"
          :current="orders.meta.current_page"
          :total="orders.meta.total_pages"
          @change="currentPage = $event"
        />
      </div>
    </div>
  </div>
</template>

<style>
.row-actions {
  display: flex;
  gap: 8px;
  align-items: center;
  justify-content: flex-end;
}
</style>
