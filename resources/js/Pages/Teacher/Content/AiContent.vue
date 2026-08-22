<script setup lang="ts">
import { ref, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'

const props = defineProps<{
  courses: Array<any>
  aiContents?: Array<any>
  selectedCourseId?: number | null
}>()

const searchQuery = ref('')
const selectedType = ref<'all' | 'quiz' | 'summary' | 'flashcards'>('all')
const statusFilter = ref<'all' | 'draft' | 'approved'>('all')

// Generate Modal state
const showGenerateModal = ref(false)
const generationType = ref<'quiz' | 'summary' | 'flashcards'>('quiz')
const isGenerating = ref(false)
const generationSuccess = ref(false)

const generateForm = useForm({
  course_id: props.selectedCourseId || (props.courses?.[0]?.id ?? null),
  lesson_id: null as number | null,
  topic: '',
  num_questions: 4,
  text_content: '',
  context_text: '',
})

// Selected Course Lessons for dropdown
const availableLessons = computed(() => {
  const course = props.courses?.find(c => c.id == generateForm.course_id)
  if (!course) return []
  const lessons: any[] = []
  if (course.modules) {
    course.modules.forEach((m: any) => {
      if (m.lessons) lessons.push(...m.lessons)
      if (m.chapters) {
        m.chapters.forEach((ch: any) => {
          if (ch.lessons) lessons.push(...ch.lessons)
        })
      }
    })
  }
  return lessons
})

// Preview / Review Modal State
const showReviewModal = ref(false)
const activeAiItem = ref<any>(null)

const openReviewModal = (item: any) => {
  activeAiItem.value = item
  showReviewModal.value = true
}

const openGenerateModal = (type: 'quiz' | 'summary' | 'flashcards') => {
  generationType.value = type
  generateForm.course_id = props.selectedCourseId || (props.courses?.[0]?.id ?? null)
  generateForm.lesson_id = availableLessons.value[0]?.id || null
  generateForm.topic = ''
  showGenerateModal.value = true
  generationSuccess.value = false
}

const submitGenerate = () => {
  if (!generateForm.course_id) return
  isGenerating.value = true

  const endpoint = generationType.value === 'quiz' 
    ? `/teacher/courses/${generateForm.course_id}/ai/generate-quiz`
    : generationType.value === 'summary'
    ? `/teacher/courses/${generateForm.course_id}/ai/summarize`
    : `/teacher/courses/${generateForm.course_id}/ai/flashcards`

  generateForm.post(endpoint, {
    preserveScroll: true,
    onSuccess: () => {
      isGenerating.value = false
      generationSuccess.value = true
      setTimeout(() => {
        showGenerateModal.value = false
      }, 1000)
    },
    onError: () => {
      isGenerating.value = false
    }
  })
}

const approveItem = (item: any) => {
  router.post(`/teacher/courses/ai/${item.id}/approve`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      if (activeAiItem.value?.id === item.id) {
        activeAiItem.value.status = 'approved'
      }
    }
  })
}

