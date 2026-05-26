<script setup lang="ts">
import { onMounted } from 'vue'
import { useCartStore } from '@/stores/cart'
import AppSpinner from '@/components/ui/AppSpinner.vue'

const cart = useCartStore()

onMounted(() => cart.fetchCart())

async function handleCheckout(): Promise<void> {
  const url = await cart.checkout('EUR')
  if (url) window.location.href = url
}
</script>

<template>
  <div class="content">
    <section class="section">
      <div class="section__header">
        <h2 class="section__title">CART</h2>
        <div class="section__line"></div>
      </div>
    </section>

    <AppSpinner v-if="cart.isLoading" />

    <template v-else-if="cart.cart">
      <p v-if="!cart.cart.items.length" class="empty">Your cart is empty.</p>

      <template v-else>
        <ul class="cart-items">
          <li
            v-for="item in cart.cart.items"
            :key="`${item.type}-${item.reference_id}`"
            class="cart-item"
          >
            <div class="cart-item__info">
              <strong>{{ item.title }}</strong>
              <span class="cart-item__type">{{ item.type }}</span>
            </div>
            <span class="cart-item__price">{{ item.price.formatted }}</span>
            <button
              class="cart-item__remove"
              @click="cart.removeItem(item.type, item.reference_id)"
            >
              ✕
            </button>
          </li>
        </ul>

        <div class="cart-total">
          <span>Total</span>
          <strong>{{ cart.cart.total.formatted }}</strong>
        </div>

        <button class="btn-checkout" :disabled="cart.isLoading" @click="handleCheckout">
          Checkout
        </button>
      </template>
    </template>
  </div>
</template>

<style scoped>
.page-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
}

.cart-items {
  list-style: none;
  padding: 0;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  overflow: hidden;
  margin-bottom: 1rem;
}

.cart-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
}

.cart-item + .cart-item {
  border-top: 1px solid #e5e7eb;
}

.cart-item__info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
}

.cart-item__type {
  font-size: 0.8rem;
  color: #6b7280;
  text-transform: capitalize;
}

.cart-item__price {
  font-weight: 600;
}

.cart-item__remove {
  background: none;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  font-size: 0.9rem;
  padding: 0.25rem;
}

.cart-total {
  display: flex;
  justify-content: space-between;
  padding: 1rem;
  font-size: 1.1rem;
  border-top: 2px solid #111827;
}

.btn-checkout {
  width: 100%;
  padding: 0.75rem;
  background: #4f46e5;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  margin-top: 0.5rem;
}

.btn-checkout:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.empty {
  color: #6b7280;
}
</style>
