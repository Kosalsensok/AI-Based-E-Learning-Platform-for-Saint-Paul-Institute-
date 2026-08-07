<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  report: {
    overview: {
      attempts: number
      passed: number
      passed_pct: number
      failed: number
      failed_pct: number
      avg_score: number
    }
    quiz_types: {
      pre_test: number
      practice: number
      post_test: number
    }
    difficulty_analysis: {
      easy: number
      medium: number
      hard: number
    }
    most_incorrect: Array<any>
    student_comparison: Array<any>
    question_analytics: Array<any>
    ai_quiz_recommendation: {
      module: string
      avg_score: string
      recommendations: Array<string>
    }
  }
  courses: Array<any>
}>()

const emit = defineEmits(['exportPdf', 'exportExcel', 'openQuestionAnalytics', 'openAction'])

const selectedCourse = ref(props.courses?.[0]?.id || 1)
const selectedQuiz = ref('All Quizzes')
const selectedModule = ref('All Modules')
</script>

<template>
  <div class="space-y-6">
    <!-- Header Filters & Quiz Overview -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-5">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-gray-700 pb-4">
        <div>
          <h2 class="text-base font-extrabold text-slate-800 dark:text-white flex items-center gap-2">
            <span>📝 QUIZ REPORT</span>
          </h2>
          <p class="text-xs text-slate-500">
            Comprehensive assessment analysis (Pre-Test, Practice, Post-Test, Question difficulty & Student Improvement).
          </p>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap items-center gap-2.5 text-xs">
          <select
            v-model="selectedCourse"
            class="px-3 py-2 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl font-bold text-slate-800 dark:text-white"
          >
            <option v-for="c in courses" :key="c.id" :value="c.id">📚 {{ c.title }}</option>
          </select>

          <select
            v-model="selectedQuiz"
            class="px-3 py-2 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl font-bold text-slate-800 dark:text-white"
          >
            <option value="All Quizzes">All Quizzes ▼</option>
            <option value="Pre-Test">Pre-Test</option>
            <option value="Practice Quiz">Practice Quiz</option>
            <option value="Post-Test">Post-Test</option>
          </select>

          <select
            v-model="selectedModule"
            class="px-3 py-2 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl font-bold text-slate-800 dark:text-white"
          >
            <option value="All Modules">All Modules ▼</option>
            <option value="Module 1">Module 1</option>
            <option value="Module 2">Module 2</option>
            <option value="Module 3">Module 3</option>
            <option value="Module 4">Module 4</option>
          </select>
        </div>
      </div>

      <!-- Quiz Overview Metric Cards -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
        <div class="p-4 rounded-2xl bg-blue-50/70 dark:bg-blue-900/20 border border-blue-200/60 dark:border-blue-800/40">
          <span class="text-[11px] font-extrabold text-blue-800 dark:text-blue-300 uppercase">Attempts</span>
          <p class="text-2xl font-black text-blue-700 dark:text-blue-400 mt-1">{{ report?.overview?.attempts || 420 }}</p>
          <span class="text-[10px] text-slate-500 font-medium">Total quiz tries</span>
        </div>

        <div class="p-4 rounded-2xl bg-emerald-50/70 dark:bg-emerald-900/20 border border-emerald-200/60 dark:border-emerald-800/40">
          <span class="text-[11px] font-extrabold text-emerald-800 dark:text-emerald-300 uppercase">Passed</span>
          <p class="text-2xl font-black text-emerald-700 dark:text-emerald-400 mt-1">
            {{ report?.overview?.passed || 328 }}
            <span class="text-sm font-bold">({{ report?.overview?.passed_pct || 78 }}%)</span>
          </p>
          <span class="text-[10px] text-slate-500 font-medium">Above pass threshold</span>
        </div>

        <div class="p-4 rounded-2xl bg-rose-50/70 dark:bg-rose-900/20 border border-rose-200/60 dark:border-rose-800/40">
          <span class="text-[11px] font-extrabold text-rose-800 dark:text-rose-300 uppercase">Failed</span>
          <p class="text-2xl font-black text-rose-700 dark:text-rose-400 mt-1">
            {{ report?.overview?.failed || 92 }}
            <span class="text-sm font-bold">({{ report?.overview?.failed_pct || 22 }}%)</span>
          </p>
          <span class="text-[10px] text-slate-500 font-medium">Below pass mark</span>
        </div>

        <div class="p-4 rounded-2xl bg-purple-50/70 dark:bg-purple-900/20 border border-purple-200/60 dark:border-purple-800/40">
          <span class="text-[11px] font-extrabold text-purple-800 dark:text-purple-300 uppercase">Average Score</span>
          <p class="text-2xl font-black text-purple-700 dark:text-purple-400 mt-1">{{ report?.overview?.avg_score || 78 }}%</p>
          <span class="text-[10px] text-slate-500 font-medium">Overall class mean</span>
        </div>
      </div>
    </div>

    <!-- Quiz Type Performance & Difficulty Breakdown Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 text-xs">
      <!-- Quiz Type Breakdown -->
      <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-4">
        <div class="border-b pb-3">
          <h3 class="font-extrabold text-sm text-slate-800 dark:text-white">📊 Performance by Quiz Type</h3>
        </div>

        <div class="space-y-4 pt-1">
          <div class="space-y-1">
            <div class="flex justify-between font-extrabold text-slate-800 dark:text-white">
              <span class="flex items-center gap-1.5"><span class="text-blue-600 font-black">🟦</span> Pre-Test</span>
              <span class="text-blue-600 font-extrabold">{{ report?.quiz_types?.pre_test || 45 }}%</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-gray-700 h-3 rounded-full overflow-hidden">
              <div class="bg-blue-500 h-full rounded-full" :style="{ width: (report?.quiz_types?.pre_test || 45) + '%' }"></div>
            </div>
          </div>

          <div class="space-y-1">
            <div class="flex justify-between font-extrabold text-slate-800 dark:text-white">
              <span class="flex items-center gap-1.5"><span class="text-emerald-600 font-black">🟩</span> Practice Quiz</span>
              <span class="text-emerald-600 font-extrabold">{{ report?.quiz_types?.practice || 84 }}%</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-gray-700 h-3 rounded-full overflow-hidden">
              <div class="bg-emerald-500 h-full rounded-full" :style="{ width: (report?.quiz_types?.practice || 84) + '%' }"></div>
            </div>
          </div>

          <div class="space-y-1">
            <div class="flex justify-between font-extrabold text-slate-800 dark:text-white">
              <span class="flex items-center gap-1.5"><span class="text-rose-600 font-black">🟥</span> Post-Test</span>
              <span class="text-rose-600 font-extrabold">{{ report?.quiz_types?.post_test || 78 }}%</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-gray-700 h-3 rounded-full overflow-hidden">
              <div class="bg-rose-500 h-full rounded-full" :style="{ width: (report?.quiz_types?.post_test || 78) + '%' }"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Question Difficulty Analysis -->
      <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-4">
        <div class="border-b pb-3">
          <h3 class="font-extrabold text-sm text-slate-800 dark:text-white">🎯 Question Difficulty Analysis</h3>
        </div>

        <div class="grid grid-cols-3 gap-3 text-center pt-2">
          <div class="p-3.5 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100">
            <span class="text-[11px] font-extrabold text-emerald-800 dark:text-emerald-300 block uppercase">Easy Questions</span>
            <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ report?.difficulty_analysis?.easy || 92 }}%</p>
            <span class="text-[10px] text-slate-500">Correct rate</span>
          </div>

          <div class="p-3.5 rounded-2xl bg-amber-50 dark:bg-amber-900/20 border border-amber-100">
            <span class="text-[11px] font-extrabold text-amber-800 dark:text-amber-300 block uppercase">Medium</span>
            <p class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ report?.difficulty_analysis?.medium || 74 }}%</p>
            <span class="text-[10px] text-slate-500">Correct rate</span>
          </div>

          <div class="p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-900/20 border border-rose-100">
            <span class="text-[11px] font-extrabold text-rose-800 dark:text-rose-300 block uppercase">Hard Questions</span>
            <p class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ report?.difficulty_analysis?.hard || 41 }}%</p>
            <span class="text-[10px] text-slate-500">Correct rate</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Student Result Comparison & Most Incorrect Questions Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 text-xs">
      <!-- 📈 Student Result Comparison (Improvement Tracker) -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden">
        <div class="p-4 bg-slate-50 dark:bg-gray-700/50 border-b border-slate-200/80 dark:border-gray-700">
          <h3 class="font-extrabold text-sm text-slate-800 dark:text-white">📈 Student Result Comparison (Pre vs Post Gain)</h3>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-100/70 dark:bg-gray-700/80 text-slate-500 font-extrabold uppercase border-b">
                <th class="p-3">Student</th>
                <th class="p-3">Pre-Test</th>
                <th class="p-3">Practice</th>
                <th class="p-3">Post-Test</th>
                <th class="p-3">Improvement</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
              <tr v-for="c in report?.student_comparison" :key="c.student" class="hover:bg-slate-50 dark:hover:bg-gray-700/40">
                <td class="p-3 font-extrabold text-slate-800 dark:text-white">{{ c.student }}</td>
                <td class="p-3 font-bold text-slate-500">{{ c.pre }}</td>
                <td class="p-3 font-bold text-emerald-600">{{ c.practice }}</td>
                <td class="p-3 font-extrabold text-blue-600">{{ c.post }}</td>
                <td class="p-3 font-black text-emerald-600">{{ c.improvement }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ❌ Most Incorrect Questions Table -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden">
        <div class="p-4 bg-slate-50 dark:bg-gray-700/50 border-b border-slate-200/80 dark:border-gray-700 flex justify-between items-center">
          <h3 class="font-extrabold text-sm text-slate-800 dark:text-white">❌ Most Incorrect Questions</h3>
          <span class="text-[10px] font-extrabold text-rose-600">Requires Review</span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-100/70 dark:bg-gray-700/80 text-slate-500 font-extrabold uppercase border-b">
                <th class="p-3">Question ID</th>
                <th class="p-3">Topic / Concept</th>
                <th class="p-3">Correct Rate</th>
                <th class="p-3">Difficulty</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
              <tr v-for="q in report?.most_incorrect" :key="q.id" class="hover:bg-slate-50 dark:hover:bg-gray-700/40">
                <td class="p-3 font-black text-indigo-600">{{ q.id }}</td>
                <td class="p-3 font-bold text-slate-800 dark:text-white">{{ q.topic }}</td>
                <td class="p-3 font-black text-rose-600">{{ q.correct }}</td>
                <td class="p-3">
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                    {{ q.difficulty }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Question Analytics & AI Recommendations -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 text-xs">
      <!-- Question Analytics Matrix -->
      <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-4">
        <div class="border-b pb-3 flex justify-between items-center">
          <h3 class="font-extrabold text-sm text-slate-800 dark:text-white">❓ Question Analytics Matrix</h3>
          <button @click="emit('exportPdf')" class="text-xs font-bold text-blue-600 hover:underline">Download Question Analysis</button>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-100 dark:bg-gray-700 text-slate-500 font-extrabold uppercase border-b">
                <th class="p-3">Question</th>
                <th class="p-3">Topic</th>
                <th class="p-3">Correct</th>
                <th class="p-3">Wrong</th>
                <th class="p-3">Difficulty</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
              <tr v-for="qa in report?.question_analytics" :key="qa.code" class="hover:bg-slate-50 dark:hover:bg-gray-700/50">
                <td class="p-3 font-extrabold text-blue-600">{{ qa.code }}</td>
                <td class="p-3 font-bold text-slate-800 dark:text-white">{{ qa.topic }}</td>
                <td class="p-3 font-bold text-emerald-600">{{ qa.correct }}%</td>
                <td class="p-3 font-bold text-rose-600">{{ qa.wrong }}%</td>
                <td class="p-3">
                  <span
                    class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold"
                    :class="qa.difficulty === 'Easy' ? 'bg-emerald-100 text-emerald-800' : qa.difficulty === 'Hard' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800'"
                  >
                    {{ qa.difficulty }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- AI Quiz Recommendations -->
      <div class="bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 p-6 rounded-2xl border border-purple-200/80 dark:border-purple-800/40 space-y-4 flex flex-col justify-between">
        <div>
          <div class="flex items-center gap-2 border-b border-purple-200/60 pb-3">
            <span class="text-xl">🤖</span>
            <h3 class="font-extrabold text-sm text-purple-900 dark:text-purple-200 uppercase tracking-wider">AI Quiz Intelligence</h3>
          </div>

          <div class="pt-3 space-y-2">
            <div class="p-3 bg-white dark:bg-gray-800 rounded-xl border border-purple-100 space-y-1">
              <span class="text-[10px] font-bold text-slate-400 uppercase">Target Bottleneck</span>
              <p class="font-extrabold text-purple-700 dark:text-purple-300">
                {{ report?.ai_quiz_recommendation?.module }} (Avg: {{ report?.ai_quiz_recommendation?.avg_score }})
              </p>
            </div>

            <div class="space-y-1.5 pt-1">
              <span class="font-bold text-slate-700 dark:text-slate-200 block">Recommended Teacher Actions:</span>
              <ul class="space-y-1.5">
                <li
                  v-for="(rec, idx) in report?.ai_quiz_recommendation?.recommendations"
                  :key="idx"
                  class="p-2.5 bg-white dark:bg-gray-800 rounded-xl border border-purple-100 text-[11px] font-semibold text-slate-800 dark:text-slate-200"
                >
                  {{ rec }}
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div class="pt-4 flex gap-2">
          <button
            @click="emit('openAction', 'practice_quiz')"
            class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs shadow-sm flex items-center justify-center gap-1.5 transition"
          >
            <span>🟩</span> Create Quiz
          </button>
          <button
            @click="emit('openAction', 'live_review')"
            class="flex-1 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-bold text-xs shadow-sm flex items-center justify-center gap-1.5 transition"
          >
            <span>🎯</span> Schedule Review
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
