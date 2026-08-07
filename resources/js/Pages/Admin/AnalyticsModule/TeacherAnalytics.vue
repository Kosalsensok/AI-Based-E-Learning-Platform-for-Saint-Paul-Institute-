<script setup lang="ts">
import { ref } from 'vue'
import type { TeacherAnalyticsData } from './types'

const props = defineProps<{
  data: TeacherAnalyticsData
}>()

const emit = defineEmits<{
  (e: 'sendFeedback', teacherName: string): void
  (e: 'scheduleReview', teacherName: string): void
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
          <span>👨‍🏫</span> TEACHER ANALYTICS — PERFORMANCE & RATING
        </h3>
        <p class="text-slate-400 text-xs">Analyze teacher performance, student ratings, course quality, and revenue impact.</p>
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

    <!-- 4 KPI Cards (Standardized Typography Scale) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4 rounded-2xl shadow-xl space-y-1">
        <span class="text-slate-400 text-[10px] block font-semibold">👨‍🏫 TOTAL TEACHERS</span>
        <p class="text-2xl font-black text-white">{{ data.kpis.total }}</p>
        <span class="text-[10px] text-emerald-400 font-bold">Active Instructors</span>
      </div>

      <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4 rounded-2xl shadow-xl space-y-1">
        <span class="text-slate-400 text-[10px] block font-semibold">⭐ AVG RATING</span>
        <p class="text-2xl font-black text-amber-300">{{ data.kpis.avg_rating }} / 5.0</p>
        <span class="text-[10px] text-slate-400">Student Satisfaction</span>
      </div>

      <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4 rounded-2xl shadow-xl space-y-1">
        <span class="text-slate-400 text-[10px] block font-semibold">📚 AVG COURSES</span>
        <p class="text-2xl font-black text-purple-300">{{ data.kpis.avg_courses }}</p>
        <span class="text-[10px] text-slate-400 font-medium">Courses / Teacher</span>
      </div>

      <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4 rounded-2xl shadow-xl space-y-1">
        <span class="text-slate-400 text-[10px] block font-semibold">💰 AVG EARNING</span>
        <p class="text-2xl font-black text-emerald-400">{{ data.kpis.avg_monthly_earn }}</p>
        <span class="text-[10px] text-emerald-300 font-semibold">Per Month</span>
      </div>
    </div>

    <!-- Top 10 Teachers Leaderboard Table (Interactive Links & View All) -->
    <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
      <div class="flex items-center justify-between border-b border-slate-700/60 pb-2.5">
        <h4 class="font-black text-sm text-white uppercase tracking-wide">
          🏆 TOP PERFORMING TEACHERS THIS SEMESTER
        </h4>
        <button @click="emit('scheduleReview', 'All Teachers')" class="text-xs text-purple-300 hover:text-white font-bold transition-colors">
          View All (145 Teachers) →
        </button>
      </div>

      <div class="overflow-x-auto rounded-xl border border-slate-700/80 bg-[#121827]">
        <table class="w-full text-left text-xs text-slate-300">
          <thead class="bg-slate-900 text-slate-400 uppercase font-bold border-b border-slate-700/80">
            <tr>
              <th class="p-3">Rank & Instructor</th>
              <th class="p-3">Courses</th>
              <th class="p-3">Students</th>
              <th class="p-3">Rating</th>
              <th class="p-3">Completion Rate</th>
              <th class="p-3 text-right">Revenue Generated</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/80 font-medium">
            <tr v-for="t in data.top_teachers" :key="t.rank" class="hover:bg-slate-800/40 transition-colors">
              <td class="p-3 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-purple-500/20 text-purple-300 font-black flex items-center justify-center text-[10px]">#{{ t.rank }}</span>
                <button 
                  @click="emit('scheduleReview', t.name)" 
                  class="font-bold text-white hover:text-purple-300 hover:underline transition-colors text-left cursor-pointer"
                >
                  {{ t.name }}
                </button>
              </td>
              <td class="p-3 text-slate-300 font-semibold">{{ t.courses }} Courses</td>
              <td class="p-3 text-slate-300 font-semibold">{{ t.students }} Students</td>
              <td class="p-3 font-bold text-amber-300">⭐ {{ t.rating }}</td>
              <td class="p-3 font-bold text-emerald-400">{{ t.completion }}%</td>
              <td class="p-3 text-right font-black text-purple-300">{{ t.revenue }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Rating Distribution & Performance Matrix -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
      <!-- Rating Distribution -->
      <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
        <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5">
          ⭐ TEACHER RATING DISTRIBUTION
        </h4>

        <div class="space-y-3">
          <div v-for="(r, idx) in data.rating_distribution" :key="idx" class="space-y-1">
            <div class="flex items-center justify-between text-xs">
              <span class="font-semibold text-slate-300 w-28">{{ r.stars }}</span>
              <div class="flex-1 mx-3 h-2.5 bg-slate-900 rounded-full overflow-hidden border border-slate-700/80">
                <div class="h-full bg-amber-400 rounded-full" :style="{ width: r.percent * 2 + '%' }"></div>
              </div>
              <span class="font-bold text-white w-32 text-right">{{ r.count }} Teachers ({{ r.percent }}%)</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Performance Matrix Scatter Card (Clean Clickable Badges, No Legend Noise) -->
      <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
        <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5">
          📈 PERFORMANCE MATRIX (COMPLETION VS RATING)
        </h4>

        <div class="grid grid-cols-2 gap-3 text-xs">
          <div class="bg-emerald-950/30 border border-emerald-500/40 p-3.5 rounded-xl space-y-2">
            <span class="font-bold text-emerald-400 text-xs block">🌟 Top Performers (4.8+ / 75%+)</span>
            <div class="flex flex-wrap gap-1.5 pt-1">
              <button 
                v-for="tName in ['Mr. Sophea', 'Ms. Dara', 'Mr. Vuthy']" 
                :key="tName"
                @click="emit('scheduleReview', tName)"
                class="px-2.5 py-1 rounded-lg bg-emerald-500/15 hover:bg-emerald-500/30 border border-emerald-500/40 text-emerald-300 font-bold text-[11px] transition-all active:scale-95 cursor-pointer shadow-sm"
              >
                {{ tName }}
              </button>
            </div>
          </div>

          <div class="bg-red-950/30 border border-red-500/40 p-3.5 rounded-xl space-y-2">
            <span class="font-bold text-red-400 text-xs block">⚠️ Needs Review (&lt;3.5 / &lt;50%)</span>
            <div class="flex flex-wrap gap-1.5 pt-1">
              <button 
                v-for="tName in ['Mr. Bunsan', 'Ms. Neary']" 
                :key="tName"
                @click="emit('scheduleReview', tName)"
                class="px-2.5 py-1 rounded-lg bg-red-500/15 hover:bg-red-500/30 border border-red-500/40 text-red-300 font-bold text-[11px] transition-all active:scale-95 cursor-pointer shadow-sm"
              >
                {{ tName }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Teachers Needing Attention (Real Names & Primary/Secondary Button Hierarchy) -->
    <div class="bg-[#0d1222]/95 border border-red-500/40 rounded-2xl p-5 shadow-2xl space-y-4">
      <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5 text-red-400 flex items-center gap-2">
        <span>🚨</span> TEACHERS NEEDING ATTENTION
      </h4>

      <div class="space-y-3">
        <div v-for="(t, idx) in data.needing_attention" :key="idx" class="p-3.5 bg-red-950/20 border border-red-500/30 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div>
            <div class="font-bold text-white text-sm flex items-center gap-2">
              <button @click="emit('scheduleReview', t.name)" class="hover:text-purple-300 hover:underline transition-colors cursor-pointer">
                {{ t.name }}
              </button>
              <span class="text-slate-400 text-xs font-normal">({{ t.course }})</span>
            </div>
            <p class="text-red-300 text-xs mt-0.5">Rating: <strong>{{ t.rating }}</strong> · Completion: <strong>{{ t.completion }}%</strong> · Reason: <span class="italic text-slate-300">{{ t.reason }}</span></p>
          </div>

          <div class="flex items-center gap-2 shrink-0">
            <!-- Primary CTA (Purple Filled) -->
            <button 
              @click="emit('sendFeedback', t.name)" 
              class="px-3.5 py-1.5 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-xl text-xs shadow-md shadow-purple-600/30 transition-all active:scale-95 flex items-center gap-1.5"
            >
              <span>💬</span> Send Feedback
            </button>
            <!-- Secondary CTA (Outline Button) -->
            <button 
              @click="emit('scheduleReview', t.name)" 
              class="px-3.5 py-1.5 bg-transparent hover:bg-slate-800 border border-slate-600 text-slate-300 hover:text-white font-semibold rounded-xl text-xs transition-all active:scale-95"
            >
              Schedule Review
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
