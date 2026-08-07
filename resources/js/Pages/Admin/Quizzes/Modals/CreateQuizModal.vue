<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import type { QuestionItem } from '../types'

const props = defineProps<{
  show: boolean
  allCourses: any[]
  questionBank: QuestionItem[]
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', newQuiz: any): void
}>()

const courseList = computed(() => {
  if (props.allCourses && props.allCourses.length > 0) {
    return props.allCourses.map(c => typeof c === 'string' ? c : (c.title || c.name || 'C Programming Basics'))
  }
  return [
    'C Programming Basics',
    'Tourism Management 101',
    'English Literature',
    'Agronomy Basics',
  ]
})

const quizWizardStep = ref(1)
const wizardQuestionSearch = ref('')

const newQuiz = ref({
  title: '',
  type: 'pre_test',
  course: 'C Programming Basics',
  module: 'Module 1: Introduction to C',
  time_limit_minutes: 20,
  passing_score: 70,
  max_attempts: 1,
  selected_questions: [] as string[],
  shuffle_questions: true,
  shuffle_options: true,
  instant_feedback: false,
  lock_browser: false,
  status: 'published',
})

watch(() => courseList.value, (list) => {
  if (list && list.length > 0 && !list.includes(newQuiz.value.course)) {
    newQuiz.value.course = list[0]
  }
}, { immediate: true })

const filteredWizardQuestions = computed(() => {
  return props.questionBank.filter(q => {
    return wizardQuestionSearch.value === '' || 
      q.question.toLowerCase().includes(wizardQuestionSearch.value.toLowerCase()) || 
      q.id.toLowerCase().includes(wizardQuestionSearch.value.toLowerCase())
  })
})

function toggleQuestionSelectInQuiz(qId: string) {
  const index = newQuiz.value.selected_questions.indexOf(qId)
  if (index > -1) {
    newQuiz.value.selected_questions.splice(index, 1)
  } else {
    newQuiz.value.selected_questions.push(qId)
  }
}

function selectAllQuestions() {
  newQuiz.value.selected_questions = props.questionBank.map(q => q.id)
}

function clearAllQuestionSelections() {
  newQuiz.value.selected_questions = []
}

