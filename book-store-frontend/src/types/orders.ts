import type { Money, PaginationMeta } from './common'
export enum OrderItemType {
  Book = 'book',
  Subscription = 'subscription',
}

export interface OrderUser {
  id: number
  email: string
  name: string
}

export interface OrderItem {
  type: OrderItemType
  reference_id: number
  title: string
  price: Money
  access_granted: boolean
}

export interface Order {
  id: number
  user: OrderUser
  items: OrderItem[]
  total: Money
  item_count: number
  stripe_payment_intent: string | null
  checked_out_at: string
}

export interface OrderCollectionResponse {
  data: Order[]
  meta: PaginationMeta
}

export type OrderResponse = Order

export interface OrderListParams {
  search?: string
  date_from?: string
  date_to?: string
  per_page?: number
  page?: number
}
