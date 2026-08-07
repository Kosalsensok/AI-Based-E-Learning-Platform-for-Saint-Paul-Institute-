<script setup lang="ts">
import { ref } from 'vue'
import type { StudentAnalyticsData } from './types'

const props = defineProps<{
  data: StudentAnalyticsData
}>()

const emit = defineEmits<{
  (e: 'exportReport'): void
  (e: 'deepDiveRetention'): void
}>()

const selectedSemester = ref('this_semester')
const selectedMajor = ref('all')
</script>

<template>
  <div class="space-y-6 text-xs font-sans">
    <!-- Header Banner -->
    <div class="bg-[#0d1222]/95 border border-purple-500/30 rounded-2xl p-5 shadow-2xl flex flex-col md:flex-row items-center justify-between gap-4">
      <div>
        <h3 class="text-base font-black text-white flex items-center gap-2">
          <span>👨‍🎓</span> STUDENT ANALYTICS — DEEP DIVE
        </h3>
        <p class="text-slate-400 text-xs">Analyze enrollment behavior, engagement trends, retention funnel, and academic performance.</p>
      </div>

      <div class="flex flex-wrap items-center gap-2">
        <select v-model="selectedSemester" class="bg-[#121827] text-slate-200 border border-slate-700/80 rounded-xl px-3 py-1.5 text-xs focus:border-purple-500 focus:outline-none">
          <option value="this_semester">Period: This Semester (2025-S1)</option>
          <option value="last_semester">Period: Last Semester (2024-S2)</option>
        </select>

        <select v-model="selectedMajor" class="bg-[#121827] text-slate-200 border border-slate-700/80 rounded-xl px-3 py-1.5 text-xs focus:border-purple-500 focus:outline-none">
          <option value="all">Major: All Majors</option>
          <option value="IT & Networking">IT & Networking</option>
          <option value="Tourism Management">Tourism Management</option>
          <option value="English Literature">English Literature</option>
          <option value="Agronomy">Agronomy</option>
          <option value="Social Work">Social Work</option>
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

    <!-- 4 KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4 rounded-2xl shadow-xl space-y-1">
        <span class="text-slate-400 text-[10px] block font-semibold">👥 TOTAL ENROLLED</span>
        <p class="text-2xl font-black text-white">{{ data.kpis.total.toLocaleString() }}</p>
        <span class="text-[10px] text-emerald-400 font-bold">Active Cohort</span>
      </div>

      <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4 rounded-2xl shadow-xl space-y-1">
        <span class="text-slate-400 text-[10px] block font-semibold">✅ ACTIVE STUDENTS</span>
        <p class="text-2xl font-black text-emerald-400">{{ data.kpis.active.toLocaleString() }} ({{ data.kpis.active_percent }}%)</p>
        <span class="text-[10px] text-slate-400">Logged in past 7 days</span>
      </div>

      <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4 rounded-2xl shadow-xl space-y-1">
        <span class="text-slate-400 text-[10px] block font-semibold">🟢 RETENTION RATE</span>
        <p class="text-2xl font-black text-purple-300">{{ data.kpis.retention }}%</p>
        <span class="text-[10px] text-emerald-300 font-semibold">Course Completion Continuity</span>
      </div>

      <div class="bg-[#0d1222]/95 border border-red-500/40 p-4 rounded-2xl shadow-xl space-y-1 bg-red-950/10">
        <span class="text-slate-400 text-[10px] block font-semibold">🚨 AT-RISK STUDENTS</span>
        <p class="text-2xl font-black text-red-400">{{ data.kpis.at_risk }} ({{ data.kpis.at_risk_percent }}%)</p>
        <span class="text-[10px] text-red-300 font-semibold">Needs Immediate Support</span>
      </div>
    </div>

    <!-- Enrollment Trend Line Representation -->
    <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
      <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
        <h4 class="font-black text-sm text-white uppercase tracking-wide">
          📈 ENROLLMENT TREND (6 MONTHS: JAN - JUN)
        </h4>
        <span class="text-slate-400 font-mono text-[11px]">Total Enrolled: <strong class="text-emerald-400">2,458</strong> · New This Month: <strong class="text-teal-300">+86</strong> · Growth: <strong class="text-purple-300">+24%</strong></span>
      </div>

      <div class="h-44 flex items-end justify-between gap-4 pt-6 pb-2 px-4 bg-[#121827] rounded-xl border border-slate-700/80">
        <div v-for="(item, idx) in data.enrollment_trend" :key="idx" class="flex-1 flex flex-col items-center gap-2 h-full justify-end group">
          <span class="text-[10px] font-bold text-purple-300 group-hover:scale-110 transition-transform">{{ item.count }}</span>
          <div class="w-full bg-slate-800 rounded-t-lg overflow-hidden flex items-end h-32 p-0.5">
            <div
              class="w-full bg-gradient-to-t from-purple-600 via-indigo-500 to-teal-400 rounded-t transition-all duration-500 group-hover:brightness-125"
              :style="{ height: (item.count / 2500) * 100 + '%' }"
            ></div>
          </div>
          <span class="text-[11px] font-bold text-slate-400">{{ item.month }}</span>
        </div>
      </div>
    </div>

    <!-- Students by Major & Gender Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
      <!-- Students by Major (2 Cols) -->
      <div class="lg:col-span-2 bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
        <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5">
          🎓 STUDENTS BY ACADEMIC MAJOR
        </h4>

        <div class="space-y-3">
          <div v-for="(m, idx) in data.by_major" :key="idx" class="space-y-1">
            <div class="flex items-center justify-between text-xs font-semibold">
              <span class="text-slate-300 w-44">{{ m.major }}</span>
              <div class="flex-1 mx-3 h-2.5 bg-slate-900 rounded-full overflow-hidden border border-slate-700/80">
                <div class="h-full bg-purple-500 rounded-full transition-all duration-500" :style="{ width: m.percent * 3 + '%' }"></div>
              </div>
              <span class="font-bold text-white w-24 text-right">{{ m.count }} ({{ m.percent }}%)</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Students by Gender (1 Col) -->
      <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4 flex flex-col justify-between">
        <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5">
          📊 STUDENTS BY GENDER
        </h4>

        <div class="p-6 bg-[#121827] rounded-xl border border-slate-700/80 text-center space-y-4">
          <div class="w-24 h-24 rounded-full border-4 border-purple-500 border-t-teal-400 mx-auto flex items-center justify-center font-black text-white text-base shadow-lg shadow-purple-600/20">
            54% / 46%
          </div>

          <div class="flex items-center justify-center gap-6 text-xs font-bold">
            <div class="flex items-center gap-1.5 text-purple-300">
              <span class="w-3 h-3 rounded-full bg-purple-500 inline-block"></span>
              <span>Male: {{ data.by_gender.male_percent }}%</span>
            </div>
            <div class="flex items-center gap-1.5 text-teal-300">
              <span class="w-3 h-3 rounded-full bg-teal-400 inline-block"></span>
              <span>Female: {{ data.by_gender.female_percent }}%</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Engagement Distribution -->
    <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
      <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5">
        📊 STUDENT ENGAGEMENT DISTRIBUTION (WEEKLY STUDY HOURS)
      </h4>

      <div class="space-y-3">
        <div v-for="(item, idx) in data.engagement_distribution" :key="idx" class="space-y-1">
          <div class="flex items-center justify-between text-xs">
            <span class="font-semibold text-slate-300 w-64 flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full inline-block" :class="item.color"></span>
              {{ item.label }} ({{ item.range }})
            </span>
            <div class="flex-1 mx-4 h-3 bg-slate-900 rounded-full overflow-hidden border border-slate-700/80">
              <div :class="[item.color, 'h-full rounded-full transition-all duration-500']" :style="{ width: item.percent * 2 + '%' }"></div>
            </div>
            <span class="font-bold text-white w-36 text-right">{{ item.count }} Students ({{ item.percent }}%)</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Retention Funnel (Cohort Analysis) -->
    <div class="bg-[#0d1222]/95 border border-purple-500/30 rounded-2xl p-5 shadow-2xl space-y-4">
      <div class="flex items-center justify-between border-b border-slate-700/60 pb-2.5">
        <h4 class="font-black text-sm text-white uppercase tracking-wide flex items-center gap-2">
          <span>🔄</span> RETENTION FUNNEL (COHORT ANALYSIS)
        </h4>

        <button @click="emit('deepDiveRetention')" class="text-xs text-purple-300 font-bold hover:underline">
          Deep Dive Retention →
        </button>
      </div>

      <div class="space-y-2.5">
        <div v-for="(stage, idx) in data.retention_funnel" :key="idx" class="space-y-1">
          <div class="flex items-center justify-between text-xs font-semibold">
            <span class="text-slate-300 w-44">{{ stage.stage }}</span>
            <div class="flex-1 mx-4 h-3 bg-slate-900 rounded-full overflow-hidden border border-slate-700/80">
              <div class="h-full bg-gradient-to-r from-purple-600 to-teal-400 rounded-full" :style="{ width: stage.percent + '%' }"></div>
            </div>
            <span class="font-bold text-white w-32 text-right">{{ stage.percent }}% ({{ stage.count }})</span>
          </div>
        </div>
      </div>

      <div class="p-3.5 rounded-xl bg-purple-950/40 border border-purple-500/30 text-xs text-purple-200 font-medium flex items-center justify-between">
        <span>Overall Retention: <strong>94.5%</strong> · Drop-off Point: <strong class="text-red-400">Module 2 → Module 3 (-14%)</strong></span>
        <button @click="emit('deepDiveRetention')" class="px-3 py-1 bg-purple-600/30 hover:bg-purple-600 border border-purple-500/40 text-purple-200 hover:text-white rounded-lg text-xs font-bold transition-all">
          <span>Analyze Drop-off →</span>
        </button>
      </div>
    </div>
  </div>
</template>
