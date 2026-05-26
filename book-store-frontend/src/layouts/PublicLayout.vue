<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import AppBreadcrumbs from '@/components/ui/AppBreadcrumbs.vue'
import { onMounted } from 'vue'

const auth = useAuthStore()
const cart = useCartStore()

onMounted(() => cart.fetchCart())
</script>

<template>
  <header class="header">
    <div class="header__inner container">

      <a href="/" class="logo text-decoration-none">
        <span class="logo__book">Book</span>
        <span class="logo__shop">store</span>
      </a>

      <nav class="nav">
        <a href="/" class="nav__link">Catalog</a>
        <RouterLink :to="{ name: 'search' }" class="nav__link" active-class="nav__link--active">
          Search
        </RouterLink>
      </nav>

      <div class="header__actions">
        <template v-if="auth.isAuthenticated">
          <RouterLink :to="{ name: 'reading-list' }" class="header__wishlist">
            My Books
          </RouterLink>

          <RouterLink :to="{ name: 'cart' }" class="cart-btn">
            <div class="cart-btn__icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="1.8">
                <circle cx="9" cy="21" r="1"/>
                <circle cx="20" cy="21" r="1"/>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
              </svg>
              <span v-if="cart.itemsCount > 0" class="cart-btn__count">{{ cart.itemsCount }}</span>
            </div>
            <div class="cart-btn__info">
              <span class="cart-btn__label">Cart</span>
              <span v-if="cart.itemsCount > 0" class="cart-btn__total">
                {{ cart.total?.formatted }}
              </span>
            </div>
          </RouterLink>

          <button class="header__logout" @click="auth.logout()">Logout</button>
        </template>

        <template v-else>
          <RouterLink :to="{ name: 'login' }" class="nav__link">Login</RouterLink>
          <RouterLink :to="{ name: 'register' }" class="nav__link">Register</RouterLink>
        </template>
      </div>

    </div>
  </header>

  <AppBreadcrumbs />

  <main class="main container">
    <RouterView />
  </main>

  <footer class="footer">
    <div class="footer__inner container">
      <div>
        <a href="/" class="footer__logo logo text-decoration-none">
          <span class="logo__book">Book</span><span class="logo__shop">store</span>
        </a>
        <p class="footer__desc">
          Your trusted bookstore since 2010. Thousands of titles, fair prices, and fast shipping across Spain.
        </p>
      </div>

      <div>
        <h4 class="footer__heading">Categories</h4>
        <ul class="footer__links">
          <li><a href="/">Fiction</a></li>
          <li><a href="/">Science Fiction</a></li>
          <li><a href="/">Children</a></li>
          <li><a href="/">Classics</a></li>
        </ul>
      </div>

      <div>
        <h4 class="footer__heading">Information</h4>
        <ul class="footer__links">
          <li><a href="#">About us</a></li>
          <li><a href="#">Returns</a></li>
          <li><a href="#">Privacy</a></li>
        </ul>
      </div>

      <div>
        <h4 class="footer__heading">Contact</h4>
        <ul class="footer__contact">
          <li>📍 Calle Libros 12, Madrid</li>
          <li>📞 900 000 000</li>
          <li>✉️ hello@bookshop.es</li>
        </ul>
      </div>
    </div>

    <div class="footer__bottom">
      <p>© {{ new Date().getFullYear() }} BookShop. All rights reserved.</p>
    </div>
  </footer>
</template>

<style>
/* styles loaded globally via main.scss */

html, body, #app {
  min-height: 100vh;
}

body, #app {
  display: flex;
  flex-direction: column;
}

.main {
  flex: 1;
  padding-top: 2rem;
  padding-bottom: 3rem;
}
</style>
