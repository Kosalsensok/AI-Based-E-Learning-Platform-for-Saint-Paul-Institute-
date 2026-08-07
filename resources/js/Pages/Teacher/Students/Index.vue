<script setup lang="ts">
import { ref, onMounted } from 'vue'
import TeacherLayout from '@/Layouts/TeacherLayout.vue'

// Import sub-component views
import StudentListTab from './StudentList.vue'
import ProgressTab from './Progress.vue'
import CompletionTab from './Completion.vue'
import QuizScoresTab from './QuizScores.vue'

const props = defineProps<{
  courses: Array<any>
  enrollments: Array<any>
  stats?: {
    total_students: number
    active_students: number
    slow_students: number
    at_risk_students: number
    avg_progress: number
    avg_score: number
    avg_time: string
  }
}>()

const activeTab = ref<'list' | 'progress' | 'completion' | 'scores'>('list')
const selectedCourseId = ref('')
const showAlertPanel = ref(true)

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const tab = urlParams.get('tab')
  if (tab === 'list' || tab === 'progress' || tab === 'completion' || tab === 'scores') {
    activeTab.value = tab
  }
})

const changeTab = (tab: 'list' | 'progress' | 'completion' | 'scores') => {
  activeTab.value = tab
  const url = new URL(window.location.href)
  url.searchParams.set('tab', tab)
  window.history.pushState({}, '', url.toString())
}
</script>

