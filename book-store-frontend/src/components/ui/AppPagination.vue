<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  current: number
  total: number
}>()

const isPrevDisabled = computed(() => props.current <= 1)
const isNextDisabled = computed(() => props.current >= props.total)

const emit = defineEmits<{
  change: [page: number]
}>()

const goToPage = (page: number) => {
  if (page < 1 || page > props.total || page === props.current) return
  emit('change', page)
}

const pages = computed<(number | '…')[]>(() => {
  const { current, total } = props
  if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1)

  const result: (number | '…')[] = [1]

  if (current > 3) result.push('…')

  const start = Math.max(2, current - 1)
  const end = Math.min(total - 1, current + 1)

  for (let i = start; i <= end; i++) result.push(i)

  if (current < total - 2) result.push('…')
  result.push(total)

  return result
})
</script>

<template>
  <nav class="pagination" aria-label="Pagination">
    <button class="page-btn" :disabled="isPrevDisabled" @click="goToPage(current - 1)">←</button>

    <template v-for="page in pages" :key="page">
      <span v-if="page === '…'" class="pagination__ellipsis">…</span>

      <button
        v-else
        class="page-btn"
        :class="{ active: page === current }"
        :disabled="page === current"
        @click="goToPage(page)"
      >
        {{ page }}
      </button>
    </template>

    <button class="page-btn" :disabled="isNextDisabled" @click="goToPage(current + 1)">→</button>
  </nav>
</template>

<style scoped>
.pagination {
  margin: 15px 0;
  display: flex;
  gap: 4px;
  justify-self: center;
}
.page-btn {
  background: none;
  border: 1px solid var(--border);
  color: var(--text-muted);
  padding: 5px 10px;
  border-radius: 5px;
  font-size: 0.78rem;
  font-weight: 600;
  transition: all var(--transition);
  min-width: 32px;
  text-align: center;
}
.page-btn:hover {
  border-color: var(--accent);
  color: var(--accent);
}
.page-btn.active {
  background: var(--accent);
  border-color: var(--accent);
  color: #fff;
}
.page-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
</style>
