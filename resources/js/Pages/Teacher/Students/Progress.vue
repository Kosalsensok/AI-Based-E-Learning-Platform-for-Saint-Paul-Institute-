<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  courses: Array<any>
}>()

const activeSubView = ref<'students' | 'heatmap' | 'time'>('heatmap')

// Heatmap Matrix Data
const heatmapData = ref([
  { student: 'Long Vicheka', ch11: '🟩', ch12: '🟩', ch21: '🟩', ch22: '🟩', ch31: '🟢', ch32: '🟡', ch41: '🟠' },
  { student: 'Chan Dara', ch11: '🟩', ch12: '🟩', ch21: '🟩', ch22: '🟢', ch31: '🟡', ch32: '🔴', ch41: '🔴' },
  { student: 'Bun Rithy', ch11: '🟩', ch12: '🟩', ch21: '🟢', ch22: '🟡', ch31: '🟠', ch32: '🔴', ch41: '🔴' },
  { student: 'Mao Sreynich', ch11: '🟩', ch12: '🟢', ch21: '🟡', ch22: '🟠', ch31: '🔴', ch32: '🔴', ch41: '🔴' },
  { student: 'Sok Channa', ch11: '🟢', ch12: '🟡', ch21: '🟠', ch22: '🔴', ch31: '🔴', ch32: '🔴', ch41: '🔴' },
])

// Progress Overview Table Data
const progressTable = ref([
  { name: 'Long Vicheka', overall: '90%', m1: '100%', m2: '100%', m3: '85%', m4: '60%', time: '32h', trend: '↗️ Fast' },
  { name: 'Chan Dara', overall: '85%', m1: '100%', m2: '90%', m3: '60%', m4: '0%', time: '28h', trend: '↗️ Good' },
  { name: 'Bun Rithy', overall: '72%', m1: '100%', m2: '80%', m3: '40%', m4: '0%', time: '20h', trend: '→ Steady' },
  { name: 'Mao Sreynich', overall: '63%', m1: '100%', m2: '65%', m3: '20%', m4: '0%', time: '15h', trend: '↘️ Slowing' },
  { name: 'Sok Channa', overall: '45%', m1: '80%', m2: '30%', m3: '0%', m4: '0%', time: '8h', trend: '🔴 Stopped' },
])
</script>

