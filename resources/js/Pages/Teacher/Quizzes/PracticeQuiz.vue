<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  courses: Array<any>
}>()

const practiceQuizzes = ref([
  { id: 1, name: 'Variables Drill', chapter: 'Ch 2.1', attempts: '1,240', avg_score: '72%', engagement: 'High', status: 'Live' },
  { id: 2, name: 'Data Types Practice', chapter: 'Ch 2.2', attempts: '980', avg_score: '68%', engagement: 'High', status: 'Live' },
  { id: 3, name: 'If-Else Challenge', chapter: 'Ch 3.1', attempts: '650', avg_score: '61%', engagement: 'Medium', status: 'Live' },
  { id: 4, name: 'Loops Drill', chapter: 'Ch 3.2', attempts: '420', avg_score: '55%', engagement: 'Low', status: 'Live' },
  { id: 5, name: 'Functions Practice', chapter: 'Ch 4.1', attempts: '0', avg_score: '—', engagement: 'None', status: 'Draft' },
])

const showSettingsModal = ref(false)
const showPracticeRunnerModal = ref(false)
</script>

<template>
  <div class="space-y-6">
    <!-- Header Banner -->
    <div class="p-5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-700 text-white shadow-md flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="space-y-1">
        <h2 class="text-lg font-extrabold flex items-center gap-2">
          <span>🟩 PRACTICE QUIZZES — លំហាត់អនុវត្ត</span>
        </h2>
        <p class="text-xs text-emerald-100">
          💡 Practice quizzes can be taken unlimited times with instant feedback. Helps students master concepts without test stress.
        </p>
      </div>
      <div class="flex gap-2">
        <button @click="showSettingsModal = true" class="px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur text-white rounded-xl text-xs font-bold transition">
          ⚙️ Practice Settings
        </button>
      </div>
    </div>

    <!-- Practice Quiz Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-slate-50 dark:bg-gray-700/50 text-slate-500 uppercase tracking-wider border-b border-slate-200/80 dark:border-gray-700">
            <th class="p-3.5">Practice Quiz</th>
            <th class="p-3.5">Chapter</th>
            <th class="p-3.5">Attempts</th>
            <th class="p-3.5">Avg Score</th>
            <th class="p-3.5">Engagement</th>
            <th class="p-3.5">Status</th>
            <th class="p-3.5 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
          <tr v-for="p in practiceQuizzes" :key="p.id" class="hover:bg-slate-50/50 dark:hover:bg-gray-700/30 transition">
            <td class="p-3.5 font-bold text-slate-800 dark:text-white flex items-center gap-2">
              <span>🟩</span>
              <span>{{ p.name }}</span>
            </td>
            <td class="p-3.5 text-slate-600 dark:text-slate-300 font-medium">{{ p.chapter }}</td>
            <td class="p-3.5 font-bold text-slate-700 dark:text-slate-200">{{ p.attempts }}</td>
            <td class="p-3.5 font-bold text-emerald-600 dark:text-emerald-400">{{ p.avg_score }}</td>
            <td class="p-3.5">
              <span
                class="px-2 py-0.5 rounded font-bold text-[10px]"
                :class="{
                  'bg-emerald-100 text-emerald-800': p.engagement === 'High',
                  'bg-amber-100 text-amber-800': p.engagement === 'Medium',
                  'bg-rose-100 text-rose-800': p.engagement === 'Low',
                  'bg-slate-100 text-slate-600': p.engagement === 'None',
                }"
              >
                {{ p.engagement === 'High' ? '🟢 High' : (p.engagement === 'Medium' ? '🟡 Medium' : (p.engagement === 'Low' ? '🟠 Low' : '⚪ None')) }}
              </span>
            </td>
            <td class="p-3.5">
              <span class="px-2 py-0.5 rounded-full font-bold text-[10px]" :class="p.status === 'Live' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'">
                {{ p.status === 'Live' ? '🟢 Live' : '🟡 Draft' }}
              </span>
            </td>
            <td class="p-3.5 text-right space-x-1">
              <button @click="showPracticeRunnerModal = true" class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg font-bold">👁 Preview</button>
              <button class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg font-semibold">✏ Edit</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- ⚙️ PRACTICE QUIZ SETTINGS MODAL -->
    <div v-if="showSettingsModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <h3 class="text-base font-bold text-slate-800 dark:text-white">⚙️ PRACTICE QUIZ SETTINGS</h3>
        <div class="space-y-2 text-xs">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" checked class="rounded text-emerald-600" />
            <span>☑️ Unlimited Attempts Allowed</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" checked class="rounded text-emerald-600" />
            <span>☑️ Show Answer & Explanation Immediately</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" checked class="rounded text-emerald-600" />
            <span>☑️ Enable Gamification Streak & Points Counter</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" checked class="rounded text-emerald-600" />
            <span>☑️ Allow 1 Hint Per Question</span>
          </label>
        </div>
        <div class="flex justify-end pt-2 border-t">
          <button @click="showSettingsModal = false" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold shadow">💾 Save Practice Settings</button>
        </div>
      </div>
    </div>

    <!-- 👁 STUDENT VIEW PRACTICE QUIZ RUNNER MODAL -->
    <div v-if="showPracticeRunnerModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b pb-3">
          <h3 class="font-bold text-emerald-600">🟩 PRACTICE QUIZ: Variables in C (Attempt 2)</h3>
          <div class="flex gap-2 text-xs font-bold">
            <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded">🔥 Streak: 4</span>
            <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded">⭐ Points: 8/10</span>
          </div>
        </div>
        <div class="space-y-3 text-xs">
          <p class="font-extrabold text-sm">Question 5 of 15: Which keyword declares an integer variable in C?</p>
          <div class="grid grid-cols-2 gap-2">
            <button class="p-2.5 rounded-lg border bg-slate-50 text-left">( ) float</button>
            <button class="p-2.5 rounded-lg border bg-emerald-100 border-emerald-500 font-bold text-left">(●) int</button>
            <button class="p-2.5 rounded-lg border bg-slate-50 text-left">( ) char</button>
            <button class="p-2.5 rounded-lg border bg-slate-50 text-left">( ) string</button>
          </div>

          <!-- Instant Feedback Box -->
          <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-300 rounded-xl space-y-1">
            <p class="font-extrabold text-emerald-800 dark:text-emerald-300">✅ CORRECT! +2 points</p>
            <p class="text-slate-600 dark:text-slate-300">💡 Explanation: "int" is the C keyword for integer variables. Example: int age = 20;</p>
          </div>
        </div>
        <div class="flex justify-end pt-2 border-t">
          <button @click="showPracticeRunnerModal = false" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold">Close Practice Runner</button>
        </div>
      </div>
    </div>
  </div>
</template>
