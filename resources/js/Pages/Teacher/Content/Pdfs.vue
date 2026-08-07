<script setup lang="ts">
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps<{
  courses: Array<any>
  pdfs: Array<any>
}>()

const searchQuery = ref('')
const selectedCourse = ref('')

const samplePdfs = ref([
  {
    id: 1,
    name: 'C_Programming_Complete_Notes.pdf',
    title_kh: 'កំណត់ចំណាំមេរៀន C Programming',
    title_en: 'C Programming Lecture Notes',
    course: 'C Programming Basics',
    module: 'Module 1: Introduction',
    chapter: 'Chapter 1.1: History of C',
    pages: 45,
    size: '2.4MB',
    status: 'Ready',
    watermark: true,
    downloadable: false,
  },
  {
    id: 2,
    name: 'Variables_CheatSheet.pdf',
    title_kh: 'តារាងសង្ខេបអថេរ',
    title_en: 'Variables CheatSheet',
    course: 'C Programming Basics',
    module: 'Module 2: Variables',
    chapter: 'Chapter 2.1: Data Types',
    pages: 5,
    size: '800KB',
    status: 'Ready',
    watermark: true,
    downloadable: true,
  },
  {
    id: 3,
    name: 'Assignment_1_Instructions.pdf',
    title_kh: 'សេចក្តីណែនាំកិច្ចការផ្ទះ ១',
    title_en: 'Assignment 1 Instructions',
    course: 'Web Development',
    module: 'Module 1: HTML & CSS',
    chapter: 'Chapter 1.2: HTML Basics',
    pages: 2,
    size: '180KB',
    status: 'Ready',
    watermark: false,
    downloadable: true,
  }
])

// Upload PDF Modal State
const showUploadModal = ref(false)
const uploadForm = useForm({
  course_id: null,
  module_id: null,
  chapter_id: null,
  title_kh: '',
  title_en: '',
  doc_type: 'Lecture Note',
  allow_online_preview: true,
  add_watermark: true,
  allow_download: false,
  attach_chapter: true,
})

const submitUpload = () => {
  alert('PDF Document uploaded successfully!')
  showUploadModal.value = false
  uploadForm.reset()
}

// PDF Online Preview Modal
const showPreviewModal = ref(false)
const previewPdf = ref<any>(null)
const zoomLevel = ref(100)

const openPreview = (pdf: any) => {
  previewPdf.value = pdf
  showPreviewModal.value = true
}
</script>

