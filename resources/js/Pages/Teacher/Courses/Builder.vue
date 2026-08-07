<script setup lang="ts">
import TeacherLayout from '@/Layouts/TeacherLayout.vue'
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'

interface Lesson { id: number; title: string; type: string; duration_seconds: number; order: number }
interface Module { id: number; title: string; order: number; lessons: Lesson[] }
interface Course { id: number; title: string; status: string }

const props = defineProps<{ course: Course; modules: Module[] }>()

const moduleList = ref([...(props.modules || [])])

async function onModuleReorder() {
  const ids = moduleList.value.map(m => m.id)
  await fetch(`/teacher/courses/${props.course.id}/modules/reorder`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    body: JSON.stringify({ modules: ids }),
  })
}

// ── Add Module ────────────────────────────────────────────────
const showModuleDialog = ref(false)
const moduleForm = useForm({ title: '' })

function addModule() {
  moduleForm.post(`/teacher/courses/${props.course.id}/modules`, {
    onSuccess: () => { showModuleDialog.value = false; moduleForm.reset() }
  })
}

// ── Add Lesson ────────────────────────────────────────────────
const activeModule = ref<Module | null>(null)
const showLessonDialog = ref(false)
const lessonForm = useForm({
  title: '',
  type: 'video',
  video: null as File | null,
  file: null as File | null,
})

function onVideoChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (file) lessonForm.video = file
}

function uploadLesson() {
  if (!activeModule.value) return
  lessonForm.post(`/teacher/modules/${activeModule.value.id}/lessons`, {
    forceFormData: true,
    onSuccess: () => {
      showLessonDialog.value = false
      lessonForm.reset()
    },
  })
}

function getIcon(type: string) {
  const map: Record<string, string> = {
    video: '🎥',
    pdf: '📄',
    slides: '📊',
    notes: '📝',
  }
  return map[type] || '📁'
}

function formatDuration(s: number) {
  if (!s) return '--:--'
  const m = Math.floor(s / 60)
  const sec = s % 60
  return `${m}:${sec.toString().padStart(2, '0')}`
}
</script>

<template>
  <TeacherLayout :title="`Course Builder: ${course?.title}`">
    <div class="max-w-5xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between bg-white dark:bg-gray-800 p-4 rounded-xl shadow border dark:border-gray-700">
        <div>
          <h1 class="text-xl font-bold">Course Curriculum Builder</h1>
          <p class="text-xs text-gray-500 mt-1">{{ course?.title }}</p>
        </div>
        <div class="flex items-center gap-2">
          <span class="px-2.5 py-1 text-xs font-bold rounded uppercase" :class="course?.status === 'published' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'">
            {{ course?.status }}
          </span>
          <button @click="router.visit(`/teacher/courses/${course.id}`)" class="px-3 py-1.5 border rounded-lg text-xs font-medium hover:bg-gray-50">
            Preview Course
          </button>
        </div>
      </div>

      <!-- Modules List -->
      <div class="space-y-4">
        <div v-for="module in moduleList" :key="module.id" class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 overflow-hidden shadow">
          <!-- Module Header -->
          <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700/50 border-b dark:border-gray-700">
            <span class="font-bold text-sm flex-1">{{ module.title }}</span>
            <span class="text-xs text-gray-400">{{ module.lessons?.length || 0 }} lessons</span>
            <button
              @click="activeModule = module; showLessonDialog = true"
              class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-semibold"
            >
              + Add Lesson
            </button>
            <button
              @click="router.delete(`/teacher/modules/${module.id}`)"
              class="px-2.5 py-1 text-red-600 hover:bg-red-50 text-xs rounded"
            >
              Delete
            </button>
          </div>

          <!-- Lessons List -->
          <div class="divide-y dark:divide-gray-700">
            <div v-for="lesson in module.lessons || []" :key="lesson.id" class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition text-sm">
              <span>{{ getIcon(lesson.type) }}</span>
              <div class="flex-1">
                <p class="font-semibold">{{ lesson.title }}</p>
                <p class="text-xs text-gray-400">{{ formatDuration(lesson.duration_seconds) }}</p>
              </div>
              <button
                @click="router.delete(`/teacher/lessons/${lesson.id}`)"
                class="px-2.5 py-1 text-red-600 hover:bg-red-50 text-xs rounded"
              >
                Remove
              </button>
            </div>
            <div v-if="!module.lessons || module.lessons.length === 0" class="p-6 text-center text-xs text-gray-400">
              No lessons added to this module yet. Click "+ Add Lesson" to upload videos or PDFs.
            </div>
          </div>
        </div>
      </div>

      <!-- Add Module Button -->
      <button
        @click="showModuleDialog = true"
        class="w-full py-3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-xl text-sm font-semibold border border-dashed text-gray-600 dark:text-gray-300 transition"
      >
        + Add New Module
      </button>

      <!-- Add Module Modal -->
      <div v-if="showModuleDialog" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl">
          <h3 class="text-lg font-bold">Add New Course Module</h3>
          <form @submit.prevent="addModule" class="space-y-4">
            <div>
              <label class="text-xs font-semibold text-gray-500">Module Title</label>
              <input v-model="moduleForm.title" type="text" required class="w-full border rounded-lg p-2.5 text-sm mt-1" placeholder="e.g. Chapter 1: Introduction" />
            </div>
            <div class="flex justify-end gap-2 pt-2">
              <button type="button" @click="showModuleDialog = false" class="px-4 py-2 border rounded-lg text-sm">Cancel</button>
              <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold">Save Module</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Add Lesson Modal -->
      <div v-if="showLessonDialog" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl">
          <h3 class="text-lg font-bold">Add Lesson to "{{ activeModule?.title }}"</h3>
          <form @submit.prevent="uploadLesson" class="space-y-4">
            <div>
              <label class="text-xs font-semibold text-gray-500">Lesson Title</label>
              <input v-model="lessonForm.title" type="text" required class="w-full border rounded-lg p-2.5 text-sm mt-1" placeholder="e.g. Lesson 1.1 Overview" />
            </div>
            <div>
              <label class="text-xs font-semibold text-gray-500">Content Type</label>
              <select v-model="lessonForm.type" class="w-full border rounded-lg p-2.5 text-sm mt-1">
                <option value="video">🎥 Video (MP4)</option>
                <option value="pdf">📄 PDF Document</option>
                <option value="slides">📊 Presentation Slides</option>
                <option value="notes">📝 Reading Notes</option>
              </select>
            </div>
            <div v-if="lessonForm.type === 'video'">
              <label class="text-xs font-semibold text-gray-500">Video File (MP4, MOV)</label>
              <input type="file" accept="video/*" @change="onVideoChange" class="w-full border rounded-lg p-2 text-sm mt-1" />
            </div>
            <div v-if="['pdf', 'slides'].includes(lessonForm.type)">
              <label class="text-xs font-semibold text-gray-500">Document File (PDF, PPTX)</label>
              <input type="file" :accept="lessonForm.type === 'pdf' ? '.pdf' : '.ppt,.pptx'" @change="e => lessonForm.file = (e.target as HTMLInputElement).files?.[0] || null" class="w-full border rounded-lg p-2 text-sm mt-1" />
            </div>
            <div class="flex justify-end gap-2 pt-2">
              <button type="button" @click="showLessonDialog = false" class="px-4 py-2 border rounded-lg text-sm">Cancel</button>
              <button type="submit" :disabled="lessonForm.processing" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold">Upload Lesson</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </TeacherLayout>
</template>
