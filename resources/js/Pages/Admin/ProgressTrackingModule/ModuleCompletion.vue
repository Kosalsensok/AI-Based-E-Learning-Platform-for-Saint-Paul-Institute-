<script setup lang="ts">
import { ref } from 'vue'
import type { ModuleAnalyticsItem } from './types'

const props = defineProps<{
  modules: ModuleAnalyticsItem[]
}>()

const emit = defineEmits<{
  (e: 'addPracticeQuiz', moduleId: number): void
  (e: 'sendStudyTip', moduleId: number): void
}>()

const selectedCourse = ref('C Programming Basics')

const courseOptions = [
  'C Programming Basics',
  'Tourism Management 101',
  'English Literature',
  'Agronomy Basics',
]

// Helper function to resolve dynamic status logic consistently
const getStatusInfo = (m: ModuleAnalyticsItem) => {
  if (m.status_color === 'red' || m.dropout_rate >= 10) {
    return {
      label: '🔴 High Difficulty',
      badgeClass: 'bg-rose-500/20 text-rose-300 border-rose-500/40 shadow-sm shadow-rose-500/10',
      cardBorder: 'border-rose-500/50 bg-rose-950/20 shadow-rose-950/20',
      barColor: 'bg-rose-500',
      textColor: 'text-rose-400',
    }
  } else if (m.status_color === 'yellow' || m.dropout_rate >= 5) {
    return {
      label: '🟡 Moderate',
      badgeClass: 'bg-amber-500/20 text-amber-300 border-amber-500/40 shadow-sm shadow-amber-500/10',
      cardBorder: 'border-amber-500/40 bg-amber-950/20 shadow-amber-950/20',
      barColor: 'bg-amber-500',
      textColor: 'text-amber-300',
    }
  }
  return {
    label: '🟢 Smooth',
    badgeClass: 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40 shadow-sm shadow-emerald-500/10',
    cardBorder: 'border-slate-700/60 bg-[#0d1222]/95',
    barColor: 'bg-emerald-500',
    textColor: 'text-emerald-400',
  }
}
</script>