function submitForm() {
  if (!newQuiz.value.title.trim()) {
    alert('Please enter quiz title.')
    return
  }
  emit('save', JSON.parse(JSON.stringify(newQuiz.value)))
  newQuiz.value.title = ''
  newQuiz.value.selected_questions = []
  quizWizardStep.value = 1
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-[#0e1322] border border-purple-500/30 rounded-2xl max-w-3xl w-full p-6 space-y-4 shadow-2xl max-h-[90vh] overflow-y-auto">
      <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
        <h3 class="font-bold text-base text-white flex items-center gap-2">
          <span>➕</span> CREATE NEW QUIZ (Step {{ quizWizardStep }} of 3)
        </h3>
        <button @click="emit('close')" class="text-slate-400 hover:text-white">✕</button>
      </div>

      <!-- Step Indicators -->
      <div class="flex items-center gap-2 border-b border-slate-700/60 pb-3 text-xs">
        <span :class="quizWizardStep >= 1 ? 'text-purple-400 font-bold' : 'text-slate-500'">1. General Info</span>
        <span class="text-slate-600">→</span>
        <span :class="quizWizardStep >= 2 ? 'text-purple-400 font-bold' : 'text-slate-500'">2. Select Questions ({{ newQuiz.selected_questions.length }})</span>
        <span class="text-slate-600">→</span>
        <span :class="quizWizardStep >= 3 ? 'text-purple-400 font-bold' : 'text-slate-500'">3. Quiz Settings</span>
      </div>

      <!-- Wizard Step 1: Basic Info -->
      <div v-if="quizWizardStep === 1" class="space-y-3 text-xs">
        <div>
          <label class="block font-semibold text-slate-300 mb-1">Quiz Title:</label>
          <input v-model="newQuiz.title" type="text" placeholder="e.g. Pre-Test — Module 2: Variables & Data Types" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-white focus:ring-2 focus:ring-purple-500 focus:outline-none" />
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-semibold text-slate-300 mb-1">Quiz Category Type:</label>
            <select v-model="newQuiz.type" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-slate-200 focus:outline-none">
              <option value="pre_test" class="bg-[#121827] text-white">🚀 Pre-Test Evaluation</option>
              <option value="practice" class="bg-[#121827] text-white">✍ Practice Quiz (Unlimited)</option>
              <option value="post_test" class="bg-[#121827] text-white">🏁 Post-Test Assessment</option>
              <option value="standard" class="bg-[#121827] text-white">📋 Standard Quiz</option>
            </select>
          </div>
          <div>
            <label class="block font-semibold text-slate-300 mb-1">Course:</label>
            <select v-model="newQuiz.course" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-slate-200 focus:outline-none">
              <option v-for="cName in courseList" :key="cName" :value="cName" class="bg-[#121827] text-white py-1">{{ cName }}</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-3 gap-3">
          <div>
            <label class="block font-semibold text-slate-300 mb-1">Time Limit (mins):</label>
            <input v-model="newQuiz.time_limit_minutes" type="number" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-white" />
          </div>
          <div>
            <label class="block font-semibold text-slate-300 mb-1">Passing Score (%):</label>
            <input v-model="newQuiz.passing_score" type="number" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-white" />
          </div>
          <div>
            <label class="block font-semibold text-slate-300 mb-1">Max Attempts:</label>
            <input v-model="newQuiz.max_attempts" type="number" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-white" />
          </div>
        </div>
      </div>

      <!-- Wizard Step 2: Select Questions -->
      <div v-if="quizWizardStep === 2" class="space-y-3 text-xs">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
          <input
            v-model="wizardQuestionSearch"
            type="text"
            placeholder="Search questions in bank..."
            class="bg-[#121827] text-slate-200 border border-slate-700/80 rounded-xl px-3 py-1.5 text-xs w-full sm:w-64"
          />
          <div class="flex items-center gap-2">
            <button @click="selectAllQuestions" class="px-2.5 py-1 bg-purple-600/20 text-purple-300 hover:bg-purple-600/30 rounded text-[11px] font-semibold">Select All</button>
            <button @click="clearAllQuestionSelections" class="px-2.5 py-1 bg-slate-700 hover:bg-slate-600 rounded text-[11px] font-semibold text-slate-300">Clear</button>
            <span class="text-purple-400 font-bold ml-2">Total Marks: {{ newQuiz.selected_questions.length * 2 }}</span>
          </div>
        </div>

        <div class="border border-slate-700/80 rounded-xl max-h-60 overflow-y-auto divide-y divide-slate-700/60 bg-[#121827]">
          <div
            v-for="q in filteredWizardQuestions"
            :key="q.id"
            @click="toggleQuestionSelectInQuiz(q.id)"
            class="p-2.5 flex items-center justify-between cursor-pointer hover:bg-slate-800 transition-colors"
            :class="newQuiz.selected_questions.includes(q.id) ? 'bg-purple-950/40 border-l-4 border-purple-500' : ''"
          >
            <div class="flex items-center gap-2.5">
              <input type="checkbox" :checked="newQuiz.selected_questions.includes(q.id)" class="text-purple-600 rounded" />
              <div>
                <p class="font-semibold text-white">{{ q.question }}</p>
                <span class="text-[10px] text-slate-400">{{ q.id }} · {{ q.type_label }} · {{ q.difficulty }}</span>
              </div>
            </div>
            <span class="font-bold text-amber-400">{{ q.marks }} Marks</span>
          </div>
        </div>
      </div>

      <!-- Wizard Step 3: Settings -->
      <div v-if="quizWizardStep === 3" class="space-y-3 text-xs">
        <div class="space-y-2 bg-[#121827] p-3 rounded-xl border border-slate-700/80">
          <h4 class="font-bold text-white mb-2">Quiz Security & Exam Control Options:</h4>
          <label class="flex items-center gap-2 cursor-pointer">
            <input v-model="newQuiz.shuffle_questions" type="checkbox" class="text-purple-600 rounded" />
            <span class="text-slate-200">Shuffle question order for each student</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input v-model="newQuiz.shuffle_options" type="checkbox" class="text-purple-600 rounded" />
            <span class="text-slate-200">Shuffle MCQ answer choices</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input v-model="newQuiz.instant_feedback" type="checkbox" class="text-purple-600 rounded" />
            <span class="text-slate-200">Show instant answer explanation after each question</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input v-model="newQuiz.lock_browser" type="checkbox" class="text-purple-600 rounded" />
            <span class="text-slate-200">Enable Exam Lock (Prevent switching browser tabs during quiz)</span>
          </label>
        </div>

        <div>
          <label class="block font-semibold text-slate-300 mb-1">Status:</label>
          <select v-model="newQuiz.status" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-slate-200">
            <option value="published" class="bg-[#121827] text-emerald-400 font-bold">🟢 Published (Live for Students)</option>
            <option value="draft" class="bg-[#121827] text-amber-400 font-bold">🟡 Draft (Hidden)</option>
            <option value="scheduled" class="bg-[#121827] text-cyan-400 font-bold">📅 Scheduled Exam</option>
          </select>
        </div>
      </div>

      <!-- Wizard Actions -->
      <div class="flex justify-between items-center border-t border-slate-700/60 pt-4">
        <button v-if="quizWizardStep > 1" @click="quizWizardStep--" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-semibold">← Back</button>
        <span v-else></span>
        <button v-if="quizWizardStep < 3" @click="quizWizardStep++" class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl text-xs font-bold">Next Step →</button>
        <button v-else @click="submitForm" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-emerald-600/30">Save & Create Quiz</button>
      </div>
    </div>
  </div>
</template>
