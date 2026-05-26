<script setup lang="ts">
const searchQuery = defineModel<string>('query', {
  required: true,
  default: '',
})

defineEmits<{
  close: []
  search: []
}>()
</script>

<template>
  <div class="search-panel" :class="{ open: true }">
    <div class="search-panel__inner">
      <input
        v-model="searchQuery"
        type="text"
        class="search-panel__input"
        placeholder="Search in the book..."
        @keyup.enter="$emit('search')"
      />
      <button class="search-panel__close" @click="$emit('close')">×</button>
    </div>
    <div class="search-panel__results">
      <p v-if="!searchQuery" class="search-panel__hint">
        Type to search for words or phrases in the text
      </p>
    </div>
  </div>
</template>

<style></style>
