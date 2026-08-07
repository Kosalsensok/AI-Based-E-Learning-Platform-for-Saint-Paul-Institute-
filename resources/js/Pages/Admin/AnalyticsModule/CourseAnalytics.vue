<script setup lang="ts">
import { ref } from 'vue'
import type { CourseAnalyticsData } from './types'

const props = defineProps<{
  data: CourseAnalyticsData
}>()

const emit = defineEmits<{
  (e: 'notifyTeacher', courseName: string): void
  (e: 'scheduleReview', courseName: string): void
  (e: 'archiveCourse', courseName: string): void
  (e: 'exportReport'): void
}>()

const selectedSemester = ref('this_semester')
</script>

<template>
  <div class="space-y-6 text-xs font-sans">
    <!-- Header Banner -->
    <div class="bg-[#0d1222]/95 border border-purple-500/30 rounded-2xl p-5 shadow-2xl flex flex-col md:flex-row items-center justify-between gap-4">
      <div>
        <h3 class="text-base font-black text-white flex items-center gap-2">
          <span>📚</span> COURSE ANALYTICS — POPULARITY & DIFFICULTY
        </h3>
        <p class="text-slate-400 text-xs">Analyze course completion rates, revenue performance, module drop-off heatmaps, and quality indicators.</p>
      </div>

      <div class="flex items-center gap-2">
        <select v-model="selectedSemester" class="bg-[#121827] text-slate-200 border border-slate-700/80 rounded-xl px-3 py-1.5 text-xs focus:border-purple-500 focus:outline-none">
          <option value="this_semester">Period: This Semester (2025-S1)</option>
          <option value="previous_semester">Period: Previous Semester</option>
        </select>

        <!-- Compact Icon Refresh Button -->
        <button 
          @click="emit('exportReport')" 
          title="Refresh Data"
          class="p-2 bg-slate-800 hover:bg-slate-700/90 border border-slate-700 text-slate-300 hover:text-white rounded-xl transition-all active:scale-95 flex items-center justify-center shrink-0"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
        </button>

        <button @click="emit('exportReport')" class="px-4 py-1.5 bg-purple-600 hover:bg-purple-500 text-white rounded-xl font-bold shadow-md shadow-purple-600/30 flex items-center gap-1.5 transition-all">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
          </svg>
          <span>Export Report</span>
        </button>
      </div>
    </div>

    <!-- 4 KPI Cards (Active Courses Comparison Ratio) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4 rounded-2xl shadow-xl space-y-1">
        <span class="text-slate-400 text-[10px] block font-semibold">📚 TOTAL COURSES</span>
        <p class="text-2xl font-black text-white">{{ data.kpis.total }}</p>
        <span class="text-[10px] text-emerald-400 font-bold">Catalog Scope</span>
      </div>

      <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4 rounded-2xl shadow-xl space-y-1.5">
        <span class="text-slate-400 text-[10px] block font-semibold">🟢 ACTIVE COURSES</span>
        <div class="flex items-baseline justify-between">
          <p class="text-2xl font-black text-emerald-400">{{ data.kpis.active }}</p>
          <span class="text-[11px] font-bold text-emerald-300">290 / 328 (88%)</span>
        </div>
        <div class="w-full h-1.5 bg-slate-900 rounded-full overflow-hidden border border-slate-800">
          <div class="h-full bg-emerald-400 rounded-full" style="width: 88.4%"></div>
        </div>
        <span class="text-[10px] text-slate-400 block">Currently Enrolling Ratio</span>
      </div>

      <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4 rounded-2xl shadow-xl space-y-1">
        <span class="text-slate-400 text-[10px] block font-semibold">📈 AVG COMPLETION</span>
        <p class="text-2xl font-black text-purple-300">{{ data.kpis.avg_completion }}%</p>
        <span class="text-[10px] text-slate-400">Completion Threshold</span>
      </div>

      <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4 rounded-2xl shadow-xl space-y-1">
        <span class="text-slate-400 text-[10px] block font-semibold">💰 TOTAL REVENUE</span>
        <p class="text-2xl font-black text-emerald-400">{{ data.kpis.total_revenue }}</p>
        <span class="text-[10px] text-emerald-300 font-semibold">This Semester</span>
      </div>
    </div>

    <!-- Top 10 Popular Courses Table (Interactive Links & View All) -->
    <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
      <div class="flex items-center justify-between border-b border-slate-700/60 pb-2.5">
        <h4 class="font-black text-sm text-white uppercase tracking-wide">
          🔥 TOP 10 POPULAR COURSES THIS MONTH
        </h4>
        <button @click="emit('scheduleReview', 'All Courses')" class="text-xs text-purple-300 hover:text-white font-bold transition-colors">
          View All (328 Courses) →
        </button>
      </div>

      <div class="overflow-x-auto rounded-xl border border-slate-700/80 bg-[#121827]">
        <table class="w-full text-left text-xs text-slate-300">
          <thead class="bg-slate-900 text-slate-400 uppercase font-bold border-b border-slate-700/80">
            <tr>
              <th class="p-3">Rank & Course Title</th>
              <th class="p-3">Instructor</th>
              <th class="p-3">Enrolled</th>
              <th class="p-3">Completion Rate</th>
              <th class="p-3">Rating</th>
              <th class="p-3 text-right">Revenue</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/80 font-medium">
            <tr v-for="c in data.top_popular" :key="c.rank" class="hover:bg-slate-800/40 transition-colors">
              <td class="p-3 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-purple-500/20 text-purple-300 font-black flex items-center justify-center text-[10px]">#{{ c.rank }}</span>
                <button 
                  @click="emit('scheduleReview', c.title)" 
                  class="font-bold text-white hover:text-purple-300 hover:underline transition-colors text-left cursor-pointer"
                >
                  {{ c.title }}
                </button>
              </td>
              <td class="p-3 text-slate-300">
                <button 
                  @click="emit('notifyTeacher', c.title)" 
                  class="hover:text-purple-300 hover:underline transition-colors text-left cursor-pointer"
                >
                  {{ c.teacher }}
                </button>
              </td>
              <td class="p-3 font-bold text-slate-200">{{ c.enrolled }} Students</td>
              <td class="p-3 font-bold text-emerald-400">{{ c.completed_percent }}%</td>
              <td class="p-3 font-bold text-amber-300">⭐ {{ c.rating }}</td>
              <td class="p-3 text-right font-black text-purple-300">{{ c.revenue }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Course Completion Heatmap Grid (Explicit Column Headers) -->
    <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
      <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
        <h4 class="font-black text-sm text-white uppercase tracking-wide">
          📊 COURSE COMPLETION HEATMAP (MODULE 1 TO MODULE 5)
        </h4>

        <div class="flex items-center gap-2 text-[10px]">
          <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">🟢 80-100%</span>
          <span class="px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/40">🟡 60-79%</span>
          <span class="px-2 py-0.5 rounded bg-orange-500/20 text-orange-300 border border-orange-500/40">🟠 40-59%</span>
          <span class="px-2 py-0.5 rounded bg-red-500/20 text-red-300 border border-red-500/40">🔴 &lt;40%</span>
        </div>
      </div>

      <div class="overflow-x-auto rounded-xl border border-slate-700/80 bg-[#121827] p-3">
        <table class="w-full text-center text-xs">
          <thead class="text-slate-400 uppercase font-bold border-b border-slate-800">
            <tr>
              <th class="p-2.5 text-left w-52">Course Title</th>
              <th class="p-2.5">Module 1</th>
              <th class="p-2.5">Module 2</th>
              <th class="p-2.5">Module 3</th>
              <th class="p-2.5">Module 4</th>
              <th class="p-2.5">Module 5</th>
              <th class="p-2.5 text-right w-44">Status Flag</th>
            </tr>
          </thead>
          <tbody class="font-medium">
            <tr v-for="item in data.completion_heatmap" :key="item.course" class="border-t border-slate-800/80">
              <td class="p-2.5 font-bold text-white text-left">
                <button @click="emit('scheduleReview', item.course)" class="hover:text-purple-300 hover:underline transition-colors text-left cursor-pointer">
                  {{ item.course }}
                </button>
              </td>
              <td v-for="(m, mIdx) in item.modules" :key="mIdx" class="p-2">
                <span
                  class="inline-block px-2.5 py-1 rounded-lg text-xs font-black border"
                  :class="
                    m.score >= 80 ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' : 
                    (m.score >= 60 ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : 
                    (m.score >= 40 ? 'bg-orange-500/20 text-orange-300 border-orange-500/40' : 'bg-red-500/20 text-red-300 border-red-500/40'))
                  "
                >
                  {{ m.score }}%
                </span>
              </td>
              <td class="p-2.5 text-right font-bold text-amber-300">
                {{ item.modules.find(x => x.flag)?.flag || '🟢 Smooth' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Revenue by Course (Top 5 Bar Chart) -->
    <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
      <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5">
        💰 REVENUE GENERATED BY COURSE (TOP 5)
      </h4>

      <div class="space-y-3">
        <div v-for="(c, idx) in data.revenue_by_course" :key="idx" class="space-y-1">
          <div class="flex items-center justify-between text-xs">
            <span class="font-semibold text-slate-300 w-44">{{ c.course }}</span>
            <div class="flex-1 mx-3 h-3 bg-slate-900 rounded-full overflow-hidden border border-slate-700/80">
              <div class="h-full bg-gradient-to-r from-purple-600 to-teal-400 rounded-full" :style="{ width: (c.amount / 15000) * 100 + '%' }"></div>
            </div>
            <span class="font-bold text-emerald-400 w-28 text-right">{{ c.revenue }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Courses Needing Attention (Refined Action Buttons & More Options Icon Button) -->
    <div class="bg-[#0d1222]/95 border border-amber-500/40 rounded-2xl p-5 shadow-2xl space-y-4">
      <h4 class="font-black text-sm text-amber-300 uppercase tracking-wide border-b border-slate-700/60 pb-2.5 flex items-center gap-2">
        <span>📉</span> COURSES NEEDING ATTENTION
      </h4>

      <div class="space-y-3">
        <div v-for="(c, idx) in data.needing_attention" :key="idx" class="p-3.5 bg-amber-950/20 border border-amber-500/30 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div>
            <div class="font-bold text-white text-sm flex items-center gap-2">
              <button @click="emit('scheduleReview', c.course)" class="hover:text-purple-300 hover:underline transition-colors cursor-pointer">
                {{ c.course }}
              </button>
            </div>
            <p class="text-amber-300 text-xs mt-0.5">Enrolled: <strong>{{ c.enrolled }}</strong> · Completion: <strong>{{ c.completed_percent }}%</strong> · Rating: <strong>⭐ {{ c.rating }}</strong> · Reason: <span class="italic text-slate-300">{{ c.reason }}</span></p>
          </div>

          <div class="flex items-center gap-2 shrink-0">
            <!-- Primary Action (Solid Purple) -->
            <button 
              @click="emit('notifyTeacher', c.course)" 
              class="px-3.5 py-1.5 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-xl text-xs shadow-md shadow-purple-600/30 transition-all active:scale-95 flex items-center gap-1.5"
            >
              <span>📣</span> Notify Teacher
            </button>
            <!-- Secondary Action (Outline Button) -->
            <button 
              @click="emit('scheduleReview', c.course)" 
              class="px-3.5 py-1.5 bg-transparent hover:bg-slate-800 border border-slate-600 text-slate-300 hover:text-white font-semibold rounded-xl text-xs transition-all active:scale-95"
            >
              Schedule Review
            </button>
            <!-- More Options Icon Button (···) -->
            <button 
              @click="emit('archiveCourse', c.course)" 
              title="More Options / Archive" 
              class="p-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 hover:text-white rounded-xl transition-all active:scale-95 flex items-center justify-center shrink-0"
            >
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
