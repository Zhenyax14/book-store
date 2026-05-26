import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { Toast, ToastType } from '@/types/toast'

function generateId(): string {
  return `toast-${Date.now()}-${Math.random().toString(36).slice(2, 7)}`
}

export const useToastStore = defineStore('toast', () => {
  const toasts = ref<Toast[]>([])

  function add(toast: Omit<Toast, 'id'>): string {
    const id = generateId()
    toasts.value.push({ ...toast, id })

    if (toast.duration > 0) {
      setTimeout(() => remove(id), toast.duration)
    }

    return id
  }

  function remove(id: string): void {
    const index = toasts.value.findIndex((t) => t.id === id)
    if (index !== -1) toasts.value.splice(index, 1)
  }

  function success(title: string, message?: string, duration = 4000): string {
    return add({ type: 'success' as ToastType, title, message, duration })
  }

  function error(title: string, message?: string, duration = 6000): string {
    return add({ type: 'error' as ToastType, title, message, duration })
  }

  function warning(title: string, message?: string, duration = 5000): string {
    return add({ type: 'warning' as ToastType, title, message, duration })
  }

  function info(title: string, message?: string, duration = 4000): string {
    return add({ type: 'info' as ToastType, title, message, duration })
  }

  return { toasts, add, remove, success, error, warning, info }
})
