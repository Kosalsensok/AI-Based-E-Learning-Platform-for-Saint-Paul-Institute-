<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  report: {
    course_title: string
    students: number
    completed: number
    completion_rate: string
    avg_quiz: string
    avg_time: string
    modules_completion: Array<any>
    revenue: {
      total: string
      paid_count: number
      pending_count: number
    }
    analytics: {
      enrollment_trend: Array<any>
      avg_progress: number
      avg_quiz: number
      avg_attendance: number
      avg_time: number
    }
    ai_recommendation: {
      issue: string
      suggestion: string
    }
  }
  courses: Array<any>
}>()

const emit = defineEmits(['exportPdf', 'exportExcel', 'exportCsv', 'print', 'openAction'])

const selectedCourseId = ref(props.courses?.[0]?.id || 1)
const selectedSemester = ref('Semester 1')
const selectedDate = ref('This Semester')
</script>

<template>
  <div class="space-y-6">
    <!-- Header Controls & Course Summary Cards -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-5">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-gray-700 pb-4">
        <div>
          <h2 class="text-base font-extrabold text-slate-800 dark:text-white flex items-center gap-2">
            <span>📚 COURSE REPORT</span>
          </h2>
          <p class="text-xs text-slate-500">
            Comprehensive course-level analysis (Enrollment, Completion, Learning Time, Revenue & Performance).
          </p>
        </div>

        <!-- Toolbar Filters -->
        <div class="flex flex-wrap items-center gap-2.5 text-xs">
          <select
            v-model="selectedCourseId"
            class="px-3 py-2 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl font-bold text-slate-800 dark:text-white"
          >
            <option v-for="c in courses" :key="c.id" :value="c.id">📚 {{ c.title }}</option>
          </select>

          <select
            v-model="selectedSemester"
            class="px-3 py-2 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl font-bold text-slate-800 dark:text-white"
          >
            <option value="Semester 1">Semester 1 ▼</option>
            <option value="Semester 2">Semester 2</option>
          </select>

          <select
            v-model="selectedDate"
            class="px-3 py-2 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl font-bold text-slate-800 dark:text-white"
          >
            <option value="This Semester">This Semester ▼</option>
            <option value="Last 30 Days">Last 30 Days</option>
            <option value="All Time">All Time</option>
          </select>
        </div>
      </div>

      <!-- Course Summary Metrics -->
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 text-center">
        <div class="p-3.5 rounded-2xl bg-blue-50/70 dark:bg-blue-900/20 border border-blue-200/60 dark:border-blue-800/40">
          <span class="text-[10px] font-extrabold text-blue-800 dark:text-blue-300 uppercase">Course</span>
          <p class="text-sm font-extrabold text-blue-700 dark:text-blue-400 mt-1 truncate">{{ report?.course_title || 'C Programming Basics' }}</p>
        </div>

        <div class="p-3.5 rounded-2xl bg-indigo-50/70 dark:bg-indigo-900/20 border border-indigo-200/60 dark:border-indigo-800/40">
          <span class="text-[10px] font-extrabold text-indigo-800 dark:text-indigo-300 uppercase">Students</span>
          <p class="text-xl font-black text-indigo-700 dark:text-indigo-400 mt-0.5">{{ report?.students || 120 }}</p>
        </div>

        <div class="p-3.5 rounded-2xl bg-emerald-50/70 dark:bg-emerald-900/20 border border-emerald-200/60 dark:border-emerald-800/40">
          <span class="text-[10px] font-extrabold text-emerald-800 dark:text-emerald-300 uppercase">Completed</span>
          <p class="text-xl font-black text-emerald-700 dark:text-emerald-400 mt-0.5">{{ report?.completed || 26 }}</p>
        </div>

        <div class="p-3.5 rounded-2xl bg-teal-50/70 dark:bg-teal-900/20 border border-teal-200/60 dark:border-teal-800/40">
          <span class="text-[10px] font-extrabold text-teal-800 dark:text-teal-300 uppercase">Completion Rate</span>
          <p class="text-xl font-black text-teal-700 dark:text-teal-400 mt-0.5">{{ report?.completion_rate || '22%' }}</p>
        </div>

        <div class="p-3.5 rounded-2xl bg-amber-50/70 dark:bg-amber-900/20 border border-amber-200/60 dark:border-amber-800/40">
          <span class="text-[10px] font-extrabold text-amber-800 dark:text-amber-300 uppercase">Avg Quiz</span>
          <p class="text-xl font-black text-amber-700 dark:text-amber-400 mt-0.5">{{ report?.avg_quiz || '78%' }}</p>
        </div>

        <div class="p-3.5 rounded-2xl bg-purple-50/70 dark:bg-purple-900/20 border border-purple-200/60 dark:border-purple-800/40">
          <span class="text-[10px] font-extrabold text-purple-800 dark:text-purple-300 uppercase">Avg Learning Time</span>
          <p class="text-xl font-black text-purple-700 dark:text-purple-400 mt-0.5">{{ report?.avg_time || '26 Hours' }}</p>
        </div>
      </div>
    </div>

    <!-- Module Completion & Revenue Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 text-xs">
      <!-- Module Completion Progress Bars -->
      <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-4">
        <div class="border-b pb-3">
          <h3 class="font-extrabold text-sm text-slate-800 dark:text-white">📦 Module Completion Breakdown</h3>
        </div>

        <div class="space-y-4 pt-1">
          <div v-for="m in report?.modules_completion" :key="m.name" class="space-y-1.5">
            <div class="flex justify-between font-extrabold">
              <span class="text-slate-800 dark:text-white">{{ m.name }}</span>
              <span :class="m.rate >= 75 ? 'text-emerald-600' : m.rate >= 50 ? 'text-amber-600' : 'text-rose-600'">
                {{ m.rate }}%
              </span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-gray-700 h-4 rounded-full overflow-hidden p-0.5 border border-slate-200/60 dark:border-gray-600">
              <div
                class="h-full rounded-full transition-all duration-700 flex items-center justify-end pr-2 text-[10px] font-extrabold text-white"
                :class="m.rate >= 75 ? 'bg-emerald-500' : m.rate >= 50 ? 'bg-amber-500' : 'bg-rose-500'"
                :style="{ width: m.rate + '%' }"
              >
                <span v-if="m.rate >= 15">{{ m.rate }}%</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Revenue Overview Card -->
      <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-4 flex flex-col justify-between">
        <div>
          <div class="border-b pb-3">
            <h3 class="font-extrabold text-sm text-slate-800 dark:text-white">💰 Revenue Overview</h3>
          </div>

          <div class="pt-4 text-center space-y-2">
            <span class="text-xs font-bold text-slate-400 uppercase">Total Revenue Collected</span>
            <h2 class="text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ report?.revenue?.total || '$3,240' }}</h2>
          </div>

          <div class="space-y-2 pt-5">
            <div class="flex justify-between items-center p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100">
              <span class="font-bold text-slate-700 dark:text-slate-200">Students Paid</span>
              <span class="font-extrabold text-emerald-600">{{ report?.revenue?.paid_count || 108 }} Students</span>
            </div>

            <div class="flex justify-between items-center p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-100">
              <span class="font-bold text-slate-700 dark:text-slate-200">Pending Payments</span>
              <span class="font-extrabold text-amber-600">{{ report?.revenue?.pending_count || 12 }} Students</span>
            </div>
          </div>
        </div>

        <div class="pt-3 border-t flex gap-2">
          <button @click="emit('exportPdf')" class="flex-1 py-2 bg-rose-600 text-white font-bold rounded-xl text-xs">📄 Export PDF</button>
          <button @click="emit('exportExcel')" class="flex-1 py-2 bg-emerald-600 text-white font-bold rounded-xl text-xs">📊 Excel</button>
        </div>
      </div>
    </div>

    <!-- Course Analytics & AI Recommendations Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- 📈 Course Analytics Trend Graph -->
      <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-4">
        <div class="border-b pb-3 flex justify-between items-center">
          <h3 class="font-extrabold text-sm text-slate-800 dark:text-white">📈 Enrollment Trend</h3>
          <span class="text-xs font-bold text-slate-400">Jan – Jun</span>
        </div>

        <!-- Custom CSS Bar Graph -->
        <div class="h-44 flex items-end justify-between gap-2 pt-6 pb-2 px-2 border-b border-slate-200 dark:border-gray-700">
          <div v-for="e in report?.analytics?.enrollment_trend" :key="e.month" class="flex-1 flex flex-col items-center gap-2 group h-full justify-end">
            <span class="text-[10px] font-extrabold text-slate-500 opacity-80 group-hover:opacity-100">{{ e.count }} std</span>
            <div class="w-full max-w-[36px] bg-slate-100 dark:bg-gray-700 rounded-t-lg h-32 flex items-end overflow-hidden">
              <div
                class="w-full rounded-t-lg bg-blue-600 transition-all duration-700"
                :style="{ height: (e.count / 120 * 100) + '%' }"
              ></div>
            </div>
            <span class="text-[11px] font-extrabold text-slate-700 dark:text-slate-300">{{ e.month }}</span>
          </div>
        </div>

        <!-- Average Metrics Grid -->
        <div class="grid grid-cols-4 gap-2 text-center text-xs pt-2">
          <div class="p-2 rounded-xl bg-slate-50 dark:bg-gray-700/50">
            <span class="text-[10px] text-slate-400 font-bold block">Avg Progress</span>
            <span class="font-extrabold text-blue-600">{{ report?.analytics?.avg_progress || 72 }}%</span>
          </div>
          <div class="p-2 rounded-xl bg-slate-50 dark:bg-gray-700/50">
            <span class="text-[10px] text-slate-400 font-bold block">Avg Quiz</span>
            <span class="font-extrabold text-emerald-600">{{ report?.analytics?.avg_quiz || 78 }}%</span>
          </div>
          <div class="p-2 rounded-xl bg-slate-50 dark:bg-gray-700/50">
            <span class="text-[10px] text-slate-400 font-bold block">Avg Attendance</span>
            <span class="font-extrabold text-indigo-600">{{ report?.analytics?.avg_attendance || 88 }}%</span>
          </div>
          <div class="p-2 rounded-xl bg-slate-50 dark:bg-gray-700/50">
            <span class="text-[10px] text-slate-400 font-bold block">Avg Time</span>
            <span class="font-extrabold text-purple-600">{{ report?.analytics?.avg_time || 26 }} Hours</span>
          </div>
        </div>
      </div>

      <!-- 🤖 AI Recommendation Box -->
      <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 p-6 rounded-2xl border border-blue-200/80 dark:border-blue-800/40 space-y-4 flex flex-col justify-between">
        <div>
          <div class="flex items-center gap-2 border-b border-blue-200/60 dark:border-blue-800/40 pb-3">
            <span class="text-xl">🤖</span>
            <h3 class="font-extrabold text-sm text-blue-900 dark:text-blue-200 uppercase tracking-wider">AI Course Intelligence & Recommendation</h3>
          </div>

          <div class="space-y-3 pt-3 text-xs">
            <div class="p-3 bg-white dark:bg-gray-800 rounded-xl border border-blue-100 space-y-1">
              <span class="font-bold text-rose-600">Issue Detected:</span>
              <p class="font-extrabold text-slate-800 dark:text-white">{{ report?.ai_recommendation?.issue || 'Module 3 is too difficult.' }}</p>
            </div>

            <div class="p-3 bg-white dark:bg-gray-800 rounded-xl border border-blue-100 space-y-1">
              <span class="font-bold text-emerald-600">Action Suggestion:</span>
              <p class="font-extrabold text-slate-800 dark:text-white">{{ report?.ai_recommendation?.suggestion || 'Create another Practice Quiz and review nested loops during Live Class.' }}</p>
            </div>
          </div>
        </div>

        <div class="flex gap-2 pt-4">
          <button
            @click="emit('openAction', 'practice_quiz')"
            class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs shadow-sm flex items-center justify-center gap-1.5 transition"
          >
            <span>🟩</span> Create Practice Quiz
          </button>

          <button
            @click="emit('openAction', 'recap_video')"
            class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-xs shadow-sm flex items-center justify-center gap-1.5 transition"
          >
            <span>🎬</span> Upload Video
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
