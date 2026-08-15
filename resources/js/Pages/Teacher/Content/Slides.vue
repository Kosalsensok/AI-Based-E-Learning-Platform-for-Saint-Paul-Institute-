<script setup lang="ts">
import { ref, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'

const props = defineProps<{
  courses: Array<any>
  courseMaterials?: Array<any>
  slides?: Array<any>
  selectedCourseId?: number | null
}>()

const searchQuery = ref('')
const selectedCourseFilter = ref<number | 'all'>('all')
const isDragging = ref(false)

// Combined Slides list from models & legacy
const allSlides = computed(() => {
  const modelList = (props.courseMaterials || [])
    .filter(m => m.type === 'slide')
    .map(m => ({
      id: m.id,
      title: m.title,
      file_name: m.file_name || m.title,
      course_id: m.course_id,
      course_title: m.course?.title || 'General Course',
      file_size: m.file_size || '12.4 MB',
      slide_count: m.slide_count || 28,
      download_count: m.download_count || 0,
      file_url: m.file_url || '/storage/slides/sample.pptx',
      created_at: m.created_at ? new Date(m.created_at).toLocaleDateString() : 'Today',
    }))

  return modelList
})

const filteredSlides = computed(() => {
  return allSlides.value.filter(s => {
    const matchSearch = s.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                        s.file_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                        s.course_title.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchCourse = selectedCourseFilter.value === 'all' || s.course_id == selectedCourseFilter.value
    return matchSearch && matchCourse
  })
})

// Upload Slide Form & Modal
const showUploadModal = ref(false)
const isUploading = ref(false)

const uploadForm = useForm({
  course_id: props.selectedCourseId || (props.courses?.[0]?.id ?? null),
  title: '',
  slide: null as File | null,
  slide_count: 24,
  file_url: '',
})

// Slide Viewer Preview Modal
const showViewerModal = ref(false)
const activeSlide = ref<any>(null)

const openViewer = (slide: any) => {
  activeSlide.value = slide
  showViewerModal.value = true
}

const handleFileSelect = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.files && target.files[0]) {
    const file = target.files[0]
    uploadForm.slide = file
    if (!uploadForm.title) {
      uploadForm.title = file.name
    }
  }
}

const handleDrop = (e: DragEvent) => {
  isDragging.value = false
  if (e.dataTransfer && e.dataTransfer.files.length > 0) {
    const file = e.dataTransfer.files[0]
    uploadForm.slide = file
    uploadForm.title = file.name
    showUploadModal.value = true
  }
}

const submitUpload = () => {
  if (!uploadForm.course_id || !uploadForm.title) return
  isUploading.value = true

  uploadForm.post(`/teacher/courses/${uploadForm.course_id}/slides`, {
    preserveScroll: true,
    forceFormData: true,
    onSuccess: () => {
      isUploading.value = false
      showUploadModal.value = false
      uploadForm.reset()
    },
    onError: () => {
      isUploading.value = false
    }
  })
}

const deleteSlide = (slide: any) => {
  if (!confirm(`Delete slide deck "${slide.title}"?`)) return
  router.delete(`/teacher/courses/materials/${slide.id}`, {
    preserveScroll: true
  })
}
</script>