<template>
  <div class="space-y-5">
    <!-- Top Toolbar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-gray-800 p-4 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm">
      <div class="flex flex-wrap items-center gap-3">
        <select v-model="selectedCourse" class="p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-xs font-semibold">
          <option value="">All Courses ▼</option>
          <option v-for="c in courses" :key="c.id" :value="c.id">{{ c.title }}</option>
        </select>

        <div class="relative">
          <i class="pi pi-search absolute left-3 top-3 text-slate-400 text-xs"></i>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search PDF..."
            class="pl-8 pr-3 py-2 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-xs focus:ring-2 focus:ring-blue-500 w-56"
          />
        </div>
      </div>

      <button
        @click="showUploadModal = true"
        class="px-4 py-2 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white rounded-xl text-xs font-bold shadow-md shadow-red-500/20 transition flex items-center gap-2"
      >
        <i class="pi pi-file-pdf"></i>
        <span>+ Upload PDF</span>
      </button>
    </div>

    <!-- PDF Library Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-slate-50 dark:bg-gray-700/50 text-slate-500 uppercase tracking-wider border-b border-slate-200/80 dark:border-gray-700">
            <th class="p-3.5">PDF Document Name</th>
            <th class="p-3.5">Course</th>
            <th class="p-3.5">Pages</th>
            <th class="p-3.5">Size</th>
            <th class="p-3.5">Watermark</th>
            <th class="p-3.5">Status</th>
            <th class="p-3.5 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
          <tr v-for="pdf in samplePdfs" :key="pdf.id" class="hover:bg-slate-50/50 dark:hover:bg-gray-700/30 transition">
            <td class="p-3.5">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-600 font-bold flex items-center justify-center text-lg shadow-sm border border-rose-200/60">
                  📄
                </div>
                <div>
                  <p class="font-bold text-slate-800 dark:text-white">{{ pdf.name }}</p>
                  <p class="text-[11px] text-rose-600 dark:text-rose-400 font-medium">{{ pdf.title_kh }}</p>
                </div>
              </div>
            </td>
            <td class="p-3.5 font-medium text-slate-600 dark:text-slate-300">{{ pdf.course }}</td>
            <td class="p-3.5 font-bold text-slate-700 dark:text-slate-200">{{ pdf.pages }} Pages</td>
            <td class="p-3.5 text-slate-500">{{ pdf.size }}</td>
            <td class="p-3.5">
              <span class="px-2 py-0.5 font-bold rounded text-[10px]" :class="pdf.watermark ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500'">
                {{ pdf.watermark ? '☑️ Enabled' : '☐ Disabled' }}
              </span>
            </td>
            <td class="p-3.5">
              <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 font-bold rounded-full text-[10px]">
                🟢 Ready
              </span>
            </td>
            <td class="p-3.5 text-right space-x-1.5">
              <button @click="openPreview(pdf)" class="px-2.5 py-1 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-lg font-bold">👁 View Online</button>
              <button class="px-2.5 py-1 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-lg font-semibold">✏ Edit</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- 📄 UPLOAD PDF MODAL -->
    <div v-if="showUploadModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl overflow-y-auto max-h-[90vh]">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-gray-700 pb-3">
          <h3 class="text-base font-bold text-slate-800 dark:text-white">📄 Upload PDF Document</h3>
          <button @click="showUploadModal = false" class="text-slate-400 hover:text-slate-600"><i class="pi pi-times"></i></button>
        </div>

        <div class="border-2 border-dashed border-rose-400 dark:border-rose-500/50 bg-rose-50/50 dark:bg-rose-900/10 rounded-2xl p-6 text-center space-y-2 cursor-pointer">
          <i class="pi pi-file-pdf text-3xl text-rose-600"></i>
          <p class="text-xs font-bold text-slate-700 dark:text-slate-200">Drag & Drop PDF document here</p>
          <p class="text-[11px] text-slate-400">Support: PDF, DOCX auto-convert to PDF</p>
          <button class="px-4 py-1.5 bg-rose-600 text-white rounded-xl text-xs font-bold shadow">Browse File</button>
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
                <option :value="null">Module 2: Variables ▼</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold mb-1">Chapter</label>
              <select v-model="uploadForm.chapter_id" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700">
                <option :value="null">Chapter 2.1: Data Types ▼</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold mb-1">Title KH</label>
              <input v-model="uploadForm.title_kh" type="text" placeholder="កំណត់ចំណាំអំពីអថេរ" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700" />
            </div>
            <div>
              <label class="block font-semibold mb-1">Title EN</label>
              <input v-model="uploadForm.title_en" type="text" placeholder="Variable Notes" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700" />
            </div>
          </div>

          <div class="p-3 bg-slate-50 dark:bg-gray-700/50 rounded-xl space-y-2">
            <p class="font-bold text-[11px] text-slate-500 uppercase">PDF Security Options</p>
            <div class="grid grid-cols-2 gap-2">
              <label class="flex items-center gap-2 cursor-pointer">
                <input v-model="uploadForm.allow_online_preview" type="checkbox" class="rounded text-rose-600" />
                <span>☑️ Allow online preview</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input v-model="uploadForm.add_watermark" type="checkbox" class="rounded text-rose-600" />
                <span>☑️ Add watermark with student name</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input v-model="uploadForm.allow_download" type="checkbox" class="rounded text-rose-600" />
                <span>☐ Allow download</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input v-model="uploadForm.attach_chapter" type="checkbox" class="rounded text-rose-600" />
                <span>☑️ Attach to selected chapter</span>
              </label>
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-slate-100 dark:border-gray-700">
          <button @click="showUploadModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">❌ Cancel</button>
          <button @click="submitUpload" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow">📄 Upload PDF</button>
        </div>
      </div>
    </div>

    <!-- 👁 PDF PREVIEW MODAL -->
    <div v-if="showPreviewModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-4xl w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-gray-700 pb-3">
          <h3 class="text-base font-bold text-slate-800 dark:text-white">📄 PDF Viewer: {{ previewPdf?.name }}</h3>
          <div class="flex items-center gap-2 text-xs">
            <button @click="zoomLevel += 10" class="px-2.5 py-1 bg-slate-100 rounded font-bold">Zoom +</button>
            <button @click="zoomLevel -= 10" class="px-2.5 py-1 bg-slate-100 rounded font-bold">Zoom -</button>
            <button @click="showPreviewModal = false" class="text-slate-400 hover:text-slate-700 ml-2"><i class="pi pi-times text-lg"></i></button>
          </div>
        </div>

        <!-- Simulated PDF Document Canvas with Watermark Overlay -->
        <div class="h-96 bg-slate-100 dark:bg-gray-900 rounded-2xl border border-slate-200 dark:border-gray-700 p-8 relative overflow-y-auto flex flex-col items-center shadow-inner">
          <div class="w-full max-w-2xl bg-white dark:bg-gray-800 min-h-[500px] p-8 shadow-md rounded border border-slate-200 relative">
            <h2 class="text-lg font-bold border-b pb-2 text-slate-800 dark:text-white">{{ previewPdf?.title_en }}</h2>
            <p class="text-xs text-slate-500 mt-4 leading-relaxed">
              Lorem ipsum dolor sit amet, consectetur adipiscing interim C programming variables, memory allocation, pointers, and array indices.
            </p>

            <!-- Student Watermark Overlay -->
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-20 transform -rotate-45 font-black text-2xl text-slate-400 tracking-widest uppercase">
              Watermark: Chan Dara - STU241001
            </div>
          </div>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-gray-700 text-xs">
          <span class="text-slate-400">Page 1 / {{ previewPdf?.pages || 45 }}</span>
          <div class="flex gap-2">
            <button class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl font-bold">⬇ Download</button>
            <button @click="showPreviewModal = false" class="px-5 py-2 bg-rose-600 text-white rounded-xl font-bold shadow">Close Preview</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
