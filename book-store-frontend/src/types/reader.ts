export type ReaderSubscriptionStatus = 'subscribed' | 'not_subscribed'
export type HasBooksStatus = 'has_books' | 'has_not_books'

export interface ReaderIndexParams {
  filter?: ReaderSubscriptionStatus | HasBooksStatus
  per_page?: number
  page?: number
  search?: string
}

export interface Reader {
  id: number
  name: string
  email: string
  has_active_subscriptions: boolean
  has_books: boolean
  created_at: string
}

export const subscriptionStatus = {
  badge: {
    true: 'badge-green',
    false: 'badge-red',
  } as const,

  label: {
    true: 'SUBSCRIBED',
    false: 'NOT SUBSCRIBED',
  } as const,
} as const

export const booksStatus = {
  badge: {
    true: 'badge-green',
    false: 'badge-red',
  } as const,

  label: {
    true: 'HAS BOOKS',
    false: 'HAS NOT BOOKS',
  } as const,
} as const

export const getReaderSubscriptionBadge = (hasActive: boolean): string =>
  hasActive
    ? subscriptionStatus.badge.true
    : subscriptionStatus.badge.false;

export const getReaderSubscriptionLabel = (hasActive: boolean): string =>
  hasActive ? subscriptionStatus.label.true : subscriptionStatus.label.false

export const getReaderBooksBadge = (hasBooks: boolean): string =>
  hasBooks ? booksStatus.badge.true : booksStatus.badge.false

export const getReaderBooksLabel = (hasBooks: boolean): string =>
  hasBooks ? booksStatus.label.true : booksStatus.label.false
