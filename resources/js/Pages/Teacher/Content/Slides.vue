<script setup lang="ts">
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps<{
  courses: Array<any>
  slides: Array<any>
}>()

const statusFilter = ref('All')

const sampleSlides = ref([
  {
    id: 1,
    name: 'Chapter_1_Intro.pptx',
    title_kh: 'ស្លាយណែនាំមេរៀនទី ១',
    title_en: 'Chapter 1 Intro Presentation',
    course: 'C Programming Basics',
    slides_count: 12,
    converted: 'Ready',
    speaker_notes: 'Introduce C language history and setup environment.',
  },
  {
    id: 2,
    name: 'Loops_Examples.pptx',
    title_kh: 'ស្លាយឧទាហរណ៍ Loops',
    title_en: 'Loops Examples Slides',
    course: 'C Programming Basics',
    slides_count: 8,
    converted: 'Ready',
    speaker_notes: 'Explain for, while, and do-while loops with memory diagram.',
  },
  {
    id: 3,
    name: 'Database_Design.pptx',
    title_kh: 'ស្លាយរចនាសម្ព័ន្ធ Database',
    title_en: 'Database Design Slides',
    course: 'Database Systems',
    slides_count: 24,
    converted: 'Processing',
    speaker_notes: 'Normalization forms 1NF, 2NF, 3NF.',
  }
])

// Upload Slides Modal
const showUploadModal = ref(false)
const uploadForm = useForm({
  course_id: null,
  module_id: null,
  chapter_id: null,
  title_kh: '',
  title_en: '',
  convert_pdf: true,
  generate_thumbnails: true,
  allow_download: true,
  attach_chapter: true,
})

const submitUpload = () => {
  alert('PPTX Slide presentation uploaded and conversion queued!')
  showUploadModal.value = false
  uploadForm.reset()
}

// Slide Viewer Modal
const showViewerModal = ref(false)
const viewingSlide = ref<any>(null)
const currentSlideIndex = ref(1)

const openViewer = (slide: any) => {
  viewingSlide.value = slide
  currentSlideIndex.value = 1
  showViewerModal.value = true
}

const prevSlide = () => {
  if (currentSlideIndex.value > 1) currentSlideIndex.value--
}

const nextSlide = () => {
  if (currentSlideIndex.value < (viewingSlide.value?.slides_count || 12)) currentSlideIndex.value++
}
</script>

