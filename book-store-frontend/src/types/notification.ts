import type { PaginationMeta } from './common'

export type NotificationType = 'welcome' | 'book_published' | 'book_finished' | 'purchase_receipt'

export interface Notification {
  id: string
  type: NotificationType
  title: string
  body: string
  data: Record<string, unknown>
  is_read: boolean
  read_at: string | null
  created_at: string
}

export interface NotificationsResponse {
  data: Notification[]
  meta: PaginationMeta & { unread_count: number }
}
