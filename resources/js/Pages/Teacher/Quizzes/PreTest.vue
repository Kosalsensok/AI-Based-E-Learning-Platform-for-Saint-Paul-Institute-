<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  courses?: Array<any>
}>()

const preTests = ref([
  { id: 1, name: 'Pre-Test: Baseline Skills & Programming Logic', module: 'Module 1: Intro', questions_count: 20, time_limit: '30 mins', required_before_start: true, taken: 520, avg_score: '45%', gap: 'High', status: 'Active' },
  { id: 2, name: 'Pre-Test: Memory & Variables Concept Check', module: 'Module 2: Variables', questions_count: 15, time_limit: '25 mins', required_before_start: true, taken: 480, avg_score: '52%', gap: 'Medium', status: 'Active' },
  { id: 3, name: 'Pre-Test: Loops & Algorithm Foundations', module: 'Module 3: Loops', questions_count: 18, time_limit: '30 mins', required_before_start: false, taken: 420, avg_score: '38%', gap: 'High', status: 'Active' },
  { id: 4, name: 'Pre-Test: Modular Functions & Recursion', module: 'Module 4: Functions', questions_count: 12, time_limit: '20 mins', required_before_start: false, taken: 0, avg_score: '—', gap: 'None', status: 'Draft' },
])

const showCreateModal = ref(false)
const showGapModal = ref(false)
const selectedPreTest = ref<any>(null)

const newPreTestForm = ref({
  name: '',
  module: 'Module 1: Intro',
  questions_count: 15,
  time_limit: 30,
  required_before_start: true,
})

const openGapAnalysis = (test: any) => {
  selectedPreTest.value = test
  showGapModal.value = true
}

