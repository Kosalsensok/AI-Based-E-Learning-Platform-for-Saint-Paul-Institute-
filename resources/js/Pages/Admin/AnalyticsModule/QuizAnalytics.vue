<script setup lang="ts">
import { ref } from 'vue'
import type { QuizAnalyticsData } from './types'

const props = defineProps<{
  data: QuizAnalyticsData
}>()

const emit = defineEmits<{
  (e: 'reviewQuestions', quizName: string): void
  (e: 'sendToTeacher', quizName: string): void
  (e: 'modifyQuiz', quizName: string): void
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
          <span>📝</span> QUIZ & ASSESSMENT ANALYTICS
        </h3>
        <p class="text-slate-400 text-xs">Analyze pass rates, difficulty index, score distributions, and individual question failure rates.</p>
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

    <!-- 4 KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4 rounded-2xl shadow-xl space-y-1">
        <span class="text-slate-400 text-[10px] block font-semibold">📝 TOTAL QUIZZES</span>
        <p class="text-2xl font-black text-white">{{ data.kpis.total_quizzes }}</p>
        <span class="text-[10px] text-emerald-400 font-bold">Assessments Active</span>
      </div>

      <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4 rounded-2xl shadow-xl space-y-1">
        <span class="text-slate-400 text-[10px] block font-semibold">✅ PASS RATE</span>
        <p class="text-2xl font-black text-emerald-400">{{ data.kpis.pass_rate }}%</p>
        <span class="text-[10px] text-slate-400">Passing Grade: 70%</span>
      </div>

      <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4 rounded-2xl shadow-xl space-y-1">
        <span class="text-slate-400 text-[10px] block font-semibold">📊 AVG SCORE</span>
        <p class="text-2xl font-black text-purple-300">{{ data.kpis.avg_score }}%</p>
        <span class="text-[10px] text-slate-400">Mean Score</span>
      </div>

      <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4 rounded-2xl shadow-xl space-y-1">
        <span class="text-slate-400 text-[10px] block font-semibold">⏱️ AVG TIME SPENT</span>
        <p class="text-2xl font-black text-cyan-300">{{ data.kpis.avg_time }}</p>
        <span class="text-[10px] text-cyan-400 font-semibold">Per Quiz Attempt</span>
      </div>
    </div>

    <!-- Score Distribution & Pass Rate by Quiz Type -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
      <!-- Score Distribution -->
      <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
        <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5">
          📊 SCORE DISTRIBUTION (ALL QUIZZES)
        </h4>

        <div class="space-y-3">
          <div v-for="(s, idx) in data.score_distribution" :key="idx" class="space-y-1">
            <div class="flex items-center justify-between text-xs">
              <span class="font-semibold text-slate-300 w-28">{{ s.range }}</span>
              <div class="flex-1 mx-3 h-2.5 bg-slate-900 rounded-full overflow-hidden border border-slate-700/80">
                <div
                  class="h-full rounded-full"
                  :class="s.flag ? 'bg-red-500' : 'bg-purple-500'"
                  :style="{ width: s.percent * 2 + '%' }"
                ></div>
              </div>
              <span class="font-bold w-32 text-right" :class="s.flag ? 'text-red-400' : 'text-white'">
                {{ s.count }} Students ({{ s.percent }}%) {{ s.flag ? '⚠️' : '' }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Pass Rate by Quiz Type -->
      <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
        <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5">
          🎯 PASS RATE BY QUIZ TYPE
        </h4>

        <div class="space-y-3">
          <div v-for="(p, idx) in data.pass_by_type" :key="idx" class="space-y-1">
            <div class="flex items-center justify-between text-xs">
              <span class="font-semibold text-slate-300 w-44">{{ p.type }}</span>
              <div class="flex-1 mx-3 h-2.5 bg-slate-900 rounded-full overflow-hidden border border-slate-700/80">
                <div class="h-full bg-teal-400 rounded-full" :style="{ width: p.pass_rate + '%' }"></div>
              </div>
              <span class="font-bold text-white w-28 text-right">{{ p.pass_rate }}% <span v-if="p.note" class="text-[10px] text-slate-400">({{ p.note }})</span></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Most Difficult Quizzes -->
    <div class="bg-[#0d1222]/95 border border-red-500/40 rounded-2xl p-5 shadow-2xl space-y-4">
      <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5 text-red-400 flex items-center gap-2">
        <span>⚠️</span> MOST DIFFICULT QUIZZES (LOW PASS RATE)
      </h4>

      <div class="overflow-x-auto rounded-xl border border-slate-700/80 bg-[#121827]">
        <table class="w-full text-left text-xs text-slate-300">
          <thead class="bg-slate-900 text-slate-400 uppercase font-bold border-b border-slate-700/80">
            <tr>
              <th class="p-3">Quiz Name</th>
              <th class="p-3">Course</th>
              <th class="p-3">Total Attempts</th>
              <th class="p-3">Pass Rate</th>
              <th class="p-3">Avg Score</th>
              <th class="p-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/80 font-medium">
            <tr v-for="(q, idx) in data.difficult_quizzes" :key="idx" class="hover:bg-slate-800/40 transition-colors">
              <td class="p-3 font-bold text-white">{{ q.name }}</td>
              <td class="p-3 text-slate-300">{{ q.course }}</td>
              <td class="p-3 font-semibold text-slate-200">{{ q.attempts }} Attempts</td>
              <td class="p-3 font-black" :class="q.status === 'danger' ? 'text-red-400' : 'text-amber-300'">
                {{ q.pass_rate }}% {{ q.status === 'danger' ? '❌' : '⚠️' }}
              </td>
              <td class="p-3 font-bold text-purple-300">{{ q.avg_score }}%</td>
              <td class="p-3 text-right space-x-1.5">
                <button @click="emit('reviewQuestions', q.name)" class="px-2.5 py-1 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-lg text-xs">
                  Review Questions
                </button>
                <button @click="emit('sendToTeacher', q.name)" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 font-semibold rounded-lg text-xs">
                  Send to Teacher
                </button>
                <button @click="emit('modifyQuiz', q.name)" class="px-2.5 py-1 bg-amber-600/30 hover:bg-amber-600/50 text-amber-300 font-semibold rounded-lg text-xs">
                  Modify
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Most Difficult Questions -->
    <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
      <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5">
        🎯 MOST DIFFICULT QUESTIONS (LOWEST ACCURACY)
      </h4>

      <div class="overflow-x-auto rounded-xl border border-slate-700/80 bg-[#121827]">
        <table class="w-full text-left text-xs text-slate-300">
          <thead class="bg-slate-900 text-slate-400 uppercase font-bold border-b border-slate-700/80">
            <tr>
              <th class="p-3">Q ID</th>
              <th class="p-3">Question Preview</th>
              <th class="p-3">Type</th>
              <th class="p-3">Correct Rate</th>
              <th class="p-3 text-right">Difficulty Tag</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/80 font-medium">
            <tr v-for="q in data.difficult_questions" :key="q.id" class="hover:bg-slate-800/40 transition-colors">
              <td class="p-3 font-mono font-bold text-purple-300">{{ q.id }}</td>
              <td class="p-3 font-semibold text-white">{{ q.preview }}</td>
              <td class="p-3 text-slate-300"><span class="px-2 py-0.5 rounded bg-slate-800 border border-slate-700 text-[10px] font-mono">{{ q.type }}</span></td>
              <td class="p-3 font-bold text-red-400">{{ q.correct_rate }}%</td>
              <td class="p-3 text-right font-bold text-red-300">{{ q.difficulty }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="p-3.5 rounded-xl bg-purple-950/40 border border-purple-500/30 text-xs text-purple-200 font-medium flex items-center gap-2">
        <span>🧠 Suggestion: <strong>"Review these questions for clarity or add more code examples and hints."</strong></span>
      </div>
    </div>

    <!-- Pre-Test vs Post-Test Improvement -->
    <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
      <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5">
        📈 PRE-TEST VS POST-TEST LEARNING GAIN IMPROVEMENT
      </h4>

      <div class="overflow-x-auto rounded-xl border border-slate-700/80 bg-[#121827]">
        <table class="w-full text-left text-xs text-slate-300">
          <thead class="bg-slate-900 text-slate-400 uppercase font-bold border-b border-slate-700/80">
            <tr>
              <th class="p-3">Course Title</th>
              <th class="p-3">Pre-Test Avg</th>
              <th class="p-3">Post-Test Avg</th>
              <th class="p-3 text-right">Net Improvement</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/80 font-medium">
            <tr v-for="(imp, idx) in data.improvement" :key="idx" class="hover:bg-slate-800/40 transition-colors">
              <td class="p-3 font-bold text-white">{{ imp.course }}</td>
              <td class="p-3 font-bold text-amber-300">{{ imp.pre_test }}%</td>
              <td class="p-3 font-bold text-emerald-400">{{ imp.post_test }}%</td>
              <td class="p-3 text-right font-black text-emerald-400">↑ +{{ imp.growth }}% 🟢</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="p-3.5 rounded-xl bg-emerald-950/40 border border-emerald-500/30 text-xs text-emerald-200 font-bold text-center">
        Overall Learning Score Improvement: +25.5% Across All Courses
      </div>
    </div>
  </div>
</template>
