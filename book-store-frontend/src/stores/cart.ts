import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import { cartApi } from '@/api/cart'
import type { Cart, CartItemType } from '@/types'

export const useCartStore = defineStore('cart', () => {
  const cart = ref<Cart | null>(null)
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const itemsCount = computed(() => cart.value?.items_count ?? 0)
  const total = computed(() => cart.value?.total ?? null)

  async function fetchCart(): Promise<void> {
    isLoading.value = true
    error.value = null
    try {
      cart.value = await cartApi.show()
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Failed to fetch cart'
    } finally {
      isLoading.value = false
    }
  }

  async function addItem(type: CartItemType, referenceId: number): Promise<void> {
    isLoading.value = true
    error.value = null
    try {
      cart.value = await cartApi.addItem({ type, reference_id: referenceId })
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Failed to add item'
    } finally {
      isLoading.value = false
    }
  }

  async function removeItem(type: CartItemType, referenceId: number): Promise<void> {
    isLoading.value = true
    error.value = null
    try {
      cart.value = await cartApi.removeItem(type, referenceId)
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Failed to remove item'
    } finally {
      isLoading.value = false
    }
  }

  async function checkout(currency: 'USD' | 'EUR' = 'EUR'): Promise<string | null> {
    isLoading.value = true
    error.value = null
    try {
      const response = await cartApi.checkout({ currency })
      return response.payment_url
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Checkout failed'
      return null
    } finally {
      isLoading.value = false
    }
  }

  return {
    cart,
    isLoading,
    error,
    itemsCount,
    total,
    fetchCart,
    addItem,
    removeItem,
    checkout,
  }
})
