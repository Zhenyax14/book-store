<script setup lang="ts">
import { useToastStore } from '@/stores/toast'
import ToastItem from './ToastItem.vue'

const { toasts, remove } = useToastStore()
</script>

<template>
  <Teleport to="body">
    <div class="toast-container" aria-label="Notifications" role="region">
      <TransitionGroup name="toast" tag="div" class="toast-list">
        <ToastItem v-for="toast in toasts" :key="toast.id" :toast="toast" @dismiss="remove" />
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<style scoped>
.toast-container {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  z-index: 9999;
  pointer-events: none;
}

.toast-list {
  display: flex;
  flex-direction: column-reverse;
  gap: 0.625rem;
  pointer-events: auto;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(1.5rem) scale(0.96);
}
.toast-enter-active {
  transition:
    opacity 0.28s cubic-bezier(0.16, 1, 0.3, 1),
    transform 0.28s cubic-bezier(0.16, 1, 0.3, 1);
}
.toast-enter-to {
  opacity: 1;
  transform: translateX(0) scale(1);
}

.toast-leave-from {
  opacity: 1;
  transform: translateX(0);
}
.toast-leave-active {
  transition:
    opacity 0.2s ease,
    transform 0.2s ease;
  pointer-events: none;
}
.toast-leave-to {
  opacity: 0;
  transform: translateX(1.5rem);
}

@media (max-width: 480px) {
  .toast-container {
    right: 1rem;
    left: 1rem;
    bottom: 1rem;
  }

  .toast-list :deep(.toast) {
    width: 100%;
  }
}
</style>
