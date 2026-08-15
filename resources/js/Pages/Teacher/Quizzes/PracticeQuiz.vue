<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  courses?: Array<any>
}>()

const practiceQuizzes = ref([
  { id: 1, name: 'Variables & Syntax Drill', module: 'Module 1: Intro', questions_count: 15, attempts: '1,240', avg_score: '72%', status: 'Live', unlimited: true },
  { id: 2, name: 'Data Types & Constants Practice', module: 'Module 1: Intro', questions_count: 12, attempts: '980', avg_score: '68%', status: 'Live', unlimited: true },
  { id: 3, name: 'If-Else & Switch Case Challenge', module: 'Module 2: Flow Control', questions_count: 20, attempts: '650', avg_score: '61%', status: 'Live', unlimited: true },
  { id: 4, name: 'Loops & Nested Iterations Drill', module: 'Module 2: Flow Control', questions_count: 18, attempts: '420', avg_score: '55%', status: 'Live', unlimited: true },
  { id: 5, name: 'Functions & Recursion Practice', module: 'Module 3: Modular Code', questions_count: 10, attempts: '0', avg_score: '—', status: 'Draft', unlimited: true },
])

const showCreateModal = ref(false)
const showSettingsModal = ref(false)
const showPracticeRunnerModal = ref(false)
const selectedPractice = ref<any>(null)

const newPracticeForm = ref({
  name: '',
  module: 'Module 1: Intro',
  questions_count: 10,
  time_limit: 'Unlimited',
})

