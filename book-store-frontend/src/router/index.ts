import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

declare module 'vue-router' {
  interface RouteMeta {
    requiresAuth?: boolean
    requiresAdmin?: boolean
    requiresGuest?: boolean
  }
}

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  scrollBehavior: () => ({ top: 0 }),
  routes: [
    // ── Public ──────────────────────────────────────────────
    {
      path: '/',
      component: () => import('@/layouts/PublicLayout.vue'),
      children: [
        {
          path: '',
          name: 'home',
          component: () => import('@/views/HomeView.vue'),
          meta: {
            breadcrumb: [{ text: 'Main' }],
          },
        },
        {
          path: 'catalog',
          name: 'catalog',
          component: () => import('@/views/CatalogView.vue'),
          meta: {
            breadcrumb: [{ text: 'Main', to: '/' }, { text: 'Catalog' }],
          },
        },
        {
          path: 'books/:id',
          name: 'book-detail',
          component: () => import('@/views/BookDetailView.vue'),
          props: (route) => ({ id: Number(route.params.id) }),
          meta: {
            breadcrumb: (route) => [
              { text: 'Main', to: '/' },
              { text: 'Catalog', to: '/catalog' },
              { text: `Book #${route.params.id}` },
            ],
          },
        },
        {
          path: 'search',
          name: 'search',
          component: () => import('@/views/SearchView.vue'),
          meta: {
            breadcrumb: [{ text: 'Main', to: '/' }, { text: 'Search' }],
          },
        },
      ],
    },

    // ── Guest-only ───────────────────────────────────────────
    {
      path: '/auth',
      component: () => import('@/layouts/AuthLayout.vue'),
      meta: { requiresGuest: true },
      children: [
        {
          path: 'login',
          name: 'login',
          component: () => import('@/views/auth/LoginView.vue'),
        },
        {
          path: 'register',
          name: 'register',
          component: () => import('@/views/auth/RegisterView.vue'),
        },
        {
          path: '/admin/login',
          name: 'admin-login',
          component: () => import('@/views/auth/AdminLoginView.vue'),
          meta: { requiresGuest: true },
        },
      ],
    },

    // ── Reader (authenticated) ───────────────────────────────
    {
      path: '/reader',
      component: () => import('@/layouts/PublicLayout.vue'),
      meta: { requiresAuth: true },
      children: [
        {
          path: 'reading-list',
          name: 'reading-list',
          component: () => import('@/views/reader/ReadingListView.vue'),
          meta: {
            breadcrumb: [{ text: 'Main', to: '/' }, { text: 'Reading List' }],
          },
        },
        {
          path: 'book/:id',
          name: 'reading-book-detail',
          component: () => import('@/views/reader/BookDetailView.vue'),
          meta: {
            breadcrumb: (route) => [
              { text: 'Main', to: '/' },
              { text: 'Reading List', to: '/reader/reading-list' },
              { text: `Book #${route.params.id}` },
            ],
          },
        },
        {
          path: 'cart',
          name: 'cart',
          component: () => import('@/views/reader/CartView.vue'),
          meta: {
            breadcrumb: [{ text: 'Main', to: '/' }, { text: 'Cart' }],
          },
        },
      ],
    },
    {
      path: '/reading/:bookId/read',
      component: () => import('@/layouts/ReadingLayout.vue'),
      meta: { requiresAuth: true },
      props: (route) => ({
        bookId: Number(route.params.bookId),
      }),
      children: [
        {
          path: ':chapterId',
          name: 'read-chapter',
          component: () => import('@/views/reading/BookChapterView.vue'),
          props: (route) => ({
            chapterId: Number(route.params.chapterId),
          }),
          children: [
            {
              path: ':pageId',
              name: 'read-page',
              component: () => import('@/views/reading/BookPageView.vue'),
              props: (route) => ({
                pageId: Number(route.params.pageId),
              }),
            },
          ],
        },
      ],
    },

    // ── Admin ────────────────────────────────────────────────
    {
      path: '/admin',
      component: () => import('@/layouts/AdminLayout.vue'),
      meta: { requiresAdmin: true },
      children: [
        {
          path: '',
          name: 'admin-dashboard',
          component: () => import('@/views/admin/DashboardView.vue'),
        },
        {
          path: 'readers',
          name: 'admin-readers',
          component: () => import('@/views/admin/ReadersView.vue'),
        },
        {
          path: 'readers/:id',
          name: 'admin-readers-show',
          component: () => import('@/views/admin/ReaderView.vue'),
          props: (route) => ({ id: Number(route.params.id) }),
        },
        {
          path: 'orders',
          name: 'admin-orders',
          component: () => import('@/views/admin/OrdersView.vue'),
        },
        {
          path: 'orders/:id',
          name: 'admin-orders-show',
          component: () => import('@/views/admin/OrderView.vue'),
          props: (route) => ({ id: Number(route.params.id) }),
        },
        {
          path: 'subscriptions',
          name: 'admin-subscriptions',
          component: () => import('@/views/admin/SubscriptionsView.vue'),
        },
        {
          path: 'subscriptions/:id',
          name: 'admin-subscriptions-show',
          component: () => import('@/views/admin/SubscriptionView.vue'),
          props: (route) => ({ id: Number(route.params.id) }),
        },
        {
          path: 'books',
          name: 'admin-books',
          component: () => import('@/views/admin/BooksView.vue'),
        },
        {
          path: 'books/create',
          name: 'admin-book-create',
          component: () => import('@/views/admin/BookFormView.vue'),
        },
        {
          path: 'books/:id/edit',
          name: 'admin-book-edit',
          component: () => import('@/views/admin/BookFormView.vue'),
          props: (route) => ({ id: Number(route.params.id) }),
        },
      ],
    },

    {
      path: '/admin/login',
      name: 'admin-login',
      component: () => import('@/views/auth/AdminLoginView.vue'),
      meta: { requiresGuest: true },
    },
    {
      path: '/payment/success',
      name: 'payment.success',
      component: () => import('@/views/payment/PaymentSuccess.vue'),
    },
    {
      path: '/payment/decline',
      name: 'payment.decline',
      component: () => import('@/views/payment/PaymentDecline.vue'),
    },

    // // ── Fallback ─────────────────────────────────────────────
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => import('@/views/NotFoundView.vue'),
    },
  ],
})

router.beforeEach((to) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }

  if (to.meta.requiresAdmin && (!auth.isAuthenticated || !auth.isAdmin)) {
    return { name: 'admin-login' }
  }

  if (to.meta.requiresGuest && auth.isAuthenticated) {
    return auth.isAdmin ? { name: 'admin-dashboard' } : { name: 'home' }
  }
})

export default router
