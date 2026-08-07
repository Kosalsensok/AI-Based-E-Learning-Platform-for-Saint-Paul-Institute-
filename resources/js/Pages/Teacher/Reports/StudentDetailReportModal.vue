<script setup lang="ts">
const props = defineProps<{
  show: boolean
  student: any
}>()

const emit = defineEmits(['close', 'downloadPdf', 'exportExcel', 'sendEmail'])
</script>

<template>
  <div v-if="show && student" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
    <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-xl w-full shadow-2xl border border-slate-200 dark:border-gray-700 overflow-hidden flex flex-col max-h-[92vh]">
      <!-- Header -->
      <div class="px-6 py-5 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-2xl border border-white/30">
            {{ student.avatar || '👨‍🎓' }}
          </div>
          <div>
            <div class="text-[11px] font-bold text-blue-200 uppercase tracking-wider">Student Performance Report</div>
            <h2 class="text-xl font-black mt-0.5">{{ student.name }}</h2>
            <p class="text-xs text-blue-100 font-medium">{{ student.major }} · {{ student.semester }}</p>
          </div>
        </div>
        <button
          @click="emit('close')"
          class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition font-bold"
        >
          ✕
        </button>
      </div>

      <!-- Body Content -->
      <div class="p-6 overflow-y-auto space-y-5 text-xs text-slate-700 dark:text-slate-200">
        <!-- 1. Overall Progress Bar -->
        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-gray-700/50 border border-slate-200/80 dark:border-gray-600 space-y-2">
          <div class="flex justify-between items-center font-extrabold text-sm text-slate-800 dark:text-white">
            <span>Overall Course Progress</span>
            <span class="text-emerald-600 dark:text-emerald-400 text-lg">{{ student.progress }}%</span>
          </div>
          <div class="w-full bg-slate-200 dark:bg-gray-600 h-4 rounded-full overflow-hidden p-0.5">
            <div
              class="h-full rounded-full bg-emerald-500 transition-all duration-700 flex items-center justify-end pr-2 text-[10px] text-white font-extrabold"
              :style="{ width: student.progress + '%' }"
            >
              <span v-if="student.progress >= 20">{{ student.progress }}%</span>
            </div>
          </div>
        </div>

        <!-- 2. Learning Time Breakdown -->
        <div class="space-y-2.5">
          <h3 class="font-extrabold text-slate-400 uppercase tracking-wider text-[11px]">⏱️ Learning Time Breakdown</h3>
          <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5 text-center">
            <div class="p-2.5 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/40">
              <span class="text-[10px] font-bold text-slate-400 uppercase block">Total</span>
              <span class="text-sm font-extrabold text-blue-600 dark:text-blue-400 mt-0.5 block">{{ student.time_breakdown?.total || '32 Hours' }}</span>
            </div>

            <div class="p-2.5 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800/40">
              <span class="text-[10px] font-bold text-slate-400 uppercase block">Video</span>
              <span class="text-sm font-extrabold text-indigo-600 dark:text-indigo-400 mt-0.5 block">{{ student.time_breakdown?.video || '18h' }}</span>
            </div>

            <div class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800/40">
              <span class="text-[10px] font-bold text-slate-400 uppercase block">PDF</span>
              <span class="text-sm font-extrabold text-amber-600 dark:text-amber-400 mt-0.5 block">{{ student.time_breakdown?.pdf || '6h' }}</span>
            </div>

            <div class="p-2.5 rounded-xl bg-purple-50 dark:bg-purple-900/20 border border-purple-100 dark:border-purple-800/40">
              <span class="text-[10px] font-bold text-slate-400 uppercase block">Slides</span>
              <span class="text-sm font-extrabold text-purple-600 dark:text-purple-400 mt-0.5 block">{{ student.time_breakdown?.slides || '3h' }}</span>
            </div>

            <div class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/40 col-span-2 sm:col-span-1">
              <span class="text-[10px] font-bold text-slate-400 uppercase block">Practice</span>
              <span class="text-sm font-extrabold text-emerald-600 dark:text-emerald-400 mt-0.5 block">{{ student.time_breakdown?.practice || '5h' }}</span>
            </div>
          </div>
        </div>

        <!-- 3. Quiz Score Breakdown -->
        <div class="space-y-2.5">
          <h3 class="font-extrabold text-slate-400 uppercase tracking-wider text-[11px]">📝 Quiz Performance</h3>
          <div class="grid grid-cols-3 gap-3 text-center">
            <div class="p-3 rounded-xl bg-blue-50/80 dark:bg-blue-900/20 border border-blue-100">
              <span class="text-[10px] font-extrabold text-blue-800 dark:text-blue-300 block">🟦 Pre-Test</span>
              <span class="text-base font-extrabold text-blue-600 dark:text-blue-400 mt-0.5 block">{{ student.quiz_breakdown?.pre_test || 45 }}%</span>
            </div>

            <div class="p-3 rounded-xl bg-emerald-50/80 dark:bg-emerald-900/20 border border-emerald-100">
              <span class="text-[10px] font-extrabold text-emerald-800 dark:text-emerald-300 block">🟩 Practice</span>
              <span class="text-base font-extrabold text-emerald-600 dark:text-emerald-400 mt-0.5 block">{{ student.quiz_breakdown?.practice || 95 }}%</span>
            </div>

            <div class="p-3 rounded-xl bg-rose-50/80 dark:bg-rose-900/20 border border-rose-100">
              <span class="text-[10px] font-extrabold text-rose-800 dark:text-rose-300 block">🟥 Post-Test</span>
              <span class="text-base font-extrabold text-rose-600 dark:text-rose-400 mt-0.5 block">{{ student.quiz_breakdown?.post_test || 90 }}%</span>
            </div>
          </div>
        </div>

        <!-- 4. Assignments & Certificate Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-gray-700/40 border border-slate-200/60 dark:border-gray-600 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase">📁 Assignments</span>
            <div class="flex justify-between items-center font-bold text-slate-800 dark:text-white pt-1">
              <span>{{ student.assignments?.submitted || 4 }} Submitted / {{ student.assignments?.graded || 4 }} Graded</span>
              <span class="text-indigo-600 font-extrabold">Avg: {{ student.assignments?.avg || 92 }}%</span>
            </div>
          </div>

          <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-gray-700/40 border border-slate-200/60 dark:border-gray-600 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase">🎓 Certificate Status</span>
            <div class="pt-1">
              <span
                class="px-3 py-1 rounded-full text-xs font-extrabold inline-flex items-center gap-1"
                :class="student.certificate === 'Eligible' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300'"
              >
                {{ student.certificate === 'Eligible' ? '✅ Certificate Eligible' : '⏳ In Progress' }}
              </span>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="pt-3 flex flex-wrap gap-2.5">
          <button
            @click="emit('downloadPdf', student)"
            class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-extrabold text-xs shadow-sm flex items-center justify-center gap-1.5 transition"
          >
            <span>📄</span> Download PDF
          </button>

          <button
            @click="emit('exportExcel', student)"
            class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-extrabold text-xs shadow-sm flex items-center justify-center gap-1.5 transition"
          >
            <span>📊</span> Export Excel
          </button>

          <button
            @click="emit('sendEmail', student)"
            class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-extrabold text-xs shadow-sm flex items-center justify-center gap-1.5 transition"
          >
            <span>📧</span> Send Email
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
