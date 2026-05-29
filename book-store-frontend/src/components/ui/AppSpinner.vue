<script setup lang="ts"></script>

<template>
  <div class="loader" role="status" aria-label="Loading">
    <div class="loader__ring"></div>
    <div class="loader__ring loader__ring--delay"></div>
  </div>
</template>

<style scoped>
.loader {
  position: relative;
  width: 40px;
  height: 40px;
}

.loader__ring {
  position: absolute;
  inset: 0;
  border-radius: 50%;
  border: 2px solid transparent;

  border-top-color: var(--accent);
  border-right-color: var(--accent);

  animation: spin 0.9s linear infinite;
}

.loader__ring--delay {
  border-top-color: var(--accent-dk);
  border-right-color: transparent;

  animation-duration: 1.2s;
  animation-direction: reverse;
  opacity: 0.7;
}

.loader::after {
  content: '';
  position: absolute;
  inset: 0;
  border-radius: 50%;

  background: radial-gradient(circle, var(--accent-glow) 0%, transparent 70%);
  filter: blur(6px);
  z-index: -1;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
