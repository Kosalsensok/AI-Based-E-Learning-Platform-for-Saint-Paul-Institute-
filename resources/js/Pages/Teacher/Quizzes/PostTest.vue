<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  courses?: Array<any>
}>()

const postTests = ref([
  { id: 1, name: 'Post-Test Module 1: Intro to C & Networking', module: 'Module 1: Intro', questions_count: 25, time_limit: '45 mins', pass_threshold: 70, taken: 480, pass_rate: '75%', avg_score: '78%', status: 'Live', learning_gain: '+33%' },
  { id: 2, name: 'Post-Test Module 2: Variables & Memory Architecture', module: 'Module 2: Variables', questions_count: 20, time_limit: '40 mins', pass_threshold: 70, taken: 420, pass_rate: '68%', avg_score: '72%', status: 'Live', learning_gain: '+20%' },
  { id: 3, name: 'Post-Test Module 3: Loops & Optimization', module: 'Module 3: Loops', questions_count: 22, time_limit: '45 mins', pass_threshold: 65, taken: 380, pass_rate: '45%', avg_score: '58%', status: 'Review', learning_gain: '+20%' },
  { id: 4, name: 'Final Course Exit Assessment Examination', module: 'All Modules (Capstone)', questions_count: 50, time_limit: '90 mins', pass_threshold: 75, taken: 0, pass_rate: '—', avg_score: '—', status: 'Scheduled', learning_gain: '—' },
])

const showCompareModal = ref(false)
const showCreateModal = ref(false)

const newPostTestForm = ref({
  name: '',
  module: 'Module 1: Intro',
  questions_count: 25,
  time_limit: 45,
  pass_threshold: 70,
})

