<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  courses: Array<any>
}>()

const postTests = ref([
  { id: 1, name: 'Post-Test Module 1: Intro to C', module: 'Module 1', taken: 480, pass_rate: '75%', avg_score: '78%', status: 'Live' },
  { id: 2, name: 'Post-Test Module 2: Variables', module: 'Module 2', taken: 420, pass_rate: '68%', avg_score: '72%', status: 'Live' },
  { id: 3, name: 'Post-Test Module 3: Loops', module: 'Module 3', taken: 380, pass_rate: '45%', avg_score: '58%', status: 'Review' },
  { id: 4, name: 'Final Assessment Course', module: 'All Modules', taken: 0, pass_rate: '—', avg_score: '—', status: 'Scheduled' },
])

const showCompareModal = ref(false)
</script>

<template>
  <div class="space-y-6">
    <!-- Header Banner -->
    <div class="p-5 rounded-2xl bg-gradient-to-r from-rose-600 to-red-700 text-white shadow-md flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="space-y-1">
        <h2 class="text-lg font-extrabold flex items-center gap-2">
          <span>🟥 POST-TESTS — តេស្តក្រោយរៀន</span>
        </h2>
        <p class="text-xs text-rose-100">
          ⚠️ Important: Post-Test results affect module completion, unlock next module, and calculate final certificate eligibility.
        </p>
      </div>
      <div class="flex gap-2">
        <button @click="showCompareModal = true" class="px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur text-white rounded-xl text-xs font-bold transition">
          📈 Pre vs Post Comparison
        </button>
      </div>
    </div>

    <!-- Warning Bar for Low Pass Rate -->
    <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200/80 rounded-2xl flex items-center justify-between text-xs font-medium text-amber-800 dark:text-amber-300">
      <span>⚠️ <strong>Module 3 Post-Test</strong> has low pass rate (45%). Consider reviewing lesson content or question difficulty.</span>
      <button class="px-3 py-1 bg-amber-600 text-white rounded-lg font-bold">Review Questions</button>
    </div>

    <!-- Post-Test Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-slate-50 dark:bg-gray-700/50 text-slate-500 uppercase tracking-wider border-b border-slate-200/80 dark:border-gray-700">
            <th class="p-3.5">Post-Test Name</th>
            <th class="p-3.5">Module</th>
            <th class="p-3.5">Taken</th>
            <th class="p-3.5">Pass Rate</th>
            <th class="p-3.5">Avg Score</th>
            <th class="p-3.5">Status</th>
            <th class="p-3.5 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
          <tr v-for="t in postTests" :key="t.id" class="hover:bg-slate-50/50 dark:hover:bg-gray-700/30 transition">
            <td class="p-3.5 font-bold text-slate-800 dark:text-white flex items-center gap-2">
              <span>🟥</span>
              <span>{{ t.name }}</span>
            </td>
            <td class="p-3.5 text-slate-600 dark:text-slate-300 font-medium">{{ t.module }}</td>
            <td class="p-3.5 font-bold text-slate-700 dark:text-slate-200">{{ t.taken }}</td>
            <td class="p-3.5 font-bold text-emerald-600 dark:text-emerald-400">{{ t.pass_rate }}</td>
            <td class="p-3.5 font-bold text-slate-800 dark:text-white">{{ t.avg_score }}</td>
            <td class="p-3.5">
              <span
                class="px-2 py-0.5 rounded-full font-bold text-[10px]"
                :class="{
                  'bg-emerald-100 text-emerald-800': t.status === 'Live',
                  'bg-amber-100 text-amber-800': t.status === 'Review',
                  'bg-slate-100 text-slate-600': t.status === 'Scheduled',
                }"
              >
                {{ t.status === 'Live' ? '🟢 Live' : (t.status === 'Review' ? '⚠️ Review' : '📅 Scheduled') }}
              </span>
            </td>
            <td class="p-3.5 text-right space-x-1">
              <button class="px-2.5 py-1 bg-rose-50 text-rose-600 rounded-lg font-bold">👁 Preview</button>
              <button class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg font-semibold">✏ Edit</button>
              <button class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg font-bold">📊 Results</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- 📈 PRE vs POST COMPARISON MODAL -->
    <div v-if="showCompareModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full p-6 space-y-5 shadow-2xl overflow-y-auto max-h-[90vh]">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-gray-700 pb-3">
          <h3 class="text-base font-bold text-slate-800 dark:text-white">📈 PRE-TEST vs POST-TEST — TEACHING EFFECTIVENESS</h3>
          <button @click="showCompareModal = false" class="text-slate-400 hover:text-slate-600"><i class="pi pi-times"></i></button>
        </div>

        <div class="space-y-4 text-xs">
          <div class="p-3.5 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 rounded-xl flex justify-between font-bold text-emerald-800 dark:text-emerald-300">
            <span>Overall Teaching Impact: +24.3% average score improvement 🎉</span>
          </div>

          <!-- Comparison Table -->
          <div class="border border-slate-200 rounded-xl overflow-hidden">
            <table class="w-full text-left border-collapse">
              <thead class="bg-slate-50 font-bold text-slate-500 uppercase">
                <tr>
                  <th class="p-2.5">Module</th>
                  <th class="p-2.5">🟦 Pre-Test</th>
                  <th class="p-2.5">🟥 Post-Test</th>
                  <th class="p-2.5">Improvement</th>
                  <th class="p-2.5">Rating</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 font-medium">
                <tr>
                  <td class="p-2.5 font-bold">Module 1</td>
                  <td class="p-2.5 text-blue-600 font-bold">45%</td>
                  <td class="p-2.5 text-rose-600 font-bold">78%</td>
                  <td class="p-2.5 text-emerald-600 font-bold">↑ +33%</td>
                  <td class="p-2.5 font-bold text-emerald-600">🟢 Excellent</td>
                </tr>
                <tr>
                  <td class="p-2.5 font-bold">Module 2</td>
                  <td class="p-2.5 text-blue-600 font-bold">52%</td>
                  <td class="p-2.5 text-rose-600 font-bold">72%</td>
                  <td class="p-2.5 text-emerald-600 font-bold">↑ +20%</td>
                  <td class="p-2.5 font-bold text-emerald-600">🟢 Good</td>
                </tr>
                <tr>
                  <td class="p-2.5 font-bold">Module 3</td>
                  <td class="p-2.5 text-blue-600 font-bold">38%</td>
                  <td class="p-2.5 text-rose-600 font-bold">58%</td>
                  <td class="p-2.5 text-amber-600 font-bold">↑ +20%</td>
                  <td class="p-2.5 font-bold text-amber-600">🟡 Needs work</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="p-4 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 rounded-xl space-y-1">
            <p class="font-bold text-purple-900 dark:text-purple-300">🧠 AI INSIGHT:</p>
            <p class="text-slate-600 dark:text-slate-300">
              "Module 1 shows excellent improvement. Module 3 improvement is lower — students may need more practice drills."
            </p>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-slate-100 dark:border-gray-700 text-xs">
          <button @click="showCompareModal = false" class="px-4 py-2 bg-rose-600 text-white rounded-xl font-bold">Close Comparison</button>
        </div>
      </div>
    </div>
  </div>
</template>