<template>
  <TeacherLayout title="Students Module">
    <div class="space-y-6 max-w-7xl mx-auto pb-12">
      <!-- MAIN MODULE HEADER BANNER -->
      <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 md:p-8 text-white shadow-xl relative overflow-hidden">
        <!-- Background Glow Accents -->
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-12 left-1/3 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl"></div>

        <div class="relative z-10 space-y-6">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800 pb-5">
            <div>
              <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight flex items-center gap-3">
                <span class="p-2.5 bg-blue-600/30 rounded-2xl border border-blue-400/30">👥</span>
                <span>My Students Workspace</span>
              </h1>
              <p class="text-xs md:text-sm text-slate-300 mt-1">
                Track, support, and evaluate students enrolled in your courses. (Teacher Scoped Access)
              </p>
            </div>

            <!-- Course Filter Selector -->
            <div class="bg-slate-800/80 backdrop-blur border border-slate-700 p-2.5 rounded-2xl">
              <label class="block text-[10px] text-slate-400 font-bold uppercase mb-1">Filter by Course:</label>
              <select v-model="selectedCourseId" class="bg-slate-900 text-white text-xs font-semibold rounded-xl p-2 border border-slate-700 w-56">
                <option value="">C Programming Basics ▼</option>
                <option v-for="c in props.courses" :key="c.id" :value="c.id">{{ c.title }}</option>
              </select>
            </div>
          </div>

          <!-- TOP STATS KPI CARDS -->
          <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
            <div class="p-3 bg-slate-800/50 rounded-2xl border border-slate-700/60 text-center">
              <span class="text-[10px] text-slate-400 font-bold uppercase block">Total</span>
              <strong class="text-lg font-extrabold text-white">{{ props.stats?.total_students || 128 }}</strong>
            </div>

            <div class="p-3 bg-emerald-950/40 rounded-2xl border border-emerald-800/50 text-center">
              <span class="text-[10px] text-emerald-400 font-bold uppercase block">Active</span>
              <strong class="text-lg font-extrabold text-emerald-400">{{ props.stats?.active_students || 95 }}</strong>
            </div>

            <div class="p-3 bg-amber-950/40 rounded-2xl border border-amber-800/50 text-center">
              <span class="text-[10px] text-amber-400 font-bold uppercase block">Slow</span>
              <strong class="text-lg font-extrabold text-amber-400">{{ props.stats?.slow_students || 21 }}</strong>
            </div>

            <div class="p-3 bg-rose-950/40 rounded-2xl border border-rose-800/50 text-center">
              <span class="text-[10px] text-rose-400 font-bold uppercase block">At-Risk</span>
              <strong class="text-lg font-extrabold text-rose-400">{{ props.stats?.at_risk_students || 12 }}</strong>
            </div>

            <div class="p-3 bg-blue-950/40 rounded-2xl border border-blue-800/50 text-center">
              <span class="text-[10px] text-blue-400 font-bold uppercase block">Avg Progress</span>
              <strong class="text-lg font-extrabold text-blue-400">{{ props.stats?.avg_progress || 72 }}%</strong>
            </div>

            <div class="p-3 bg-purple-950/40 rounded-2xl border border-purple-800/50 text-center">
              <span class="text-[10px] text-purple-400 font-bold uppercase block">Avg Score</span>
              <strong class="text-lg font-extrabold text-purple-400">{{ props.stats?.avg_score || 78 }}%</strong>
            </div>

            <div class="p-3 bg-slate-800/50 rounded-2xl border border-slate-700/60 text-center col-span-2 sm:col-span-1">
              <span class="text-[10px] text-slate-400 font-bold uppercase block">Avg Time</span>
              <strong class="text-base font-extrabold text-white">{{ props.stats?.avg_time || '19h 30m' }}</strong>
            </div>
          </div>
        </div>
      </div>

      <!-- 🚨 AT-RISK ALERT PANEL -->
      <div v-if="showAlertPanel" class="p-4 bg-rose-50 dark:bg-rose-900/20 border border-rose-200/80 rounded-2xl space-y-3">
        <div class="flex items-center justify-between">
          <h3 class="font-extrabold text-xs text-rose-800 dark:text-rose-300 flex items-center gap-2">
            <span>🚨 Students Needing Help (5 Students At-Risk)</span>
          </h3>
          <button @click="showAlertPanel = false" class="text-xs text-slate-400 hover:text-slate-600">Dismiss</button>
        </div>

        <div class="space-y-2 text-xs">
          <div class="p-2.5 bg-white dark:bg-gray-800 rounded-xl border border-rose-200/60 flex items-center justify-between">
            <div>
              <p class="font-bold text-slate-800 dark:text-white">🔴 Sok Channa (STU241102) — Inactive for 9 days | Progress 45% | Score 58%</p>
              <p class="text-[11px] text-slate-400">Reason: Stuck at Chapter 2.2 (Data Types)</p>
            </div>
            <div class="flex gap-2">
              <button class="px-2.5 py-1 bg-rose-600 text-white rounded-lg font-bold">🔔 Remind</button>
              <button class="px-2.5 py-1 bg-blue-600 text-white rounded-lg font-bold">📧 Message</button>
            </div>
          </div>
        </div>
      </div>

      <!-- MAIN TABS SWITCHER -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-1.5 border border-slate-200/80 dark:border-gray-700 shadow-sm flex flex-wrap gap-1">
        <button
          @click="changeTab('list')"
          :class="[
            'px-5 py-3 rounded-xl font-bold text-xs md:text-sm transition-all flex items-center gap-2',
            activeTab === 'list'
              ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20'
              : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-gray-700'
          ]"
        >
          <span>📋 Student List</span>
        </button>

        <button
          @click="changeTab('progress')"
          :class="[
            'px-5 py-3 rounded-xl font-bold text-xs md:text-sm transition-all flex items-center gap-2',
            activeTab === 'progress'
              ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20'
              : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-gray-700'
          ]"
        >
          <span>📈 Progress</span>
        </button>

        <button
          @click="changeTab('completion')"
          :class="[
            'px-5 py-3 rounded-xl font-bold text-xs md:text-sm transition-all flex items-center gap-2',
            activeTab === 'completion'
              ? 'bg-emerald-600 text-white shadow-md shadow-emerald-500/20'
              : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-gray-700'
          ]"
        >
          <span>📊 Completion</span>
        </button>

        <button
          @click="changeTab('scores')"
          :class="[
            'px-5 py-3 rounded-xl font-bold text-xs md:text-sm transition-all flex items-center gap-2',
            activeTab === 'scores'
              ? 'bg-purple-600 text-white shadow-md shadow-purple-500/20'
              : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-gray-700'
          ]"
        >
          <span>🏆 Quiz Scores</span>
        </button>
      </div>

      <!-- TAB CONTENTS -->
      <div>
        <StudentListTab v-if="activeTab === 'list'" :courses="props.courses" />
        <ProgressTab v-if="activeTab === 'progress'" :courses="props.courses" />
        <CompletionTab v-if="activeTab === 'completion'" :courses="props.courses" />
        <QuizScoresTab v-if="activeTab === 'scores'" :courses="props.courses" />
      </div>
    </div>
  </TeacherLayout>
</template>
