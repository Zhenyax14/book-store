<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useToastStore } from '@/stores/toast'
import AppSpinner from '@/components/ui/AppSpinner.vue'

import { useOrdersStore } from '@/stores/orders'

const toast = useToastStore()
const orders = useOrdersStore()
const router = useRouter()

const props = defineProps<{
  id: number
}>()

const currentOrder = computed(() => orders.currentOrder)
const isLoading = ref(true)

onMounted(async () => {
  try {
    await orders.fetchOrder(props.id)
  } catch {
    toast.error('Error', 'Failed to fetch order details.')
    router.back()
  } finally {
    isLoading.value = false
  }
})
const formattedDate = computed(() => {
  if (!currentOrder.value?.checked_out_at) return '—'
  return new Date(currentOrder.value.checked_out_at).toLocaleDateString('en-GB', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
})
</script>

<template>
  <div class="page">
    <!-- Page Header -->
    <div class="page-header">
      <div>
        <h1 class="page-header__title">Order #{{ props.id }}</h1>
        <p class="page-header__sub">Placed on {{ formattedDate }}</p>
      </div>
      <div class="page-header__actions">
        <button class="btn-secondary" @click="router.back()">
          <svg
            width="13"
            height="13"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <polyline points="15 18 9 12 15 6" />
          </svg>
          Back
        </button>
      </div>
    </div>

    <AppSpinner v-if="isLoading" />

    <div v-else-if="currentOrder" class="order-detail">
      <!-- Customer Info -->
      <div class="user-modal-header">
        <div class="user-modal-avatar">
          {{ currentOrder.user.name.charAt(0).toUpperCase() }}
        </div>
        <div>
          <div class="user-modal-name">{{ currentOrder.user.name }}</div>
          <div class="user-modal-email">{{ currentOrder.user.email }}</div>
        </div>
      </div>

      <!-- Order Summary -->
      <div class="modal__body">
        <div class="order-summary">
          <div class="summary-item">
            <label>Order ID</label>
            <p class="td-mono">#{{ currentOrder.id }}</p>
          </div>
          <div class="summary-item">
            <label>Payment Intent</label>
            <p class="td-mono">{{ currentOrder.stripe_payment_intent || '—' }}</p>
          </div>
          <div class="summary-item">
            <label>Total Amount</label>
            <p class="total-amount">{{ currentOrder.total.formatted }}</p>
          </div>
          <div class="summary-item">
            <label>Items Count</label>
            <p>{{ currentOrder.item_count }} item{{ currentOrder.item_count !== 1 ? 's' : '' }}</p>
          </div>
        </div>

        <!-- Items List -->
        <div class="items-section">
          <h3 class="section-title">Order Items</h3>
          <div class="items-list">
            <div v-for="item in currentOrder.items" :key="item.reference_id" class="order-item">
              <div class="item-info">
                <div class="item-title">{{ item.title }}</div>
                <div class="item-meta">
                  {{ item.type.toUpperCase() }} • ID: {{ item.reference_id }}
                </div>
              </div>
              <div class="item-price">
                {{ item.price.formatted }}
              </div>
              <div class="item-access">
                <span
                  class="badge"
                  :class="{
                    'badge-success': item.access_granted,
                    'badge-warning': !item.access_granted,
                  }"
                >
                  {{ item.access_granted ? 'Access Granted' : 'No Access' }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="modal__footer">
        <button class="btn-secondary" @click="router.back()">Close</button>
      </div>
    </div>

    <!-- Not Found -->
    <div v-else class="not-found">
      <p>Order not found</p>
    </div>
  </div>
</template>

<style>
</style>
