<script setup lang="ts">
defineProps<{
  chapterNumber: number
  chapterTitle: string
}>()
</script>

<template>
  <div class="chapter-header">
    <div class="chapter-header__ornament">❧</div>
    <h1 class="chapter-header__number">Chapter {{ chapterNumber }}</h1>
    <h2 class="chapter-header__title">{{ chapterTitle }}</h2>
    <div class="chapter-header__line">
      <span></span>
      <span class="chapter-header__diamond">◆</span>
      <span></span>
    </div>
  </div>
</template>

<style></style>
