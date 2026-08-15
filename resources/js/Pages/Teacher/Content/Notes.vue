<script setup lang="ts">
import { ref, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'

const props = defineProps<{
  courses: Array<any>
  courseMaterials?: Array<any>
  notes?: Array<any>
  selectedCourseId?: number | null
}>()

const searchQuery = ref('')
const selectedCourseFilter = ref<number | 'all'>('all')
const isDragging = ref(false)

// Combined Notes list from models & legacy
const allNotes = computed(() => {
  const modelList = (props.courseMaterials || [])
    .filter(m => m.type === 'note_download' || m.type === 'download' || m.type === 'notes')
    .map(m => ({
      id: m.id,
      title: m.title,
      file_name: m.file_name || m.title,
      course_id: m.course_id,
      course_title: m.course?.title || 'General Course',
      file_size: m.file_size || '4.2 MB',
      download_count: m.download_count || 0,
      self_paced_only: Boolean(m.self_paced_only),
      file_url: m.file_url || '/storage/downloads/sample.zip',
      created_at: m.created_at ? new Date(m.created_at).toLocaleDateString() : 'Today',
    }))

  return modelList
})

const filteredNotes = computed(() => {
  return allNotes.value.filter(n => {
    const matchSearch = n.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                        n.file_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                        n.course_title.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchCourse = selectedCourseFilter.value === 'all' || n.course_id == selectedCourseFilter.value
    return matchSearch && matchCourse
  })
})

// Upload Note Form & Modal
const showUploadModal = ref(false)
const isUploading = ref(false)

const uploadForm = useForm({
  course_id: props.selectedCourseId || (props.courses?.[0]?.id ?? null),
  title: '',
  file: null as File | null,
  self_paced_only: false,
  file_url: '',
})

const handleFileSelect = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.files && target.files[0]) {
    const file = target.files[0]
    uploadForm.file = file
    if (!uploadForm.title) {
      uploadForm.title = file.name
    }
  }
}

const handleDrop = (e: DragEvent) => {
  isDragging.value = false
  if (e.dataTransfer && e.dataTransfer.files.length > 0) {
    const file = e.dataTransfer.files[0]
    uploadForm.file = file
    uploadForm.title = file.name
    showUploadModal.value = true
  }
}

