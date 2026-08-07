<script setup lang="ts">
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps<{
  courses: Array<any>
  quizzes: Array<any>
}>()

const searchQuery = ref('')
const typeFilter = ref('All')

const sampleQuizzes = ref([
  {
    id: 1,
    title: 'Pre-Test Module 1: Intro to C',
    type: 'pre_test',
    typeName: 'Pre-Test',
    badgeColor: 'blue',
    course: 'C Programming Basics',
    qs_count: 20,
    time_limit: '20m',
    passing_score: '—',
    status: 'Live',
  },
  {
    id: 2,
    title: 'Variables Practice Drill',
    type: 'practice',
    typeName: 'Practice Quiz',
    badgeColor: 'green',
    course: 'C Programming Basics',
    qs_count: 15,
    time_limit: '15m',
    passing_score: '60%',
    status: 'Live',
  },
  {
    id: 3,
    title: 'Post-Test Module 1: Assessment',
    type: 'post_test',
    typeName: 'Post-Test',
    badgeColor: 'red',
    course: 'C Programming Basics',
    qs_count: 25,
    time_limit: '30m',
    passing_score: '70%',
    status: 'Live',
  },
  {
    id: 4,
    title: 'Pre-Test Module 2: Variables',
    type: 'pre_test',
    typeName: 'Pre-Test',
    badgeColor: 'blue',
    course: 'C Programming Basics',
    qs_count: 15,
    time_limit: '15m',
    passing_score: '—',
    status: 'Live',
  },
  {
    id: 5,
    title: 'Loops Practice Drill',
    type: 'practice',
    typeName: 'Practice Quiz',
    badgeColor: 'green',
    course: 'C Programming Basics',
    qs_count: 20,
    time_limit: '20m',
    passing_score: '60%',
    status: 'Draft',
  }
])

// 4-STEP WIZARD CREATE QUIZ MODAL
const showCreateWizard = ref(false)
const currentStep = ref(1)

const quizForm = useForm({
  title: '',
  type: 'post_test',
  course_id: null,
  module_id: null,
  semester: 'Semester 2 · 2024-2025',
  description: '',
  instructions: '',
  time_limit_minutes: 30,
  max_attempts: 1,
  passing_score: 70,
  randomize_questions: true,
  randomize_options: true,
  tab_switch_warning: true,
  disable_copy_paste: true,
  unlock_next_module: true,
})

const nextStep = () => {
  if (currentStep.value < 4) currentStep.value++
}

const prevStep = () => {
  if (currentStep.value > 1) currentStep.value--
}

const finishCreateQuiz = () => {
  sampleQuizzes.value.unshift({
    id: Date.now(),
    title: quizForm.title || 'New Created Quiz',
    type: quizForm.type,
    typeName: quizForm.type === 'pre_test' ? 'Pre-Test' : (quizForm.type === 'practice' ? 'Practice Quiz' : 'Post-Test'),
    badgeColor: quizForm.type === 'pre_test' ? 'blue' : (quizForm.type === 'practice' ? 'green' : 'red'),
    course: 'C Programming Basics',
    qs_count: 18,
    time_limit: `${quizForm.time_limit_minutes}m`,
    passing_score: `${quizForm.passing_score}%`,
    status: 'Live',
  })
  alert('Quiz created and published successfully!')
  showCreateWizard.value = false
  currentStep.value = 1
  quizForm.reset()
}
</script>

