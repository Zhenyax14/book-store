<script setup lang="ts">
defineProps<{
  progress: number
  isMobile?: boolean
}>()

</script>

<template>
  <div class="progress-bar">
    <div class="progress-bar__fill" :style="{ width: `${progress}%` }"></div>
    <span v-if="!isMobile" class="progress-bar__label">{{ Math.round(progress) }}%</span>
  </div>
</template>

<style></style>
