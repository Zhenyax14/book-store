import { computed, ref } from 'vue'
import { defineStore } from 'pinia'
import { dashboardApi } from '@/api/dashboard'
import type {
  Card,
  PeriodType,
  ReadingSessionData,
  ReadingSessionsChartResponse,
  ReadingSessionsSummary
} from '@/types'

export const useDashboardStore = defineStore('dashboard', () => {
  const cards = ref<Card[]>([])
  const readingSessionsChart = ref<ReadingSessionData[]>([])
  const readingSessionsSummary = ref<ReadingSessionsSummary | null>(null)

  const currentPeriod = ref<PeriodType>('day')

  const loading = ref({
    stats: false,
    chart: false,
  })

  const error = ref<string | null>(null)

  const totalSessions = computed(() => readingSessionsSummary.value?.total_sessions ?? 0)
  const totalPagesRead = computed(() => readingSessionsSummary.value?.total_pages_read ?? 0)
  const totalDurationSeconds = computed(
    () => readingSessionsSummary.value?.total_duration_seconds ?? 0,
  )

  const totalDurationFormatted = computed(() => {
    const seconds = totalDurationSeconds.value
    const hours = Math.floor(seconds / 3600)
    const minutes = Math.floor((seconds % 3600) / 60)
    return `${hours}ч ${minutes}м`
  })

  const fetchStatistics = async () => {
    loading.value.stats = true
    error.value = null

    try {
      const response = await dashboardApi.statistic()
      cards.value = [
        response.today_revenue,
        response.new_orders,
        response.active_readers,
        response.active_subscriptions,
      ]
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Error fetching statistics'
      console.error(err)
    } finally {
      loading.value.stats = false
    }
  }

  const fetchReadingSessionsChart = async (period: PeriodType = 'day') => {
    loading.value.chart = true
    error.value = null
    currentPeriod.value = period

    try {
      const response: ReadingSessionsChartResponse = await dashboardApi.readingSessionsChart({
        period,
      })

      readingSessionsChart.value = response.data
      readingSessionsSummary.value = response.summary
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Error fetching reading sessions chart'
      console.error(err);
    } finally {
      loading.value.chart = false
    }
  }

  const fetchAll = async (period: PeriodType = 'day') => {
    await Promise.allSettled([fetchStatistics(), fetchReadingSessionsChart(period)])
  }

  const reset = () => {
    cards.value = []
    readingSessionsChart.value = []
    readingSessionsSummary.value = null
    currentPeriod.value = 'day'
    error.value = null
  }

  return {
    cards,
    readingSessionsChart,
    readingSessionsSummary,
    currentPeriod,
    loading,
    error,

    totalSessions,
    totalPagesRead,
    totalDurationSeconds,
    totalDurationFormatted,

    fetchStatistics,
    fetchReadingSessionsChart,
    fetchAll,
    reset,
  }
})