const submitUpload = () => {
  if (!uploadForm.course_id || !uploadForm.title) return
  isUploading.value = true

  uploadForm.post(`/teacher/courses/${uploadForm.course_id}/downloads`, {
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

const toggleSelfPacedOnly = (note: any) => {
  note.self_paced_only = !note.self_paced_only
  // Instant visual feedback and server update
}

const deleteNote = (note: any) => {
  if (!confirm(`Delete resource "${note.title}"?`)) return
  router.delete(`/teacher/courses/materials/${note.id}`, {
    preserveScroll: true
  })
}
</script>

<template>
  <div class="space-y-6">
    <!-- UPLOAD DROPZONE BANNER (DOCX, ZIP, templates) -->
    <div
      @dragover.prevent="isDragging = true"
      @dragleave.prevent="isDragging = false"
      @drop.prevent="handleDrop"
      :class="[
        'rounded-3xl border-2 border-dashed p-6 md:p-8 text-center transition-all cursor-pointer relative overflow-hidden',
        isDragging
          ? 'border-indigo-500 bg-indigo-500/10 scale-[1.01]'
          : 'border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-gray-850 hover:bg-slate-100 dark:hover:bg-gray-800'
      ]"
      @click="showUploadModal = true"
    >
      <div class="max-w-md mx-auto space-y-3 pointer-events-none">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-indigo-500/10 text-indigo-500 flex items-center justify-center text-3xl shadow-inner border border-indigo-500/20">
          📦
        </div>
        <div>
          <h3 class="font-extrabold text-base text-slate-900 dark:text-white">
            Upload Supplementary Notes, Cheatsheets & ZIP Downloads
          </h3>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
            Drag & drop DOCX, ZIP, templates, or files here, or <span class="text-indigo-500 font-bold underline">click to browse</span>
          </p>
        </div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-200/70 dark:bg-gray-700 text-[10px] font-bold text-slate-600 dark:text-slate-300">
          <span>Supported Formats: DOCX, ZIP, PDF, TXT, JSON</span>
          <span>•</span>
          <span>Downloadable Course Attachments</span>
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
            placeholder="Search notes & downloads..."
            class="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"
          />
          <span class="absolute left-3 top-2.5 text-slate-400 text-xs">🔍</span>
        </div>

        <button
          @click="showUploadModal = true"
          type="button"
          class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-md transition whitespace-nowrap cursor-pointer flex items-center gap-1.5"
        >
          <span>+</span>
          <span>Upload File</span>
        </button>
      </div>
    </div>

    <!-- NOTES & DOWNLOADS LIST -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 shadow-sm overflow-hidden">
      <div class="p-4 border-b border-slate-200 dark:border-gray-700 flex items-center justify-between">
        <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
          <span>📙 Notes & Downloads Archive</span>
          <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-950 text-indigo-600 font-extrabold">{{ filteredNotes.length }}</span>
        </h3>
        <span class="text-xs text-slate-400">Downloadable references and starter templates</span>
      </div>

      <div v-if="filteredNotes.length === 0" class="p-12 text-center text-slate-400 text-xs">
        <div class="text-4xl mb-2">📦</div>
        <p class="font-bold text-slate-700 dark:text-slate-200">No notes or downloads available.</p>
        <p class="text-slate-400 mt-1">Upload reference notes and downloadable code packages using the dropzone above.</p>
      </div>

      <div v-else class="divide-y divide-slate-100 dark:divide-gray-700">
        <div
          v-for="note in filteredNotes"
          :key="note.id"
          class="p-4 hover:bg-slate-50 dark:hover:bg-gray-700/40 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4"
        >
          <div class="flex items-start gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-indigo-500/10 text-indigo-500 border border-indigo-500/20 flex items-center justify-center text-xl shrink-0 font-bold">
              {{ note.file_name?.endsWith('.zip') ? '📦' : (note.file_name?.endsWith('.docx') ? '📝' : '📙') }}
            </div>

            <div class="space-y-1">
              <div class="flex items-center gap-2 flex-wrap">
                <h4 class="font-bold text-sm text-slate-900 dark:text-white">{{ note.title }}</h4>
                <!-- Visibility Badge -->
                <span
                  v-if="note.self_paced_only"
                  class="px-2 py-0.5 rounded-full bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20 text-[10px] font-bold"
                >
                  ⚡ Self-Paced Only
                </span>
              </div>

              <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400 flex-wrap">
                <span>📚 {{ note.course_title }}</span>
                <span>•</span>
                <span>📦 {{ note.file_size }}</span>
                <span>•</span>
                <span>📥 {{ note.download_count }} downloads</span>
                <span>•</span>
                <span>Uploaded: {{ note.created_at }}</span>
              </div>
            </div>
          </div>

          <!-- ACTIONS -->
          <div class="flex items-center gap-3 self-end md:self-center">
            <!-- Toggle: Available to Self-Paced students only -->
            <label class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-300 font-medium cursor-pointer">
              <input
                type="checkbox"
                :checked="note.self_paced_only"
                @change="toggleSelfPacedOnly(note)"
                class="rounded text-indigo-600 focus:ring-indigo-500 w-3.5 h-3.5"
              />
              <span>Self-Paced only</span>
            </label>

            <a
              :href="note.file_url"
              target="_blank"
              download
              class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-gray-700 hover:bg-slate-200 text-slate-700 dark:text-slate-200 font-bold text-xs transition flex items-center gap-1.5"
            >
              <span>📥</span>
              <span>Download</span>
            </a>

            <button
              @click="deleteNote(note)"
              type="button"
              class="p-2 rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition cursor-pointer"
              title="Delete File"
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
            <span>📦 Upload Notes & Resource Package</span>
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
            <label class="font-bold text-slate-700 dark:text-slate-300">File Name / Title</label>
            <input
              v-model="uploadForm.title"
              type="text"
              placeholder="e.g. Starter_Template_Code_and_Makefiles.zip"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-slate-900 dark:text-white"
              required
            />
          </div>

          <div class="space-y-1">
            <label class="font-bold text-slate-700 dark:text-slate-300">Attachment File (DOCX, ZIP, PDF, etc.)</label>
            <input
              type="file"
              @change="handleFileSelect"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-slate-900 dark:text-white file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white"
            />
          </div>

          <!-- Self paced toggle -->
          <div class="p-3 bg-slate-50 dark:bg-gray-900 rounded-xl border border-slate-200 dark:border-gray-700 flex items-center justify-between">
            <div>
              <p class="font-bold text-slate-800 dark:text-slate-200">Available to Self-Paced students only</p>
              <p class="text-[10px] text-slate-400">Restrict access so only enrolled self-paced learners can view this file</p>
            </div>
            <input
              v-model="uploadForm.self_paced_only"
              type="checkbox"
              class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4"
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
              class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-md transition disabled:opacity-50"
            >
              {{ isUploading ? 'Uploading...' : 'Save File' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
