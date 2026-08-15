<script setup lang="ts">
import { ref, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'

const props = defineProps<{
  courses: Array<any>
  courseMaterials?: Array<any>
  pdfs?: Array<any>
  selectedCourseId?: number | null
}>()

const searchQuery = ref('')
const selectedCourseFilter = ref<number | 'all'>('all')
const isDragging = ref(false)

// Combined PDFs list from models & legacy
const allPdfs = computed(() => {
  const modelList = (props.courseMaterials || [])
    .filter(m => m.type === 'pdf')
    .map(m => ({
      id: m.id,
      title: m.title,
      file_name: m.file_name || m.title,
      course_id: m.course_id,
      course_title: m.course?.title || 'General Course',
      file_size: m.file_size || '2.4 MB',
      download_count: m.download_count || 0,
      file_url: m.file_url || '/storage/pdfs/sample.pdf',
      created_at: m.created_at ? new Date(m.created_at).toLocaleDateString() : 'Today',
    }))

  return modelList
})

const filteredPdfs = computed(() => {
  return allPdfs.value.filter(p => {
    const matchSearch = p.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                        p.file_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                        p.course_title.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchCourse = selectedCourseFilter.value === 'all' || p.course_id == selectedCourseFilter.value
    return matchSearch && matchCourse
  })
})

// Upload PDF Form & Modal
const showUploadModal = ref(false)
const isUploading = ref(false)

const uploadForm = useForm({
  course_id: props.selectedCourseId || (props.courses?.[0]?.id ?? null),
  title: '',
  pdf: null as File | null,
  file_url: '',
})

// Replace PDF Modal
const showReplaceModal = ref(false)
const selectedPdfForReplace = ref<any>(null)
const replaceForm = useForm({
  pdf: null as File | null,
})

const handleFileSelect = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.files && target.files[0]) {
    const file = target.files[0]
    uploadForm.pdf = file
    if (!uploadForm.title) {
      uploadForm.title = file.name
    }
  }
}

const handleDrop = (e: DragEvent) => {
  isDragging.value = false
  if (e.dataTransfer && e.dataTransfer.files.length > 0) {
    const file = e.dataTransfer.files[0]
    if (file.type === 'application/pdf' || file.name.endsWith('.pdf')) {
      uploadForm.pdf = file
      uploadForm.title = file.name
      showUploadModal.value = true
    } else {
      alert('Please drop a PDF file.')
    }
  }
}