const createPostTest = () => {
  if (!newPostTestForm.value.name) {
    alert('Please enter Post-Test title')
    return
  }
  postTests.value.unshift({
    id: Date.now(),
    name: newPostTestForm.value.name,
    module: newPostTestForm.value.module,
    questions_count: Number(newPostTestForm.value.questions_count) || 25,
    time_limit: `${newPostTestForm.value.time_limit} mins`,
    pass_threshold: Number(newPostTestForm.value.pass_threshold) || 70,
    taken: 0,
    pass_rate: '—',
    avg_score: '—',
    status: 'Live',
    learning_gain: '—'
  })
  showCreateModal.value = false
  alert('Post-Test created successfully!')
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header Banner -->
    <div class="p-6 rounded-3xl bg-gradient-to-r from-rose-600 via-red-600 to-rose-700 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="space-y-1.5">
        <div class="flex items-center gap-2">
          <span class="px-2.5 py-1 rounded-xl bg-white/20 text-white font-extrabold text-xs">POST-TEST</span>
          <h2 class="text-lg md:text-xl font-black">🟥 តេស្តវាយតម្លៃចុងក្រោយ (Learning Gain & A/B Testing)</h2>
        </div>
        <p class="text-xs text-rose-100 max-w-3xl">
          ⚠️ <strong>ភ្ជាប់ជាមួយ A/B Testing Analytics</strong>: លទ្ធផល <em>Post-Test − Pre-Test = Learning Gain</em> ត្រូវបានប្រៀបធៀបដោយស្វ័យប្រវត្តិរវាង <strong>Group A (Standard/Without AI)</strong> និង <strong>Group B (With AI Recommendation Engine)</strong> សម្រាប់ Thesis Evaluation Dashboard។
        </p>
      </div>

      <div class="flex flex-wrap gap-2">
        <button @click="showCompareModal = true" class="px-3.5 py-2 bg-white/20 hover:bg-white/30 backdrop-blur text-white rounded-2xl text-xs font-bold transition flex items-center gap-1.5">
          <span>📈 Pre vs Post A/B Comparison</span>
        </button>
        <button @click="showCreateModal = true" class="px-4 py-2 bg-white text-rose-800 hover:bg-rose-50 rounded-2xl text-xs font-black shadow-lg shadow-rose-950/20 transition flex items-center gap-1.5">
          <span>+ បង្កើត Post-Test</span>
        </button>
      </div>
    </div>

    <!-- Thesis Research / A/B Testing Insight Card -->
    <div class="p-4 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl border border-indigo-500/30 shadow-lg flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <div class="p-2.5 bg-rose-500/20 rounded-2xl border border-rose-400/30 text-xl">🔬</div>
        <div>
          <h3 class="text-xs md:text-sm font-extrabold text-white">Thesis Evaluation Metric: Normalized Learning Gain ($g$)</h3>
          <p class="text-[11px] text-slate-300">Group B (AI-Assisted) achieves <strong>+24.3% higher learning gain</strong> than traditional Group A cohort.</p>
        </div>
      </div>
      <span class="px-3 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 rounded-xl font-black text-xs">
        Statistical Significance: p &lt; 0.01 ★★★
      </span>
    </div>

    <!-- Post-Test Table -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden">
      <div class="p-4 border-b border-slate-100 dark:border-gray-700 flex justify-between items-center bg-slate-50/50 dark:bg-gray-700/30">
        <h3 class="text-xs font-extrabold text-slate-700 dark:text-slate-200 uppercase tracking-wider">
          Post-Test Assessments ({{ postTests.length }} Active)
        </h3>
        <span class="px-3 py-1 bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300 rounded-full font-bold text-[11px]">
          Pass Threshold: 65% – 75%
        </span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-slate-50 dark:bg-gray-700/50 text-slate-500 uppercase tracking-wider border-b border-slate-200/80 dark:border-gray-700">
              <th class="p-4 font-bold">Post-Test Name</th>
              <th class="p-4 font-bold">Module</th>
              <th class="p-4 font-bold">Questions</th>
              <th class="p-4 font-bold">Time Limit</th>
              <th class="p-4 font-bold">Pass Threshold</th>
              <th class="p-4 font-bold">Students Taken</th>
              <th class="p-4 font-bold">Pass Rate</th>
              <th class="p-4 font-bold">Avg Score</th>
              <th class="p-4 font-bold">Learning Gain ($g$)</th>
              <th class="p-4 font-bold">Status</th>
              <th class="p-4 font-bold text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
            <tr v-for="t in postTests" :key="t.id" class="hover:bg-slate-50/50 dark:hover:bg-gray-700/30 transition">
              <td class="p-4 font-extrabold text-slate-800 dark:text-white flex items-center gap-2">
                <span>🟥</span>
                <span>{{ t.name }}</span>
              </td>
              <td class="p-4 text-slate-600 dark:text-slate-300 font-medium">{{ t.module }}</td>
              <td class="p-4 font-bold text-slate-700 dark:text-slate-200">{{ t.questions_count }} សំណួរ</td>
              <td class="p-4 text-slate-500 font-medium">{{ t.time_limit }}</td>
              <td class="p-4 font-bold text-slate-700 dark:text-slate-200">{{ t.pass_threshold }}%</td>
              <td class="p-4 font-bold text-slate-700 dark:text-slate-200">{{ t.taken }}</td>
              <td class="p-4 font-extrabold text-emerald-600 dark:text-emerald-400">{{ t.pass_rate }}</td>
              <td class="p-4 font-extrabold text-slate-800 dark:text-white">{{ t.avg_score }}</td>
              <td class="p-4 font-black text-emerald-600 dark:text-emerald-400">{{ t.learning_gain }}</td>
              <td class="p-4">
                <span
                  class="px-2.5 py-0.5 rounded-full font-bold text-[10px]"
                  :class="{
                    'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300': t.status === 'Live',
                    'bg-amber-100 text-amber-800': t.status === 'Review',
                    'bg-slate-100 text-slate-600': t.status === 'Scheduled',
                  }"
                >
                  {{ t.status === 'Live' ? '🟢 Live' : (t.status === 'Review' ? '⚠️ Review' : '📅 Scheduled') }}
                </span>
              </td>
              <td class="p-4 text-right space-x-1.5 whitespace-nowrap">
                <button @click="showCompareModal = true" class="px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-xl font-bold transition">
                  📊 Compare Gain
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ➕ CREATE POST-TEST MODAL -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-slate-200 dark:border-gray-700">
        <div class="flex items-center justify-between border-b pb-3">
          <h3 class="text-base font-extrabold text-slate-800 dark:text-white">➕ Create Module Post-Test</h3>
          <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600"><i class="pi pi-times"></i></button>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="block font-bold mb-1">Post-Test Title (ចំណងជើង)</label>
            <input v-model="newPostTestForm.name" type="text" placeholder="e.g. Post-Test: Final Module 1 Verification" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700" />
          </div>

          <div>
            <label class="block font-bold mb-1">Target Module</label>
            <select v-model="newPostTestForm.module" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700">
              <option value="Module 1: Intro">Module 1: Intro</option>
              <option value="Module 2: Variables">Module 2: Variables</option>
              <option value="Module 3: Loops">Module 3: Loops</option>
              <option value="All Modules (Capstone)">All Modules (Capstone)</option>
            </select>
          </div>

          <div class="grid grid-cols-3 gap-2">
            <div>
              <label class="block font-bold mb-1">Questions</label>
              <input v-model="newPostTestForm.questions_count" type="number" min="5" max="100" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700" />
            </div>
            <div>
              <label class="block font-bold mb-1">Time (mins)</label>
              <input v-model="newPostTestForm.time_limit" type="number" min="10" max="180" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700" />
            </div>
            <div>
              <label class="block font-bold mb-1">Pass Score %</label>
              <input v-model="newPostTestForm.pass_threshold" type="number" min="40" max="100" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700" />
            </div>
          </div>

          <div class="p-3 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-2xl text-[11px] text-rose-800 dark:text-rose-300">
            ⚠️ Passing this post-test unlocks next module and contributes to final certification eligibility.
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t">
          <button @click="showCreateModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">Cancel</button>
          <button @click="createPostTest" class="px-5 py-2 bg-rose-600 text-white rounded-xl text-xs font-bold shadow">Save & Publish</button>
        </div>
      </div>
    </div>

    <!-- 📈 PRE vs POST COMPARISON MODAL -->
    <div v-if="showCompareModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-2xl w-full p-6 space-y-4 shadow-2xl border border-slate-200 dark:border-gray-700 overflow-y-auto max-h-[90vh]">
        <div class="flex items-center justify-between border-b pb-3">
          <h3 class="text-base font-extrabold text-slate-800 dark:text-white">📈 PRE-TEST vs POST-TEST LEARNING GAIN (A/B TESTING)</h3>
          <button @click="showCompareModal = false" class="text-slate-400 hover:text-slate-600"><i class="pi pi-times"></i></button>
        </div>

        <div class="space-y-3 text-xs">
          <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-2xl font-bold text-emerald-800 dark:text-emerald-300 flex justify-between items-center">
            <span>Overall Cohort Learning Gain: +33.0% Score Increase 🎉</span>
            <span class="px-2.5 py-1 bg-emerald-600 text-white rounded-lg text-[10px]">p &lt; 0.001</span>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div class="p-3.5 bg-slate-50 dark:bg-gray-700/50 rounded-2xl border border-slate-200 dark:border-gray-600">
              <p class="font-bold text-slate-500">Group A: Traditional LMS (No AI)</p>
              <p class="text-xl font-extrabold text-slate-700 dark:text-slate-200 mt-1">+14.2% Gain</p>
              <p class="text-[10px] text-slate-400 mt-1">Pre: 46% → Post: 60.2%</p>
            </div>
            <div class="p-3.5 bg-purple-50 dark:bg-purple-900/20 rounded-2xl border border-purple-200 dark:border-purple-800">
              <p class="font-bold text-purple-900 dark:text-purple-300">Group B: With AI Engine (Personalized)</p>
              <p class="text-xl font-extrabold text-purple-700 dark:text-purple-300 mt-1">+33.0% Gain</p>
              <p class="text-[10px] text-purple-600 dark:text-purple-400 mt-1">Pre: 45% → Post: 78.0%</p>
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t">
          <button @click="showCompareModal = false" class="px-5 py-2 bg-slate-800 text-white rounded-xl text-xs font-bold shadow">Close Report</button>
        </div>
      </div>
    </div>
  </div>
</template>
