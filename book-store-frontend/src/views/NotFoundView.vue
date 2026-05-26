<script setup lang="ts">
import { RouterLink } from 'vue-router'
</script>

<template>
  <div class="not-found">
    <h1>404</h1>
    <p>Page not found.</p>
    <RouterLink :to="{ name: 'home' }">← Back to Home</RouterLink>
  </div>
</template>

<style scoped>
.not-found {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  text-align: center;
}

.not-found h1 {
  font-size: 5rem;
  font-weight: 800;
  color: #d1d5db;
}

.not-found p {
  color: #6b7280;
  font-size: 1.1rem;
}
</style>
