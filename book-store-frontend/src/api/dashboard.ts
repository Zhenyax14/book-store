import { http, buildQuery } from './client'
import type {
  PeriodParams,
  StatisticResponse,
  ReadingSessionsChartResponse,
} from '@/types/dashboard'

export const dashboardApi = {
  statistic: () => http.get<StatisticResponse>('/admin/dashboard/stats'),

  readingSessionsChart: (params: PeriodParams = { period: 'day' }) =>
    http.get<ReadingSessionsChartResponse>(
      `/admin/dashboard/charts/reading-sessions${buildQuery({
        period: params.period,
      } as Record<string, unknown>)}`,
    ),
}
