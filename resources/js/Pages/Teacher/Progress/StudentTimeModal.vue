<script setup lang="ts">
const props = defineProps<{
  show: boolean
  student: any
}>()

const emit = defineEmits(['close', 'encourage', 'recommend'])
</script>

<template>
  <div v-if="show && student" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
    <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-xl w-full shadow-2xl border border-slate-200 dark:border-gray-700 overflow-hidden flex flex-col max-h-[90vh]">
      <!-- Header -->
      <div class="px-6 py-5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white flex items-center justify-between">
        <div>
          <div class="text-xs font-semibold text-emerald-200 uppercase tracking-wider">⏱️ Learning Time Detail</div>
          <h2 class="text-xl font-extrabold flex items-center gap-2 mt-0.5">
            <span>{{ student.student }}</span>
          </h2>
        </div>
        <button
          @click="emit('close')"
          class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition"
        >
          ✕
        </button>
      </div>

      <!-- Body -->
      <div class="p-6 overflow-y-auto space-y-6 text-slate-700 dark:text-slate-200">
        <!-- Top Metrics Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
          <div class="bg-slate-50 dark:bg-gray-700/60 p-3 rounded-2xl border border-slate-200/60 text-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase">Total Time</span>
            <p class="text-lg font-extrabold text-emerald-600 dark:text-emerald-400 mt-0.5">{{ student.total_time }}</p>
          </div>
          <div class="bg-slate-50 dark:bg-gray-700/60 p-3 rounded-2xl border border-slate-200/60 text-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase">Last Active</span>
            <p class="text-lg font-extrabold text-blue-600 dark:text-blue-400 mt-0.5">{{ student.last_active }}</p>
          </div>
          <div class="bg-slate-50 dark:bg-gray-700/60 p-3 rounded-2xl border border-slate-200/60 text-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase">Avg / Session</span>
            <p class="text-lg font-extrabold text-indigo-600 dark:text-indigo-400 mt-0.5">{{ student.avg_session }}</p>
          </div>
          <div class="bg-slate-50 dark:bg-gray-700/60 p-3 rounded-2xl border border-slate-200/60 text-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase">Sessions This Week</span>
            <p class="text-lg font-extrabold text-purple-600 dark:text-purple-400 mt-0.5">{{ student.sessions_week }}</p>
          </div>
        </div>

        <!-- Time by Content Type -->
        <div class="space-y-3">
          <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Time by Content Type</h3>
          <div class="grid grid-cols-2 gap-3">
            <div class="flex items-center gap-3 p-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/40">
              <span class="text-xl">🎬</span>
              <div>
                <div class="text-[11px] font-bold text-slate-500">Videos</div>
                <div class="text-sm font-extrabold text-blue-600 dark:text-blue-400">{{ student.videos_time }}</div>
              </div>
            </div>

            <div class="flex items-center gap-3 p-3 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800/40">
              <span class="text-xl">📄</span>
              <div>
                <div class="text-[11px] font-bold text-slate-500">PDFs</div>
                <div class="text-sm font-extrabold text-indigo-600 dark:text-indigo-400">{{ student.pdfs_time }}</div>
              </div>
            </div>

            <div class="flex items-center gap-3 p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800/40">
              <span class="text-xl">📊</span>
              <div>
                <div class="text-[11px] font-bold text-slate-500">Slides</div>
                <div class="text-sm font-extrabold text-amber-600 dark:text-amber-400">{{ student.slides_time }}</div>
              </div>
            </div>

            <div class="flex items-center gap-3 p-3 rounded-xl bg-purple-50 dark:bg-purple-900/20 border border-purple-100 dark:border-purple-800/40">
              <span class="text-xl">📝</span>
              <div>
                <div class="text-[11px] font-bold text-slate-500">Quizzes</div>
                <div class="text-sm font-extrabold text-purple-600 dark:text-purple-400">{{ student.quiz_time }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Most & Least Time Spent On -->
        <div class="space-y-2 text-xs bg-slate-50 dark:bg-gray-700/40 p-4 rounded-2xl border border-slate-200/60 dark:border-gray-600">
          <div class="flex justify-between items-center py-1 border-b border-slate-200/60 dark:border-gray-600">
            <span class="font-semibold text-slate-500">Most Time Spent On:</span>
            <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ student.most_time_on }}</span>
          </div>
          <div class="flex justify-between items-center py-1">
            <span class="font-semibold text-slate-500">Least Time Spent On:</span>
            <span class="font-bold text-rose-600 dark:text-rose-400">{{ student.least_time_on }}</span>
          </div>
        </div>

        <!-- Teacher Actions -->
        <div class="flex gap-2 pt-2">
          <button
            @click="emit('encourage', student)"
            class="flex-1 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold text-xs shadow-sm flex items-center justify-center gap-1.5 transition"
          >
            <span>📧</span>
            <span>Encourage Student</span>
          </button>
          <button
            @click="emit('recommend', student)"
            class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-xs shadow-sm flex items-center justify-center gap-1.5 transition"
          >
            <span>🎯</span>
            <span>Recommend Content</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
