<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  courses: Array<any>
}>()

const moduleCompletion = ref([
  { module: 'M1: Introduction', completed: '44 (98%)', in_progress: '1', not_started: '0', avg_time: '4h 20m', status: '🟢' },
  { module: 'M2: Variables', completed: '38 (84%)', in_progress: '5', not_started: '2', avg_time: '6h 15m', status: '🟢' },
  { module: 'M3: Loops', completed: '28 (62%)', in_progress: '12', not_started: '5', avg_time: '8h 40m', status: '🟡' },
  { module: 'M4: Functions', completed: '15 (33%)', in_progress: '18', not_started: '12', avg_time: '12h 30m', status: '🔴' },
])

const dropoffFunnel = ref([
  { step: 'Enrolled', count: '45 (100%)', drop: '' },
  { step: 'Started M1', count: '44 (98%)', drop: '↓ -1' },
  { step: 'Completed M1', count: '44 (98%)', drop: '' },
  { step: 'Started M2', count: '43 (96%)', drop: '↓ -1' },
  { step: 'Completed M2', count: '38 (84%)', drop: '↓ -5' },
  { step: 'Started M3', count: '40 (89%)', drop: '' },
  { step: 'Completed M3', count: '28 (62%)', drop: '↓ -12 ⚠️' },
  { step: 'Started M4', count: '33 (73%)', drop: '' },
  { step: 'Completed M4', count: '15 (33%)', drop: '↓ -18 🔴' },
  { step: 'Passed Post-Test', count: '13 (29%)', drop: '' },
  { step: 'Got Certificate', count: '13 (29%)', drop: '' },
])
</script>

<template>
  <div class="space-y-6">
    <!-- Completion Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex items-center justify-between">
        <div>
          <p class="text-xs text-slate-400 font-bold uppercase">Overall Completion</p>
          <p class="text-2xl font-extrabold text-emerald-600">71%</p>
          <p class="text-[11px] text-slate-500 font-medium">32 of 45 students completed</p>
        </div>
        <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-700 font-extrabold flex items-center justify-center text-base border-4 border-emerald-500">
          71%
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex items-center justify-between">
        <div>
          <p class="text-xs text-slate-400 font-bold uppercase">In Progress</p>
          <p class="text-2xl font-extrabold text-blue-600">18%</p>
          <p class="text-[11px] text-slate-500 font-medium">8 students currently learning</p>
        </div>
        <div class="w-14 h-14 rounded-full bg-blue-100 text-blue-700 font-extrabold flex items-center justify-center text-base border-4 border-blue-500">
          18%
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex items-center justify-between">
        <div>
          <p class="text-xs text-slate-400 font-bold uppercase">Not Started</p>
          <p class="text-2xl font-extrabold text-rose-600">11%</p>
          <p class="text-[11px] text-slate-500 font-medium">5 students inactive</p>
        </div>
        <div class="w-14 h-14 rounded-full bg-rose-100 text-rose-700 font-extrabold flex items-center justify-center text-base border-4 border-rose-500">
          11%
        </div>
      </div>
    </div>

    <!-- 🔻 DROP-OFF FUNNEL -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm space-y-4 text-xs">
      <div class="flex items-center justify-between border-b pb-3">
        <h3 class="font-extrabold text-sm text-slate-800 dark:text-white">🔻 Student Drop-off Funnel Analysis</h3>
        <span class="text-rose-600 font-bold">🔴 Biggest Drop: Module 4 (-18 students)</span>
      </div>

      <div class="space-y-2">
        <div v-for="f in dropoffFunnel" :key="f.step" class="flex items-center justify-between p-2 rounded-lg bg-slate-50 dark:bg-gray-700">
          <span class="font-bold text-slate-700 dark:text-slate-200 w-36">{{ f.step }}</span>
          <div class="flex-1 mx-4 bg-slate-200 dark:bg-gray-600 h-3 rounded-full overflow-hidden">
            <div class="bg-blue-600 h-full rounded-full" :style="{ width: f.count.split('(')[1]?.replace('%)', '%') || '100%' }"></div>
          </div>
          <div class="w-32 text-right font-bold text-slate-800 dark:text-white">
            <span>{{ f.count }}</span>
            <span class="text-rose-600 ml-2">{{ f.drop }}</span>
          </div>
        </div>
      </div>

      <!-- AI Funnel Insight -->
      <div class="p-4 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 rounded-2xl space-y-2">
        <p class="font-extrabold text-rose-900 dark:text-rose-300">💡 AI FUNNEL REASONING:</p>
        <p class="text-slate-600 dark:text-slate-300">
          "Module 4 duration is too long (12h) without sufficient practice quizzes. Splitting Module 4 into 2 shorter modules will increase completion by ~35%."
        </p>
        <div class="flex gap-2 pt-1">
          <button class="px-3 py-1 bg-emerald-600 text-white rounded-lg font-bold">🟩 Add Practice Quiz</button>
          <button class="px-3 py-1 bg-blue-600 text-white rounded-lg font-bold">✂️ Split Module 4</button>
        </div>
      </div>
    </div>

    <!-- MODULE COMPLETION TABLE -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden text-xs">
      <div class="p-4 border-b font-extrabold text-sm">📦 Module Completion Breakdown</div>
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-slate-50 text-slate-500 uppercase font-bold border-b">
            <th class="p-3.5">Module</th>
            <th class="p-3.5">Completed</th>
            <th class="p-3.5">In Progress</th>
            <th class="p-3.5">Not Started</th>
            <th class="p-3.5">Avg Time</th>
            <th class="p-3.5">Rate</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="m in moduleCompletion" :key="m.module" class="hover:bg-slate-50/50 transition">
            <td class="p-3.5 font-bold text-slate-800 dark:text-white">{{ m.module }}</td>
            <td class="p-3.5 font-bold text-emerald-600">{{ m.completed }}</td>
            <td class="p-3.5 text-blue-600 font-bold">{{ m.in_progress }}</td>
            <td class="p-3.5 text-rose-600 font-bold">{{ m.not_started }}</td>
            <td class="p-3.5 text-slate-500">{{ m.avg_time }}</td>
            <td class="p-3.5 text-base font-bold">{{ m.status }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