const deleteItem = (item: any) => {
  if (!confirm(`Are you sure you want to delete "${item.title}"?`)) return
  router.delete(`/teacher/courses/ai/${item.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      showReviewModal.value = false
    }
  })
}

const filteredList = computed(() => {
  let list = props.aiContents || []
  if (selectedType.value !== 'all') {
    list = list.filter(item => item.type === selectedType.value)
  }
  if (statusFilter.value !== 'all') {
    list = list.filter(item => item.status === statusFilter.value)
  }
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(item => 
      item.title?.toLowerCase().includes(q) || 
      item.course?.title?.toLowerCase().includes(q) ||
      item.type?.toLowerCase().includes(q)
    )
  }
  return list
})
</script>

<template>
  <div class="space-y-6">
    <!-- HIGHLIGHTED PRO PURPLE PANEL -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-purple-900 via-indigo-950 to-slate-900 p-6 md:p-8 text-white border border-purple-500/30 shadow-2xl">
      <!-- Glow effect -->
      <div class="absolute -top-20 -right-20 w-80 h-80 bg-purple-500/20 rounded-full blur-3xl pointer-events-none"></div>
      <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>

      <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="space-y-3 max-w-2xl">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/20 border border-purple-400/40 text-purple-300 text-xs font-bold uppercase tracking-wider">
            <span>✨ Cloudflare Workers AI</span>
            <span class="w-1.5 h-1.5 rounded-full bg-purple-400 animate-pulse"></span>
            <span>AI Gateway Connected</span>
          </div>

          <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight">
            AI-Assisted Content Generation 🤖
          </h2>
          <p class="text-xs md:text-sm text-purple-200/80 leading-relaxed">
            Generate multiple-choice quizzes, extract key summaries from transcripts, and create study flashcards automatically via Cloudflare AI Gateway. 
            All AI-generated items are saved as <span class="font-bold text-amber-300">Draft</span> for teacher review and approval before publishing.
          </p>
        </div>

        <!-- 3 ACTION BUTTONS -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 shrink-0">
          <button
            @click="openGenerateModal('quiz')"
            type="button"
            class="px-4 py-3.5 rounded-2xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg shadow-purple-600/30 transition-all flex flex-col items-center justify-center gap-1.5 text-center cursor-pointer border border-purple-400/30 hover:scale-[1.02]"
          >
            <span class="text-xl">❓</span>
            <span>បង្កើត Quiz ស្វ័យប្រវត្តិ</span>
            <span class="text-[10px] text-purple-200 font-normal">MCQ Questions Generator</span>
          </button>

          <button
            @click="openGenerateModal('summary')"
            type="button"
            class="px-4 py-3.5 rounded-2xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white font-bold text-xs shadow-lg shadow-indigo-600/30 transition-all flex flex-col items-center justify-center gap-1.5 text-center cursor-pointer border border-indigo-400/30 hover:scale-[1.02]"
          >
            <span class="text-xl">📜</span>
            <span>សង្ខេបមេរៀន</span>
            <span class="text-[10px] text-indigo-200 font-normal">Auto-Summarize Transcripts</span>
          </button>

          <button
            @click="openGenerateModal('flashcards')"
            type="button"
            class="px-4 py-3.5 rounded-2xl bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-500 hover:to-purple-500 text-white font-bold text-xs shadow-lg shadow-violet-600/30 transition-all flex flex-col items-center justify-center gap-1.5 text-center cursor-pointer border border-violet-400/30 hover:scale-[1.02]"
          >
            <span class="text-xl">🗂️</span>
            <span>បង្កើត Flashcards</span>
            <span class="text-[10px] text-violet-200 font-normal">Key Terms Extraction</span>
          </button>
        </div>
      </div>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 border border-slate-200 dark:border-gray-700 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="flex items-center gap-2 flex-wrap">
        <button
          v-for="type in ['all', 'quiz', 'summary', 'flashcards'] as const"
          :key="type"
          @click="selectedType = type"
          :class="[
            'px-3 py-1.5 rounded-xl text-xs font-bold capitalize transition-all',
            selectedType === type
              ? 'bg-purple-600 text-white shadow-sm'
              : 'bg-slate-100 dark:bg-gray-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200'
          ]"
        >
          {{ type === 'all' ? 'All Content' : type }}
        </button>

        <span class="h-4 w-px bg-slate-200 dark:bg-gray-700 mx-1"></span>

        <button
          v-for="status in ['all', 'draft', 'approved'] as const"
          :key="status"
          @click="statusFilter = status"
          :class="[
            'px-3 py-1.5 rounded-xl text-xs font-bold capitalize transition-all',
            statusFilter === status
              ? 'bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 shadow-sm'
              : 'bg-slate-100 dark:bg-gray-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200'
          ]"
        >
          {{ status === 'all' ? 'All Status' : status }}
        </button>
      </div>

      <div class="relative w-full md:w-64">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search AI drafts & content..."
          class="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:outline-none"
        />
        <span class="absolute left-3 top-2.5 text-slate-400 text-xs">🔍</span>
      </div>
    </div>

    <!-- AI DRAFTS & APPROVED CONTENT LIST -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 shadow-sm overflow-hidden">
      <div class="p-4 border-b border-slate-200 dark:border-gray-700 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <span class="p-1.5 rounded-lg bg-purple-500/10 text-purple-600 dark:text-purple-400 text-sm">🤖</span>
          <h3 class="font-bold text-sm text-slate-900 dark:text-white">AI Content Approval Queue & Repository</h3>
        </div>
        <span class="text-xs font-semibold text-slate-500">{{ filteredList.length }} items total</span>
      </div>

      <div v-if="filteredList.length === 0" class="p-12 text-center text-slate-400 text-xs">
        <div class="text-4xl mb-2">✨</div>
        <p class="font-bold text-slate-700 dark:text-slate-200">No AI-generated content found.</p>
        <p class="text-slate-400 mt-1">Click one of the generate buttons above to create automated quizzes, summaries, or flashcards.</p>
      </div>

      <div v-else class="divide-y divide-slate-100 dark:divide-gray-700">
        <div 
          v-for="item in filteredList" 
          :key="item.id"
          class="p-4 hover:bg-slate-50 dark:hover:bg-gray-700/50 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4"
        >
          <div class="flex items-start gap-3.5">
            <div 
              :class="[
                'w-10 h-10 rounded-xl flex items-center justify-center text-lg font-bold shrink-0 mt-0.5 border',
                item.type === 'quiz' ? 'bg-purple-500/10 border-purple-500/30 text-purple-500' :
                item.type === 'summary' ? 'bg-indigo-500/10 border-indigo-500/30 text-indigo-500' :
                'bg-violet-500/10 border-violet-500/30 text-violet-500'
              ]"
            >
              {{ item.type === 'quiz' ? '❓' : item.type === 'summary' ? '📜' : '🗂️' }}
            </div>

            <div class="space-y-1">
              <div class="flex items-center gap-2 flex-wrap">
                <h4 class="font-bold text-sm text-slate-900 dark:text-white">{{ item.title }}</h4>
                <!-- Status Badge -->
                <span 
                  :class="[
                    'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase border',
                    item.status === 'approved' 
                      ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20' 
                      : 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20'
                  ]"
                >
                  {{ item.status === 'approved' ? '✓ Approved (Published)' : '⏳ Draft (Pending Review)' }}
                </span>
              </div>

              <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400">
                <span>📚 {{ item.course?.title || 'General Course' }}</span>
                <span>•</span>
                <span class="capitalize">Type: {{ item.type }}</span>
                <span>•</span>
                <span>Created {{ new Date(item.created_at).toLocaleDateString() }}</span>
              </div>
            </div>
          </div>

          <!-- ACTIONS -->
          <div class="flex items-center gap-2 self-end md:self-center">
            <button
              @click="openReviewModal(item)"
              type="button"
              class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-gray-700 hover:bg-slate-200 dark:hover:bg-gray-600 text-slate-700 dark:text-slate-200 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer"
            >
              <span>👁️</span>
              <span>Review Content</span>
            </button>

            <button
              v-if="item.status === 'draft'"
              @click="approveItem(item)"
              type="button"
              class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-sm transition flex items-center gap-1.5 cursor-pointer"
            >
              <span>✓</span>
              <span>Approve & Publish</span>
            </button>

            <button
              @click="deleteItem(item)"
              type="button"
              class="p-1.5 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-900/30 text-rose-500 transition cursor-pointer"
              title="Delete draft"
            >
              🗑️
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- AI GENERATE MODAL -->
    <div 
      v-if="showGenerateModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
    >
      <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-gray-700 space-y-5 animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-gray-700 pb-3">
          <div class="flex items-center gap-2">
            <span class="text-2xl">✨</span>
            <h3 class="font-extrabold text-base text-slate-900 dark:text-white capitalize">
              AI Generation: {{ generationType }}
            </h3>
          </div>
          <button @click="showGenerateModal = false" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
        </div>

        <form @submit.prevent="submitGenerate" class="space-y-4 text-xs">
          <!-- Course Selection -->
          <div class="space-y-1">
            <label class="font-bold text-slate-700 dark:text-slate-300">Select Target Course</label>
            <select
              v-model="generateForm.course_id"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-slate-900 dark:text-white font-medium"
            >
              <option v-for="c in courses" :key="c.id" :value="c.id">{{ c.title }} ({{ c.code }})</option>
            </select>
          </div>

          <!-- Lesson Selection (Optional) -->
          <div class="space-y-1">
            <label class="font-bold text-slate-700 dark:text-slate-300">Select Lesson Context (Optional)</label>
            <select
              v-model="generateForm.lesson_id"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-slate-900 dark:text-white font-medium"
            >
              <option :value="null">-- Whole Course Syllabus --</option>
              <option v-for="l in availableLessons" :key="l.id" :value="l.id">{{ l.title }}</option>
            </select>
          </div>

          <!-- Specific Topic (For Quiz) -->
          <div v-if="generationType === 'quiz'" class="space-y-1">
            <label class="font-bold text-slate-700 dark:text-slate-300">Topic or Specific Focus Area</label>
            <input
              v-model="generateForm.topic"
              type="text"
              placeholder="e.g. Memory Allocation, Pointers, and Data Types"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-slate-900 dark:text-white font-medium"
            />
          </div>

          <!-- Number of questions -->
          <div v-if="generationType === 'quiz'" class="space-y-1">
            <label class="font-bold text-slate-700 dark:text-slate-300">Number of Multiple Choice Questions</label>
            <select
              v-model="generateForm.num_questions"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-slate-900 dark:text-white font-medium"
            >
              <option :value="2">2 Questions (Micro-quiz)</option>
              <option :value="4">4 Questions (Standard)</option>
              <option :value="8">8 Questions (Comprehensive)</option>
            </select>
          </div>

          <div v-if="isGenerating" class="p-4 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-600 dark:text-purple-300 flex items-center gap-3">
            <div class="w-4 h-4 border-2 border-purple-500 border-t-transparent rounded-full animate-spin"></div>
            <span>Generating high quality content with Gemini 3.7 Flash LLM...</span>
          </div>

          <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-gray-700">
            <button
              type="button"
              @click="showGenerateModal = false"
              class="px-4 py-2 rounded-xl text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-100"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="isGenerating"
              class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold shadow-md shadow-purple-600/30 hover:opacity-90 disabled:opacity-50 transition"
            >
              {{ isGenerating ? 'Generating...' : '✨ Generate Draft' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- REVIEW & INSPECT MODAL -->
    <div 
      v-if="showReviewModal && activeAiItem"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
    >
      <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 dark:border-gray-700 space-y-5 max-h-[85vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-gray-700 pb-3">
          <div class="space-y-0.5">
            <div class="flex items-center gap-2">
              <span class="text-xl">{{ activeAiItem.type === 'quiz' ? '❓' : activeAiItem.type === 'summary' ? '📜' : '🗂️' }}</span>
              <h3 class="font-extrabold text-base text-slate-900 dark:text-white">{{ activeAiItem.title }}</h3>
            </div>
            <p class="text-xs text-slate-500">Status: <span class="font-bold capitalize">{{ activeAiItem.status }}</span></p>
          </div>
          <button @click="showReviewModal = false" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
        </div>

        <!-- Render Content by Type -->
        <!-- 1. QUIZ QUESTIONS -->
        <div v-if="activeAiItem.type === 'quiz'" class="space-y-4 text-xs">
          <div 
            v-for="(q, idx) in activeAiItem.content_json?.questions || []" 
            :key="q.id"
            class="p-4 rounded-2xl bg-slate-50 dark:bg-gray-900/60 border border-slate-200 dark:border-gray-700 space-y-2"
          >
            <p class="font-bold text-slate-900 dark:text-white text-sm">
              {{ Number(idx) + 1 }}. {{ q.question }}
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
              <div 
                v-for="(opt, optIdx) in q.options" 
                :key="optIdx"
                :class="[
                  'p-2.5 rounded-xl border text-xs font-medium',
                  optIdx === q.correct_answer 
                    ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-500/40 text-emerald-700 dark:text-emerald-300 font-bold'
                    : 'bg-white dark:bg-gray-800 border-slate-200 dark:border-gray-700 text-slate-700 dark:text-slate-300'
                ]"
              >
                <span>{{ String.fromCharCode(65 + Number(optIdx)) }}.</span> {{ opt }}
                <span v-if="optIdx === q.correct_answer" class="ml-1 text-emerald-500 font-bold">✓ (Correct)</span>
              </div>
            </div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 italic pt-1">
              💡 Explanation: {{ q.explanation }}
            </p>
          </div>
        </div>

        <!-- 2. SUMMARY -->
        <div v-else-if="activeAiItem.type === 'summary'" class="space-y-4 text-xs">
          <div class="p-4 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800/40 space-y-3">
            <h4 class="font-bold text-sm text-indigo-900 dark:text-indigo-200">Overview</h4>
            <p class="text-slate-700 dark:text-slate-300 leading-relaxed">{{ activeAiItem.content_json?.overview }}</p>

            <h4 class="font-bold text-sm text-indigo-900 dark:text-indigo-200 pt-2">Key Takeaways</h4>
            <ul class="list-disc pl-5 space-y-1 text-slate-700 dark:text-slate-300">
              <li v-for="(point, pIdx) in activeAiItem.content_json?.key_takeaways || []" :key="pIdx">
                {{ point }}
              </li>
            </ul>
          </div>
        </div>

        <!-- 3. FLASHCARDS -->
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
          <div 
            v-for="card in activeAiItem.content_json?.cards || []" 
            :key="card.id"
            class="p-4 rounded-2xl bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 space-y-2"
          >
            <div class="flex justify-between items-center text-[10px] font-bold text-purple-500">
              <span>🏷️ {{ card.tag }}</span>
              <span>Level: {{ card.mastery_level }}</span>
            </div>
            <p class="font-bold text-slate-900 dark:text-white">{{ card.front }}</p>
            <div class="p-2.5 rounded-xl bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 text-slate-700 dark:text-slate-300 text-[11px]">
              {{ card.back }}
            </div>
          </div>
        </div>

        <!-- FOOTER BUTTONS -->
        <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-gray-700">
          <button
            @click="deleteItem(activeAiItem)"
            type="button"
            class="px-4 py-2 rounded-xl text-rose-600 dark:text-rose-400 font-bold hover:bg-rose-50 text-xs"
          >
            Delete Draft
          </button>

          <div class="flex items-center gap-3">
            <button
              @click="showReviewModal = false"
              type="button"
              class="px-4 py-2 rounded-xl text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-100 text-xs"
            >
              Close
            </button>
            <button
              v-if="activeAiItem.status === 'draft'"
              @click="approveItem(activeAiItem)"
              type="button"
              class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold shadow-md text-xs transition flex items-center gap-1.5"
            >
              <span>✓ Approve & Publish to Students</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
