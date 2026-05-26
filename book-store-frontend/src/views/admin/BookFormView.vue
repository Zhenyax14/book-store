<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { booksApi } from '@/api/books'
import { HttpError } from '@/api/client'
import AppSpinner from '@/components/ui/AppSpinner.vue'
import type { AccessType, BookStatus } from '@/types'
import { useToastStore } from '@/stores/toast'

const toast = useToastStore()

const props = defineProps<{ id?: number }>()

const router = useRouter()
const isEdit = computed(() => props.id !== undefined)

const title = ref('')
const description = ref('')
const isbn = ref('')
const language = ref('en')
const publisher = ref('')
const publishedYear = ref<number | null>(null)
const accessType = ref<AccessType>('free')
const price = ref(0)
const currency = ref<'USD' | 'EUR'>('EUR')
const status = ref<BookStatus>('draft')

const fieldErrors = ref<Record<string, string[]>>({})
const isLoading = ref(false)
const isFetching = ref(false)

onMounted(async () => {
  if (!isEdit.value) return
  isFetching.value = true
  try {
    const book = await booksApi.show(props.id!)
    title.value = book.title
    description.value = book.description ?? ''
    isbn.value = book.isbn ?? ''
    language.value = book.language
    publisher.value = book.publisher ?? ''
    publishedYear.value = book.published_year
    accessType.value = book.access_type
    price.value = book.price.amount
    currency.value = book.price.currency as 'USD' | 'EUR'
    status.value = book.status
  } finally {
    isFetching.value = false
  }
})

