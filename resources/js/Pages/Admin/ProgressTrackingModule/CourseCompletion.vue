<script setup lang="ts">
import { ref, computed } from 'vue'
import type { CourseCompletionItem } from './types'

const props = defineProps<{
  courses: CourseCompletionItem[]
}>()

const emit = defineEmits<{
  (e: 'downloadReport', courseId: number): void
  (e: 'notifyIncomplete', courseId: number): void
}>()

const searchQuery = ref('')
const selectedMajor = ref('all')
const selectedSemester = ref('current')

const filteredCourses = computed(() => {
  return props.courses.filter(c => {
    const matchSearch = searchQuery.value === '' || 
      c.title.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
      c.teacher.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchMajor = selectedMajor.value === 'all' || c.major === selectedMajor.value
    return matchSearch && matchMajor
  })
})

// Math-consistent breakdown (Sum = 520 Students total for C Programming Basics; In-Progress sum = 380 Students / 73%)
const distributionData = [
  { label: 'Completed (100% Progress)', count: 120, percent: 23, color: 'bg-emerald-500', badgeColor: 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30' },
  { label: '75% - 99% Progress (Near Done)', count: 200, percent: 38, color: 'bg-cyan-500', badgeColor: 'bg-cyan-500/10 text-cyan-300 border-cyan-500/30' },
  { label: '50% - 74% Progress (On Track)', count: 120, percent: 23, color: 'bg-amber-500', badgeColor: 'bg-amber-500/10 text-amber-300 border-amber-500/30' },
  { label: '25% - 49% Progress (Lagging)', count: 40, percent: 8, color: 'bg-orange-500', badgeColor: 'bg-orange-500/10 text-orange-300 border-orange-500/30' },
  { label: 'Under 25% Progress (At-Risk)', count: 20, percent: 4, color: 'bg-rose-500', badgeColor: 'bg-rose-500/10 text-rose-300 border-rose-500/30' },
]
</script>

<template>
  <div class="space-y-5 text-xs font-sans">
    <!-- Header Banner -->
    <div class="bg-[#0d1222]/95 border border-purple-500/30 rounded-2xl p-5 shadow-2xl flex flex-col md:flex-row items-center justify-between gap-4">
      <div class="space-y-1">
        <h3 class="text-base font-black text-white flex items-center gap-2.5">
          <div class="p-1.5 rounded-lg bg-purple-500/20 border border-purple-500/40 flex items-center justify-center shrink-0 shadow-inner">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" stroke="#C084FC" stroke-width="2" fill="#9333EA" fill-opacity="0.3" />
            </svg>
          </div>
          <span>COURSE COMPLETION RATE</span>
        </h3>
        <p class="text-slate-200 text-xs font-semibold">Track overall completion rates and cohort distribution across all courses and majors.</p>
      </div>

      <!-- Bulk Actions Header Buttons (Distinguishes Bulk vs Single Row Actions) -->
      <div class="flex flex-wrap items-center gap-2.5">
        <button @click="emit('downloadReport', 0)" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 rounded-xl font-bold flex items-center gap-2 transition-all active:scale-95 shadow-md" title="Export completion report for all active courses">
          <svg class="w-4 h-4 text-purple-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
          </svg>
          <span>Export All Reports</span>
        </button>
        <button @click="emit('notifyIncomplete', 0)" class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl font-bold shadow-lg shadow-purple-600/30 flex items-center gap-2 transition-all active:scale-95 ring-1 ring-purple-400/50" title="Send reminder emails to all incomplete students across cohorts">
          <svg class="w-4 h-4 text-white shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
          <span>Bulk Notify Incomplete</span>
        </button>
      </div>
    </div>

    <!-- Filters Toolbar -->
    <div class="bg-[#0d1222]/90 border border-slate-700/60 p-3.5 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-3">
      <input
        v-model="searchQuery"
        type="text"
        placeholder="🔍 Search course or instructor name..."
        class="w-full sm:w-72 bg-[#121827] text-slate-200 border border-slate-700/80 rounded-xl px-3.5 py-2 text-xs focus:border-purple-500 focus:outline-none placeholder:text-slate-400 font-medium"
      />

      <div class="flex items-center gap-2.5 w-full sm:w-auto">
        <select v-model="selectedMajor" class="bg-[#121827] text-slate-200 border border-slate-700/80 rounded-xl px-3 py-2 text-xs font-semibold focus:border-purple-500 focus:outline-none">
          <option value="all">Major: All Majors</option>
          <option value="IT & Networking">IT & Networking</option>
          <option value="Tourism Management">Tourism Management</option>
          <option value="English Literature">English Literature</option>
          <option value="Agronomy">Agronomy</option>
          <option value="Social Work">Social Work</option>
        </select>

        <select v-model="selectedSemester" class="bg-[#121827] text-slate-200 border border-slate-700/80 rounded-xl px-3 py-2 text-xs font-semibold focus:border-purple-500 focus:outline-none">
          <option value="current">Semester: Current (2025-S1)</option>
          <option value="previous">Semester: Previous (2024-S2)</option>
        </select>
      </div>
    </div>

    <!-- Course Completion Table -->
    <div class="overflow-x-auto rounded-2xl border border-slate-700/60 bg-[#0d1222]/95 shadow-xl">
      <table class="w-full text-left text-xs text-slate-200">
        <thead class="bg-[#121827] text-slate-200 uppercase font-black tracking-wider border-b border-slate-700/80">
          <tr>
            <th class="p-3.5">COURSE NAME</th>
            <th class="p-3.5">INSTRUCTOR</th>
            <th class="p-3.5">ENROLLED</th>
            <th class="p-3.5">COMPLETED (100%)</th>
            <th class="p-3.5">IN PROGRESS</th>
            <th class="p-3.5">AVG SCORE</th>
            <th class="p-3.5 text-right">ROW ACTIONS</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/80 font-medium">
          <tr v-for="c in filteredCourses" :key="c.id" class="hover:bg-slate-800/50 transition-colors">
            <td class="p-3.5">
              <div class="font-bold text-white text-sm">{{ c.title }}</div>
              <div class="text-xs text-slate-300 font-semibold mt-0.5">{{ c.major }}</div>
            </td>
            <td class="p-3.5 font-bold text-slate-200">
              {{ c.teacher }}
            </td>
            <td class="p-3.5 font-bold text-white">
              {{ c.enrolled }} Students
            </td>
            <td class="p-3.5">
              <div class="font-bold text-emerald-400">{{ c.completed }} ({{ c.completed_percent }}%)</div>
              <div class="w-24 h-2 bg-slate-800 rounded-full mt-1 overflow-hidden border border-slate-700/80">
                <div class="h-full bg-emerald-400 rounded-full transition-all duration-500 shadow-sm" :style="{ width: c.completed_percent + '%' }"></div>
              </div>
            </td>
            <td class="p-3.5">
              <div class="font-bold text-amber-300">{{ c.in_progress }} ({{ c.in_progress_percent }}%)</div>
              <div class="w-24 h-2 bg-slate-800 rounded-full mt-1 overflow-hidden border border-slate-700/80">
                <div class="h-full bg-amber-400 rounded-full transition-all duration-500 shadow-sm" :style="{ width: c.in_progress_percent + '%' }"></div>
              </div>
            </td>
            <td class="p-3.5 font-black text-purple-300 text-sm">
              {{ c.avg_score }}%
            </td>
            <td class="p-3.5 text-right space-x-2">
              <button @click="emit('downloadReport', c.id)" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5 transition-all active:scale-95" title="Download report for this course">
                <svg class="w-3.5 h-3.5 text-purple-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>Report</span>
              </button>
              <button @click="emit('notifyIncomplete', c.id)" class="px-3 py-1.5 bg-purple-600/30 hover:bg-purple-600/50 border border-purple-500/40 text-purple-300 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5 transition-all active:scale-95" title="Send email notification to incomplete students in this course">
                <svg class="w-3.5 h-3.5 text-purple-300 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span>Remind</span>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Cohort Progress Distribution Section -->
    <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
      <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5 flex items-center gap-2.5">
        <div class="p-1.5 rounded-lg bg-purple-500/20 border border-purple-500/40 flex items-center justify-center shrink-0 shadow-inner">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
            <rect x="3" y="3" width="18" height="18" rx="4" fill="#9333EA" fill-opacity="0.3" stroke="#C084FC" stroke-width="2" />
            <path d="M7 16V12M12 16V8M17 16V10" stroke="white" stroke-width="2.5" stroke-linecap="round" />
          </svg>
        </div>
        <span>COHORT PROGRESS DISTRIBUTION (C PROGRAMMING BASICS)</span>
      </h4>

      <div class="space-y-3 pt-1">
        <div v-for="(item, idx) in distributionData" :key="idx" class="space-y-1.5">
          <div class="flex items-center justify-between text-xs">
            <span class="font-bold text-slate-200 w-56 flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full shrink-0" :class="item.color"></span>
              <span>{{ item.label }}</span>
            </span>
            <div class="flex-1 mx-4 h-3 bg-slate-900 rounded-full overflow-hidden border border-slate-700/80 p-0.5">
              <div :class="[item.color, 'h-full rounded-full transition-all duration-500 shadow-sm']" :style="{ width: item.percent + '%' }"></div>
            </div>
            <span class="font-black text-white w-32 text-right flex items-center justify-end gap-1.5">
              <span class="px-2 py-0.5 rounded border text-[11px]" :class="item.badgeColor">
                {{ item.count }} Students ({{ item.percent }}%)
              </span>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
