<script setup lang="ts">
import { computed } from 'vue'
import { Line } from 'vue-chartjs'
import type { AppChartConfig } from '@/types/chart'

const props = defineProps<{
  config: AppChartConfig<'line'>
}>()

const component = computed(() => {
  switch (props.config.type) {
    case 'line':
      return Line
    default:
      return Line
  }
})
</script>

<template>
  <component :is="component" :data="config.data" :options="config.options" />
</template>