async function handleSubmit(): Promise<void> {
  fieldErrors.value = {}
  isLoading.value = true

  const payload = {
    title: title.value,
    description: description.value || null,
    isbn: isbn.value || null,
    language: language.value,
    publisher: publisher.value || null,
    published_year: publishedYear.value,
    access_type: accessType.value,
    price: price.value,
    currency: currency.value,
  }

  try {
    if (isEdit.value) {
      await booksApi.update(props.id!, payload)
      toast.success('Success', 'Book updated successfully')
    } else {
      await booksApi.create(payload)
      toast.success('Success', 'Book created successfully')
    }
    await router.push({ name: 'admin-books' })
  } catch (e) {
    let message = 'Something went wrong. Please try again.'
    if (e instanceof HttpError) {
      if (e.body?.message) message = e.body.message
      if (e.body?.errors) fieldErrors.value = e.body.errors
    }
    toast.error('Error', message)
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="page">
    <!-- Header -->
    <div class="page-header">
      <div>
        <h1 class="page-header__title">{{ isEdit ? 'Edit Book' : 'New Book' }}</h1>
        <p class="page-header__sub">
          {{
            isEdit
              ? 'Update the book details below'
              : 'Fill in the details to add a new book to the catalogue'
          }}
        </p>
      </div>
      <div class="page-header__actions">
        <button class="btn-secondary" type="button" @click="router.back()">
          <svg
            width="13"
            height="13"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <polyline points="15 18 9 12 15 6" />
          </svg>
          Back
        </button>
      </div>
    </div>

    <AppSpinner v-if="isFetching" />

    <form v-else @submit.prevent="handleSubmit">
      <!-- Main card -->
      <div class="table-wrap">
        <!-- Section: Basic info -->
        <div class="form-section">
          <span class="form-section__label">Basic Information</span>

          <div class="form-grid">
            <div class="form-field" :class="{ 'has-error': fieldErrors.title }">
              <label class="form-label">Title <span class="form-req">*</span></label>
              <input
                v-model="title"
                type="text"
                class="form-input"
                placeholder="Book title"
                required
              />
              <span v-if="fieldErrors.title" class="form-error-msg">{{
                fieldErrors.title[0]
              }}</span>
            </div>

            <div class="form-field" :class="{ 'has-error': fieldErrors.language }">
              <label class="form-label">Language <span class="form-req">*</span></label>
              <input
                v-model="language"
                type="text"
                class="form-input"
                maxlength="2"
                placeholder="en"
                required
              />
              <span v-if="fieldErrors.language" class="form-error-msg">{{
                fieldErrors.language[0]
              }}</span>
            </div>

            <div class="form-field" :class="{ 'has-error': fieldErrors.isbn }">
              <label class="form-label">ISBN</label>
              <input
                v-model="isbn"
                type="text"
                class="form-input"
                placeholder="978-3-16-148410-0"
              />
              <span v-if="fieldErrors.isbn" class="form-error-msg">{{ fieldErrors.isbn[0] }}</span>
            </div>

            <div class="form-field" :class="{ 'has-error': fieldErrors.publisher }">
              <label class="form-label">Publisher</label>
              <input
                v-model="publisher"
                type="text"
                class="form-input"
                placeholder="Publisher name"
              />
              <span v-if="fieldErrors.publisher" class="form-error-msg">{{
                fieldErrors.publisher[0]
              }}</span>
            </div>

            <div class="form-field" :class="{ 'has-error': fieldErrors.published_year }">
              <label class="form-label">Published Year</label>
              <input
                v-model.number="publishedYear"
                type="number"
                class="form-input"
                min="1000"
                max="2100"
                placeholder="2024"
              />
              <span v-if="fieldErrors.published_year" class="form-error-msg">{{
                fieldErrors.published_year[0]
              }}</span>
            </div>
          </div>

          <div
            class="form-field form-field--full"
            :class="{ 'has-error': fieldErrors.description }"
          >
            <label class="form-label">Description</label>
            <textarea
              v-model="description"
              class="form-input form-textarea"
              rows="4"
              placeholder="A short summary of the book…"
            />
            <span v-if="fieldErrors.description" class="form-error-msg">{{
              fieldErrors.description[0]
            }}</span>
          </div>
        </div>

        <div class="form-divider" />

        <!-- Section: Pricing & access -->
        <div class="form-section">
          <span class="form-section__label">Pricing &amp; Access</span>

          <div class="form-grid">
            <div class="form-field" :class="{ 'has-error': fieldErrors.access_type }">
              <label class="form-label">Access Type <span class="form-req">*</span></label>
              <select v-model="accessType" class="form-select">
                <option value="free">Free</option>
                <option value="purchase">Purchase</option>
                <option value="subscription">Subscription</option>
              </select>
              <span v-if="fieldErrors.access_type" class="form-error-msg">{{
                fieldErrors.access_type[0]
              }}</span>
            </div>

            <div class="form-field" :class="{ 'has-error': fieldErrors.price }">
              <label class="form-label">Price (cents) <span class="form-req">*</span></label>
              <input
                v-model.number="price"
                type="number"
                class="form-input"
                min="0"
                placeholder="1990"
                required
              />
              <span v-if="fieldErrors.price" class="form-error-msg">{{
                fieldErrors.price[0]
              }}</span>
            </div>

            <div class="form-field" :class="{ 'has-error': fieldErrors.currency }">
              <label class="form-label">Currency <span class="form-req">*</span></label>
              <select v-model="currency" class="form-select">
                <option value="EUR">EUR</option>
                <option value="USD">USD</option>
              </select>
              <span v-if="fieldErrors.currency" class="form-error-msg">{{
                fieldErrors.currency[0]
              }}</span>
            </div>
          </div>

          <!-- Price preview pill -->
          <div v-if="price > 0 && !fieldErrors.price" class="price-preview">
            <span class="price-preview__label">Preview</span>
            <span class="price-preview__value">
              {{ (price / 100).toFixed(2) }} {{ currency }}
            </span>
            <span
              class="badge"
              :class="
                accessType === 'free'
                  ? 'badge-teal'
                  : accessType === 'subscription'
                    ? 'badge-purple'
                    : 'badge-blue'
              "
            >
              {{ accessType }}
            </span>
          </div>
        </div>
      </div>
      <!-- /table-wrap -->

      <!-- Footer actions -->
      <div class="form-footer">
        <p class="form-footer__hint"><span class="form-req">*</span> Required fields</p>
        <div class="form-footer__actions">
          <button type="button" class="btn-secondary" @click="router.back()">Cancel</button>
          <button type="submit" class="btn-primary" :disabled="isLoading">
            <svg v-if="isLoading" class="btn-spinner" viewBox="0 0 24 24" fill="none">
              <circle
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="2"
                stroke-dasharray="60"
                stroke-dashoffset="20"
                stroke-linecap="round"
              />
            </svg>
            <svg
              v-else
              width="13"
              height="13"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2.5"
            >
              <polyline points="20 6 9 17 4 12" />
            </svg>
            {{ isLoading ? 'Saving…' : isEdit ? 'Update Book' : 'Create Book' }}
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<style scoped>
/* ── Page wrapper — same as BooksView ─────────────────── */
.page {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* ── Section block inside the card ───────────────────── */
.form-section {
  padding: 24px 28px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-section__label {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: var(--text-muted);
}

.form-divider {
  height: 1px;
  background: var(--border);
}

/* ── Grid ─────────────────────────────────────────────── */
.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px 24px;
}

.form-field--full {
  grid-column: 1 / -1;
}

/* ── Field ────────────────────────────────────────────── */
.form-field {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-label {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: var(--text-muted);
}

.form-req {
  color: var(--accent);
  margin-left: 2px;
}

.form-input,
.form-select {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  color: var(--text);
  padding: 10px 14px;
  border-radius: var(--radius);
  font-size: 0.88rem;
  outline: none;
  font-family: 'Barlow', sans-serif;
  transition:
    border-color var(--transition),
    box-shadow var(--transition);
  width: 100%;
}

.form-input::placeholder {
  color: var(--text-dim);
}

.form-input:focus,
.form-select:focus {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(232, 160, 32, 0.1);
}

.has-error .form-input,
.has-error .form-select {
  border-color: var(--red);
  box-shadow: 0 0 0 3px rgba(255, 102, 102, 0.08);
}

.form-textarea {
  resize: vertical;
  min-height: 96px;
  line-height: 1.55;
}

.form-select {
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23888880' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
  padding-right: 36px;
}

.form-error-msg {
  font-size: 0.73rem;
  color: var(--red);
  margin-top: 2px;
}

/* ── Price preview pill ───────────────────────────────── */
.price-preview {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 6px 14px;
  align-self: flex-start;
}

.price-preview__label {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--text-muted);
}

.price-preview__value {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.92rem;
  font-weight: 700;
  color: var(--text);
}

/* badge reused from admin.css */
.badge-teal {
  background: rgba(62, 207, 142, 0.1);
  color: var(--green);
}

/* ── Footer bar ───────────────────────────────────────── */
.form-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 4px 0;
  margin-top: 10px;
}

.form-footer__hint {
  font-size: 0.78rem;
  color: var(--text-dim);
}

.form-footer__actions {
  display: flex;
  gap: 10px;
}

/* ── Spinner inside button ────────────────────────────── */
.btn-spinner {
  width: 14px;
  height: 14px;
  animation: spin 0.8s linear infinite;
  color: rgba(255, 255, 255, 0.7);
  flex-shrink: 0;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
