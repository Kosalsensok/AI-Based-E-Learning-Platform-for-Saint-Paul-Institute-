<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  courses: Array<any>
}>()

const preTests = ref([
  { id: 1, name: 'Pre-Test: Intro to C', module: 'Module 1', taken: 520, avg_score: '45%', gap: 'High', gapColor: 'red', status: 'Live' },
  { id: 2, name: 'Pre-Test: Variables', module: 'Module 2', taken: 480, avg_score: '52%', gap: 'Medium', gapColor: 'yellow', status: 'Live' },
  { id: 3, name: 'Pre-Test: Loops', module: 'Module 3', taken: 420, avg_score: '38%', gap: 'High', gapColor: 'red', status: 'Live' },
  { id: 4, name: 'Pre-Test: Functions', module: 'Module 4', taken: 0, avg_score: '—', gap: 'None', gapColor: 'slate', status: 'Draft' },
])

const showGapModal = ref(false)
const selectedPreTest = ref<any>(null)

const openGapAnalysis = (test: any) => {
  selectedPreTest.value = test
  showGapModal.value = true
}

const showStudentRunnerModal = ref(false)
</script>

<template>
  <div class="space-y-6">
    <!-- Header Banner -->
    <div class="p-5 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-md flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="space-y-1">
        <h2 class="text-lg font-extrabold flex items-center gap-2">
          <span>🟦 PRE-TESTS — តេស្តមុនរៀន</span>
        </h2>
        <p class="text-xs text-blue-100">
          💡 Purpose: Understand student baseline knowledge before teaching. Scores do NOT affect final course grades.
        </p>
      </div>
      <div class="flex gap-2">
        <button @click="openGapAnalysis(preTests[0])" class="px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur text-white rounded-xl text-xs font-bold transition">
          📊 View Knowledge Gap Analysis
        </button>
      </div>
    </div>

    <!-- Pre-Test Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-slate-50 dark:bg-gray-700/50 text-slate-500 uppercase tracking-wider border-b border-slate-200/80 dark:border-gray-700">
            <th class="p-3.5">Pre-Test Name</th>
            <th class="p-3.5">Module</th>
            <th class="p-3.5">Students Taken</th>
            <th class="p-3.5">Avg Score</th>
            <th class="p-3.5">Gap Level</th>
            <th class="p-3.5">Status</th>
            <th class="p-3.5 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
          <tr v-for="t in preTests" :key="t.id" class="hover:bg-slate-50/50 dark:hover:bg-gray-700/30 transition">
            <td class="p-3.5 font-bold text-slate-800 dark:text-white flex items-center gap-2">
              <span>🟦</span>
              <span>{{ t.name }}</span>
            </td>
            <td class="p-3.5 text-slate-600 dark:text-slate-300 font-medium">{{ t.module }}</td>
            <td class="p-3.5 font-bold text-slate-700 dark:text-slate-200">{{ t.taken }}</td>
            <td class="p-3.5 font-bold text-blue-600 dark:text-blue-400">{{ t.avg_score }}</td>
            <td class="p-3.5">
              <span
                class="px-2 py-0.5 rounded font-bold text-[10px]"
                :class="{
                  'bg-rose-100 text-rose-800': t.gap === 'High',
                  'bg-amber-100 text-amber-800': t.gap === 'Medium',
                  'bg-slate-100 text-slate-600': t.gap === 'None',
                }"
              >
                {{ t.gap === 'High' ? '🔴 High' : (t.gap === 'Medium' ? '🟡 Medium' : '⚪ None') }}
              </span>
            </td>
            <td class="p-3.5">
              <span class="px-2 py-0.5 rounded-full font-bold text-[10px]" :class="t.status === 'Live' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'">
                {{ t.status === 'Live' ? '🟢 Live' : '🟡 Draft' }}
              </span>
            </td>
            <td class="p-3.5 text-right space-x-1">
              <button @click="showStudentRunnerModal = true" class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-lg font-bold">👁 Preview</button>
              <button @click="openGapAnalysis(t)" class="px-2.5 py-1 bg-purple-50 text-purple-700 rounded-lg font-bold">📊 Gap Analysis</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- 📊 KNOWLEDGE GAP ANALYSIS MODAL -->
    <div v-if="showGapModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full p-6 space-y-5 shadow-2xl overflow-y-auto max-h-[90vh]">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-gray-700 pb-3">
          <h3 class="text-base font-bold text-slate-800 dark:text-white">📊 KNOWLEDGE GAP ANALYSIS — {{ selectedPreTest?.name }}</h3>
          <button @click="showGapModal = false" class="text-slate-400 hover:text-slate-600"><i class="pi pi-times"></i></button>
        </div>

        <div class="space-y-4 text-xs">
          <div class="p-3.5 bg-blue-50 dark:bg-gray-700/50 rounded-xl flex justify-between font-bold">
            <span>Students Taken: {{ selectedPreTest?.taken }}</span>
            <span>Average Score: {{ selectedPreTest?.avg_score }}</span>
            <span class="text-rose-600 font-extrabold">🔴 Beginner Level Baseline</span>
          </div>

          <!-- Distribution Breakdown -->
          <div class="space-y-2">
            <p class="font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider text-[11px]">STUDENT LEVEL DISTRIBUTION</p>
            <div class="space-y-1.5 font-medium">
              <div class="flex items-center gap-2">
                <span class="w-36">🔴 Beginner (0-40%)</span>
                <div class="flex-1 bg-slate-100 dark:bg-gray-700 h-3 rounded-full overflow-hidden">
                  <div class="bg-rose-500 h-full w-[55%] rounded-full"></div>
                </div>
                <span class="font-bold">285 students (55%)</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="w-36">🟡 Basic (41-60%)</span>
                <div class="flex-1 bg-slate-100 dark:bg-gray-700 h-3 rounded-full overflow-hidden">
                  <div class="bg-amber-500 h-full w-[30%] rounded-full"></div>
                </div>
                <span class="font-bold">156 students (30%)</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="w-36">🟢 Good (61-80%)</span>
                <div class="flex-1 bg-slate-100 dark:bg-gray-700 h-3 rounded-full overflow-hidden">
                  <div class="bg-emerald-500 h-full w-[12%] rounded-full"></div>
                </div>
                <span class="font-bold">62 students (12%)</span>
              </div>
            </div>
          </div>

          <!-- AI Teaching Suggestion -->
          <div class="p-4 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 rounded-xl space-y-1">
            <p class="font-bold text-purple-900 dark:text-purple-300">🧠 AI TEACHING SUGGESTION:</p>
            <p class="text-slate-600 dark:text-slate-300">
              "55% of your students are beginners in compiler concepts and dev environment setup. Focus extra class time on live demos."
            </p>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-slate-100 dark:border-gray-700 text-xs">
          <button @click="showGapModal = false" class="px-4 py-2 bg-blue-600 text-white rounded-xl font-bold">Close Gap Analysis</button>
        </div>
      </div>
    </div>

    <!-- 👁 STUDENT VIEW PRE-TEST RUNNER MODAL -->
    <div v-if="showStudentRunnerModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b pb-3">
          <h3 class="font-bold text-blue-600">🟦 PRE-TEST: Introduction to C (Student View)</h3>
          <span class="text-xs font-mono bg-slate-100 p-1.5 rounded font-bold">⏱ Time: 18:42</span>
        </div>
        <div class="space-y-3 text-xs">
          <p class="font-extrabold text-sm">Question 4 of 20: What is a compiler?</p>
          <div class="space-y-1.5">
            <label class="block p-2.5 rounded-lg border bg-slate-50">( ) A. A computer game</label>
            <label class="block p-2.5 rounded-lg border bg-blue-50 border-blue-500 font-bold">(●) B. A program that translates source code into machine code</label>
            <label class="block p-2.5 rounded-lg border bg-slate-50">( ) C. A web browser</label>
          </div>
        </div>
        <div class="flex justify-end pt-2 border-t">
          <button @click="showStudentRunnerModal = false" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold">Close Student View</button>
        </div>
      </div>
    </div>
  </div>
</template>