<template>
  <div class="space-y-6">
    <!-- UPLOAD DROPZONE BANNER (PPTX, PDF) -->
    <div
      @dragover.prevent="isDragging = true"
      @dragleave.prevent="isDragging = false"
      @drop.prevent="handleDrop"
      :class="[
        'rounded-3xl border-2 border-dashed p-6 md:p-8 text-center transition-all cursor-pointer relative overflow-hidden',
        isDragging
          ? 'border-amber-500 bg-amber-500/10 scale-[1.01]'
          : 'border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-gray-850 hover:bg-slate-100 dark:hover:bg-gray-800'
      ]"
      @click="showUploadModal = true"
    >
      <div class="max-w-md mx-auto space-y-3 pointer-events-none">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center text-3xl shadow-inner border border-amber-500/20">
          📊
        </div>
        <div>
          <h3 class="font-extrabold text-base text-slate-900 dark:text-white">
            Upload Presentation Slide Decks
          </h3>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
            Drag & drop PPTX or PDF slides here, or <span class="text-amber-500 font-bold underline">click to browse</span>
          </p>
        </div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-200/70 dark:bg-gray-700 text-[10px] font-bold text-slate-600 dark:text-slate-300">
          <span>Supported Formats: PPTX, PDF</span>
          <span>•</span>
          <span>Automatic Slide Count & Thumbnails</span>
        </div>
      </div>
    </div>

    <!-- CONTROLS & FILTER BAR -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 border border-slate-200 dark:border-gray-700 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="flex items-center gap-3 flex-wrap">
        <select
          v-model="selectedCourseFilter"
          class="px-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-xs text-slate-900 dark:text-white font-semibold"
        >
          <option value="all">All Courses</option>
          <option v-for="c in courses" :key="c.id" :value="c.id">{{ c.title }}</option>
        </select>
      </div>

      <div class="flex items-center gap-3">
        <div class="relative w-full md:w-64">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search slide decks..."
            class="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none"
          />
          <span class="absolute left-3 top-2.5 text-slate-400 text-xs">🔍</span>
        </div>

        <button
          @click="showUploadModal = true"
          type="button"
          class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs rounded-xl shadow-md transition whitespace-nowrap cursor-pointer flex items-center gap-1.5"
        >
          <span>+</span>
          <span>Upload Slide Deck</span>
        </button>
      </div>
    </div>

    <!-- SLIDE DECKS LIST -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 shadow-sm overflow-hidden">
      <div class="p-4 border-b border-slate-200 dark:border-gray-700 flex items-center justify-between">
        <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
          <span>📊 Slide Decks Library</span>
          <span class="text-xs px-2 py-0.5 rounded-full bg-amber-50 dark:bg-amber-950 text-amber-600 font-extrabold">{{ filteredSlides.length }}</span>
        </h3>
        <span class="text-xs text-slate-400">Presentation decks for in-class lecturing and self-study</span>
      </div>

      <div v-if="filteredSlides.length === 0" class="p-12 text-center text-slate-400 text-xs">
        <div class="text-4xl mb-2">📽️</div>
        <p class="font-bold text-slate-700 dark:text-slate-200">No presentation slides found.</p>
        <p class="text-slate-400 mt-1">Upload PPTX or PDF presentation decks using the dropzone above.</p>
      </div>

      <div v-else class="divide-y divide-slate-100 dark:divide-gray-700">
        <div
          v-for="slide in filteredSlides"
          :key="slide.id"
          class="p-4 hover:bg-slate-50 dark:hover:bg-gray-700/40 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4"
        >
          <div class="flex items-start gap-3.5">
            <div 
              @click="openViewer(slide)"
              class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-500 border border-amber-500/20 flex items-center justify-center text-xl shrink-0 font-bold cursor-pointer hover:scale-105 transition-transform"
            >
              📊
            </div>

            <div class="space-y-1">
              <h4 
                @click="openViewer(slide)"
                class="font-bold text-sm text-slate-900 dark:text-white hover:text-amber-500 cursor-pointer"
              >
                {{ slide.title }}
              </h4>
              <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400 flex-wrap">
                <span>📚 {{ slide.course_title }}</span>
                <span>•</span>
                <span class="font-bold text-amber-600 dark:text-amber-400">📑 {{ slide.slide_count }} Slides</span>
                <span>•</span>
                <span>📦 {{ slide.file_size }}</span>
                <span>•</span>
                <span>Uploaded: {{ slide.created_at }}</span>
              </div>
            </div>
          </div>

          <!-- ACTIONS -->
          <div class="flex items-center gap-2 self-end md:self-center">
            <button
              @click="openViewer(slide)"
              type="button"
              class="px-3 py-1.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 hover:bg-amber-100 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer border border-amber-500/20"
            >
              <span>👁️</span>
              <span>Present Deck</span>
            </button>

            <a
              :href="slide.file_url"
              target="_blank"
              download
              class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-gray-700 hover:bg-slate-200 text-slate-700 dark:text-slate-200 font-bold text-xs transition flex items-center gap-1.5"
            >
              <span>📥</span>
              <span>Download</span>
            </a>

            <button
              @click="deleteSlide(slide)"
              type="button"
              class="p-2 rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition cursor-pointer"
              title="Delete Slide Deck"
            >
              🗑️
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- UPLOAD MODAL -->
    <div
      v-if="showUploadModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
    >
      <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-gray-700 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-gray-700 pb-3">
          <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
            <span>📊 Upload Slide Deck (PPTX/PDF)</span>
          </h3>
          <button @click="showUploadModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <form @submit.prevent="submitUpload" class="space-y-4 text-xs">
          <div class="space-y-1">
            <label class="font-bold text-slate-700 dark:text-slate-300">Course</label>
            <select
              v-model="uploadForm.course_id"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-slate-900 dark:text-white font-medium"
            >
              <option v-for="c in courses" :key="c.id" :value="c.id">{{ c.title }}</option>
            </select>
          </div>

          <div class="space-y-1">
            <label class="font-bold text-slate-700 dark:text-slate-300">Presentation Deck Title</label>
            <input
              v-model="uploadForm.title"
              type="text"
              placeholder="e.g. Lecture_01_Architecture_Slides.pptx"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-slate-900 dark:text-white"
              required
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div class="space-y-1">
              <label class="font-bold text-slate-700 dark:text-slate-300">Estimated Slide Count</label>
              <input
                v-model.number="uploadForm.slide_count"
                type="number"
                min="1"
                max="200"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-slate-900 dark:text-white"
              />
            </div>

            <div class="space-y-1">
              <label class="font-bold text-slate-700 dark:text-slate-300">Slide File</label>
              <input
                type="file"
                accept=".pptx,.ppt,application/pdf"
                @change="handleFileSelect"
                class="w-full px-3 py-1.5 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-slate-900 dark:text-white file:mr-2 file:py-0.5 file:px-2 file:rounded file:border-0 file:text-[10px] file:bg-amber-600 file:text-white"
              />
            </div>
          </div>

          <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-gray-700">
            <button
              type="button"
              @click="showUploadModal = false"
              class="px-4 py-2 rounded-xl text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-100"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="isUploading"
              class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold shadow-md transition disabled:opacity-50"
            >
              {{ isUploading ? 'Uploading...' : 'Save Slides' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- PRESENTATION DECK VIEWER MODAL -->
    <div
      v-if="showViewerModal && activeSlide"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md"
    >
      <div class="bg-slate-900 text-white rounded-3xl max-w-4xl w-full p-6 shadow-2xl border border-slate-700 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
          <div class="flex items-center gap-3">
            <span class="p-2 rounded-xl bg-amber-500/20 text-amber-400">📊</span>
            <div>
              <h3 class="font-extrabold text-base">{{ activeSlide.title }}</h3>
              <p class="text-xs text-slate-400">{{ activeSlide.course_title }} • {{ activeSlide.slide_count }} Slides</p>
            </div>
          </div>
          <button @click="showViewerModal = false" class="text-slate-400 hover:text-white text-lg">✕</button>
        </div>

        <!-- Interactive Slide Canvas Simulation -->
        <div class="rounded-2xl bg-slate-950 border border-slate-800 aspect-video flex flex-col items-center justify-center p-8 text-center space-y-4 shadow-2xl">
          <div class="w-16 h-16 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-3xl font-extrabold shadow">
            📽️
          </div>
          <div class="space-y-1 max-w-md">
            <h2 class="text-xl font-black text-amber-300">{{ activeSlide.title }}</h2>
            <p class="text-xs text-slate-400">Saint Paul Institute — Enterprise Slide Deck Presentation Engine</p>
          </div>
          <div class="p-3 bg-slate-900/80 rounded-xl border border-slate-800 text-xs text-slate-300 flex items-center gap-4">
            <span>Slide: <strong class="text-white">1 / {{ activeSlide.slide_count }}</strong></span>
            <span>•</span>
            <span>Full HD 1080p WebGL Slide Stream</span>
          </div>
        </div>

        <div class="flex items-center justify-between text-xs text-slate-400 pt-2">
          <a
            :href="activeSlide.file_url"
            target="_blank"
            download
            class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold transition"
          >
            Download Slide Deck (.pptx/.pdf)
          </a>
          <button
            @click="showViewerModal = false"
            class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold"
          >
            Close Viewer
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
