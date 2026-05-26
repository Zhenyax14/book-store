import { ref } from 'vue'
import { defineStore } from 'pinia'
import { subscriptionsApi } from '@/api/subscriptions'
import type {
  Subscription,
  PaginationMeta,
  SubscriptionIndexParams
} from '@/types'

export const useSubscriptionsStore = defineStore('subscriptions', () => {
  const subscriptions = ref<Subscription[]>([])
  const currentSubscription = ref<Subscription | null>(null)
  const meta = ref<PaginationMeta | null>(null)
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  async function fetchSubscriptions(params: SubscriptionIndexParams = {}): Promise<void> {
    isLoading.value = true
    error.value = null
    try {
      const response = await subscriptionsApi.index(params)
      subscriptions.value = response.data
      meta.value = response.meta
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Failed to fetch subscriptions'
    } finally {
      isLoading.value = false
    }
  }

  async function fetchSubscription(id: number): Promise<void> {
    isLoading.value = true
    error.value = null
    try {
      currentSubscription.value = await subscriptionsApi.show(id)
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Failed to fetch subscription'
    } finally {
      isLoading.value = false
    }
  }

  return {
    subscriptions,
    currentSubscription,
    meta,
    isLoading,
    error,
    fetchSubscriptions,
    fetchSubscription,
  }
})
