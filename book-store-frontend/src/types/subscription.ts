export type SubscriptionStatus = 'active' | 'expired' | 'cancelled';

export interface Subscription {
  id: number
  status: SubscriptionStatus
  stripe_subscription_id: string
  user_id: number
  started_at: string
  expires_at: string
}

export interface SubscriptionIndexParams {
  status?: SubscriptionStatus
  per_page?: number
  page?: number
  search?: string
}

const subscriptionStatus = {
  badge: {
    active: 'badge-green',
    expired: 'badge-amber',
    cancelled: 'badge-red',
  } as const,

  label: {
    active: 'ACTIVE',
    expired: 'EXPIRED',
    cancelled: 'CANCELED',
  } as const,
} as const

export const getSubscriptionBadge = (status: SubscriptionStatus): string => {
  return subscriptionStatus.badge[status]
}

export const getSubscriptionLabel = (status: SubscriptionStatus): string => {
  return subscriptionStatus.label[status]
}
