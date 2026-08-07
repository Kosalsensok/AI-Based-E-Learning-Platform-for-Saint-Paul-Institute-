<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  show: boolean
  module: any
}>()

const emit = defineEmits(['close', 'action'])

const handleAction = (actionType: string) => {
  emit('action', actionType, props.module)
}
</script>

<template>
  <div v-if="show && module" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
    <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-2xl w-full shadow-2xl border border-slate-200 dark:border-gray-700 overflow-hidden flex flex-col max-h-[90vh]">
      <!-- Header -->
      <div class="px-6 py-5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white flex items-center justify-between">
        <div>
          <div class="text-xs font-semibold text-blue-200 uppercase tracking-wider">📦 Module Detail View</div>
          <h2 class="text-xl font-extrabold flex items-center gap-2 mt-0.5">
            <span>{{ module.name }}: {{ module.title }}</span>
          </h2>
        </div>
        <button
          @click="emit('close')"
          class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition"
        >
          ✕
        </button>
      </div>

      <!-- Body Content -->
      <div class="p-6 overflow-y-auto space-y-6 text-slate-700 dark:text-slate-200">
        <!-- Metric Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
          <div class="bg-slate-50 dark:bg-gray-700/60 p-3.5 rounded-2xl border border-slate-200/60 dark:border-gray-600 text-center">
            <span class="text-[11px] font-bold text-slate-400 dark:text-slate-400 uppercase">Completion</span>
            <p class="text-xl font-extrabold text-blue-600 dark:text-blue-400 mt-1">{{ module.rate }}%</p>
            <span class="text-[10px] text-slate-500 font-medium">({{ module.completed }}/{{ module.students }} std)</span>
          </div>

          <div class="bg-slate-50 dark:bg-gray-700/60 p-3.5 rounded-2xl border border-slate-200/60 dark:border-gray-600 text-center">
            <span class="text-[11px] font-bold text-slate-400 dark:text-slate-400 uppercase">Avg Time Spent</span>
            <p class="text-xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-1">{{ module.avg_time }}</p>
            <span class="text-[10px] text-slate-500 font-medium">per student</span>
          </div>

          <div class="bg-slate-50 dark:bg-gray-700/60 p-3.5 rounded-2xl border border-slate-200/60 dark:border-gray-600 text-center">
            <span class="text-[11px] font-bold text-slate-400 dark:text-slate-400 uppercase">Avg Quiz Score</span>
            <p class="text-xl font-extrabold text-amber-600 dark:text-amber-400 mt-1">{{ module.avg_quiz || '58%' }}</p>
            <span class="text-[10px] text-slate-500 font-medium">attempts</span>
          </div>

          <div class="bg-slate-50 dark:bg-gray-700/60 p-3.5 rounded-2xl border border-slate-200/60 dark:border-gray-600 text-center">
            <span class="text-[11px] font-bold text-slate-400 dark:text-slate-400 uppercase">Status</span>
            <p class="text-xs font-bold mt-2.5 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-rose-700 bg-rose-100 dark:bg-rose-900/40 dark:text-rose-300">
              🔴 {{ module.status === 'red' ? 'Needs Attention' : module.status === 'yellow' ? 'Warning' : 'Good' }}
            </p>
          </div>
        </div>

        <!-- Chapter Progress -->
        <div class="space-y-3">
          <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-400">Chapter Completion Breakdown</h3>
          <div class="space-y-3 bg-slate-50 dark:bg-gray-700/40 p-4 rounded-2xl border border-slate-200/60 dark:border-gray-600">
            <div v-for="(ch, idx) in module.chapters" :key="idx" class="space-y-1 text-xs">
              <div class="flex justify-between font-bold text-slate-800 dark:text-white">
                <span>{{ ch.title }}</span>
                <span class="text-blue-600 font-extrabold">{{ ch.progress }}%</span>
              </div>
              <div class="w-full bg-slate-200 dark:bg-gray-600 h-2.5 rounded-full overflow-hidden">
                <div
                  class="h-full rounded-full transition-all duration-500"
                  :class="ch.progress >= 75 ? 'bg-emerald-500' : ch.progress >= 40 ? 'bg-amber-500' : 'bg-rose-500'"
                  :style="{ width: ch.progress + '%' }"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Insights Box -->
        <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200/80 dark:border-amber-700/50 space-y-2">
          <div class="flex items-center gap-2 text-amber-900 dark:text-amber-300 font-extrabold text-xs uppercase">
            <span>🚨 Insights & Learning Bottlenecks:</span>
          </div>
          <p class="text-xs text-amber-800 dark:text-amber-200 leading-relaxed">
            • {{ module.insight }}
          </p>
        </div>

        <!-- Quick Action Buttons -->
        <div class="pt-2 space-y-2">
          <p class="text-[11px] font-bold text-slate-400 uppercase">Recommended Actions for Teacher:</p>
          <div class="flex flex-wrap gap-2">
            <button
              @click="handleAction('add_video')"
              class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm flex items-center gap-1.5 transition"
            >
              <span>🎬</span>
              <span>Add Extra Video</span>
            </button>

            <button
              @click="handleAction('create_quiz')"
              class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm flex items-center gap-1.5 transition"
            >
              <span>🟩</span>
              <span>Create Practice Quiz</span>
            </button>

            <button
              @click="handleAction('announcement')"
              class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm flex items-center gap-1.5 transition"
            >
              <span>📢</span>
              <span>Send Announcement</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="px-6 py-4 bg-slate-50 dark:bg-gray-700/50 border-t border-slate-200 dark:border-gray-700 flex justify-end">
        <button
          @click="emit('close')"
          class="px-4 py-2 bg-slate-200 dark:bg-gray-600 hover:bg-slate-300 dark:hover:bg-gray-500 font-bold text-xs rounded-xl text-slate-700 dark:text-white transition"
        >
          Close Detail
        </button>
      </div>
    </div>
  </div>
</template>
