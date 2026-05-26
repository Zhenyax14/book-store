import { ref } from 'vue'
import { defineStore } from 'pinia'
import { ordersApi } from '@/api/orders'
import type { Order, PaginationMeta, OrderListParams } from '@/types'

export const useOrdersStore = defineStore('orders', () => {
  const orders = ref<Order[]>([])
  const currentOrder = ref<Order | null>(null)
  const meta = ref<PaginationMeta | null>(null)
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  async function fetchOrders(params: OrderListParams = {}): Promise<void> {
    isLoading.value = true
    error.value = null
    try {
      const response = await ordersApi.index(params)
      orders.value = response.data
      meta.value = response.meta
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Failed to fetch orders'
    } finally {
      isLoading.value = false
    }
  }

  async function fetchOrder(id: number): Promise<void> {
    isLoading.value = true
    error.value = null
    try {
      currentOrder.value = await ordersApi.show(id)
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Failed to fetch order'
    } finally {
      isLoading.value = false
    }
  }

  return {
    orders,
    currentOrder,
    meta,
    isLoading,
    error,
    fetchOrders,
    fetchOrder,
  }
})