const submitUpload = () => {
  if (!uploadForm.course_id || !uploadForm.title) return
  isUploading.value = true

  uploadForm.post(`/teacher/courses/${uploadForm.course_id}/pdfs`, {
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

const openReplace = (pdf: any) => {
  selectedPdfForReplace.value = pdf
  showReplaceModal.value = true
}

const submitReplace = () => {
  if (!selectedPdfForReplace.value || !replaceForm.pdf) return
  const courseId = selectedPdfForReplace.value.course_id
  replaceForm.post(`/teacher/courses/${courseId}/pdfs`, {
    preserveScroll: true,
    forceFormData: true,
    onSuccess: () => {
      showReplaceModal.value = false
      replaceForm.reset()
    }
  })
}

const deletePdf = (pdf: any) => {
  if (!confirm(`Delete PDF "${pdf.title}"?`)) return
  router.delete(`/teacher/courses/materials/${pdf.id}`, {
    preserveScroll: true
  })
}
</script>

<template>
  <div class="space-y-6">
    <!-- UPLOAD DROPZONE BANNER (PDF only) -->
    <div
      @dragover.prevent="isDragging = true"
      @dragleave.prevent="isDragging = false"
      @drop.prevent="handleDrop"
      :class="[
        'rounded-3xl border-2 border-dashed p-6 md:p-8 text-center transition-all cursor-pointer relative overflow-hidden',
        isDragging
          ? 'border-rose-500 bg-rose-500/10 scale-[1.01]'
          : 'border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-gray-850 hover:bg-slate-100 dark:hover:bg-gray-800'
      ]"
      @click="showUploadModal = true"
    >
      <div class="max-w-md mx-auto space-y-3 pointer-events-none">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-rose-500/10 text-rose-500 flex items-center justify-center text-3xl shadow-inner border border-rose-500/20">
          📄
        </div>
        <div>
          <h3 class="font-extrabold text-base text-slate-900 dark:text-white">
            Upload PDF Reading Material & Handouts
          </h3>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
            Drag & drop PDF files here, or <span class="text-rose-500 font-bold underline">click to browse</span>
          </p>
        </div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-200/70 dark:bg-gray-700 text-[10px] font-bold text-slate-600 dark:text-slate-300">
          <span>Format: PDF Only</span>
          <span>•</span>
          <span>Handouts, Worksheets & Reading Guides</span>
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
            placeholder="Search PDF handouts..."
            class="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none"
          />
          <span class="absolute left-3 top-2.5 text-slate-400 text-xs">🔍</span>
        </div>

        <button
          @click="showUploadModal = true"
          type="button"
          class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs rounded-xl shadow-md transition whitespace-nowrap cursor-pointer flex items-center gap-1.5"
        >
          <span>+</span>
          <span>Upload PDF</span>
        </button>
      </div>
    </div>

    <!-- PDF ITEMS LIST -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 shadow-sm overflow-hidden">
      <div class="p-4 border-b border-slate-200 dark:border-gray-700 flex items-center justify-between">
        <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
          <span>📄 Course PDFs Repository</span>
          <span class="text-xs px-2 py-0.5 rounded-full bg-rose-50 dark:bg-rose-950 text-rose-600 font-extrabold">{{ filteredPdfs.length }}</span>
        </h3>
        <span class="text-xs text-slate-400">Available to enrolled students for offline reading</span>
      </div>

      <div v-if="filteredPdfs.length === 0" class="p-12 text-center text-slate-400 text-xs">
        <div class="text-4xl mb-2">📑</div>
        <p class="font-bold text-slate-700 dark:text-slate-200">No PDF documents found.</p>
        <p class="text-slate-400 mt-1">Upload PDF handouts and reading material using the dropzone above.</p>
      </div>

      <div v-else class="divide-y divide-slate-100 dark:divide-gray-700">
        <div
          v-for="pdf in filteredPdfs"
          :key="pdf.id"
          class="p-4 hover:bg-slate-50 dark:hover:bg-gray-700/40 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4"
        >
          <div class="flex items-start gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-rose-500/10 text-rose-500 border border-rose-500/20 flex items-center justify-center text-xl shrink-0 font-bold">
              📄
            </div>

            <div class="space-y-1">
              <h4 class="font-bold text-sm text-slate-900 dark:text-white">{{ pdf.title }}</h4>
              <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400 flex-wrap">
                <span>📚 {{ pdf.course_title }}</span>
                <span>•</span>
                <span>📦 {{ pdf.file_size }}</span>
                <span>•</span>
                <span>📥 {{ pdf.download_count }} downloads</span>
                <span>•</span>
                <span>Uploaded: {{ pdf.created_at }}</span>
              </div>
            </div>
          </div>

          <!-- ACTIONS: Download, Replace, Delete -->
          <div class="flex items-center gap-2 self-end md:self-center">
            <a
              :href="pdf.file_url"
              target="_blank"
              download
              class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-gray-700 hover:bg-slate-200 text-slate-700 dark:text-slate-200 font-bold text-xs transition flex items-center gap-1.5"
            >
              <span>📥</span>
              <span>Download</span>
            </a>

            <button
              @click="openReplace(pdf)"
              type="button"
              class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-gray-700 hover:bg-slate-200 text-slate-700 dark:text-slate-200 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer"
            >
              <span>🔄</span>
              <span>Replace</span>
            </button>

            <button
              @click="deletePdf(pdf)"
              type="button"
              class="p-2 rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition cursor-pointer"
              title="Delete PDF"
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
            <span>📄 Upload PDF Handout</span>
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
            <label class="font-bold text-slate-700 dark:text-slate-300">Document Title</label>
            <input
              v-model="uploadForm.title"
              type="text"
              placeholder="e.g. C_Programming_Module1_Handout.pdf"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-slate-900 dark:text-white"
              required
            />
          </div>

          <div class="space-y-1">
            <label class="font-bold text-slate-700 dark:text-slate-300">PDF File (PDF Only)</label>
            <input
              type="file"
              accept="application/pdf"
              @change="handleFileSelect"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-slate-900 dark:text-white file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-rose-600 file:text-white"
            />
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
              class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold shadow-md transition disabled:opacity-50"
            >
              {{ isUploading ? 'Uploading...' : 'Save PDF' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- REPLACE MODAL -->
    <div
      v-if="showReplaceModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
    >
      <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-gray-700 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-gray-700 pb-3">
          <h3 class="font-extrabold text-base text-slate-900 dark:text-white">Replace PDF Document</h3>
          <button @click="showReplaceModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <p class="text-xs text-slate-500">
          Upload a new version to replace: <span class="font-bold text-slate-800 dark:text-slate-200">{{ selectedPdfForReplace?.title }}</span>
        </p>

        <form @submit.prevent="submitReplace" class="space-y-4 text-xs">
          <input
            type="file"
            accept="application/pdf"
            @change="(e: any) => replaceForm.pdf = e.target.files[0]"
            class="w-full px-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl"
            required
          />

          <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-gray-700">
            <button
              type="button"
              @click="showReplaceModal = false"
              class="px-4 py-2 rounded-xl text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-100"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold shadow-md"
            >
              Confirm Replace
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