const createPreTest = () => {
  if (!newPreTestForm.value.name) {
    alert('Please enter Pre-Test title')
    return
  }
  preTests.value.unshift({
    id: Date.now(),
    name: newPreTestForm.value.name,
    module: newPreTestForm.value.module,
    questions_count: Number(newPreTestForm.value.questions_count) || 15,
    time_limit: `${newPreTestForm.value.time_limit} mins`,
    required_before_start: newPreTestForm.value.required_before_start,
    taken: 0,
    avg_score: '—',
    gap: 'None',
    status: 'Active'
  })
  showCreateModal.value = false
  alert('Pre-Test created and set as course baseline!')
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header Banner -->
    <div class="p-6 rounded-3xl bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="space-y-1.5">
        <div class="flex items-center gap-2">
          <span class="px-2.5 py-1 rounded-xl bg-white/20 text-white font-extrabold text-xs">PRE-TEST</span>
          <h2 class="text-lg md:text-xl font-black">🟦 តេស្តវាយតម្លៃចំណេះដឹងមុនចូលរៀន (Baseline Assessment)</h2>
        </div>
        <p class="text-xs text-blue-100 max-w-3xl">
          💡 <strong>ភ្ជាប់ជាមួយ AI Recommendation Engine</strong>: លទ្ធផល Pre-Test ត្រូវបានប្រើជា <em>Baseline Score</em> សម្រាប់ AI Recommendation Engine កំណត់ Personalized Learning Path ដំបូងសម្រាប់សិស្សម្នាក់ៗ។ ពិន្ទុនេះមិនប៉ះពាល់ដល់ពិន្ទុបញ្ចប់វគ្គសិក្សាផ្លូវការទេ។
        </p>
      </div>

      <div class="flex flex-wrap gap-2">
        <button @click="openGapAnalysis(preTests[0])" class="px-3.5 py-2 bg-white/20 hover:bg-white/30 backdrop-blur text-white rounded-2xl text-xs font-bold transition flex items-center gap-1">
          <span>📊 Knowledge Gap Analysis</span>
        </button>
        <button @click="showCreateModal = true" class="px-4 py-2 bg-white text-blue-800 hover:bg-blue-50 rounded-2xl text-xs font-black shadow-lg shadow-blue-950/20 transition flex items-center gap-1.5">
          <span>+ បង្កើត Pre-Test</span>
        </button>
      </div>
    </div>

    <!-- Pre-Test Table -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden">
      <div class="p-4 border-b border-slate-100 dark:border-gray-700 flex justify-between items-center bg-slate-50/50 dark:bg-gray-700/30">
        <h3 class="text-xs font-extrabold text-slate-700 dark:text-slate-200 uppercase tracking-wider">
          Pre-Test Assessments ({{ preTests.length }} Active Baselines)
        </h3>
        <span class="px-3 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 rounded-full font-bold text-[11px]">
          🤖 AI Initial Diagnostic Enabled
        </span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-slate-50 dark:bg-gray-700/50 text-slate-500 uppercase tracking-wider border-b border-slate-200/80 dark:border-gray-700">
              <th class="p-4 font-bold">Pre-Test Name</th>
              <th class="p-4 font-bold">Module</th>
              <th class="p-4 font-bold">Questions</th>
              <th class="p-4 font-bold">Time Limit</th>
              <th class="p-4 font-bold">Required Flag</th>
              <th class="p-4 font-bold">Students Taken</th>
              <th class="p-4 font-bold">Baseline Avg</th>
              <th class="p-4 font-bold">Knowledge Gap</th>
              <th class="p-4 font-bold">Status</th>
              <th class="p-4 font-bold text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
            <tr v-for="t in preTests" :key="t.id" class="hover:bg-slate-50/50 dark:hover:bg-gray-700/30 transition">
              <td class="p-4 font-extrabold text-slate-800 dark:text-white flex items-center gap-2">
                <span>🟦</span>
                <span>{{ t.name }}</span>
              </td>
              <td class="p-4 text-slate-600 dark:text-slate-300 font-medium">{{ t.module }}</td>
              <td class="p-4 font-bold text-slate-700 dark:text-slate-200">{{ t.questions_count }} សំណួរ</td>
              <td class="p-4 text-slate-500 font-medium">{{ t.time_limit }}</td>
              <td class="p-4">
                <span
                  class="px-2.5 py-0.5 rounded-lg font-extrabold text-[10px]"
                  :class="t.required_before_start ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300 border border-indigo-200' : 'bg-slate-100 text-slate-600'"
                >
                  {{ t.required_before_start ? '⚡ Required Before Start' : 'Optional Check' }}
                </span>
              </td>
              <td class="p-4 font-bold text-slate-700 dark:text-slate-200">{{ t.taken }}</td>
              <td class="p-4 font-extrabold text-blue-600 dark:text-blue-400">{{ t.avg_score }}</td>
              <td class="p-4">
                <span
                  class="px-2.5 py-0.5 rounded-lg font-bold text-[10px]"
                  :class="{
                    'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300': t.gap === 'High',
                    'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300': t.gap === 'Medium',
                    'bg-slate-100 text-slate-600': t.gap === 'None',
                  }"
                >
                  {{ t.gap === 'High' ? '🔴 High Gap' : (t.gap === 'Medium' ? '🟡 Moderate' : '⚪ Minor') }}
                </span>
              </td>
              <td class="p-4">
                <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px]" :class="t.status === 'Active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-amber-100 text-amber-800'">
                  {{ t.status === 'Active' ? '🟢 Active' : '🟡 Draft' }}
                </span>
              </td>
              <td class="p-4 text-right space-x-1.5 whitespace-nowrap">
                <button @click="openGapAnalysis(t)" class="px-3 py-1.5 bg-purple-50 text-purple-700 hover:bg-purple-100 rounded-xl font-bold transition">
                  📊 Gap Analysis
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ➕ CREATE PRE-TEST MODAL -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-slate-200 dark:border-gray-700">
        <div class="flex items-center justify-between border-b pb-3">
          <h3 class="text-base font-extrabold text-slate-800 dark:text-white">➕ Create Baseline Pre-Test</h3>
          <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600"><i class="pi pi-times"></i></button>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="block font-bold mb-1">Pre-Test Title (ចំណងជើង)</label>
            <input v-model="newPreTestForm.name" type="text" placeholder="e.g. Pre-Test: Fundamental Networking Concepts" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700" />
          </div>

          <div>
            <label class="block font-bold mb-1">Target Module</label>
            <select v-model="newPreTestForm.module" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700">
              <option value="Module 1: Intro">Module 1: Intro</option>
              <option value="Module 2: Flow Control">Module 2: Flow Control</option>
              <option value="Module 3: Modular Code">Module 3: Modular Code</option>
            </select>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-bold mb-1">Question Count</label>
              <input v-model="newPreTestForm.questions_count" type="number" min="5" max="50" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700" />
            </div>
            <div>
              <label class="block font-bold mb-1">Time Limit (mins)</label>
              <input v-model="newPreTestForm.time_limit" type="number" min="10" max="120" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700" />
            </div>
          </div>

          <label class="flex items-center gap-2 cursor-pointer p-2.5 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800">
            <input type="checkbox" v-model="newPreTestForm.required_before_start" class="rounded text-blue-600" />
            <span class="font-bold text-blue-900 dark:text-blue-200">Required before course start (សិស្សត្រូវធ្វើជាកាតព្វកិច្ចមុនរៀន)</span>
          </label>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t">
          <button @click="showCreateModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">Cancel</button>
          <button @click="createPreTest" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold shadow">Create Pre-Test</button>
        </div>
      </div>
    </div>

    <!-- 📊 KNOWLEDGE GAP ANALYSIS MODAL -->
    <div v-if="showGapModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-2xl w-full p-6 space-y-4 shadow-2xl border border-slate-200 dark:border-gray-700 overflow-y-auto max-h-[90vh]">
        <div class="flex items-center justify-between border-b pb-3">
          <h3 class="text-base font-extrabold text-slate-800 dark:text-white">📊 AI KNOWLEDGE GAP ANALYSIS — {{ selectedPreTest?.name }}</h3>
          <button @click="showGapModal = false" class="text-slate-400 hover:text-slate-600"><i class="pi pi-times"></i></button>
        </div>

        <div class="space-y-3 text-xs">
          <div class="p-3.5 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl">
            <p class="font-bold text-blue-900 dark:text-blue-200">AI Recommendation Diagnostic Summary:</p>
            <p class="text-slate-700 dark:text-slate-300 mt-1">
              520 students took this baseline evaluation. Average score is 45%. 62% of students struggled with variable memory allocation and pointer arithmetic. AI has scheduled foundation review materials in their personalized dashboard.
            </p>
          </div>

          <div class="space-y-2">
            <div class="p-3 bg-slate-50 dark:bg-gray-700/50 rounded-xl flex justify-between items-center">
              <span class="font-bold">Topic 1: Data Types & Bits in Memory</span>
              <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold">78% Mastery</span>
            </div>
            <div class="p-3 bg-slate-50 dark:bg-gray-700/50 rounded-xl flex justify-between items-center">
              <span class="font-bold">Topic 2: Pointer Arithmetic & RAM Addressing</span>
              <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-800 font-bold">34% (Needs AI Boost)</span>
            </div>
            <div class="p-3 bg-slate-50 dark:bg-gray-700/50 rounded-xl flex justify-between items-center">
              <span class="font-bold">Topic 3: Conditional Branching</span>
              <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-bold">58% Moderate</span>
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t">
          <button @click="showGapModal = false" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold shadow">Close Analysis</button>
        </div>
      </div>
    </div>
  </div>
</template>
