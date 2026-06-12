<script setup lang="ts">
import { RouterLink, RouterView, useRouter } from 'vue-router'
import '@/assets/admin.scss'
import { useAuthStore } from '@/stores/auth'
import { computed } from 'vue'

const auth = useAuthStore()
const router = useRouter()

const initials = computed(() => {
  return (
    auth.user?.name
      .split(' ')
      .map((w) => w[0])
      .join('')
      .slice(0, 2)
      .toUpperCase() ?? '?'
  )
})

async function handleLogout(): Promise<void> {
  await auth.adminLogout()
  await router.push({ name: 'admin-login' })
}
</script>

<template>
  <div class="admin-layout">
  <aside class="sidebar">
    <div class="sidebar__logo">
      <span class="sidebar__logo-text"><em>Book</em>shop</span>
      <span class="sidebar__badge">Admin</span>
    </div>

    <nav class="sidebar__nav">
      <RouterLink
        class="nav-item"
        :to="{ name: 'admin-dashboard' }"
        active-class=""
        exact-active-class="active"
      >
        <svg
          class="nav-item__icon"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <rect x="3" y="3" width="7" height="7" rx="1" />
          <rect x="14" y="3" width="7" height="7" rx="1" />
          <rect x="3" y="14" width="7" height="7" rx="1" />
          <rect x="14" y="14" width="7" height="7" rx="1" />
        </svg>
        Dashboard
      </RouterLink>

      <span class="sidebar__section-label">Catalog</span>

      <RouterLink
        class="nav-item"
        :to="{ name: 'admin-books' }"
        active-class=""
        exact-active-class="active"
      >
        <svg
          class="nav-item__icon"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
          <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
        </svg>
        Books
      </RouterLink>

      <span class="sidebar__section-label">Users</span>

      <RouterLink
        class="nav-item"
        :to="{ name: 'admin-readers' }"
        active-class=""
        exact-active-class="active"
      >
        <svg
          class="nav-item__icon"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
          <circle cx="9" cy="7" r="4" />
          <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
          <path d="M16 3.13a4 4 0 0 1 0 7.75" />
        </svg>
        Readers
      </RouterLink>

      <span class="sidebar__section-label">Payments</span>

      <RouterLink
        class="nav-item"
        :to="{ name: 'admin-orders' }"
        active-class=""
        exact-active-class="active"
      >
        <svg
          class="nav-item__icon"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
          <line x1="3" y1="6" x2="21" y2="6" />
          <path d="M16 10a4 4 0 0 1-8 0" />
        </svg>
        Orders
      </RouterLink>

      <RouterLink
        class="nav-item"
        :to="{ name: 'admin-subscriptions' }"
        active-class=""
        exact-active-class="active"
      >
        <svg
          class="nav-item__icon"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
          <line x1="3" y1="6" x2="21" y2="6" />
          <path d="M16 10a4 4 0 0 1-8 0" />
        </svg>
        Subscriptions
      </RouterLink>
    </nav>

    <div class="sidebar__footer">
      <div class="sidebar__user">
        <div class="sidebar__avatar">{{ initials }}</div>
        <div>
          <div class="sidebar__user-name">{{ auth.user?.name }}</div>
          <div class="sidebar__user-role">Administrator</div>
        </div>
        <button class="sidebar__logout" @click="handleLogout">
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            width="16"
            height="16"
          >
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <polyline points="16 17 21 12 16 7" />
            <line x1="21" y1="12" x2="9" y2="12" />
          </svg>
        </button>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="content">
      <RouterView />
    </div>
  </div>
  </div>
</template>

<style>
@import '@/assets/admin.scss';
</style>
