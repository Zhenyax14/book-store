<script setup lang="ts">
import { useDashboardStore } from '@/stores/dashboard'
import { storeToRefs } from 'pinia'
import { computed, onMounted } from 'vue'
import type { PeriodType } from '@/types/dashboard'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler,
} from 'chart.js'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler,
)
import AppChart from '@/components/ui/AppChart.vue'
import { ReadingSessionsChartFactory } from '@/services/chart/readingSessionsChartFactory'
import AppSpinner from '@/components/ui/AppSpinner.vue'
import { useToastStore } from '@/stores'

const dashboard = useDashboardStore()
const toast = useToastStore()

const { cards, readingSessionsChart, readingSessionsSummary, currentPeriod, loading, error } =
  storeToRefs(dashboard)

const chartConfig = computed(() => ReadingSessionsChartFactory.make(readingSessionsChart.value))

type PeriodKey = PeriodType
const PERIODS: { key: PeriodKey; label: string }[] = [
  { key: 'day', label: 'Today' },
  { key: 'week', label: 'Week' },
  { key: 'month', label: 'Month' },
  { key: 'year', label: 'Year' },
]
const periodLabels = {
  day: 'Today',
  week: 'Week',
  month: 'Month',
  year: 'Year',
} as const

const cardLabels = {
  today_revenue: 'Today Revenue',
  new_orders: 'New Orders',
  active_readers: 'Active Readers',
  active_subscriptions: 'Active Subscriptions',
} as const

async function load(): Promise<void> {
  try {
    await dashboard.fetchAll()
  } catch {
    toast.error('Error', 'Error loading data')
  }
}

async function sessionsChart(period: PeriodType): Promise<void> {
  try {
    await dashboard.fetchReadingSessionsChart(period)
  } catch {
    toast.error('Error', 'Error loading data')
  }
}

onMounted(load)

const getIcon = (type: string): string => {
  switch (type) {
    case 'today_revenue':
      return '💰'
    case 'new_orders':
      return '📦'
    case 'active_readers':
      return '👥'
    case 'active_subscriptions':
      return '🔄'
    default:
      return '📊'
  }
}

const summary = computed(() => readingSessionsSummary.value)
</script>

<template>
  <div class="page">
    <div class="page-header">
      <div>
        <h1 class="page-header__title">Dashboard</h1>
      </div>
    </div>

    <div v-if="loading.stats" class="loading">
      <AppSpinner />
    </div>

    <div v-else-if="error" class="error">
      {{ error }}
    </div>

    <div v-else class="stats-grid">
      <div
        v-for="stat in cards"
        :key="stat.label"
        class="stat-card"
        :style="{ '--stat-color': stat.is_up ? 'var(--accent)' : 'var(--red)' }"
      >
        <div class="stat-card__label">{{ cardLabels[stat.label] }}</div>
        <div class="stat-card__value">{{ stat.value }}</div>

        <div class="stat-card__delta" :class="{ up: stat.is_up, down: !stat.is_up }">
          {{ stat.is_up ? '▲' : '▼' }} {{ stat.delta }}
        </div>

        <div class="stat-card__icon">
          {{ getIcon(stat.label) }}
        </div>
      </div>
    </div>

    <div class="chart-wrap">
      <div class="section-header">
        <h2>Reading Sessions</h2>
        <div class="period-selector">
          <button
            v-for="p in PERIODS"
            :key="p.key"
            :class="{ active: currentPeriod === p.key }"
            class="filter-tab"
            @click="sessionsChart(p.key)"
          >
            {{ periodLabels[p.key] }}
          </button>
        </div>
      </div>

      <div v-if="loading.chart" class="chart-loading"><AppSpinner /></div>
      <div v-else-if="readingSessionsChart.length === 0" class="chart-container no-data">
        <span>No data for selected period</span>
      </div>
      <div v-else class="chart-container">
        <AppChart :config="chartConfig" />
      </div>

      <div v-if="summary" class="summary-grid">
        <div class="summary-card">
          <div class="summary-label">Total sessions</div>
          <div class="summary-value">{{ summary.total_sessions.toLocaleString() }}</div>
        </div>
        <div class="summary-card">
          <div class="summary-label">Pages read</div>
          <div class="summary-value">{{ summary.total_pages_read.toLocaleString() }}</div>
        </div>
        <div class="summary-card">
          <div class="summary-label">Reading time</div>
          <div class="summary-value">
            {{ Math.floor(summary.total_duration_seconds / 3600) }}h
            {{ Math.floor((summary.total_duration_seconds % 3600) / 60) }}m
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
.summary-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
  margin-top: 20px;
}

.summary-card {
  position: relative;
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 18px 20px;

  transition: all var(--transition);
  overflow: hidden;
}

.summary-card:hover {
  background: var(--bg-hover);
  border-color: var(--border-lt);
  transform: translateY(-2px);
}

.summary-card::after {
  content: '';
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at top left, var(--accent-glow), transparent 70%);
  opacity: 0;
  transition: opacity var(--transition);
}

.summary-card:hover::after {
  opacity: 1;
}

.summary-label {
  font-size: 12px;
  color: var(--text-muted);
  margin-bottom: 6px;
  letter-spacing: 0;
}

/* value */
.summary-value {
  font-size: 22px;
  font-weight: 600;
  color: var(--text);
  letter-spacing: 0;
}

.summary-card:nth-child(1) {
  border-left: 3px solid var(--blue);
}

.summary-card:nth-child(2) {
  border-left: 3px solid var(--green);
}

.summary-card:nth-child(3) {
  border-left: 3px solid var(--accent);
}

.period-selector button.active::after {
  content: '';
  position: absolute;
  inset: 0;
  border-radius: inherit;
  background: radial-gradient(circle at center, var(--accent-glow), transparent 70%);
  opacity: 0.8;
  pointer-events: none;
}

/* active press effect */
.period-selector button:active {
  transform: scale(0.97);
}
.chart-container {
  width: 100%;
  min-height: 300px;
}
.chart-container.no-data {
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  color: var(--text-muted);
  font-family: 'Barlow Condensed', sans-serif;
}
.chart-loading {
  margin: 20px 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 12px;
  padding: 20px;
  width: 100%;
  min-height: 300px;
}
</style>
