<script setup lang="ts">
import { ref } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const selectedAttempt = ref<any | null>(null)

const historyLog = ref([
  { date: '27/05 09:30', name: 'Pointer Drill', type: '🟩 Practice', score: '68%', time: '22m', status: '✅ Done' },
  { date: '22/05 14:15', name: 'Post-Test Module 2', type: '🟥 Post-Test', score: '82%', time: '26m', status: '✅ Pass' },
  { date: '18/05 20:30', name: 'Loops Practice', type: '🟩 Practice', score: '71%', time: '18m', status: '✅ Done' },
  { date: '12/05 10:00', name: 'Post-Test Module 1', type: '🟥 Post-Test', score: '88%', time: '22m', status: '✅ Pass' },
  { date: '08/05 15:45', name: 'Variables Practice', type: '🟩 Practice', score: '85%', time: '25m', status: '✅ Done' },
  { date: '05/02 09:15', name: 'Pre-Test Module 1', type: '🟦 Pre-Test', score: '65%', time: '12m', status: '✅ Done' }
])

const openDetail = (row: any) => {
  selectedAttempt.value = row
}
</script>

<template>
  <StudentLayout title="Quiz History & Audit Trail">
    <div class="space-y-6">
      
      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
          <h1 class="text-xl sm:text-2xl font-black text-white flex items-center gap-2.5">
            <span>📊</span>
            <span>QUIZ HISTORY & AUDIT TRAIL</span>
          </h1>
          <p class="text-xs text-slate-400 mt-1">
            ប្រវត្តិប្រឡង និងធ្វើ Quiz ទាំងអស់ All-Time តាមដាន Score Trend និងការវិវត្តចំណេះដឹង
          </p>
        </div>

        <span class="px-3.5 py-1.5 rounded-full bg-slate-800 text-indigo-400 border border-slate-700 text-xs font-bold font-mono self-start">
          Total Attempts: 28
        </span>
      </div>

      <!-- Filters & Score Trend -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-5 shadow-xl space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3 text-xs">
          <div class="flex flex-wrap items-center gap-2">
            <select class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-slate-300">
              <option>Course: C Programming Basics</option>
            </select>
            <select class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-slate-300">
              <option>Type: All Types</option>
              <option>Pre-Test</option>
              <option>Practice</option>
              <option>Post-Test</option>
            </select>
          </div>

          <span class="text-emerald-400 font-bold">General Trend: ↗️ Improving!</span>
        </div>

        <!-- Score Trend Bar -->
        <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 text-xs text-slate-300 space-y-1">
          <p class="font-bold text-white">📈 My Score Trend:</p>
          <p class="font-mono text-indigo-300">65% (Pre) → 71% (Prac) → 82% (Post) → 85% (Prac) → 88% (Post) → 68% (Prac)</p>
        </div>
      </div>

      <!-- HISTORY LOG TABLE (Matching Prompt Specs) -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 shadow-xl space-y-4">
        <h3 class="text-sm font-bold text-white uppercase tracking-wider">ALL ATTEMPTS AUDIT LOG</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-900 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-700">
              <tr>
                <th class="p-3">Date/Time</th>
                <th class="p-3">Quiz Name</th>
                <th class="p-3">Type</th>
                <th class="p-3">Score</th>
                <th class="p-3">Time Spent</th>
                <th class="p-3">Status</th>
                <th class="p-3 text-right">Detail</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50 font-medium">
              <tr v-for="item in historyLog" :key="item.date" @click="openDetail(item)" class="hover:bg-slate-700/30 cursor-pointer transition-colors">
                <td class="p-3 font-mono text-slate-400">{{ item.date }}</td>
                <td class="p-3 font-bold text-white">{{ item.name }}</td>
                <td class="p-3 font-bold">{{ item.type }}</td>
                <td class="p-3 font-mono font-bold text-emerald-400">{{ item.score }}</td>
                <td class="p-3 font-mono text-slate-400">{{ item.time }}</td>
                <td class="p-3"><span class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-emerald-300">{{ item.status }}</span></td>
                <td class="p-3 text-right">
                  <button class="px-2.5 py-1 rounded-lg bg-indigo-600/30 text-indigo-300 font-bold text-[10px] border border-indigo-500/30">
                    👁 View
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ATTEMPT DETAIL MODAL -->
      <div v-if="selectedAttempt" class="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-indigo-500/40 rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl">
          <div class="border-b border-slate-800 pb-3 flex items-center justify-between">
            <h3 class="text-base font-black text-white">📊 History Detail – {{ selectedAttempt.name }}</h3>
            <button @click="selectedAttempt = null" class="text-slate-400 hover:text-white font-bold text-sm">✕</button>
          </div>

          <div class="p-3 bg-slate-950 rounded-xl border border-slate-800 text-xs font-mono text-slate-300 space-y-1">
            <p>Score: <strong class="text-emerald-400">{{ selectedAttempt.score }} PASSED</strong> | Time: {{ selectedAttempt.time }}</p>
            <p>Taken on: {{ selectedAttempt.date }}</p>
          </div>

          <div class="space-y-2 text-xs max-h-60 overflow-y-auto custom-scrollbar">
            <h4 class="font-bold text-white uppercase text-[10px]">Question-by-Question Breakdown:</h4>
            <div class="p-2.5 bg-slate-950 rounded-xl border border-slate-800 space-y-1">
              <p class="font-bold text-emerald-400">Q1 ✅ +5pts "What is a variable?" (15s)</p>
            </div>
            <div class="p-2.5 bg-slate-950 rounded-xl border border-slate-800 space-y-1">
              <p class="font-bold text-emerald-400">Q2 ✅ +5pts "int is data type – True/False?" (8s)</p>
            </div>
            <div class="p-2.5 bg-slate-950 rounded-xl border border-slate-800 space-y-1 text-rose-300">
              <p class="font-bold">Q3 ❌ 0pts "Pointer arithmetic output?" (45s)</p>
              <p class="text-[10px] text-slate-400">Your: A | Correct: C  •  💡 *p adds sizeof(type) not 1 byte...</p>
            </div>
          </div>

          <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
            <button @click="selectedAttempt = null" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">
              Close
            </button>
            <button class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md">
              📄 Export Detailed Report
            </button>
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
