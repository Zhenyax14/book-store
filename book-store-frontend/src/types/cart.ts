import type { Money } from './common'

export type CartStatus = 'active' | 'checked_out' | 'abandoned'
export type CartItemType = 'book' | 'subscription'

export interface CartItem {
  type: CartItemType
  reference_id: number
  title: string
  price: Money
}

export interface Cart {
  id: string | null
  status: CartStatus
  items: CartItem[]
  total: Money
  items_count: number
  created_at: string
}

export interface AddItemPayload {
  type: CartItemType
  reference_id: number
}

export interface CheckoutPayload {
  currency: 'USD' | 'EUR'
}

export interface CheckoutResponse {
  cart_id: string
  total: string
  payment_url: string
}
