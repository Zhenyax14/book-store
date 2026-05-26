import { http } from './client'
import type { Cart, AddItemPayload, CheckoutPayload, CheckoutResponse, CartItemType } from '@/types'

export const cartApi = {
  show: () => http.get<Cart>('/cart'),

  addItem: (payload: AddItemPayload) => http.post<Cart>('/cart/items', payload),

  removeItem: (type: CartItemType, referenceId: number) =>
    http.delete<Cart>(`/cart/items/${type}/${referenceId}`),

  checkout: (payload: CheckoutPayload) => http.post<CheckoutResponse>('/cart/checkout', payload),
}
