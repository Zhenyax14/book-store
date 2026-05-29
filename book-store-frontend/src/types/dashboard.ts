export type CardType = 'today_revenue' | 'new_orders' | 'active_readers' | 'active_subscriptions'
export type PeriodType = 'day' | 'week' | 'month' | 'year'

export interface Card {
  label: CardType
  value: string
  delta: string
  is_up: boolean
}

export interface PeriodParams {
  period?: PeriodType
}

export interface StatisticResponse {
  today_revenue: Card
  new_orders: Card
  active_readers: Card
  active_subscriptions: Card
}

export interface ReadingSessionsChartResponse {
  period: PeriodType
  data: ReadingSessionData[]
  summary: ReadingSessionsSummary
}

export interface ReadingSessionData {
  date: string
  sessions: number
  pages_read: number
  duration_seconds: number
}

export interface ReadingSessionsSummary {
  total_sessions: number
  total_pages_read: number
  total_duration_seconds: number
}
