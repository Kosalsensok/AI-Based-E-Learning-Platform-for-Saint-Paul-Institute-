<script setup lang="ts">
import type { WeeklyProgressMetrics } from './types'

const props = defineProps<{
  metrics: WeeklyProgressMetrics
}>()

const emit = defineEmits<{
  (e: 'downloadPdf'): void
  (e: 'emailStakeholders'): void
}>()
</script>

<template>
  <div class="space-y-5 text-xs font-sans">
    <!-- Header Banner -->
    <div class="bg-[#0d1222]/95 border border-purple-500/30 rounded-2xl p-5 shadow-2xl flex flex-col md:flex-row items-center justify-between gap-4">
      <div class="space-y-1">
        <h3 class="text-base font-black text-white flex items-center gap-2">
          <svg class="w-5 h-5 text-purple-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" fill="currentColor" fill-opacity="0.2" />
          </svg>
          <span>WEEKLY PROGRESS STATEMENT</span>
        </h3>
        <p class="text-slate-300 text-xs font-medium">Compare engagement & completion metrics between this week and last week.</p>
      </div>

      <div class="flex items-center gap-2">
        <button @click="emit('downloadPdf')" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl text-slate-200 font-semibold flex items-center gap-1.5 transition-all">
          <svg class="w-4 h-4 text-purple-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" fill="currentColor" fill-opacity="0.2" />
          </svg>
          <span>Download Weekly Report (PDF)</span>
        </button>
        <button @click="emit('emailStakeholders')" class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl font-bold shadow-md shadow-purple-600/30 flex items-center gap-1.5 transition-all">
          <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" fill="currentColor" fill-opacity="0.2" />
          </svg>
          <span>Email to Stakeholders</span>
        </button>
      </div>
    </div>

    <!-- Weekly Growth Metrics Table -->
    <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
      <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5 flex items-center gap-2">
        <svg class="w-4 h-4 text-emerald-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
        </svg>
        <span>WEEKLY GROWTH METRICS (THIS WEEK VS LAST WEEK)</span>
      </h4>

      <div class="overflow-x-auto rounded-xl border border-slate-700/80 bg-[#121827]">
        <table class="w-full text-left text-xs text-slate-300">
          <thead class="bg-slate-900 text-slate-400 uppercase font-bold border-b border-slate-700/80">
            <tr>
              <th class="p-3.5">Metric Category</th>
              <th class="p-3.5">This Week Count</th>
              <th class="p-3.5 text-right">Growth vs Last Week</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/80 font-medium">
            <tr class="hover:bg-slate-800/40 transition-colors">
              <td class="p-3.5 font-bold text-white flex items-center gap-2">
                <span>👥</span> Weekly Active Users (WAU)
              </td>
              <td class="p-3.5 font-black text-purple-300">1,890 Users</td>
              <td class="p-3.5 text-right font-black text-emerald-400">↑ +12%</td>
            </tr>

            <tr class="hover:bg-slate-800/40 transition-colors">
              <td class="p-3.5 font-bold text-white flex items-center gap-2">
                <span>⏱️</span> Average Study Hours
              </td>
              <td class="p-3.5 font-black text-purple-300">5h 15m per student</td>
              <td class="p-3.5 text-right font-black text-emerald-400">↑ +8%</td>
            </tr>

            <tr class="hover:bg-slate-800/40 transition-colors">
              <td class="p-3.5 font-bold text-white flex items-center gap-2">
                <span>🎥</span> Total Videos Watched
              </td>
              <td class="p-3.5 font-black text-purple-300">4,250 Videos</td>
              <td class="p-3.5 text-right font-black text-emerald-400">↑ +15%</td>
            </tr>

            <tr class="hover:bg-slate-800/40 transition-colors">
              <td class="p-3.5 font-bold text-white flex items-center gap-2">
                <span>📝</span> Quizzes Taken
              </td>
              <td class="p-3.5 font-black text-purple-300">1,200 Quizzes</td>
              <td class="p-3.5 text-right font-black text-amber-400">↓ -3%</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Weekly Activity Heatmap Grid -->
    <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-700/60 pb-3">
        <h4 class="font-black text-sm text-white uppercase tracking-wide">
          WEEKLY ACTIVITY HEATMAP (STUDENT STUDY HOURS)
        </h4>

        <!-- Legend -->
        <div class="flex items-center gap-3 text-[10px] text-slate-400 font-semibold">
          <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-slate-800 border border-slate-700"></span> Low</span>
          <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-purple-600/40 border border-purple-500"></span> Medium</span>
          <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-purple-500 shadow shadow-purple-500"></span> High Activity</span>
        </div>
      </div>

      <!-- Heatmap Table Grid -->
      <div class="overflow-x-auto rounded-xl border border-slate-700/80 bg-[#121827] p-3">
        <table class="w-full text-center text-xs">
          <thead class="text-slate-400 uppercase font-bold">
            <tr>
              <th class="p-2 text-left w-16">Hour</th>
              <th class="p-2">Mon</th>
              <th class="p-2">Tue</th>
              <th class="p-2">Wed</th>
              <th class="p-2">Thu</th>
              <th class="p-2">Fri</th>
              <th class="p-2">Sat</th>
              <th class="p-2">Sun</th>
            </tr>
          </thead>
          <tbody class="font-mono">
            <tr v-for="h in metrics.heatmap" :key="h.hour" class="border-t border-slate-800/60">
              <td class="p-2.5 font-bold text-slate-300 text-left font-sans">{{ h.hour }}</td>
              <td v-for="(d, dIdx) in h.days" :key="dIdx" class="p-2">
                <div
                  class="w-full h-8 rounded-lg flex items-center justify-center font-extrabold transition-transform hover:scale-105"
                  :class="
                    d.level === 'high' ? 'bg-purple-600 text-white shadow-md shadow-purple-600/30' : 
                    (d.level === 'med' ? 'bg-purple-900/60 text-purple-200 border border-purple-500/40' : 'bg-slate-800/80 text-slate-500 border border-slate-700/50')
                  "
                >
                  {{ d.level === 'high' ? '██' : (d.level === 'med' ? '▒▒' : '░░') }}
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- AI Heatmap Insight Banner -->
      <div class="p-3.5 rounded-xl bg-purple-950/40 border border-purple-500/30 text-xs text-purple-200 font-medium flex items-center gap-2">
        <span class="text-base">📊</span>
        <span>Insight: <strong>"Students are most active during weekdays between 7:00 PM and 10:00 PM."</strong></span>
      </div>
    </div>
  </div>
</template>