<template>
  <div class="space-y-5">
    <!-- Top Toolbar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-gray-800 p-4 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm">
      <div class="flex flex-wrap items-center gap-3">
        <select class="p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-xs font-semibold">
          <option value="">Course: All ▼</option>
          <option v-for="c in courses" :key="c.id" :value="c.id">{{ c.title }}</option>
        </select>

        <select v-model="statusFilter" class="p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-xs font-semibold">
          <option value="All">Converted: All ▼</option>
          <option value="Ready">Ready 🟢</option>
          <option value="Processing">Processing 🟡</option>
        </select>
      </div>

      <button
        @click="showUploadModal = true"
        class="px-4 py-2 bg-gradient-to-r from-amber-500 to-indigo-600 hover:from-amber-600 hover:to-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-amber-500/20 transition flex items-center gap-2"
      >
        <i class="pi pi-desktop"></i>
        <span>+ Upload Slides</span>
      </button>
    </div>

    <!-- Slide Library Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-slate-50 dark:bg-gray-700/50 text-slate-500 uppercase tracking-wider border-b border-slate-200/80 dark:border-gray-700">
            <th class="p-3.5">Slide Presentation</th>
            <th class="p-3.5">Course</th>
            <th class="p-3.5">Slides Count</th>
            <th class="p-3.5">Converted Status</th>
            <th class="p-3.5 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
          <tr v-for="s in sampleSlides" :key="s.id" class="hover:bg-slate-50/50 dark:hover:bg-gray-700/30 transition">
            <td class="p-3.5">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 font-bold flex items-center justify-center text-lg shadow-sm border border-amber-200/60">
                  📊
                </div>
                <div>
                  <p class="font-bold text-slate-800 dark:text-white">{{ s.name }}</p>
                  <p class="text-[11px] text-amber-600 dark:text-amber-400 font-medium">{{ s.title_kh }}</p>
                </div>
              </div>
            </td>
            <td class="p-3.5 font-medium text-slate-600 dark:text-slate-300">{{ s.course }}</td>
            <td class="p-3.5 font-bold text-slate-700 dark:text-slate-200">{{ s.slides_count }} Slides</td>
            <td class="p-3.5">
              <span
                class="px-2.5 py-1 font-bold rounded-full text-[10px]"
                :class="s.converted === 'Ready' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 animate-pulse'"
              >
                {{ s.converted === 'Ready' ? '🟢 Ready' : '🟡 Processing ⏳' }}
              </span>
            </td>
            <td class="p-3.5 text-right space-x-1.5">
              <button v-if="s.converted === 'Ready'" @click="openViewer(s)" class="px-2.5 py-1 bg-amber-50 text-amber-700 hover:bg-amber-100 rounded-lg font-bold">👁 Preview</button>
              <button class="px-2.5 py-1 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-lg font-semibold">✏ Edit</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- 📊 UPLOAD SLIDES MODAL -->
    <div v-if="showUploadModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl overflow-y-auto max-h-[90vh]">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-gray-700 pb-3">
          <h3 class="text-base font-bold text-slate-800 dark:text-white">📊 Upload Presentation Slides</h3>
          <button @click="showUploadModal = false" class="text-slate-400 hover:text-slate-600"><i class="pi pi-times"></i></button>
        </div>

        <div class="border-2 border-dashed border-amber-400 dark:border-amber-500/50 bg-amber-50/50 dark:bg-amber-900/10 rounded-2xl p-6 text-center space-y-2 cursor-pointer">
          <i class="pi pi-desktop text-3xl text-amber-600"></i>
          <p class="text-xs font-bold text-slate-700 dark:text-slate-200">Drag & Drop PPTX / PDF / Keynote file here</p>
          <button class="px-4 py-1.5 bg-amber-600 text-white rounded-xl text-xs font-bold shadow">Browse File</button>
        </div>

        <div class="space-y-3 text-xs">
          <div class="grid grid-cols-3 gap-3">
            <div>
              <label class="block font-semibold mb-1">Course</label>
              <select v-model="uploadForm.course_id" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700">
                <option :value="null">C Programming ▼</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold mb-1">Module</label>
              <select v-model="uploadForm.module_id" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700">
                <option :value="null">Module 3: Loops ▼</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold mb-1">Chapter</label>
              <select v-model="uploadForm.chapter_id" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700">
                <option :value="null">Chapter 3.1: For Loop ▼</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold mb-1">Title KH</label>
              <input v-model="uploadForm.title_kh" type="text" placeholder="ស្លាយអំពី Loops" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700" />
            </div>
            <div>
              <label class="block font-semibold mb-1">Title EN</label>
              <input v-model="uploadForm.title_en" type="text" placeholder="Loops Presentation" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700" />
            </div>
          </div>

          <div class="p-3 bg-slate-50 dark:bg-gray-700/50 rounded-xl space-y-2">
            <p class="font-bold text-[11px] text-slate-500 uppercase">Slide Options</p>
            <div class="grid grid-cols-2 gap-2">
              <label class="flex items-center gap-2 cursor-pointer">
                <input v-model="uploadForm.convert_pdf" type="checkbox" class="rounded text-amber-600" />
                <span>☑️ Convert PPTX to PDF</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input v-model="uploadForm.generate_thumbnails" type="checkbox" class="rounded text-amber-600" />
                <span>☑️ Generate slide thumbnails</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input v-model="uploadForm.allow_download" type="checkbox" class="rounded text-amber-600" />
                <span>☑️ Allow student download</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input v-model="uploadForm.attach_chapter" type="checkbox" class="rounded text-amber-600" />
                <span>☑️ Attach to selected chapter</span>
              </label>
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-slate-100 dark:border-gray-700">
          <button @click="showUploadModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">❌ Cancel</button>
          <button @click="submitUpload" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold shadow">📊 Upload Slides</button>
        </div>
      </div>
    </div>

    <!-- 👁 SLIDE VIEWER MODAL -->
    <div v-if="showViewerModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-4xl w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-gray-700 pb-3">
          <h3 class="text-base font-bold text-slate-800 dark:text-white">📊 Slide Presentation: {{ viewingSlide?.name }}</h3>
          <button @click="showViewerModal = false" class="text-slate-400 hover:text-slate-700"><i class="pi pi-times text-lg"></i></button>
        </div>

        <!-- Slide Canvas Screen -->
        <div class="h-96 bg-slate-900 rounded-2xl flex flex-col items-center justify-center text-white p-8 relative overflow-hidden border border-slate-800 shadow-inner">
          <span class="text-[10px] font-mono px-2 py-0.5 bg-amber-600 text-white rounded absolute top-4 left-4 font-bold">
            Slide {{ currentSlideIndex }} / {{ viewingSlide?.slides_count }}
          </span>

          <div class="max-w-xl text-center space-y-3">
            <h2 class="text-2xl font-extrabold text-amber-400">for (int i = 0; i < 10; i++) { ... }</h2>
            <p class="text-xs text-slate-300">Explanation of C Programming For-Loop execution flow and memory increment.</p>
          </div>

          <!-- Speaker Notes Box -->
          <div class="absolute bottom-4 left-4 right-4 bg-slate-800/90 backdrop-blur p-3 rounded-xl border border-slate-700 text-[11px] text-slate-300">
            <strong class="text-amber-400 block">📝 Speaker Notes:</strong>
            {{ viewingSlide?.speaker_notes }}
          </div>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-gray-700 text-xs">
          <div class="flex gap-2">
            <button @click="prevSlide" :disabled="currentSlideIndex <= 1" class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-xl font-bold disabled:opacity-50">← Previous</button>
            <button @click="nextSlide" :disabled="currentSlideIndex >= (viewingSlide?.slides_count || 12)" class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-xl font-bold disabled:opacity-50">Next →</button>
          </div>
          <div class="flex gap-2">
            <button class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-xl font-bold">Fullscreen</button>
            <button @click="showViewerModal = false" class="px-4 py-1.5 bg-amber-600 text-white rounded-xl font-bold shadow">Close Viewer</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
