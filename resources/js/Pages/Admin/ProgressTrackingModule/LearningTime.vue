<script setup lang="ts">
import type { LearningTimeData } from './types'

const props = defineProps<{
  data: LearningTimeData
}>()

const emit = defineEmits<{
  (e: 'viewAllLogs'): void
}>()
</script>

<template>
  <div class="space-y-5 text-xs font-sans">
    <!-- Header Banner -->
    <div class="bg-[#0d1222]/95 border border-purple-500/30 rounded-2xl p-5 shadow-2xl space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-700/60 pb-3.5">
        <div>
          <h3 class="text-base font-black text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-purple-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="9" fill="currentColor" fill-opacity="0.2" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3" />
            </svg>
            <span>LEARNING TIME ANALYTICS</span>
          </h3>
          <p class="text-slate-300 text-xs font-medium">Analyze student engagement rate — Active Time, Video Watch Time, and Reading Time</p>
        </div>

        <button @click="emit('viewAllLogs')" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl text-slate-200 font-semibold flex items-center gap-1.5 transition-all">
          <svg class="w-4 h-4 text-purple-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" fill="currentColor" fill-opacity="0.2" />
          </svg>
          <span>View All Active Logs →</span>
        </button>
      </div>

      <!-- 4 KPI Cards -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-[#121827] p-4 rounded-xl border border-slate-700/80 space-y-1">
          <div class="flex items-center text-slate-400 text-[10px] font-semibold">
            <svg class="w-3.5 h-3.5 text-cyan-400 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="9" fill="currentColor" fill-opacity="0.2" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3" />
            </svg>
            <span>LEARN / DAY</span>
          </div>
          <p class="text-lg font-black text-white">{{ data.daily_avg_mins }} Minutes</p>
          <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 text-[10px] font-bold">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Normal Engagement
          </span>
        </div>

        <div class="bg-[#121827] p-4 rounded-xl border border-slate-700/80 space-y-1">
          <div class="flex items-center text-slate-400 text-[10px] font-semibold">
            <svg class="w-3.5 h-3.5 text-purple-400 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="9" fill="currentColor" fill-opacity="0.2" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3" />
            </svg>
            <span>WEEKLY AVERAGE</span>
          </div>
          <p class="text-lg font-black text-purple-300">{{ data.weekly_avg_hours }}</p>
          <span class="text-[10px] text-slate-400">per Student per Week</span>
        </div>

        <div class="bg-[#121827] p-4 rounded-xl border border-slate-700/80 space-y-1">
          <div class="flex items-center text-slate-400 text-[10px] font-semibold">
            <svg class="w-3.5 h-3.5 text-amber-400 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" fill="currentColor" fill-opacity="0.2" />
            </svg>
            <span>VIDEO VIEW TIME</span>
          </div>
          <p class="text-lg font-black text-cyan-300">3h 10m ({{ data.video_split_percent }}%)</p>
          <span class="text-[10px] text-cyan-400 font-semibold">Primary Learning Format</span>
        </div>

        <div class="bg-[#121827] p-4 rounded-xl border border-slate-700/80 space-y-1">
          <span class="text-slate-400 text-[10px] block font-semibold">📄 DOC & PDF READING</span>
          <p class="text-lg font-black text-amber-300">2h 05m ({{ data.document_split_percent }}%)</p>
          <span class="text-[10px] text-amber-400 font-semibold">Supplementary Notes</span>
        </div>
      </div>
    </div>

    <!-- Daily Learning Time Chart & Peak Hours Banner -->
    <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
      <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
        <h4 class="font-black text-sm text-white uppercase tracking-wide">
          DAILY LEARNING TIME DISTRIBUTION (HOURS)
        </h4>
        <span class="text-slate-400 font-mono text-[11px]">PEAK STUDY HOURS: <strong class="text-teal-300 font-bold">{{ data.peak_hours }}</strong> (Cambodia Time)</span>
      </div>

      <!-- Custom Bar Chart Representation -->
      <div class="h-44 flex items-end justify-between gap-4 pt-6 pb-2 px-4 bg-[#121827] rounded-xl border border-slate-700/80">
        <div v-for="(dayItem, idx) in data.daily_hours" :key="idx" class="flex-1 flex flex-col items-center gap-2 h-full justify-end group">
          <span class="text-[10px] font-bold text-purple-300 group-hover:scale-110 transition-transform">{{ dayItem.hours }}h</span>
          <div class="w-full bg-slate-800 rounded-t-lg overflow-hidden flex items-end h-32 p-0.5">
            <div
              class="w-full bg-gradient-to-t from-purple-600 to-teal-400 rounded-t transition-all duration-500 group-hover:brightness-125"
              :style="{ height: (dayItem.hours / 150) * 100 + '%' }"
            ></div>
          </div>
          <span class="text-[11px] font-bold text-slate-400">{{ dayItem.day }}</span>
        </div>
      </div>
    </div>

    <!-- Top Active Students Leaderboard -->
    <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
      <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5">
        🏆 TOP ACTIVE STUDENTS THIS WEEK
      </h4>

      <div class="overflow-x-auto rounded-xl border border-slate-700/80 bg-[#121827]">
        <table class="w-full text-left text-xs text-slate-300">
          <thead class="bg-slate-900 text-slate-400 uppercase font-bold border-b border-slate-700/80">
            <tr>
              <th class="p-3">Rank & Student</th>
              <th class="p-3">Major</th>
              <th class="p-3">Learning Time</th>
              <th class="p-3">Completed Items</th>
              <th class="p-3 text-right">Points Earned</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/80 font-medium">
            <tr v-for="(s, idx) in data.top_active_students" :key="idx" class="hover:bg-slate-800/40 transition-colors">
              <td class="p-3 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-purple-500/20 text-purple-300 font-black flex items-center justify-center text-[10px]">#{{ idx + 1 }}</span>
                <span class="font-bold text-white">{{ s.name }}</span>
              </td>
              <td class="p-3 text-slate-400">{{ s.major }}</td>
              <td class="p-3 font-bold text-emerald-400">{{ s.time_spent }}</td>
              <td class="p-3 font-semibold text-slate-200">{{ s.completed_items }} Items</td>
              <td class="p-3 text-right font-black text-amber-400">+{{ s.xp_points }} XP</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
