export type ReadingStatus = 'want_to_read' | 'reading' | 'finished' | 'dropped'
export type Theme = 'light' | 'dark'
export type PaginationMode = 'page' | 'scroll'
export type FontFamily = 'Lora' | 'Playfair Display' | 'Georgia'
export type LineHeight = '1.5' | '1.8' | '2'

export interface BookChapter {
  id: number
  title: string
  number: number
  slug: string
  bookId: number
  pageIds: number[]
}

export interface FontOption {
  name: string
  value: string
  style: string
}

export interface ReadingEntry {
  id: number | null
  book_id: number
  status: ReadingStatus
  current_page: number
  total_pages: number | null
  progress_percentage: number | null
  started_at: string | null
  finished_at: string | null
}

export interface ReadingProgress {
  progress: {
    book_id: number
    total_pages: number
    read_pages: number
    percentage: number
    is_finished: boolean
  }
  last_position: {
    chapter_id: number
    page_id: number
    scroll_position: number
  } | null
  last_read_at: string
}

export interface BookPage {
  page: {
    id: string
    chapter_id: string
    number: string
    global_number: string
    content: string
    content_format: string
    word_count: string
  }
  adjacent: {
    previous_page_id: string
    next_page_id: string
    has_previous: string
    has_next: string
  }
  progress: {
    book_id: string
    total_pages: string
    read_pages: string
    percentage: string
    is_finished: string
  }
}

export interface Bookmark {
  id: number
  userId: number
  bookId: number
  chapterId: number
  pageId: number
  label: string
  color: string
}

export interface ReadingBook {
  id: number
  title: string
  slug: string
  description: string
  publisher: string | null
  bookmark: Bookmark
  chapters: BookChapter[]
}

export interface ReadingHistoryMeta {
  total_sessions: number
  total_pages_read: number
  total_duration_seconds: number
}

export interface ReadingHistory {
  data: unknown[]
  meta: ReadingHistoryMeta
}

export interface ReadingSettings {
  theme: Theme
  fontSize: number
  lineHeight: LineHeight
  fontFamily: FontFamily
  paginationMode: PaginationMode
  wordsPerPage: number
  pageWidth: number
}

export interface SaveProgressPayload {
  chapter_id: number
  page_id: number
  global_page_number: number
  scroll_position: number
  total_pages: number
  book_title: string
}

export interface StartSessionPayload {
  current_page_id?: number | null
}

export interface EndSessionPayload {
  end_page_id: number
  duration_seconds: number
}
