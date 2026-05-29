<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useReadingStore } from '@/stores/reading'
import AppSpinner from '@/components/ui/AppSpinner.vue'
import type { ReadingStatus } from '@/types'

const reading = useReadingStore()
const status = ref<ReadingStatus | ''>('')

function load(): void {
  reading.fetchList({ status: status.value || undefined })
}

watch(status, load)
onMounted(load)

async function remove(bookId: number): Promise<void> {
  await reading.removeFromList(bookId)
}
</script>

<template>
  <div class="content">
    <section class="section">
      <div class="section__header">
        <h2 class="section__title">READING LIST</h2>
        <p class="section__subtitle">Your books — in progress, saved, and finished</p>
        <div class="section__line"></div>
      </div>

      <div class="filters-bar">
        <select class="sort-select" v-model="status">
          <option value="">All Statuses</option>
          <option value="want_to_read">Want to Read</option>
          <option value="reading">Currently Reading</option>
          <option value="finished">Finished</option>
          <option value="dropped">Dropped</option>
        </select>
      </div>

      <AppSpinner v-if="reading.isLoading" />

      <div v-else-if="!reading.list.length" class="empty-state">
        <div class="empty-state__icon">📚</div>
        <h3>Your reading list is empty</h3>
        <p>Start adding books from the store and they will appear here.</p>
        <RouterLink to="/shop" class="btn btn--primary">Browse the Store</RouterLink>
      </div>

      <div v-else class="reading-list">
        <div v-for="entry in reading.list" :key="entry.book_id" class="reading-item">
          <div class="reading-item__cover-wrapper">
            <span class="reading-item__rank">#{{ entry.book_id }}</span>
            <div class="book-thumb book-thumb--sm book-thumb--orange">
              <span>BOOK #{{ entry.book_id }}</span>
            </div>
          </div>

          <div class="reading-item__main">
            <h4 class="reading-item__title">Book #{{ entry.book_id }}</h4>

            <div class="reading-item__status">
              <span class="badge" :class="`badge--${entry.status}`">
                {{ entry.status.replace('_', ' ').toUpperCase() }}
              </span>
            </div>

            <div class="reading-item__progress">
              <div class="progress-bar">
                <div
                  class="progress-bar__fill"
                  :style="{
                    width: `${entry.progress_percentage ?? (entry.current_page / (entry.total_pages || 1)) * 100}%`,
                  }"
                ></div>
              </div>
              <span class="progress-text">
                {{ entry.current_page }} / {{ entry.total_pages ?? '?' }} pages
              </span>
            </div>
          </div>

          <div class="reading-item__actions">
            <RouterLink
              :to="{ name: 'reading-book-detail', params: { id: entry.book_id } }"
              class="btn btn--sm btn--view-detail"
            >
              View Detail
            </RouterLink>
            <button class="btn btn--sm btn--danger" @click="remove(entry.book_id)">Remove</button>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
.reading-list {
  display: flex;
  flex-direction: column;
  gap: 48px;
}

.reading-item {
  display: flex;
  gap: 28px;
  position: relative;
}

.reading-item__main {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.reading-item__actions {
  display: flex;
  flex-direction: column;
  gap: 10px;
  align-items: flex-end;
  align-self: flex-end;
}

.reading-item__cover-wrapper {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 6px;
  min-width: 68px;
}

.reading-item__rank {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 1.35rem;
  font-weight: 700;
  letter-spacing: -0.02em;
  color: #1a1a1a;
  margin-left: 4px;
}

.book-thumb--sm.book-thumb--orange {
  width: 68px;
  height: 92px;
  background: linear-gradient(180deg, #d97f2e 0%, #b85f1a 100%);
  border-radius: 6px;
  box-shadow:
    6px 8px 16px -4px rgba(217, 127, 46, 0.25),
    0 4px 8px -2px rgba(0, 0, 0, 0.15);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.68rem;
  font-weight: 700;
  line-height: 1.05;
  text-align: center;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  padding: 4px;
}

.reading-item__title {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: 1.45rem;
  font-weight: 700;
  color: #1a1a1a;
  margin-bottom: 8px;
  line-height: 1.1;
}

.reading-item__status {
  margin-bottom: 16px;
}

.reading-item__progress {
  width: 100%;
}

.progress-bar {
  height: 7px;
  background: #e8e2d8;
  border-radius: 9999px;
  overflow: hidden;
  margin-bottom: 6px;
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.06);
}

.progress-bar__fill {
  height: 100%;
  background: linear-gradient(90deg, #e8a020, #d97f2e);
  transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.progress-text {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.82rem;
  font-weight: 600;
  color: #6b5e4f;
  letter-spacing: 0.03em;
}

.reading-item__actions {
  display: flex;
  flex-direction: column;
  gap: 10px;
  align-items: flex-end;
  margin-top: 8px;
}

.btn--sm {
  padding: 9px 24px;
  font-size: 0.82rem;
  font-weight: 700;
  letter-spacing: 0.04em;
  border-radius: 4px;
  transition: all 0.2s ease;
  white-space: nowrap;
  border: none !important;
  outline: none;
}

.btn--view-detail {
  background: #e8a020;
  color: #fff;
  box-shadow: 0 4px 12px -4px rgba(232, 160, 32, 0.4);
}

.btn--view-detail:hover {
  background: #d97f2e;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px -4px rgba(232, 160, 32, 0.5);
}

.btn--danger {
  background: #d94040;
  color: #fff;
  box-shadow: 0 4px 12px -4px rgba(217, 64, 64, 0.3);
}

.btn--danger:hover {
  background: #c22f2f;
  transform: translateY(-1px);
}

.badge {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.74rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  padding: 4px 14px;
  border-radius: 4px;
  text-transform: uppercase;
  display: inline-block;
  box-shadow: 0 2px 6px -2px rgba(0, 0, 0, 0.15);
}

.badge--want_to_read {
  background: #3ecf8e;
  color: #fff;
}
.badge--reading {
  background: #e8a020;
  color: #fff;
}
.badge--finished {
  background: #1a1a1a;
  color: #fff;
}
.badge--dropped {
  background: #d94040;
  color: #fff;
}

/* HEADER & FILTER (polished) */
.section__header {
  margin-bottom: 24px;
}

.section__title {
  font-size: 2.1rem;
  font-weight: 700;
  letter-spacing: -0.03em;
  margin: 0;
}

.section__subtitle {
  color: #6b5e4f;
  font-size: 1.05rem;
  margin: 4px 0 12px;
}

.section__line {
  height: 3px;
  width: 64px;
  background: #e8a020;
  border-radius: 9999px;
}

.filters-bar {
  display: flex;
  justify-content: flex-end;
  margin-bottom: 32px;
}

/* EMPTY STATE (already nice, slightly enhanced) */
.empty-state {
  text-align: center;
  padding: 72px 40px;
  background: #fff;
  border: 2px solid #e0d8cc;
  border-radius: 12px;
  max-width: 420px;
  margin: 0 auto;
}

.empty-state__icon {
  font-size: 4.8rem;
  margin-bottom: 20px;
  opacity: 0.25;
}

.empty-state h3 {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: 1.55rem;
  margin-bottom: 8px;
}

.empty-state p {
  color: #888;
  max-width: 280px;
  margin: 0 auto 28px;
}
</style>
