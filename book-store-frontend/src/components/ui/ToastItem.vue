<script setup lang="ts">
import { computed } from 'vue'
import type { Toast } from '@/types/toast'

const props = defineProps<{ toast: Toast }>()
const emit = defineEmits<{ dismiss: [id: string] }>()

const icons: Record<Toast['type'], string> = {
  success: '✓',
  error: '✕',
  warning: '!',
  info: 'i',
}

const icon = computed(() => icons[props.toast.type])
const hasDuration = computed(() => props.toast.duration > 0)
</script>

<template>
  <div :class="['toast', `toast--${toast.type}`]" role="status" aria-live="polite">
    <div class="toast__icon-wrap">
      <span class="toast__icon">{{ icon }}</span>
    </div>

    <div class="toast__body">
      <p class="toast__title">{{ toast.title }}</p>
      <p v-if="toast.message" class="toast__message">{{ toast.message }}</p>
    </div>

    <button class="toast__close" aria-label="Dismiss" @click="emit('dismiss', toast.id)">
      <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
        <path
          d="M1 1l10 10M11 1L1 11"
          stroke="currentColor"
          stroke-width="1.5"
          stroke-linecap="round"
        />
      </svg>
    </button>

    <div v-if="hasDuration" class="toast__progress" />
  </div>
</template>

<style scoped>
.toast {
  --_accent: var(--toast-accent);
  --_bg: var(--toast-bg);
  --_dur: v-bind('toast.duration + "ms"');

  position: relative;
  display: grid;
  grid-template-columns: 2rem 1fr auto;
  align-items: start;
  gap: 0.75rem;
  width: 22rem;
  padding: 1rem 1rem 1.25rem;
  background: var(--_bg);
  border: 1px solid #e8e2d9;
  border-left: 3px solid var(--_accent);
  border-radius: 2px;
  box-shadow:
    0 4px 24px rgba(0, 0, 0, 0.08),
    0 1px 4px rgba(0, 0, 0, 0.04);
  overflow: hidden;
  cursor: default;
  transition: box-shadow 0.2s ease;
}

.toast:hover {
  box-shadow:
    0 8px 32px rgba(0, 0, 0, 0.12),
    0 2px 8px rgba(0, 0, 0, 0.06);
}

.toast--success {
  --toast-accent: #4a7c59;
  --toast-bg: #f0f5f1;
}
.toast--error {
  --toast-accent: #b84c4c;
  --toast-bg: #fdf0f0;
}
.toast--warning {
  --toast-accent: #b8843a;
  --toast-bg: #fdf6ec;
}
.toast--info {
  --toast-accent: #4a6b8a;
  --toast-bg: #f0f4f8;
}

.toast__icon-wrap {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2rem;
  height: 2rem;
  background: var(--_accent);
  border-radius: 50%;
  flex-shrink: 0;
}

.toast__icon {
  font-family: 'Georgia', serif;
  font-size: 0.9rem;
  font-weight: 700;
  color: #fff;
  line-height: 1;
}

.toast__body {
  padding-top: 0.1rem;
  min-width: 0;
}

.toast__title {
  font-family: 'Georgia', 'Times New Roman', serif;
  font-size: 0.875rem;
  font-weight: 700;
  color: #2c2416;
  line-height: 1.3;
  margin: 0;
  letter-spacing: 0.01em;
}

.toast__message {
  font-family: 'Palatino Linotype', 'Palatino', serif;
  font-size: 0.8rem;
  color: #6b5f4e;
  line-height: 1.5;
  margin: 0.3rem 0 0;
}

.toast__close {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 1.5rem;
  height: 1.5rem;
  border: none;
  background: transparent;
  color: #a09080;
  border-radius: 2px;
  cursor: pointer;
  flex-shrink: 0;
  transition:
    color 0.15s ease,
    background 0.15s ease;
  margin-top: -0.15rem;
}

.toast__close:hover {
  color: #2c2416;
  background: rgba(0, 0, 0, 0.06);
}

.toast__progress {
  position: absolute;
  bottom: 0;
  left: 0;
  height: 2px;
  width: 100%;
  background: var(--_accent);
  opacity: 0.35;
  transform-origin: left;
  animation: toast-progress var(--_dur) linear forwards;
}

@keyframes toast-progress {
  from {
    transform: scaleX(1);
  }
  to {
    transform: scaleX(0);
  }
}
</style>