<template>
  <div class="space-y-6">
    <!-- View Switcher Bar -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div class="flex items-center gap-1 p-1 bg-slate-100 dark:bg-gray-700 rounded-xl text-xs font-bold">
        <button @click="activeSubView = 'heatmap'" :class="['px-3 py-1.5 rounded-lg transition', activeSubView === 'heatmap' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-sm' : 'text-slate-500']">🔥 Progress Heatmap</button>
        <button @click="activeSubView = 'students'" :class="['px-3 py-1.5 rounded-lg transition', activeSubView === 'students' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-sm' : 'text-slate-500']">👤 By Student</button>
        <button @click="activeSubView = 'time'" :class="['px-3 py-1.5 rounded-lg transition', activeSubView === 'time' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-sm' : 'text-slate-500']">⏱️ Learning Time</button>
      </div>

      <div class="text-xs font-bold text-slate-500">
        Class Average: <span class="text-blue-600 font-extrabold text-sm">72%</span> | Fastest: Long Vicheka (90%)
      </div>
    </div>

    <!-- SUBVIEW 1: HEATMAP -->
    <div v-if="activeSubView === 'heatmap'" class="space-y-4">
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="font-extrabold text-sm text-slate-800 dark:text-white">🔥 Class Progress Heatmap Matrix</h3>
          <div class="flex gap-2 text-[10px] font-bold">
            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded">🟩 100%</span>
            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded">🟢 75-99%</span>
            <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded">🟡 50-74%</span>
            <span class="px-2 py-0.5 bg-orange-100 text-orange-800 rounded">🟠 25-49%</span>
            <span class="px-2 py-0.5 bg-rose-100 text-rose-800 rounded">🔴 0-24%</span>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-center border-collapse text-xs">
            <thead>
              <tr class="bg-slate-50 dark:bg-gray-700/50 text-slate-500 font-bold border-b">
                <th class="p-3 text-left">Student</th>
                <th class="p-3">Ch 1.1</th>
                <th class="p-3">Ch 1.2</th>
                <th class="p-3">Ch 2.1</th>
                <th class="p-3">Ch 2.2</th>
                <th class="p-3">Ch 3.1</th>
                <th class="p-3">Ch 3.2</th>
                <th class="p-3">Ch 4.1</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="h in heatmapData" :key="h.student" class="hover:bg-slate-50/50 transition">
                <td class="p-3 text-left font-bold text-slate-800 dark:text-white">{{ h.student }}</td>
                <td class="p-3 text-lg">{{ h.ch11 }}</td>
                <td class="p-3 text-lg">{{ h.ch12 }}</td>
                <td class="p-3 text-lg">{{ h.ch21 }}</td>
                <td class="p-3 text-lg">{{ h.ch22 }}</td>
                <td class="p-3 text-lg">{{ h.ch31 }}</td>
                <td class="p-3 text-lg">{{ h.ch32 }}</td>
                <td class="p-3 text-lg">{{ h.ch41 }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- AI Heatmap Insight -->
        <div class="p-4 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 rounded-2xl space-y-2 text-xs">
          <p class="font-extrabold text-purple-900 dark:text-purple-300">💡 AI HEATMAP INSIGHT:</p>
          <p class="text-slate-600 dark:text-slate-300">
            "Chapter 3.2 (While Loop) contains multiple RED indicators. Students are getting stuck here. Consider adding an extra demo video or practice drill."
          </p>
          <div class="flex gap-2 pt-1">
            <button class="px-3 py-1 bg-purple-600 text-white rounded-lg font-bold">📹 Add Extra Video</button>
            <button class="px-3 py-1 bg-emerald-600 text-white rounded-lg font-bold">🟩 Create Practice Quiz</button>
          </div>
        </div>
      </div>
    </div>

    <!-- SUBVIEW 2: BY STUDENT TABLE -->
    <div v-if="activeSubView === 'students'" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden text-xs">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-slate-50 text-slate-500 uppercase font-bold border-b">
            <th class="p-3.5">Student</th>
            <th class="p-3.5">Overall</th>
            <th class="p-3.5">M1</th>
            <th class="p-3.5">M2</th>
            <th class="p-3.5">M3</th>
            <th class="p-3.5">M4</th>
            <th class="p-3.5">Time</th>
            <th class="p-3.5">Trend</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="p in progressTable" :key="p.name" class="hover:bg-slate-50/50 transition font-medium">
            <td class="p-3.5 font-bold text-slate-800 dark:text-white">{{ p.name }}</td>
            <td class="p-3.5 font-extrabold text-blue-600">{{ p.overall }}</td>
            <td class="p-3.5 text-emerald-600 font-bold">{{ p.m1 }}</td>
            <td class="p-3.5 text-blue-600 font-bold">{{ p.m2 }}</td>
            <td class="p-3.5 text-amber-600 font-bold">{{ p.m3 }}</td>
            <td class="p-3.5 text-rose-600 font-bold">{{ p.m4 }}</td>
            <td class="p-3.5 text-slate-500">{{ p.time }}</td>
            <td class="p-3.5 font-bold">{{ p.trend }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- SUBVIEW 3: LEARNING TIME CHART -->
    <div v-if="activeSubView === 'time'" class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm space-y-4 text-xs">
      <h3 class="font-extrabold text-sm text-slate-800 dark:text-white">⏱️ Learning Time Analysis — Peak Hours</h3>
      <div class="p-4 bg-slate-50 dark:bg-gray-900 rounded-2xl space-y-3">
        <p class="font-bold text-slate-700 dark:text-slate-200">Class Total: 245 hours | Avg per student: 5.4 hours/week</p>
        <p class="text-amber-600 font-bold">🔥 Peak Learning Time: Thursday 7–9 PM (Highest student activity)</p>
        <p class="text-slate-500">💡 Recommendation: Schedule your Live QA sessions on Thursday evening for maximum attendance.</p>
      </div>
    </div>
  </div>
</template>
