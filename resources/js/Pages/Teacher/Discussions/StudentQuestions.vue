<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  questions: Array<any>
  stats: any
  courses: Array<any>
}>()

const emit = defineEmits<{
  (e: 'openReply', question: any): void
  (e: 'openThread', question: any): void
  (e: 'openFaqBuilder'): void
  (e: 'markUrgent', question: any): void
  (e: 'reportSpam', question: any): void
  (e: 'remindUnanswered'): void
  (e: 'exportLog'): void
}>()

// Filters
const searchQuery = ref('')
const selectedCourse = ref('all')
const selectedModule = ref('all')
const selectedStatus = ref('all')

const filteredQuestions = computed(() => {
  return props.questions.filter((q) => {
    const matchesSearch =
      q.question.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      q.student_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      q.code.toLowerCase().includes(searchQuery.value.toLowerCase())

    const matchesCourse = selectedCourse.value === 'all' || q.course_name === selectedCourse.value
    const matchesModule = selectedModule.value === 'all' || q.module_name === selectedModule.value
    const matchesStatus = selectedStatus.value === 'all' || q.status === selectedStatus.value

    return matchesSearch && matchesCourse && matchesModule && matchesStatus
  })
})

const getStatusBadge = (status: string) => {
  if (status === 'new') return { bg: 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-300', text: '🔴 NEW', label: 'New Question' }
  if (status === 'in_progress') return { bg: 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-300', text: '🟡 IN-PROGRESS', label: 'In Progress' }
  return { bg: 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300', text: '🟢 RESOLVED', label: 'Resolved' }
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header Summary & KPI Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-300 flex items-center justify-center font-black text-lg">
          📊
        </div>
        <div>
          <div class="text-xs font-semibold text-slate-500">Unanswered</div>
          <div class="text-xl font-black text-slate-900 dark:text-white">{{ stats.unanswered || 3 }}</div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-300 flex items-center justify-center font-black text-lg">
          ✅
        </div>
        <div>
          <div class="text-xs font-semibold text-slate-500">Answered</div>
          <div class="text-xl font-black text-slate-900 dark:text-white">{{ stats.answered || 24 }}</div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-900/40 dark:text-amber-300 flex items-center justify-center font-black text-lg">
          ⏳
        </div>
        <div>
          <div class="text-xs font-semibold text-slate-500">Pending</div>
          <div class="text-xl font-black text-slate-900 dark:text-white">{{ stats.pending || 5 }}</div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 dark:bg-rose-900/40 dark:text-rose-300 flex items-center justify-center font-black text-lg animate-pulse">
          🚨
        </div>
        <div>
          <div class="text-xs font-semibold text-slate-500">Urgent SLA</div>
          <div class="text-xl font-black text-rose-600 dark:text-rose-400">{{ stats.urgent || 1 }}</div>
        </div>
      </div>
    </div>

    <!-- Search Toolbar & FAQ Trigger -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="flex flex-wrap items-center gap-3 flex-1">
        <div class="relative flex-1 min-w-[200px]">
          <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">🔍</span>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search student name, question or code..."
            class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl text-xs focus:ring-2 focus:ring-blue-500 dark:text-white"
          />
        </div>

        <select
          v-model="selectedCourse"
          class="px-3 py-2 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl text-xs font-medium text-slate-700 dark:text-slate-200"
        >
          <option value="all">Course: All</option>
          <option v-for="c in courses" :key="c.id" :value="c.title">{{ c.title }}</option>
        </select>

        <select
          v-model="selectedModule"
          class="px-3 py-2 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl text-xs font-medium text-slate-700 dark:text-slate-200"
        >
          <option value="all">Module: All</option>
          <option value="Module 1">Module 1</option>
          <option value="Module 2">Module 2</option>
          <option value="Module 3">Module 3</option>
        </select>

        <select
          v-model="selectedStatus"
          class="px-3 py-2 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl text-xs font-medium text-slate-700 dark:text-slate-200"
        >
          <option value="all">Status: All</option>
          <option value="new">🔴 New</option>
          <option value="in_progress">🟡 In-Progress</option>
          <option value="resolved">🟢 Resolved</option>
        </select>
      </div>

      <div class="flex items-center gap-2">
        <button
          @click="emit('openFaqBuilder')"
          class="px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 font-bold rounded-xl text-xs transition flex items-center gap-1.5"
        >
          <span>📚 Course FAQ Builder</span>
        </button>
      </div>
    </div>

    <!-- Student Question List -->
    <div class="space-y-4">
      <div
        v-for="q in filteredQuestions"
        :key="q.id"
        class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-4 transition hover:shadow-md"
      >
        <!-- Top Bar: Status, Chapter & Time -->
        <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-slate-100 dark:border-gray-700">
          <div class="flex items-center gap-2">
            <span :class="['px-2.5 py-1 rounded-full text-[10px] font-black border', getStatusBadge(q.status).bg]">
              {{ getStatusBadge(q.status).text }}
            </span>
            <span class="text-xs text-slate-400 font-semibold">• {{ q.created_at }}</span>
            <span v-if="q.urgent" class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
              🚨 URGENT SLA
            </span>
          </div>

          <div class="px-3 py-1 bg-slate-100 dark:bg-gray-700 rounded-lg text-xs font-bold text-slate-600 dark:text-slate-300">
            {{ q.module_name }} · {{ q.chapter_name }}
          </div>
        </div>

        <!-- Student Info & Question Body -->
        <div class="flex items-start gap-3">
          <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 flex items-center justify-center font-bold text-xl">
            {{ q.avatar || '👤' }}
          </div>

          <div class="flex-1 space-y-1.5">
            <div class="flex flex-wrap items-center gap-2">
              <span class="font-black text-sm text-slate-900 dark:text-white">{{ q.student_name }}</span>
              <span class="text-xs text-slate-500 font-semibold">({{ q.student_id_code }})</span>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-50 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300">
                {{ q.student_major }}
              </span>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                📖 {{ q.learning_mode }}
              </span>
            </div>

            <p class="text-xs sm:text-sm text-slate-800 dark:text-slate-100 font-medium leading-relaxed bg-slate-50 dark:bg-gray-700/50 p-3 rounded-xl border border-slate-100 dark:border-gray-700">
              💬 "{{ q.question }}"
            </p>

            <div v-if="q.attachments && q.attachments.length" class="flex items-center gap-2 pt-1">
              <span v-for="att in q.attachments" :key="att" class="px-2.5 py-1 bg-slate-200 dark:bg-gray-600 text-slate-700 dark:text-slate-200 rounded-lg text-[11px] font-semibold flex items-center gap-1">
                <span>📎</span> {{ att }}
              </span>
            </div>
          </div>
        </div>

        <!-- Latest Reply Preview if resolved/answered -->
        <div v-if="q.answer" class="p-3.5 bg-blue-50/70 dark:bg-blue-950/40 rounded-xl border border-blue-100 dark:border-blue-900/60 text-xs text-blue-950 dark:text-blue-200 space-y-1">
          <div class="font-extrabold flex items-center gap-1.5">
            <span>👨‍🏫 Instructor Solution:</span>
          </div>
          <p class="text-xs font-normal leading-relaxed">{{ q.answer }}</p>
        </div>

        <!-- Action Toolbar per Question -->
        <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-slate-100 dark:border-gray-700 text-xs">
          <div class="flex items-center gap-2 text-slate-500 font-semibold">
            <span>💬 {{ q.replies_count }} Replies</span>
          </div>

          <div class="flex flex-wrap items-center gap-2">
            <button
              @click="emit('openReply', q)"
              class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-sm transition flex items-center gap-1"
            >
              <span>✍️</span> {{ q.status === 'resolved' ? 'View/Edit Reply' : 'Reply' }}
            </button>

            <button
              @click="emit('openThread', q)"
              class="px-3 py-2 bg-slate-100 dark:bg-gray-700 hover:bg-slate-200 text-slate-700 dark:text-white font-bold rounded-xl transition flex items-center gap-1"
            >
              <span>👁</span> View Thread
            </button>

            <button
              @click="emit('markUrgent', q)"
              class="px-3 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 font-semibold rounded-xl transition"
            >
              <span>📌</span> Mark Urgent
            </button>

            <button
              @click="emit('reportSpam', q)"
              class="px-2.5 py-2 text-slate-400 hover:text-red-600 font-semibold transition"
            >
              <span>🚩</span> Spam
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Bulk Actions Footer -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
      <div class="flex items-center gap-2">
        <span class="font-bold text-slate-600 dark:text-slate-300">Bulk Actions:</span>
        <button
          @click="emit('remindUnanswered')"
          class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-sm transition"
        >
          📧 Remind Unanswered
        </button>
      </div>

      <button
        @click="emit('exportLog')"
        class="px-3.5 py-2 bg-slate-100 dark:bg-gray-700 hover:bg-slate-200 text-slate-700 dark:text-white font-extrabold rounded-xl transition flex items-center gap-1.5"
      >
        <span>📋</span> Export Q&A Log
      </button>
    </div>
  </div>
</template>