<template>
  <div class="space-y-6">
    <!-- Top Metric Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 font-bold flex items-center justify-center text-lg">🧾</div>
        <div>
          <p class="text-xs text-slate-400 font-bold uppercase">Total Quizzes</p>
          <p class="text-xl font-extrabold text-slate-800 dark:text-white">48 Quizzes</p>
        </div>
      </div>
      <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 font-bold flex items-center justify-center text-lg">🟢</div>
        <div>
          <p class="text-xs text-slate-400 font-bold uppercase">Published</p>
          <p class="text-xl font-extrabold text-slate-800 dark:text-white">38</p>
        </div>
      </div>
      <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 font-bold flex items-center justify-center text-lg">🟡</div>
        <div>
          <p class="text-xs text-slate-400 font-bold uppercase">Draft</p>
          <p class="text-xl font-extrabold text-slate-800 dark:text-white">10</p>
        </div>
      </div>
      <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 font-bold flex items-center justify-center text-lg">⏳</div>
        <div>
          <p class="text-xs text-slate-400 font-bold uppercase">Need Grade</p>
          <p class="text-xl font-extrabold text-slate-800 dark:text-white">24 Essays</p>
        </div>
      </div>
    </div>

    <!-- Toolbar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-gray-800 p-4 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm">
      <div class="flex flex-wrap items-center gap-2">
        <div class="relative">
          <i class="pi pi-search absolute left-3 top-3 text-slate-400 text-xs"></i>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search quiz name..."
            class="pl-8 pr-3 py-2 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-xs w-60"
          />
        </div>

        <select v-model="typeFilter" class="p-2 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-xs font-semibold">
          <option value="All">All Types ▼</option>
          <option value="pre_test">🟦 Pre-Test</option>
          <option value="practice">🟩 Practice Quiz</option>
          <option value="post_test">🟥 Post-Test</option>
        </select>
      </div>

      <button
        @click="showCreateWizard = true"
        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-500/20 transition flex items-center gap-1.5"
      >
        <span>➕ Create Quiz</span>
      </button>
    </div>

    <!-- Quiz Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-slate-50 dark:bg-gray-700/50 text-slate-500 uppercase tracking-wider border-b border-slate-200/80 dark:border-gray-700">
            <th class="p-3.5">Quiz Name</th>
            <th class="p-3.5">Type</th>
            <th class="p-3.5">Course</th>
            <th class="p-3.5">Qs Count</th>
            <th class="p-3.5">Time Limit</th>
            <th class="p-3.5">Passing Score</th>
            <th class="p-3.5">Status</th>
            <th class="p-3.5 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
          <tr v-for="q in sampleQuizzes" :key="q.id" class="hover:bg-slate-50/50 dark:hover:bg-gray-700/30 transition">
            <td class="p-3.5 font-bold text-slate-800 dark:text-white">{{ q.title }}</td>
            <td class="p-3.5">
              <span
                class="px-2.5 py-1 rounded-full font-bold text-[10px]"
                :class="{
                  'bg-blue-100 text-blue-800': q.type === 'pre_test',
                  'bg-emerald-100 text-emerald-800': q.type === 'practice',
                  'bg-rose-100 text-rose-800': q.type === 'post_test',
                }"
              >
                {{ q.type === 'pre_test' ? '🟦 Pre-Test' : (q.type === 'practice' ? '🟩 Practice' : '🟥 Post-Test') }}
              </span>
            </td>
            <td class="p-3.5 text-slate-600 dark:text-slate-300 font-medium">{{ q.course }}</td>
            <td class="p-3.5 font-bold text-slate-700 dark:text-slate-200">{{ q.qs_count }} Qs</td>
            <td class="p-3.5 text-slate-500">{{ q.time_limit }}</td>
            <td class="p-3.5 font-bold text-slate-700 dark:text-slate-200">{{ q.passing_score }}</td>
            <td class="p-3.5">
              <span class="px-2 py-0.5 rounded-full font-bold text-[10px]" :class="q.status === 'Live' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'">
                {{ q.status === 'Live' ? '🟢 Live' : '🟡 Draft' }}
              </span>
            </td>
            <td class="p-3.5 text-right space-x-1">
              <button class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-lg font-bold">👁 Preview</button>
              <button class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg font-semibold">✏ Edit</button>
              <button class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg font-bold">📊 Results</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- ➕ CREATE QUIZ 4-STEP WIZARD MODAL -->
    <div v-if="showCreateWizard" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full p-6 space-y-5 shadow-2xl overflow-y-auto max-h-[90vh]">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-gray-700 pb-3">
          <h3 class="text-base font-bold text-slate-800 dark:text-white">➕ CREATE QUIZ — 4-Step Wizard</h3>
          <button @click="showCreateWizard = false" class="text-slate-400 hover:text-slate-600"><i class="pi pi-times"></i></button>
        </div>

        <!-- Wizard Step Indicators Bar -->
        <div class="grid grid-cols-4 gap-2 text-center text-xs font-bold">
          <div :class="['py-2 rounded-xl border', currentStep >= 1 ? 'bg-blue-600 text-white border-blue-600 shadow' : 'bg-slate-100 text-slate-400']">① Basic Info</div>
          <div :class="['py-2 rounded-xl border', currentStep >= 2 ? 'bg-blue-600 text-white border-blue-600 shadow' : 'bg-slate-100 text-slate-400']">② Add Questions</div>
          <div :class="['py-2 rounded-xl border', currentStep >= 3 ? 'bg-blue-600 text-white border-blue-600 shadow' : 'bg-slate-100 text-slate-400']">③ Rules & Timing</div>
          <div :class="['py-2 rounded-xl border', currentStep >= 4 ? 'bg-blue-600 text-white border-blue-600 shadow' : 'bg-slate-100 text-slate-400']">④ Review & Publish</div>
        </div>

        <!-- STEP 1 -->
        <div v-if="currentStep === 1" class="space-y-3 text-xs">
          <p class="font-bold text-slate-700 dark:text-slate-200">STEP 1 — BASIC INFORMATION</p>
          <div>
            <label class="block font-semibold mb-1">Quiz Type (Color coding applies automatically)</label>
            <div class="grid grid-cols-3 gap-2">
              <button @click="quizForm.type = 'pre_test'" :class="['p-3 rounded-xl border font-bold text-xs', quizForm.type === 'pre_test' ? 'bg-blue-100 text-blue-800 border-blue-500 ring-2 ring-blue-500/20' : 'bg-slate-50 text-slate-600']">🟦 Pre-Test</button>
              <button @click="quizForm.type = 'practice'" :class="['p-3 rounded-xl border font-bold text-xs', quizForm.type === 'practice' ? 'bg-emerald-100 text-emerald-800 border-emerald-500 ring-2 ring-emerald-500/20' : 'bg-slate-50 text-slate-600']">🟩 Practice Quiz</button>
              <button @click="quizForm.type = 'post_test'" :class="['p-3 rounded-xl border font-bold text-xs', quizForm.type === 'post_test' ? 'bg-rose-100 text-rose-800 border-rose-500 ring-2 ring-rose-500/20' : 'bg-slate-50 text-slate-600']">🟥 Post-Test</button>
            </div>
          </div>
          <div>
            <label class="block font-semibold mb-1">Quiz Title</label>
            <input v-model="quizForm.title" type="text" placeholder="Post-Test — Module 1: Introduction to C" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700" />
          </div>
          <div>
            <label class="block font-semibold mb-1">Instructions to Students</label>
            <textarea v-model="quizForm.instructions" rows="2" placeholder="Read each question carefully. You have 30 minutes to finish." class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700"></textarea>
          </div>
        </div>

        <!-- STEP 2 -->
        <div v-if="currentStep === 2" class="space-y-3 text-xs">
          <p class="font-bold text-slate-700 dark:text-slate-200">STEP 2 — ADD QUESTIONS</p>
          <div class="p-3 bg-blue-50 dark:bg-gray-700/50 rounded-xl flex items-center justify-between font-bold text-blue-800 dark:text-blue-300">
            <span>Selected: 18 / 25 questions</span>
            <span>Total Marks: 42 / 50</span>
          </div>
          <div class="flex gap-2">
            <button class="px-3 py-1.5 bg-blue-600 text-white rounded-lg font-bold">+ From Question Bank</button>
            <button class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg font-semibold">🎲 Auto-Select by Difficulty</button>
          </div>
        </div>

        <!-- STEP 3 -->
        <div v-if="currentStep === 3" class="space-y-3 text-xs">
          <p class="font-bold text-slate-700 dark:text-slate-200">STEP 3 — RULES & TIMING</p>
          <div class="grid grid-cols-3 gap-3">
            <div>
              <label class="block font-semibold mb-1">Time Limit (Minutes)</label>
              <input v-model="quizForm.time_limit_minutes" type="number" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700" />
            </div>
            <div>
              <label class="block font-semibold mb-1">Passing Score (%)</label>
              <input v-model="quizForm.passing_score" type="number" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700" />
            </div>
            <div>
              <label class="block font-semibold mb-1">Max Attempts</label>
              <input v-model="quizForm.max_attempts" type="number" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700" />
            </div>
          </div>
          <div class="p-3 bg-slate-50 dark:bg-gray-700/50 rounded-xl space-y-2">
            <p class="font-bold text-[11px] text-slate-500 uppercase">Security & Controls</p>
            <label class="flex items-center gap-2 cursor-pointer">
              <input v-model="quizForm.tab_switch_warning" type="checkbox" class="rounded text-blue-600" />
              <span>☑️ Tab-switch warning limit (3x)</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input v-model="quizForm.disable_copy_paste" type="checkbox" class="rounded text-blue-600" />
              <span>☑️ Disable copy/paste</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input v-model="quizForm.unlock_next_module" type="checkbox" class="rounded text-blue-600" />
              <span>☑️ Unlock next module if passed</span>
            </label>
          </div>
        </div>

        <!-- STEP 4 -->
        <div v-if="currentStep === 4" class="space-y-3 text-xs">
          <p class="font-bold text-slate-700 dark:text-slate-200">STEP 4 — REVIEW & PUBLISH</p>
          <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 rounded-xl space-y-2">
            <p class="font-extrabold text-sm text-emerald-800 dark:text-emerald-300">Ready to Publish: {{ quizForm.title || 'Post-Test Module 1' }}</p>
            <p class="text-slate-600 dark:text-slate-300">Type: 🟥 Post-Test | Time: {{ quizForm.time_limit_minutes }} mins | Passing Score: {{ quizForm.passing_score }}%</p>
          </div>
        </div>

        <div class="flex justify-between items-center pt-3 border-t border-slate-100 dark:border-gray-700 text-xs">
          <button v-if="currentStep > 1" @click="prevStep" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl font-semibold">← Back</button>
          <div class="ml-auto space-x-2">
            <button v-if="currentStep < 4" @click="nextStep" class="px-5 py-2 bg-blue-600 text-white rounded-xl font-bold shadow">Next →</button>
            <button v-if="currentStep === 4" @click="finishCreateQuiz" class="px-5 py-2 bg-emerald-600 text-white rounded-xl font-bold shadow">🚀 Publish Quiz</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
