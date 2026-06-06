<script setup lang="ts">
import { useRouter } from 'vue-router'
const router = useRouter()
function goToStore(): void {
  window.location.href = '/'
}
</script>
<template>
  <div style="text-align: center; padding: 4rem 2rem">
    <p style="font-size: 4rem">❌</p>
    <h1>Pago no completado</h1>
    <p>No se realizó ningún cargo. Puedes intentarlo de nuevo.</p>
    <button class="btn-primary" @click="goToStore">
      Volver a la tienda
    </button>
  </div>
</template>

<style scoped>
.btn-primary {
  background: #e8a020;
  color: #fff;
  border: none;
  padding: 10px 24px;
  border-radius: 3px;
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.88rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  cursor: pointer;
  transition: background 0.25s ease;
  margin-top: 16px;
}
.btn-primary:hover {
  background: #c88010;
}
</style>
