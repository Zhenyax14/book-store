<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useToastStore } from '@/stores/toast'
import AppSpinner from '@/components/ui/AppSpinner.vue'

import { getReaderBooksBadge, getReaderBooksLabel, getReaderSubscriptionBadge, getReaderSubscriptionLabel } from '@/types'
import { useReadersStore } from '@/stores/reader'

const toast = useToastStore()
const readers = useReadersStore()
const router = useRouter()

const props = defineProps<{
  id: number
}>()

const currentReader = computed(() => readers.currentReader)
const isLoading = ref(true)

onMounted(async () => {
  try {
    await readers.fetchReader(props.id)
  } catch {
    toast.error('Error', 'Failed to fetch reader details.')
    router.back()
  } finally {
    isLoading.value = false
  }
})

const getSubscriptionBadgeClass = (hasActive: boolean) => getReaderSubscriptionBadge(hasActive)
const getSubscriptionLabelText = (hasActive: boolean) => getReaderSubscriptionLabel(hasActive)

const getBookBadgeClass = (hasBooks: boolean) => getReaderBooksBadge(hasBooks)
const getBookLabelText = (hasBooks: boolean) => getReaderBooksLabel(hasBooks)
</script>

<template>
  <div class="page">
    <!-- Page Header -->
    <div class="page-header">
      <div>
        <h1 class="page-header__title">Reader Details</h1>
        <p class="page-header__sub">View reader information</p>
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

    <div v-else-if="currentReader" class="table-wrap">
      <!-- User Header -->
      <div class="user-modal-header">
        <div
          class="user-modal-avatar"
          :style="{
            background: currentReader.has_active_subscriptions ? 'var(--green)' : 'var(--text-dim)',
          }"
        >
          {{ currentReader.name.charAt(0).toUpperCase() }}
        </div>
        <div>
          <div class="user-modal-name">{{ currentReader.name }}</div>
          <div class="user-modal-email">{{ currentReader.email }}</div>
        </div>
      </div>

      <div class="modal__body" style="padding: 24px">
        <div class="user-detail-grid">
          <!-- ID -->
          <div class="user-detail-cell">
            <label>Reader ID</label>
            <p class="td-mono">#{{ currentReader.id }}</p>
          </div>

          <!-- Role -->
          <div class="user-detail-cell">
            <label>Role</label>
            <p>Reader</p>
          </div>

          <div class="user-detail-cell">
            <label>Subscription Status</label>
            <span
              class="badge"
              :class="getSubscriptionBadgeClass(currentReader.has_active_subscriptions)"
            >
              {{ getSubscriptionLabelText(currentReader.has_active_subscriptions) }}
            </span>
          </div>

          <!-- Books Access -->
          <div class="user-detail-cell">
            <label>Books Access</label>
            <span class="badge" :class="getBookBadgeClass(currentReader.has_books)">
              {{ getBookLabelText(currentReader.has_books) }}
            </span>
          </div>
        </div>

        <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--border)">
          <div class="user-detail-grid">
            <div class="user-detail-cell">
              <label>Joined</label>
              <p v-if="currentReader.created_at">
                {{
                  new Date(currentReader.created_at).toLocaleString('en', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                  })
                }}
              </p>
              <p v-else class="td-muted">—</p>
            </div>
            <div class="user-detail-cell">
              <label>Last Activity</label>
              <p class="td-muted">—</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="modal__footer" style="border-top: 1px solid var(--border); padding: 20px 24px">
        <button class="btn-secondary" @click="router.back()">Close</button>
      </div>
    </div>

    <div
      v-else
      class="table-wrap"
      style="padding: 80px; text-align: center; color: var(--text-muted)"
    >
      Reader not found
    </div>
  </div>
</template>

<style scoped>
.user-modal-header {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 28px 28px 20px;
  border-bottom: 1px solid var(--border);
}

.user-modal-avatar {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.4rem;
  font-weight: 700;
  color: #fff;
  flex-shrink: 0;
}

.user-modal-name {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text);
}

.user-modal-email {
  font-size: 0.9rem;
  color: var(--text-muted);
  margin-top: 2px;
}

.user-detail-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 24px;
}

.user-detail-cell label {
  display: block;
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: var(--text-muted);
  margin-bottom: 8px;
}

.user-detail-cell p {
  font-size: 0.92rem;
  color: var(--text);
}

.modal__footer {
  padding: 20px 28px;
  border-top: 1px solid var(--border);
  display: flex;
  justify-content: flex-end;
}
</style>