const createPracticeSet = () => {
  if (!newPracticeForm.value.name) {
    alert('Please enter practice quiz name')
    return
  }
  practiceQuizzes.value.unshift({
    id: Date.now(),
    name: newPracticeForm.value.name,
    module: newPracticeForm.value.module,
    questions_count: Number(newPracticeForm.value.questions_count) || 10,
    attempts: '0',
    avg_score: '—',
    status: 'Live',
    unlimited: true
  })
  showCreateModal.value = false
  alert('Practice set created successfully!')
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header Banner -->
    <div class="p-6 rounded-3xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="space-y-1.5">
        <div class="flex items-center gap-2">
          <span class="px-2.5 py-1 rounded-xl bg-white/20 text-white font-extrabold text-xs">PRACTICE QUIZ</span>
          <h2 class="text-lg md:text-xl font-black">🟩 លំហាត់អនុវត្តគ្មានពិន្ទុផ្លូវការ</h2>
        </div>
        <p class="text-xs text-emerald-100 max-w-2xl">
          💡 <strong>មិនគិតពិន្ទុជាផ្លូវការទេ អាចធ្វើម្តងទៀតបានគ្មានកំណត់ (Unlimited Attempts)</strong> • សិស្សអាចធ្វើលំហាត់ដើម្បីពង្រឹងការយល់ដឹង ព្រមទាំងទទួលបាន Instant Feedback និងការពន្យល់លម្អិត។
        </p>
      </div>

      <div class="flex flex-wrap gap-2">
        <button @click="showSettingsModal = true" class="px-3.5 py-2 bg-white/20 hover:bg-white/30 backdrop-blur text-white rounded-2xl text-xs font-bold transition">
          ⚙️ Practice Settings
        </button>
        <button @click="showCreateModal = true" class="px-4 py-2 bg-white text-emerald-800 hover:bg-emerald-50 rounded-2xl text-xs font-black shadow-lg shadow-emerald-950/20 transition flex items-center gap-1.5">
          <span>+ បង្កើត Practice Set</span>
        </button>
      </div>
    </div>

    <!-- Practice Quiz Table -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden">
      <div class="p-4 border-b border-slate-100 dark:border-gray-700 flex justify-between items-center bg-slate-50/50 dark:bg-gray-700/30">
        <h3 class="text-xs font-extrabold text-slate-700 dark:text-slate-200 uppercase tracking-wider">
          Practice Sets Overview ({{ practiceQuizzes.length }} Sets)
        </h3>
        <span class="px-3 py-1 bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 rounded-full font-bold text-[11px]">
          ✓ Unlimited Attempts Mode Active
        </span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-slate-50 dark:bg-gray-700/50 text-slate-500 uppercase tracking-wider border-b border-slate-200/80 dark:border-gray-700">
              <th class="p-4 font-bold">Practice Quiz Name</th>
              <th class="p-4 font-bold">Module</th>
              <th class="p-4 font-bold">Questions</th>
              <th class="p-4 font-bold">Attempts Allowance</th>
              <th class="p-4 font-bold">Total Attempts</th>
              <th class="p-4 font-bold">Avg Score</th>
              <th class="p-4 font-bold">Status</th>
              <th class="p-4 font-bold text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
            <tr v-for="p in practiceQuizzes" :key="p.id" class="hover:bg-slate-50/50 dark:hover:bg-gray-700/30 transition">
              <td class="p-4 font-extrabold text-slate-800 dark:text-white flex items-center gap-2">
                <span>🟩</span>
                <span>{{ p.name }}</span>
              </td>
              <td class="p-4 text-slate-600 dark:text-slate-300 font-medium">{{ p.module }}</td>
              <td class="p-4 font-bold text-slate-700 dark:text-slate-200">{{ p.questions_count }} សំណួរ</td>
              <td class="p-4">
                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-xl font-extrabold text-[10px]">
                  ♾️ Unlimited Attempts
                </span>
              </td>
              <td class="p-4 font-bold text-slate-700 dark:text-slate-200">{{ p.attempts }}</td>
              <td class="p-4 font-extrabold text-emerald-600 dark:text-emerald-400">{{ p.avg_score }}</td>
              <td class="p-4">
                <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px]" :class="p.status === 'Live' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-amber-100 text-amber-800'">
                  {{ p.status === 'Live' ? '🟢 Active' : '🟡 Draft' }}
                </span>
              </td>
              <td class="p-4 text-right space-x-1.5 whitespace-nowrap">
                <button @click="showPracticeRunnerModal = true" class="px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-xl font-bold transition">
                  👁 Preview Drill
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ➕ CREATE PRACTICE SET MODAL -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-slate-200 dark:border-gray-700">
        <div class="flex items-center justify-between border-b pb-3">
          <h3 class="text-base font-extrabold text-slate-800 dark:text-white">➕ Create New Practice Quiz</h3>
          <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600"><i class="pi pi-times"></i></button>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="block font-bold mb-1">Practice Quiz Title (ចំណងជើង)</label>
            <input v-model="newPracticeForm.name" type="text" placeholder="e.g. Loops & Logic Practice Drill" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700" />
          </div>

          <div>
            <label class="block font-bold mb-1">Target Module</label>
            <select v-model="newPracticeForm.module" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700">
              <option value="Module 1: Intro">Module 1: Intro</option>
              <option value="Module 2: Flow Control">Module 2: Flow Control</option>
              <option value="Module 3: Modular Code">Module 3: Modular Code</option>
            </select>
          </div>

          <div>
            <label class="block font-bold mb-1">Question Count</label>
            <input v-model="newPracticeForm.questions_count" type="number" min="1" max="50" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700" />
          </div>

          <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-[11px] text-emerald-800 dark:text-emerald-300">
            ✓ Ungraded practice mode automatically enabled. Students can attempt unlimited times.
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t">
          <button @click="showCreateModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">Cancel</button>
          <button @click="createPracticeSet" class="px-5 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold shadow">Save & Publish</button>
        </div>
      </div>
    </div>

    <!-- ⚙️ PRACTICE QUIZ SETTINGS MODAL -->
    <div v-if="showSettingsModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-slate-200 dark:border-gray-700">
        <h3 class="text-base font-extrabold text-slate-800 dark:text-white">⚙️ PRACTICE QUIZ SETTINGS</h3>
        <div class="space-y-3 text-xs">
          <label class="flex items-center gap-2 cursor-pointer p-2.5 rounded-xl bg-slate-50 dark:bg-gray-700/50">
            <input type="checkbox" checked class="rounded text-emerald-600" />
            <span class="font-bold">☑️ Unlimited Attempts Allowed (គ្មានកំណត់)</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer p-2.5 rounded-xl bg-slate-50 dark:bg-gray-700/50">
            <input type="checkbox" checked class="rounded text-emerald-600" />
            <span class="font-bold">☑️ Show Answer & Explanation Immediately</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer p-2.5 rounded-xl bg-slate-50 dark:bg-gray-700/50">
            <input type="checkbox" checked class="rounded text-emerald-600" />
            <span class="font-bold">☑️ Shuffle questions & options per drill</span>
          </label>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t">
          <button @click="showSettingsModal = false" class="px-5 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold shadow">Done</button>
        </div>
      </div>
    </div>
  </div>
</template>