<template>
  <div class="space-y-5 text-xs font-sans">
    <!-- Header Banner -->
    <div class="bg-[#0d1222]/95 border border-purple-500/30 rounded-2xl p-5 shadow-2xl flex flex-col md:flex-row items-center justify-between gap-4">
      <div class="space-y-1">
        <h3 class="text-base font-black text-white flex items-center gap-2.5">
          <div class="p-1.5 rounded-lg bg-purple-500/20 border border-purple-500/40 flex items-center justify-center shrink-0 shadow-inner">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
              <path d="M3 7V17C3 18.1046 3.89543 19 5 19H19C20.1046 19 21 18.1046 21 17V9C21 7.89543 20.1046 7 19 7H13L11 5H5C3.89543 5 3 5.89543 3 7Z" fill="#9333EA" fill-opacity="0.3" stroke="#C084FC" stroke-width="2" />
            </svg>
          </div>
          <span>MODULE COMPLETION ANALYTICS</span>
        </h3>
        <p class="text-slate-200 text-xs font-semibold">Analyze student completion rates per module to identify learning bottlenecks and difficult topics.</p>
      </div>

      <div class="flex items-center gap-2.5">
        <label class="text-slate-300 font-bold">Course:</label>
        <select v-model="selectedCourse" class="bg-[#121827] text-slate-200 border border-slate-700/80 rounded-xl px-3 py-2 text-xs font-semibold focus:border-purple-500 focus:outline-none">
          <option v-for="c in courseOptions" :key="c" :value="c">{{ c }}</option>
        </select>
      </div>
    </div>

    <!-- Modules Breakdown Cards -->
    <div class="space-y-4">
      <div
        v-for="m in modules"
        :key="m.id"
        class="border rounded-2xl p-5 shadow-xl transition-all space-y-4"
        :class="getStatusInfo(m).cardBorder"
      >
        <!-- Card Title & Header Status -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-700/60 pb-3.5">
          <div class="flex items-center gap-3">
            <div class="p-2 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center shrink-0">
              <svg class="w-5 h-5 text-amber-400" viewBox="0 0 24 24" fill="none">
                <path d="M3 7V17C3 18.1046 3.89543 19 5 19H19C20.1046 19 21 18.1046 21 17V9C21 7.89543 20.1046 7 19 7H13L11 5H5C3.89543 5 3 5.89543 3 7Z" fill="#F59E0B" fill-opacity="0.3" stroke="#FBBF24" stroke-width="2" />
              </svg>
            </div>
            <div>
              <h4 class="font-black text-sm text-white tracking-tight">{{ m.title }}</h4>
              <div class="text-xs text-slate-300 font-semibold mt-0.5">Target Course: <span class="text-purple-300 font-bold">{{ m.course }}</span></div>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <span
              class="px-3.5 py-1.5 rounded-full text-xs font-black border tracking-wide"
              :class="getStatusInfo(m).badgeClass"
            >
              {{ getStatusInfo(m).label }}
            </span>
          </div>
        </div>

        <!-- Balanced 4-Column Metrics Grid (Fills Right Side Layout Space) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
          <!-- Box 1: Completion Rate -->
          <div class="bg-[#121827] p-3.5 rounded-xl border border-slate-700/80 space-y-1.5 shadow-md">
            <span class="text-slate-300 text-[10px] block font-bold tracking-wider uppercase">COMPLETION RATE</span>
            <p class="text-base font-black" :class="getStatusInfo(m).textColor">
              {{ m.completed_percent }}% Completed
            </p>
            <div class="w-full h-2 bg-slate-900 rounded-full overflow-hidden border border-slate-800 p-0.5">
              <div class="h-full rounded-full transition-all duration-500 shadow-sm" :class="getStatusInfo(m).barColor" :style="{ width: m.completed_percent + '%' }"></div>
            </div>
          </div>

          <!-- Box 2: Average Time -->
          <div class="bg-[#121827] p-3.5 rounded-xl border border-slate-700/80 space-y-1.5 shadow-md">
            <span class="text-slate-300 text-[10px] block font-bold tracking-wider uppercase">AVG TIME TO COMPLETE</span>
            <p class="text-base font-black text-amber-300">{{ m.avg_days_to_complete }} Days</p>
            <span class="text-xs text-slate-300 font-semibold block">Expected Pace: {{ m.expected_days }} Days</span>
          </div>

          <!-- Box 3: Dropout Rate -->
          <div class="bg-[#121827] p-3.5 rounded-xl border border-slate-700/80 space-y-1.5 shadow-md">
            <span class="text-slate-300 text-[10px] block font-bold tracking-wider uppercase">MODULE DROPOUT RATE</span>
            <p class="text-base font-black flex items-center gap-1.5" :class="m.dropout_rate >= 10 ? 'text-rose-400' : 'text-slate-200'">
              <span>{{ m.dropout_rate }}%</span>
              <span v-if="m.dropout_rate >= 10" class="text-xs px-1.5 py-0.5 bg-rose-500/20 border border-rose-500/40 rounded text-rose-300">⚠️ High Drop</span>
            </p>
            <span class="text-xs text-slate-300 font-semibold block">Students stuck or stopped</span>
          </div>

          <!-- Box 4: Quick Module Actions (Balances Layout Grid & Eliminates Empty Space) -->
          <div class="bg-[#121827] p-3.5 rounded-xl border border-slate-700/80 flex flex-col justify-between space-y-2 shadow-md">
            <span class="text-slate-300 text-[10px] block font-bold tracking-wider uppercase">MODULE QUICK ACTIONS</span>
            <div class="space-y-1.5">
              <button
                @click="emit('addPracticeQuiz', m.id)"
                class="w-full py-1.5 px-2.5 bg-purple-600/30 hover:bg-purple-600/60 border border-purple-500/40 text-purple-200 rounded-lg text-xs font-bold flex items-center justify-center gap-1.5 transition-all active:scale-95 shadow-sm"
                title="Add interactive practice quiz to reinforce learning"
              >
                <span>⚡</span> Add Practice Drill
              </button>
              <button
                @click="emit('sendStudyTip', m.id)"
                class="w-full py-1.5 px-2.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 rounded-lg text-xs font-semibold flex items-center justify-center gap-1.5 transition-all active:scale-95 shadow-sm"
                title="Send automated study tips to lagging students"
              >
                <span>📧</span> Send Study Tip
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- AI Educational Insight Box -->
    <div class="bg-gradient-to-r from-purple-950/70 via-indigo-950/70 to-slate-900 border border-purple-500/50 p-5 rounded-2xl shadow-2xl space-y-3.5">
      <div class="flex items-center gap-2.5 text-purple-300 font-black text-xs uppercase tracking-wider">
        <span class="text-xl">🤖</span>
        <span>AI EDUCATIONAL INSIGHT & ACTION RECOMMENDATION</span>
      </div>

      <p class="text-slate-200 text-xs leading-relaxed font-semibold">
        "Module 3 (Control Flow - Loops & If-Else) and Module 4 (Functions & Pointers) have a <strong class="text-rose-400 font-extrabold">12% - 15% drop-out rate</strong> and take up to <strong class="text-amber-300 font-extrabold">10 days to complete</strong> (over expected pace). We strongly suggest adding practice drills or sending a targeted study guide."
      </p>

      <div class="flex flex-wrap items-center gap-3 border-t border-purple-500/30 pt-3.5">
        <button
          @click="emit('addPracticeQuiz', 3)"
          class="px-5 py-2.5 bg-purple-600 hover:bg-purple-500 text-white rounded-xl font-bold shadow-lg shadow-purple-600/40 flex items-center gap-2 transition-all active:scale-95 ring-1 ring-purple-400/50 hover:shadow-purple-500/60"
        >
          <svg class="w-4 h-4 text-white shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m-8-8h16" />
          </svg>
          <span>＋ Add Practice Quiz to Module 3</span>
        </button>

        <button
          @click="emit('sendStudyTip', 3)"
          class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 border border-slate-600/80 text-slate-200 rounded-xl font-bold shadow-md flex items-center gap-2 transition-all active:scale-95 hover:border-slate-500"
        >
          <svg class="w-4 h-4 text-teal-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
          <span>📧 Send Study Tip to Module 3 Students</span>
        </button>
      </div>
    </div>
  </div>
</template>
