import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import { notificationsApi } from '@/api/notifications'
import type { Notification, PaginationMeta } from '@/types'

export const useNotificationsStore = defineStore('notifications', () => {
  const notifications = ref<Notification[]>([])
  const unreadCount = ref(0)
  const meta = ref<(PaginationMeta & { unread_count: number }) | null>(null)
  const isLoading = ref(false)

  const hasUnread = computed(() => unreadCount.value > 0)

  async function fetchNotifications(page = 1): Promise<void> {
    isLoading.value = true
    try {
      const response = await notificationsApi.index({ page, per_page: 20 })
      notifications.value = response.data
      meta.value = response.meta
      unreadCount.value = response.meta.unread_count
    } finally {
      isLoading.value = false
    }
  }

  async function fetchUnreadCount(): Promise<void> {
    const response = await notificationsApi.unreadCount()
    unreadCount.value = response.count
  }

  async function markRead(id: string): Promise<void> {
    await notificationsApi.markRead(id)
    const notification = notifications.value.find((n) => n.id === id)
    if (notification) {
      notification.is_read = true
      unreadCount.value = Math.max(0, unreadCount.value - 1)
    }
  }

  async function markAllRead(): Promise<void> {
    await notificationsApi.markAllRead()
    notifications.value.forEach((n) => (n.is_read = true))
    unreadCount.value = 0
  }

  return {
    notifications,
    unreadCount,
    meta,
    isLoading,
    hasUnread,
    fetchNotifications,
    fetchUnreadCount,
    markRead,
    markAllRead,
  }
})
