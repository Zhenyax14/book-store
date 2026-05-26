<script setup lang="ts">
import type { FontFamily, LineHeight, PaginationMode, Theme } from '@/types'

const fontOptions = [
  { name: 'Lora', value: 'Lora' as FontFamily, style: "'Lora', serif" },
  {
    name: 'Playfair Display',
    value: 'Playfair Display' as FontFamily,
    style: "'Playfair Display', serif",
  },
  { name: 'Georgia', value: 'Georgia' as FontFamily, style: 'Georgia, serif' },
]

const lineHeights = [
  { label: 'Small', value: '1.5' as LineHeight },
  { label: 'Normal', value: '1.8' as LineHeight },
  { label: 'Large', value: '2' as LineHeight },
]

const widths = [
  { label: 'Narrow', value: 60 },
  { label: 'Normal', value: 70 },
  { label: 'Wide', value: 90 },
]

const paginationModes = [
  { label: 'Page', value: 'page' as PaginationMode },
  { label: 'Scroll', value: 'scroll' as PaginationMode },
]

const wordsPerPageOptions = [
  { label: 'Small', value: 200 },
  { label: 'Normal', value: 300 },
  { label: 'Large', value: 400 },
]

const themes = [
  { value: 'light' as Theme, label: 'Light' },
  { value: 'dark' as Theme, label: 'Dark' },
]

defineProps<{
  theme: Theme
  fontSize: number
  fontFamily: FontFamily
  lineHeight: LineHeight
  pageWidth: number
  paginationMode: PaginationMode
  wordsPerPage: number
  isLoading?: boolean
}>()

const emit = defineEmits<{
  'update:theme': [Theme]
  'update:fontSize': [number]
  'update:fontFamily': [FontFamily]
  'update:lineHeight': [LineHeight]
  'update:pageWidth': [number]
  'update:paginationMode': [PaginationMode]
  'update:wordsPerPage': [number]
  save: []
  close: []
}>()

const updateFontSize = (size: number) => {
  if (size >= 14 && size <= 28) emit('update:fontSize', size)
}
</script>

<template>
  <div class="settings-panel" :class="{ open: true }">
    <div class="settings-panel__inner">
      <h3 class="settings-panel__title">Reading Settings</h3>

      <div class="settings-group">
        <label class="settings-label">Font Size</label>
        <div class="font-size-control">
          <button class="font-size-btn" @click="updateFontSize(fontSize - 1)">A−</button>
          <span class="font-size-value">{{ fontSize }}px</span>
          <button class="font-size-btn" @click="updateFontSize(fontSize + 1)">A+</button>
        </div>
      </div>

      <div class="settings-group">
        <label class="settings-label">Font Family</label>
        <div class="font-family-options">
          <button
            v-for="font in fontOptions"
            :key="font.value"
            class="font-family-btn"
            :class="{ active: fontFamily === font.value }"
            :style="{ fontFamily: font.style }"
            @click="emit('update:fontFamily', font.value)"
          >
            {{ font.name }}
          </button>
        </div>
      </div>

      <div class="settings-group">
        <label class="settings-label">Line Height</label>
        <div class="line-height-control">
          <button
            v-for="lh in lineHeights"
            :key="lh.value"
            class="lh-btn"
            :class="{ active: lineHeight === lh.value }"
            @click="emit('update:lineHeight', lh.value)"
          >
            {{ lh.label }}
          </button>
        </div>
      </div>

      <div class="settings-group">
        <label class="settings-label">Page Width</label>
        <div class="width-control">
          <button
            v-for="w in widths"
            :key="w.value"
            class="width-btn"
            :class="{ active: pageWidth === w.value }"
            @click="emit('update:pageWidth', w.value)"
          >
            {{ w.label }}
          </button>
        </div>
      </div>

<!--      <div class="settings-group">-->
<!--        <label class="settings-label">Pagination Mode</label>-->
<!--        <div class="line-height-control">-->
<!--          <button-->
<!--            v-for="mode in paginationModes"-->
<!--            :key="mode.value"-->
<!--            class="lh-btn"-->
<!--            :class="{ active: paginationMode === mode.value }"-->
<!--            @click="emit('update:paginationMode', mode.value)"-->
<!--          >-->
<!--            {{ mode.label }}-->
<!--          </button>-->
<!--        </div>-->
<!--      </div>-->

<!--      <div class="settings-group">-->
<!--        <label class="settings-label">Words per Page</label>-->
<!--        <div class="line-height-control">-->
<!--          <button-->
<!--            v-for="wp in wordsPerPageOptions"-->
<!--            :key="wp.value"-->
<!--            class="lh-btn"-->
<!--            :class="{ active: wordsPerPage === wp.value }"-->
<!--            @click="emit('update:wordsPerPage', wp.value)"-->
<!--          >-->
<!--            {{ wp.label }}-->
<!--          </button>-->
<!--        </div>-->
<!--      </div>-->

      <div class="settings-group">
        <label class="settings-label">Color Theme</label>
        <div class="theme-options">
          <button
            v-for="t in themes"
            :key="t.value"
            class="theme-btn"
            :class="{ active: theme === t.value }"
            @click="emit('update:theme', t.value)"
          >
            <span class="theme-btn__swatch" :class="`theme-btn__swatch--${t.value}`"></span>
            <span>{{ t.label }}</span>
          </button>
        </div>
      </div>

      <div class="settings-actions">
        <button class="btn btn--primary" :disabled="isLoading" @click="emit('save')">
          {{ isLoading ? 'Saving...' : 'Save' }}
        </button>
      </div>
    </div>
  </div>
</template>

<style></style>
