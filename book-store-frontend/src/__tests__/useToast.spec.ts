import { describe, it, expect, vi, beforeEach } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import { useToastStore } from '@/stores'

beforeEach(() => {
  setActivePinia(createPinia())
})

describe('useToastStore', () => {
  it('adds a success toast', () => {
    const store = useToastStore()
    store.success('Saved!', 'Your changes have been saved.', 0)
    expect(store.toasts).toHaveLength(1)
    const toast = store.toasts[0]!
    expect(toast.type).toBe('success')
    expect(toast.title).toBe('Saved!')
  })

  it('adds an error toast', () => {
    const store = useToastStore()
    store.error('Network error', undefined, 0)
    expect(store.toasts[0]!.type).toBe('error')
  })

  it('removes a toast by id', () => {
    const store = useToastStore()
    const id = store.info('Hello', undefined, 0)
    store.remove(id)
    expect(store.toasts).toHaveLength(0)
  })

  it('auto-removes after duration', () => {
    vi.useFakeTimers()
    const store = useToastStore()
    store.warning('Watch out', undefined, 500)
    expect(store.toasts).toHaveLength(1)
    vi.advanceTimersByTime(600)
    expect(store.toasts).toHaveLength(0)
    vi.useRealTimers()
  })

  it('toast with duration=0 never auto-removes', () => {
    vi.useFakeTimers()
    const store = useToastStore()
    store.info('Sticky', undefined, 0)
    vi.advanceTimersByTime(60_000)
    expect(store.toasts).toHaveLength(1)
    vi.useRealTimers()
  })
})
