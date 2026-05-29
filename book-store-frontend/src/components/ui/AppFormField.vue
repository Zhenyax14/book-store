<script setup lang="ts">
defineProps<{
  label: string
  errors?: string[]
}>()
</script>

<template>
  <div class="form-field">
    <label class="form-field__label">{{ label }}</label>
    <div class="form-field__control">
      <slot />
    </div>
    <ul v-if="errors?.length" class="form-field__errors">
      <li v-for="error in errors" :key="error">{{ error }}</li>
    </ul>
  </div>
</template>

<style scoped>
.form-field {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  margin-bottom: 1rem;
}

.form-field__label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
}

.form-field__control :deep(input),
.form-field__control :deep(select),
.form-field__control :deep(textarea) {
  width: 100%;
  padding: 0.6rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.95rem;
  outline: none;
  transition: border-color 0.15s;
  box-sizing: border-box;
}

.form-field__control :deep(input:focus),
.form-field__control :deep(select:focus),
.form-field__control :deep(textarea:focus) {
  border-color: #4f46e5;
}

.form-field__errors {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 0.15rem;
}

.form-field__errors li {
  font-size: 0.8rem;
  color: #ef4444;
}
</style>
