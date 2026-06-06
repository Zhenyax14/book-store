<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useReadingStore } from '@/stores/reading'
import { useAuthStore } from '@/stores/auth'
import AppSpinner from '@/components/ui/AppSpinner.vue'

type Chapter = {
  id: number
  number: number
  pageIds: number[] | string[]
  title: string
  bookId?: number
  slug?: string
}

const route = useRoute()
const router = useRouter()

const readingStore = useReadingStore()
const auth = useAuthStore()

const bookId = computed(() => Number(route.params.id))
const currentBook = computed(() => readingStore.currentBook)

const totalChapters = computed(() => currentBook.value?.chapters?.length ?? 0)

const totalPages = computed(() => {
  if (!currentBook.value?.chapters) return 0
  return currentBook.value.chapters.reduce(
    (acc: number, chapter: Chapter) => acc + (chapter.pageIds?.length ?? 0),
    0,
  )
})

const currentBookmark = computed(() => currentBook.value?.bookmark)

const currentChapterNumber = computed(() => {
  if (!currentBookmark.value || !currentBook.value?.chapters) return 1
  const chapter = currentBook.value.chapters.find(
    (c: Chapter) => c.id === currentBookmark.value!.chapterId,
  )
  return chapter?.number ?? 1
})

const readRoute = computed(() => {
  const chapters = currentBook.value?.chapters
  if (!chapters?.length) return null

  const lastPos = readingStore.currentProgress?.last_position
  const bookmark = currentBookmark.value
  const firstChapter = chapters[0]
  if (!firstChapter) return null

  const chapterId: number = lastPos?.chapter_id
    ?? bookmark?.chapterId
    ?? firstChapter.id

  const pageId: number = lastPos?.page_id
    ?? bookmark?.pageId
    ?? Number(firstChapter.pageIds?.[0] ?? 0)

  if (!chapterId || !pageId) return null

  return {
    name: 'read-page',
    params: { bookId: bookId.value, chapterId, pageId },
  }
})
const activeTab = ref<'desc' | 'chapters'>('desc')

onMounted(async () => {
  await Promise.all([
    readingStore.fetchBook(bookId.value),
    readingStore.fetchProgress(bookId.value),
  ])
})

async function handleRemoveFromList(): Promise<void> {
  if (!auth.isAuthenticated) {
    await router.push({ name: 'login' })
    return
  }
  await readingStore.removeFromList(bookId.value)
}

// Toast
const showToast = ref(false)
const toastMessage = ref('')

function triggerToast(msg: string) {
  toastMessage.value = msg
  showToast.value = true
  setTimeout(() => {
    showToast.value = false
  }, 2800)
}

function handleReadClick() {
  triggerToast('📖 Opening book...')
}
</script>

<template>
  <div class="book-detail">
    <AppSpinner v-if="readingStore.isLoading" />

    <div v-else-if="currentBook" class="book-detail__wrapper">
      <!-- HERO SECTION -->
      <section class="book-hero">
        <div class="container book-hero__inner">
          <!-- 3D Cover -->
          <div class="book-cover-col">
            <div class="book-cover-3d">
              <div class="book-spine">
                <span>{{ currentBook.title }}</span>
              </div>
              <div class="book-front">
                <div class="book-front__genre">IN YOUR LIST</div>
                <div class="book-front__title">{{ currentBook.title }}</div>
                <div class="book-front__author">Reading in progress</div>

                <div class="book-front__art">
                  <div class="generic-book-art">
                    <div class="generic-book-art__page"></div>
                    <div class="generic-book-art__glow">📖</div>
                  </div>
                </div>

                <div class="book-front__award">
                  {{ totalChapters }} chapters • {{ totalPages }} pages
                </div>
              </div>
            </div>
            <button
              v-if="readRoute"
              class="cover-btn"
              style="width: 100%; margin-bottom: 12px"
              @click="router.push(readRoute)"
            >
              Continue Reading
            </button>
          </div>

          <div class="book-info-col">
            <div class="book-tags">
              <span class="tag tag--blue">Reading</span>
              <span class="tag tag--green">In Progress</span>
              <span class="tag tag--amber">Saved</span>
            </div>

            <h1 class="book-title">{{ currentBook.title }}</h1>
            <p class="book-author">Continue from your bookmark</p>

            <div class="book-meta">
              <div class="meta-item">
                <span class="meta-item__label">Chapters</span>
                <span class="meta-item__val">{{ totalChapters }}</span>
              </div>
              <div class="meta-item">
                <span class="meta-item__label">Pages</span>
                <span class="meta-item__val">{{ totalPages }}</span>
              </div>
              <div class="meta-item">
                <span class="meta-item__label">Last read</span>
                <span class="meta-item__val">Chapter {{ currentChapterNumber }}</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <div class="book-body">
        <div class="container book-body__grid">
          <div>
            <!-- TABS -->
            <div class="ctabs">
              <button
                class="ctab"
                :class="{ active: activeTab === 'desc' }"
                @click="activeTab = 'desc'"
              >
                Description
              </button>
              <button
                class="ctab"
                :class="{ active: activeTab === 'chapters' }"
                @click="activeTab = 'chapters'"
              >
                Chapters ({{ totalChapters }})
              </button>
            </div>

            <div v-if="activeTab === 'desc'" class="csection active">
              <h2 class="csection__title">Synopsis</h2>
              <div class="synopsis">
                <p>{{ currentBook.description }}</p>
              </div>
            </div>

            <div v-if="activeTab === 'chapters'" class="csection active">
              <h2 class="csection__title">Table of Contents</h2>
              <div class="detail-row" v-for="chapter in currentBook.chapters" :key="chapter.id">
                <span class="chapter-num">Chapter {{ chapter.number }}</span>
                <span class="chapter-title">{{ chapter.title }}</span>
                <span class="chapter-pages">{{ chapter.pageIds.length }} pages</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
.book-detail__wrapper {
  opacity: 1;
  transition: opacity 0.4s ease;
}

.chapter-num {
  font-weight: 700;
  color: #e8a020;
  min-width: 110px;
}
.chapter-title {
  flex: 1;
  color: #222;
}
.chapter-pages {
  font-size: 0.8rem;
  color: #777;
  font-family: 'Barlow Condensed', sans-serif;
}

.generic-book-art {
  width: 100px;
  height: 72px;
  background: linear-gradient(160deg, #3a3a5e, #1e1e3a);
  border-radius: 4px;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  box-shadow: inset 0 0 20px rgba(255, 255, 255, 0.15);
}
.generic-book-art__page {
  position: absolute;
  top: 8px;
  left: 12px;
  right: 12px;
  bottom: 8px;
  background: #f8f4eb;
  border-radius: 2px;
  transform: rotate(8deg);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}
.generic-book-art__glow {
  font-size: 2.4rem;
  filter: drop-shadow(0 0 12px #e8a020);
  z-index: 2;
}
</style>
