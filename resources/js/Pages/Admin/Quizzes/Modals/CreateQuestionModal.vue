<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { Codemirror } from 'vue-codemirror'
import { cpp } from '@codemirror/lang-cpp'
import { oneDark } from '@codemirror/theme-one-dark'

const cmExtensions = [cpp(), oneDark]

const props = defineProps<{
  show: boolean
  allCourses: any[]
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', newQ: any): void
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

const newQuestion = ref({
  question: '',
  type: 'mcq',
  difficulty: 'easy',
  marks: 2,
  course: 'C Programming Basics',
  module: 'Module 1: Introduction to C',
  chapter: 'Chapter 1.1: History of C',
  correct_answer: 'Option A',
  explanation: '',
  sample_solution: '',
  rubric: '',
  word_limit: '100 - 300 words',
  options: ['Option A', 'Option B', 'Option C', 'Option D'],
  pairs: [
    { left: 'int', right: '4 Bytes' },
    { left: 'char', right: '1 Byte' },
  ],
})

watch(() => courseList.value, (list) => {
  if (list && list.length > 0 && !list.includes(newQuestion.value.course)) {
    newQuestion.value.course = list[0]
  }
}, { immediate: true })

watch(() => newQuestion.value.type, (newType) => {
  if (newType === 'mcq') {
    if (!newQuestion.value.options || newQuestion.value.options.length < 2) {
      newQuestion.value.options = ['Option A', 'Option B', 'Option C', 'Option D']
    }
    if (!newQuestion.value.options.includes(newQuestion.value.correct_answer)) {
      newQuestion.value.correct_answer = newQuestion.value.options[0]
    }
  } else if (newType === 'true_false') {
    if (newQuestion.value.correct_answer !== 'True' && newQuestion.value.correct_answer !== 'False') {
      newQuestion.value.correct_answer = 'True'
    }
  } else if (newType === 'essay') {
    newQuestion.value.correct_answer = 'Instructor Grading Required'
    if (!newQuestion.value.word_limit) newQuestion.value.word_limit = '100 - 300 words'
  } else if (newType === 'matching') {
    if (!newQuestion.value.pairs || newQuestion.value.pairs.length === 0) {
      newQuestion.value.pairs = [
        { left: 'int', right: '4 Bytes' },
        { left: 'char', right: '1 Byte' },
      ]
    }
    newQuestion.value.correct_answer = 'Matching Pairs Key'
  } else if (newType === 'fill_blank') {
    if (!newQuestion.value.correct_answer || newQuestion.value.correct_answer.startsWith('Option')) {
      newQuestion.value.correct_answer = ''
    }
  } else if (newType === 'coding') {
    newQuestion.value.correct_answer = 'Automated Test Suite / Sample Solution'
    if (!newQuestion.value.sample_solution) {
      newQuestion.value.sample_solution = '#include <stdio.h>\n\nint main() {\n    // Write C code solution\n    return 0;\n}'
    }
  }
})

function addOption() {
  const nextChar = String.fromCharCode(65 + newQuestion.value.options.length)
  newQuestion.value.options.push(`Option ${nextChar}`)
}

function removeOption(index: number) {
  if (newQuestion.value.options.length > 2) {
    newQuestion.value.options.splice(index, 1)
  }
}

function addPair() {
  newQuestion.value.pairs.push({ left: '', right: '' })
}

function removePair(index: number) {
  if (newQuestion.value.pairs.length > 1) {
    newQuestion.value.pairs.splice(index, 1)
  }
}

function submitForm() {
  if (!newQuestion.value.question.trim()) {
    alert('Please enter question prompt.')
    return
  }
  if (newQuestion.value.type === 'fill_blank' && !newQuestion.value.correct_answer.trim()) {
    alert('Please provide the correct blank answer key.')
    return
  }
  emit('save', JSON.parse(JSON.stringify(newQuestion.value)))
  newQuestion.value = {
    question: '',
    type: 'mcq',
    difficulty: 'easy',
    marks: 2,
    course: courseList.value[0] || 'C Programming Basics',
    module: 'Module 1: Introduction to C',
    chapter: 'Chapter 1.1: History of C',
    correct_answer: 'Option A',
    explanation: '',
    sample_solution: '',
    rubric: '',
    word_limit: '100 - 300 words',
    options: ['Option A', 'Option B', 'Option C', 'Option D'],
    pairs: [
      { left: 'int', right: '4 Bytes' },
      { left: 'char', right: '1 Byte' },
    ],
  }
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-[#0e1322] border border-purple-500/30 rounded-2xl max-w-2xl w-full flex flex-col max-h-[90vh] shadow-2xl overflow-hidden animate-fadeIn">
      <!-- Fixed Modal Header -->
      <div class="flex items-center justify-between border-b border-slate-700/60 p-4 sm:p-5 shrink-0 bg-[#0e1322]">
        <h3 class="font-bold text-sm text-white flex items-center gap-2.5 tracking-wide uppercase">
          <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>CREATE NEW QUESTION</span>
        </h3>
        <button @click="emit('close')" class="text-slate-400 hover:text-white font-bold text-base transition-colors cursor-pointer p-1">✕</button>
      </div>

      <!-- Scrollable Form Body -->
      <div class="p-5 sm:p-6 overflow-y-auto space-y-4 text-xs flex-1">
        <!-- Question Type Selection -->
        <div>
          <label class="block font-semibold text-slate-300 mb-2">Select Question Type:</label>
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
            <!-- 1. MCQ -->
            <button
              @click="newQuestion.type = 'mcq'"
              :class="newQuestion.type === 'mcq' ? 'bg-[#8b5cf6] text-white font-bold shadow-md shadow-purple-600/30 ring-2 ring-purple-400 rounded-2xl' : 'bg-[#121827] text-slate-400 hover:text-slate-200 border border-slate-700/60 rounded-2xl'"
              class="py-2.5 text-center transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
              <svg class="w-4 h-4 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>Multiple Choice</span>
            </button>

            <!-- 2. True/False -->
            <button
              @click="newQuestion.type = 'true_false'"
              :class="newQuestion.type === 'true_false' ? 'bg-[#8b5cf6] text-white font-bold shadow-md shadow-purple-600/30 ring-2 ring-purple-400 rounded-2xl' : 'bg-[#121827] text-slate-400 hover:text-slate-200 border border-slate-700/60 rounded-2xl'"
              class="py-2.5 text-center transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
              <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
              </svg>
              <span>True/False</span>
            </button>

            <!-- 3. Essay -->
            <button
              @click="newQuestion.type = 'essay'"
              :class="newQuestion.type === 'essay' ? 'bg-[#8b5cf6] text-white font-bold shadow-md shadow-purple-600/30 ring-2 ring-purple-400 rounded-2xl' : 'bg-[#121827] text-slate-400 hover:text-slate-200 border border-slate-700/60 rounded-2xl'"
              class="py-2.5 text-center transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
              <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              <span>Essay</span>
            </button>

            <!-- 4. Matching -->
            <button
              @click="newQuestion.type = 'matching'"
              :class="newQuestion.type === 'matching' ? 'bg-[#8b5cf6] text-white font-bold shadow-md shadow-purple-600/30 ring-2 ring-purple-400 rounded-2xl' : 'bg-[#121827] text-slate-400 hover:text-slate-200 border border-slate-700/60 rounded-2xl'"
              class="py-2.5 text-center transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
              <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
              </svg>
              <span>Matching</span>
            </button>

            <!-- 5. Fill Blank -->
            <button
              @click="newQuestion.type = 'fill_blank'"
              :class="newQuestion.type === 'fill_blank' ? 'bg-[#8b5cf6] text-white font-bold shadow-md shadow-purple-600/30 ring-2 ring-purple-400 rounded-2xl' : 'bg-[#121827] text-slate-400 hover:text-slate-200 border border-slate-700/60 rounded-2xl'"
              class="py-2.5 text-center transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
              <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
              </svg>
              <span>Fill Blank</span>
            </button>

            <!-- 6. Coding -->
            <button
              @click="newQuestion.type = 'coding'"
              :class="newQuestion.type === 'coding' ? 'bg-[#8b5cf6] text-white font-bold shadow-md shadow-purple-600/30 ring-2 ring-purple-400 rounded-2xl' : 'bg-[#121827] text-slate-400 hover:text-slate-200 border border-slate-700/60 rounded-2xl'"
              class="py-2.5 text-center transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
              <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
              </svg>
              <span>Coding</span>
            </button>
          </div>
        </div>

        <!-- Question Prompt -->
        <div>
          <label class="block font-semibold text-slate-300 mb-1.5">Question Prompt / Statement:</label>
          <textarea v-model="newQuestion.question" rows="3" class="w-full bg-[#121827] border border-purple-500/50 rounded-2xl p-3 text-xs text-white focus:border-purple-500 focus:ring-1 focus:ring-purple-500 focus:outline-none transition-all placeholder:text-slate-500" placeholder="Enter full question text or problem statement..."></textarea>
        </div>

        <!-- Course, Difficulty, Marks -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
          <!-- Course -->
          <div class="md:col-span-5">
            <label class="block font-semibold text-slate-300 mb-1.5">Course:</label>
            <div class="relative">
              <select v-model="newQuestion.course" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 pr-8 text-xs text-white focus:border-purple-500 focus:ring-1 focus:ring-purple-500 focus:outline-none transition-all cursor-pointer">
                <option v-for="cName in courseList" :key="cName" :value="cName" class="bg-[#121827] text-white py-1">
                  {{ cName }}
                </option>
              </select>
            </div>
          </div>

          <!-- Difficulty Custom Visual Selector with SVG Icons -->
          <div class="md:col-span-5">
            <label class="block font-semibold text-slate-300 mb-1.5">Difficulty:</label>
            <div class="grid grid-cols-3 gap-1 bg-[#121827] p-1 rounded-xl border border-slate-700/80">
              <!-- Easy -->
              <button
                type="button"
                @click="newQuestion.difficulty = 'easy'"
                :class="newQuestion.difficulty === 'easy' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/50 shadow-sm font-bold ring-1 ring-emerald-500/30' : 'text-slate-400 hover:text-slate-200 border-transparent'"
                class="py-1.5 px-2 rounded-lg border text-[11px] flex items-center justify-center gap-1.5 transition-all cursor-pointer"
              >
                <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 21V10m0 0C10.5 7.5 7.5 6 4 6c0 5 3.5 8 8 4zm0 0c1.5-2.5 4.5-4 8-4 0 5-3.5 8-8 4z" />
                </svg>
                <span>Easy</span>
              </button>

              <!-- Medium -->
              <button
                type="button"
                @click="newQuestion.difficulty = 'medium'"
                :class="newQuestion.difficulty === 'medium' ? 'bg-amber-500/20 text-amber-300 border-amber-500/50 shadow-sm font-bold ring-1 ring-amber-500/30' : 'text-slate-400 hover:text-slate-200 border-transparent'"
                class="py-1.5 px-2 rounded-lg border text-[11px] flex items-center justify-center gap-1.5 transition-all cursor-pointer"
              >
                <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                </svg>
                <span>Medium</span>
              </button>

              <!-- Hard -->
              <button
                type="button"
                @click="newQuestion.difficulty = 'hard'"
                :class="newQuestion.difficulty === 'hard' ? 'bg-rose-500/20 text-rose-300 border-rose-500/50 shadow-sm font-bold ring-1 ring-rose-500/30' : 'text-slate-400 hover:text-slate-200 border-transparent'"
                class="py-1.5 px-2 rounded-lg border text-[11px] flex items-center justify-center gap-1.5 transition-all cursor-pointer"
              >
                <svg class="w-3.5 h-3.5 text-rose-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 3.75 3.75 0 00-3.99 3.018A3.75 3.75 0 0012 18z" />
                </svg>
                <span>Hard</span>
              </button>
            </div>
          </div>

          <!-- Marks -->
          <div class="md:col-span-2">
            <label class="block font-semibold text-slate-300 mb-1.5">Marks:</label>
            <input v-model="newQuestion.marks" type="number" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-white focus:border-purple-500 focus:outline-none transition-all" />
          </div>
        </div>

        <!-- Dynamic Form Fields per Type -->

        <!-- 1. MCQ OPTIONS -->
        <div v-if="newQuestion.type === 'mcq'" class="space-y-2.5 border-t border-slate-700/60 pt-3.5">
          <div class="flex items-center justify-between mb-1">
            <label class="block font-semibold text-slate-300">Answer Options & Correct Choice:</label>
            <button @click="addOption" class="text-purple-400 hover:text-purple-300 font-bold text-xs flex items-center gap-1 transition-colors cursor-pointer">＋ Add Option</button>
          </div>
          <div v-for="(opt, idx) in newQuestion.options" :key="idx" class="flex items-center gap-3">
            <input type="radio" name="mcq_correct" :value="newQuestion.options[idx]" v-model="newQuestion.correct_answer" class="w-4 h-4 text-purple-600 bg-slate-900 border-slate-700 focus:ring-purple-500 cursor-pointer" />
            <span class="font-bold text-slate-400 w-5">{{ String.fromCharCode(65 + idx) }}.</span>
            <input v-model="newQuestion.options[idx]" type="text" :placeholder="`Option ${String.fromCharCode(65 + idx)}`" class="flex-1 bg-[#121827] border border-slate-700/80 rounded-2xl px-3.5 py-2.5 text-xs text-white focus:border-purple-500 focus:outline-none" />
            <button @click="removeOption(idx)" class="text-red-400 hover:text-red-300 px-2 py-1 text-base cursor-pointer">✕</button>
          </div>
        </div>

        <!-- 2. TRUE / FALSE -->
        <div v-else-if="newQuestion.type === 'true_false'" class="space-y-2.5 border-t border-slate-700/60 pt-3.5">
          <label class="block font-semibold text-slate-300 mb-1.5">Select Correct Statement Answer:</label>
          <div class="flex gap-4">
            <label class="flex items-center gap-2.5 cursor-pointer p-3 rounded-2xl bg-[#121827] border border-slate-700/80 flex-1 hover:border-emerald-500/50 transition-all">
              <input type="radio" value="True" v-model="newQuestion.correct_answer" class="text-emerald-500 w-4 h-4 cursor-pointer" />
              <span class="font-bold text-emerald-400">✓ True</span>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer p-3 rounded-2xl bg-[#121827] border border-slate-700/80 flex-1 hover:border-red-500/50 transition-all">
              <input type="radio" value="False" v-model="newQuestion.correct_answer" class="text-red-500 w-4 h-4 cursor-pointer" />
              <span class="font-bold text-red-400">✗ False</span>
            </label>
          </div>
        </div>

        <!-- 3. ESSAY -->
        <div v-else-if="newQuestion.type === 'essay'" class="space-y-3 border-t border-slate-700/60 pt-3.5">
          <div>
            <label class="block font-semibold text-slate-300 mb-1.5">Word Limit Guidance:</label>
            <input v-model="newQuestion.word_limit" type="text" placeholder="100 - 300 words" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-white" />
          </div>
          <div>
            <label class="block font-semibold text-slate-300 mb-1.5">Grading Rubric Criteria:</label>
            <textarea v-model="newQuestion.rubric" rows="2" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-white" placeholder="e.g. Technical accuracy (4pts), Structure (3pts), Examples (3pts)"></textarea>
          </div>
        </div>

        <!-- 4. MATCHING -->
        <div v-else-if="newQuestion.type === 'matching'" class="space-y-2.5 border-t border-slate-700/60 pt-3.5">
          <div class="flex items-center justify-between mb-1">
            <label class="block font-semibold text-slate-300">Matching Pairs (Item & Correct Match):</label>
            <button @click="addPair" class="text-purple-400 hover:text-purple-300 font-bold text-xs flex items-center gap-1 cursor-pointer">＋ Add Pair</button>
          </div>
          <div v-for="(p, pIdx) in newQuestion.pairs" :key="pIdx" class="grid grid-cols-2 gap-2.5 items-center">
            <input v-model="p.left" type="text" placeholder="Left side item" class="bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-white" />
            <div class="flex items-center gap-1.5">
              <input v-model="p.right" type="text" placeholder="Right side matching answer" class="flex-1 bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-white" />
              <button @click="removePair(pIdx)" class="text-red-400 hover:text-red-300 px-1 text-base cursor-pointer">✕</button>
            </div>
          </div>
        </div>

        <!-- 5. FILL BLANK -->
        <div v-else-if="newQuestion.type === 'fill_blank'" class="space-y-2.5 border-t border-slate-700/60 pt-3.5">
          <label class="block font-semibold text-slate-300 mb-1.5">Correct Blank Answer Key:</label>
          <input v-model="newQuestion.correct_answer" type="text" placeholder="Target keyword or number..." class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-white font-mono" />
        </div>

        <!-- 6. CODING (CodeMirror Editor) -->
        <div v-else-if="newQuestion.type === 'coding'" class="space-y-2.5 border-t border-slate-700/60 pt-3.5">
          <label class="block font-semibold text-slate-300 mb-1.5">Sample Solution Code:</label>
          <div class="rounded-xl border border-slate-700/80 bg-[#0a0d16] overflow-hidden shadow-inner">
            <div class="flex items-center justify-between px-3 py-1.5 bg-slate-900/90 border-b border-slate-800 text-[10px] font-mono text-purple-300 font-bold select-none">
              <span class="flex items-center gap-1.5">
                <span>💻</span> C/C++ SOLUTION EDITOR
              </span>
              <span class="text-slate-500">Syntax Highlighting Active</span>
            </div>
            <Codemirror
              v-model="newQuestion.sample_solution"
              placeholder="// Write C/C++ solution code here..."
              :style="{ minHeight: '130px' }"
              :indent-with-tab="true"
              :tab-size="2"
              :extensions="cmExtensions"
              class="text-xs font-mono"
            />
          </div>
        </div>

        <!-- Solution Guide / Explanation -->
        <div class="border-t border-slate-700/60 pt-3.5">
          <label class="block font-semibold text-slate-300 mb-1.5">Explanation / Solution Guide:</label>
          <textarea v-model="newQuestion.explanation" rows="2" class="w-full bg-[#121827] border border-slate-700/80 rounded-2xl p-3 text-xs text-white focus:border-purple-500 focus:ring-1 focus:ring-purple-500 focus:outline-none placeholder:text-slate-500" placeholder="Explanation for students after submission..."></textarea>
        </div>
      </div>

      <!-- Sticky Fixed Modal Footer -->
      <div class="flex justify-end gap-2.5 border-t border-slate-700/60 p-4 sm:px-6 bg-[#0e1322] shrink-0">
        <button @click="emit('close')" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-semibold transition-all cursor-pointer">Cancel</button>
        <button @click="submitForm" class="px-6 py-2.5 bg-[#8b5cf6] hover:bg-purple-500 text-white rounded-2xl text-xs font-bold shadow-lg shadow-purple-600/30 ring-1 ring-purple-400/50 transition-all cursor-pointer active:scale-95">Save & Publish Question</button>
      </div>
    </div>
  </div>
</template>
