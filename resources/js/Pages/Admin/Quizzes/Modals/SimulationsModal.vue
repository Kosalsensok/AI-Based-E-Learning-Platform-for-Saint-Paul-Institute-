<script setup lang="ts">
import { ref } from 'vue'
import { Codemirror } from 'vue-codemirror'
import { cpp } from '@codemirror/lang-cpp'
import { markdown } from '@codemirror/lang-markdown'
import { oneDark } from '@codemirror/theme-one-dark'
import { autocompletion, acceptCompletion } from '@codemirror/autocomplete'
import { keymap } from '@codemirror/view'
import type { QuestionItem, QuizItem } from '../types'

const customCompletions = (context: any) => {
  const word = context.matchBefore(/[\w\u1780-\u17FF]+/)
  if (!word && !context.explicit) return null

  return {
    from: word ? word.from : context.pos,
    options: [
      // C / C++ Keywords & Functions
      { label: 'printf', type: 'function', detail: 'printf("%d", ...)' },
      { label: 'scanf', type: 'function', detail: 'scanf("%d", &var)' },
      { label: 'include', type: 'keyword', detail: '#include <stdio.h>' },
      { label: 'main', type: 'function', detail: 'int main() { ... }' },
      { label: 'cout', type: 'keyword', detail: 'std::cout << ...' },
      { label: 'cin', type: 'keyword', detail: 'std::cin >> ...' },
      { label: 'return', type: 'keyword', detail: 'return 0;' },
      { label: 'struct', type: 'keyword', detail: 'struct Node { ... }' },
      { label: 'malloc', type: 'function', detail: 'malloc(sizeof(...))' },

      // Khmer Technical & Question Keywords
      { label: 'សំណួរ', type: 'text', detail: 'សំណួរប្រឡង' },
      { label: 'ចម្លើយ', type: 'text', detail: 'ចម្លើយត្រឹមត្រូវ' },
      { label: 'លទ្ធផល', type: 'text', detail: 'លទ្ធផលនៃការប្រតិបត្តិ' },
      { label: 'មេរៀន', type: 'text', detail: 'មេរៀន C Programming' },
      { label: 'និយមន័យ', type: 'text', detail: 'និយមន័យ Function' },
    ],
  }
}

const cmExtensions = [
  markdown(),
  oneDark,
  autocompletion({
    override: [customCompletions],
    activateOnTyping: true,
  }),
  keymap.of([{ key: 'Tab', run: acceptCompletion }]),
]

const props = defineProps<{
  showQuestionPreview: boolean
  showQuestionEdit: boolean
  showAddToQuiz: boolean
  showQuizPreview: boolean
  showQuizEdit: boolean
  showPreTestSim: boolean
  showPreTestResult: boolean
  showPracticeSim: boolean
  showPostTestResult: boolean
  showAiGenerator: boolean
  showAddCategory: boolean
  selectedQuestion: QuestionItem | null
  selectedQuiz: QuizItem | null
  quizzes: QuizItem[]
}>()

const emit = defineEmits<{
  (e: 'closeAll'): void
  (e: 'saveEditedQuestion', q: QuestionItem): void
  (e: 'saveEditedQuiz', qz: QuizItem): void
  (e: 'confirmAddToQuiz', targetQuizId: number): void
  (e: 'confirmAddToQuiz', targetQuizId: number): void
  (e: 'runAiGenerator', config?: any): void
  (e: 'addCategory', name: string): void
}>()

const targetQuizForQuestion = ref<number>(props.quizzes[0]?.id || 1)
const aiIsGenerating = ref(false)
const newCategoryName = ref('')

// AI Question Generator Config Form State
const aiCourse = ref('C Programming Basics')
const aiType = ref('mcq')
const aiDifficulty = ref('medium')
const aiNumQuestions = ref('5')
const aiTopic = ref('Pointers & Memory Allocation')
const aiCustomContext = ref('')

const presets = [
  '+ Include Code Snippets',
  '+ Focus on Errors & Debugging',
  '+ Beginner Friendly',
  '+ Real-world Scenarios',
  '+ Detailed Explanations'
]

function applyPreset(preset: string) {
  if (aiCustomContext.value.includes(preset)) return
  if (aiCustomContext.value.trim() === '') {
    aiCustomContext.value = preset
  } else {
    aiCustomContext.value += ` | ${preset}`
  }
}

// Practice Sim State
const practiceIndex = ref(0)
const practiceChecked = ref(false)
const selectedPracticeAns = ref<string | null>(null)
const practiceQuestionsList = [
  {
    q: 'Which C standard library function dynamically allocates memory?',
    opts: ['malloc()', 'alloc()', 'memget()', 'new()'],
    correct: 'malloc()',
    exp: 'malloc() allocates bytes in heap memory and returns a void pointer to the block.',
  },
  {
    q: 'What is the correct format specifier for printing a double in C?',
    opts: ['%d', '%f', '%lf', '%s'],
    correct: '%lf',
    exp: '%lf specifies a long float (double precision floating point).',
  },
]

function handleAiGen() {
  aiIsGenerating.value = true
  setTimeout(() => {
    aiIsGenerating.value = false
    emit('runAiGenerator', {
      course: aiCourse.value,
      type: aiType.value,
      difficulty: aiDifficulty.value,
      numQuestions: aiNumQuestions.value,
      topic: aiTopic.value,
      context: aiCustomContext.value
    })
  }, 1200)
}

function handleAddCategory() {
  if (!newCategoryName.value.trim()) return
  emit('addCategory', newCategoryName.value)
  newCategoryName.value = ''
}

function addOption(q: QuestionItem) {
  if (!q.options) q.options = []
  const nextChar = String.fromCharCode(65 + q.options.length)
  q.options.push(`New Option ${nextChar}`)
}

function removeOption(q: QuestionItem, index: number) {
  if (!q.options || q.options.length <= 2) return
  const removed = q.options.splice(index, 1)[0]
  if (q.correct_answer === removed && q.options.length > 0) {
    q.correct_answer = q.options[0]
  }
}

function updateOptionVal(q: QuestionItem, index: number, value: string) {
  if (!q.options) return
  const oldVal = q.options[index]
  q.options[index] = value
  if (q.correct_answer === oldVal) {
    q.correct_answer = value
  }
}

function insertCodeBlock(q: QuestionItem | null) {
  if (!q) return
  const snippet = '\n```c\n#include <stdio.h>\n\nint main() {\n    printf("Hello World\\n");\n    return 0;\n}\n```\n'
  q.question = (q.question || '') + snippet
}

function insertInlineCode(q: QuestionItem | null) {
  if (!q) return
  q.question = (q.question || '') + ' `printf("%d", 10 + 20)` '
}
</script>

<template>
  <div>
    <!-- ── QUESTION PREVIEW ── -->
    <div v-if="showQuestionPreview && selectedQuestion" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-[#0e1322] border border-purple-500/30 rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
          <div>
            <span class="text-xs font-mono text-purple-400 font-bold">{{ selectedQuestion.id }}</span>
            <h3 class="font-bold text-sm text-white uppercase tracking-wide">QUESTION PREVIEW</h3>
          </div>
          <button @click="emit('closeAll')" class="text-slate-400 hover:text-white font-bold">✕</button>
        </div>

        <div class="space-y-3 text-xs">
          <div class="bg-[#121827] p-3.5 rounded-2xl border border-slate-700/80">
            <p class="text-sm font-bold text-white leading-relaxed">{{ selectedQuestion.question }}</p>
            <div class="flex flex-wrap items-center gap-2 mt-2.5 text-[11px]">
              <span class="px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-300 font-bold border border-purple-500/30">{{ selectedQuestion.type_label }}</span>
              <span class="px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 font-bold border border-amber-500/30">{{ selectedQuestion.marks }} Marks</span>
              <span class="px-2.5 py-0.5 rounded-full bg-slate-800 text-slate-300 font-semibold capitalize border border-slate-700">{{ selectedQuestion.difficulty }}</span>
            </div>
          </div>

          <!-- Type 1: MCQ Options -->
          <div v-if="selectedQuestion.type === 'mcq' && selectedQuestion.options" class="space-y-1.5">
            <h4 class="font-semibold text-slate-300">Answer Options:</h4>
            <div
              v-for="(opt, idx) in selectedQuestion.options"
              :key="idx"
              class="p-2.5 rounded-xl border text-xs flex items-center justify-between transition-colors"
              :class="opt === selectedQuestion.correct_answer ? 'bg-emerald-950/40 border-emerald-500/80 text-emerald-300 font-bold' : 'bg-[#121827] border-slate-700/80 text-slate-300'"
            >
              <span>{{ String.fromCharCode(65 + idx) }}. {{ opt }}</span>
              <span v-if="opt === selectedQuestion.correct_answer" class="text-xs font-bold text-emerald-400">✓ Correct</span>
            </div>
          </div>

          <!-- Type 2: True/False -->
          <div v-else-if="selectedQuestion.type === 'true_false'" class="space-y-1.5">
            <h4 class="font-semibold text-slate-300">Correct Statement Answer:</h4>
            <div class="p-3 rounded-xl border bg-emerald-950/40 border-emerald-500/80 text-emerald-300 font-bold text-xs flex items-center justify-between">
              <span>Correct Choice: {{ selectedQuestion.correct_answer || 'True' }}</span>
              <span>✓ Verified Key</span>
            </div>
          </div>

          <!-- Type 3: Essay Rubric -->
          <div v-else-if="selectedQuestion.type === 'essay'" class="space-y-2">
            <div v-if="selectedQuestion.word_limit" class="bg-[#121827] p-2.5 rounded-xl border border-slate-700/80">
              <span class="text-slate-400 block text-[11px]">Word Limit:</span>
              <span class="font-bold text-purple-300">{{ selectedQuestion.word_limit }}</span>
            </div>
            <div v-if="selectedQuestion.rubric" class="bg-[#121827] p-2.5 rounded-xl border border-slate-700/80">
              <span class="text-slate-400 block text-[11px]">Grading Rubric:</span>
              <p class="text-slate-200 mt-1">{{ selectedQuestion.rubric }}</p>
            </div>
          </div>

          <!-- Type 4: Matching Pairs -->
          <div v-else-if="selectedQuestion.type === 'matching' && selectedQuestion.pairs" class="space-y-1.5">
            <h4 class="font-semibold text-slate-300">Matching Pairs:</h4>
            <div v-for="(p, pIdx) in selectedQuestion.pairs" :key="pIdx" class="p-2.5 rounded-xl border bg-[#121827] border-slate-700/80 text-xs flex items-center justify-between">
              <span class="font-mono text-purple-300">{{ p.left }}</span>
              <span class="text-slate-500">↔</span>
              <span class="font-mono text-emerald-300">{{ p.right }}</span>
            </div>
          </div>

          <!-- Type 5: Fill Blank Key -->
          <div v-else-if="selectedQuestion.type === 'fill_blank'" class="space-y-1.5">
            <h4 class="font-semibold text-slate-300">Correct Blank Key:</h4>
            <div class="p-3 rounded-xl border bg-[#121827] border-purple-500/50 font-mono text-emerald-400 text-xs">
              Answer: "{{ selectedQuestion.correct_answer }}"
            </div>
          </div>

          <!-- Type 6: Coding Solution -->
          <div v-else-if="selectedQuestion.type === 'coding' && selectedQuestion.sample_solution" class="space-y-1.5">
            <h4 class="font-semibold text-slate-300">Sample Solution Code:</h4>
            <pre class="p-3 rounded-xl border bg-slate-900 border-slate-700/80 font-mono text-emerald-400 text-[11px] overflow-x-auto whitespace-pre-wrap">{{ selectedQuestion.sample_solution }}</pre>
          </div>

          <!-- Solution Explanation -->
          <div v-if="selectedQuestion.explanation" class="bg-purple-950/30 border border-purple-500/40 p-3 rounded-xl">
            <h4 class="font-bold text-purple-300 mb-1">💡 Explanation:</h4>
            <p class="text-slate-300">{{ selectedQuestion.explanation }}</p>
          </div>
        </div>

        <div class="flex justify-end border-t border-slate-700/60 pt-4">
          <button @click="emit('closeAll')" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-semibold">Close</button>
        </div>
      </div>
    </div>

    <!-- ── QUESTION EDIT (100% FIXED ARCHITECTURE: FIXED HEADER/FOOTER + SCROLLABLE BODY) ── -->
    <div v-if="showQuestionEdit && selectedQuestion" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-[#0e1322] border border-purple-500/40 rounded-2xl max-w-2xl w-full flex flex-col max-h-[90vh] shadow-2xl overflow-hidden text-xs my-auto animate-fadeIn">
        
        <!-- 1. FIXED HEADER (Non-scrollable, shrink-0) -->
        <div class="flex items-center justify-between border-b border-slate-700/60 p-4 shrink-0 bg-[#0e1322]">
          <div class="flex items-center gap-2.5">
            <div class="p-2.5 rounded-2xl bg-purple-500/20 text-purple-300 border border-purple-500/30 shrink-0 shadow-inner flex items-center justify-center">
              <svg class="w-4 h-4 text-purple-300" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
              </svg>
            </div>
            <h3 class="font-extrabold text-sm text-white flex items-center gap-2">
              <span>Edit Question</span>
              <span class="text-slate-500 font-normal">—</span>
              <span class="font-mono text-purple-400 font-bold">#{{ selectedQuestion.id }}</span>
            </h3>
          </div>
          <button @click="emit('closeAll')" class="p-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition-colors cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- 2. SCROLLABLE BODY (flex-1 overflow-y-auto custom-scrollbar) -->
        <div class="flex-1 overflow-y-auto p-5 space-y-4">
          <!-- 1. TOP METADATA GRID (4-Column ratio: Type 30% | Course 40% | Difficulty 18% | Marks 12%) -->
          <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 bg-[#121827] p-3.5 rounded-xl border border-slate-700/80">
            <!-- Question Type (Col 1: 4/12) -->
            <div class="sm:col-span-4">
              <label class="block font-extrabold text-slate-300 mb-1">Question Type</label>
              <select v-model="selectedQuestion.type" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-xs font-semibold text-purple-300 focus:ring-2 focus:ring-purple-500 cursor-pointer">
                <option value="mcq" class="bg-[#121827] text-slate-200">Multiple Choice</option>
                <option value="true_false" class="bg-[#121827] text-slate-200">True / False</option>
                <option value="essay" class="bg-[#121827] text-slate-200">Essay</option>
                <option value="matching" class="bg-[#121827] text-slate-200">Matching</option>
                <option value="coding" class="bg-[#121827] text-slate-200">Coding</option>
                <option value="fill_blank" class="bg-[#121827] text-slate-200">Fill Blank</option>
              </select>
            </div>

            <!-- Category / Course (Col 2: 4/12 for full non-truncated course title) -->
            <div class="sm:col-span-4">
              <label class="block font-extrabold text-slate-300 mb-1">Category / Course</label>
              <select v-model="selectedQuestion.course" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-xs font-semibold text-slate-200 focus:ring-2 focus:ring-purple-500 cursor-pointer truncate">
                <option value="C Programming Basics" class="bg-[#121827] text-slate-200">C Programming Basics</option>
                <option value="Computer Science 101" class="bg-[#121827] text-slate-200">Computer Science 101</option>
                <option value="Web Development" class="bg-[#121827] text-slate-200">Web Development</option>
                <option value="Data Structures" class="bg-[#121827] text-slate-200">Data Structures</option>
              </select>
            </div>

            <!-- Difficulty (Col 3: 3/12 - Custom visual pill selector with SVG icons) -->
            <div class="sm:col-span-3">
              <label class="block font-extrabold text-slate-300 mb-1">Difficulty</label>
              <div class="grid grid-cols-3 gap-1 bg-slate-900 p-1 rounded-lg border border-slate-700">
                <button
                  type="button"
                  @click="selectedQuestion.difficulty = 'easy'"
                  :class="selectedQuestion.difficulty === 'easy' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/50 font-bold shadow-sm' : 'text-slate-400 hover:text-slate-200 border-transparent'"
                  class="py-1 px-1 rounded border text-[10px] flex items-center justify-center gap-1 transition-all cursor-pointer"
                >
                  <svg class="w-3 h-3 text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21V10m0 0C10.5 7.5 7.5 6 4 6c0 5 3.5 8 8 4zm0 0c1.5-2.5 4.5-4 8-4 0 5-3.5 8-8 4z" />
                  </svg>
                  <span>Easy</span>
                </button>

                <button
                  type="button"
                  @click="selectedQuestion.difficulty = 'medium'"
                  :class="selectedQuestion.difficulty === 'medium' ? 'bg-amber-500/20 text-amber-300 border-amber-500/50 font-bold shadow-sm' : 'text-slate-400 hover:text-slate-200 border-transparent'"
                  class="py-1 px-1 rounded border text-[10px] flex items-center justify-center gap-1 transition-all cursor-pointer"
                >
                  <svg class="w-3 h-3 text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                  </svg>
                  <span>Med</span>
                </button>

                <button
                  type="button"
                  @click="selectedQuestion.difficulty = 'hard'"
                  :class="selectedQuestion.difficulty === 'hard' ? 'bg-rose-500/20 text-rose-300 border-rose-500/50 font-bold shadow-sm' : 'text-slate-400 hover:text-slate-200 border-transparent'"
                  class="py-1 px-1 rounded border text-[10px] flex items-center justify-center gap-1 transition-all cursor-pointer"
                >
                  <svg class="w-3 h-3 text-rose-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 3.75 3.75 0 00-3.99 3.018A3.75 3.75 0 0012 18z" />
                  </svg>
                  <span>Hard</span>
                </button>
              </div>
            </div>

            <!-- Marks (Col 4: 2/12 - standalone input number) -->
            <div class="sm:col-span-2">
              <label class="block font-extrabold text-slate-300 mb-1">Marks</label>
              <div class="flex items-center gap-1.5 bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5" title="Marks">
                <span class="text-amber-400 font-black text-xs">★</span>
                <input v-model.number="selectedQuestion.marks" type="number" min="1" class="w-full bg-transparent text-xs font-bold text-amber-400 focus:outline-none" />
              </div>
            </div>
          </div>

          <!-- 2. QUESTION PROMPT (CodeMirror C/C++ Syntax Editor) -->
          <div class="space-y-1.5">
            <div class="flex items-center justify-between">
              <label class="block font-extrabold text-slate-300">Question Prompt *</label>
              <div class="flex items-center gap-2">
                <button
                  type="button"
                  @click="insertInlineCode(selectedQuestion)"
                  class="px-2.5 py-1 rounded-lg bg-purple-950/70 hover:bg-purple-900/90 border border-purple-500/40 text-purple-200 text-[11px] font-medium transition-all flex items-center gap-1.5 cursor-pointer shadow-sm active:scale-95"
                  title="Insert inline code snippet"
                >
                  <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5" />
                  </svg>
                  <span>Inline Code</span>
                </button>
                <button
                  type="button"
                  @click="insertCodeBlock(selectedQuestion)"
                  class="px-2.5 py-1 rounded-lg bg-purple-950/70 hover:bg-purple-900/90 border border-purple-500/40 text-purple-200 text-[11px] font-medium transition-all flex items-center gap-1.5 cursor-pointer shadow-sm active:scale-95"
                  title="Insert multi-line C code block"
                >
                  <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 7.5l3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0021 18V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12a2.25 2.25 0 002.25 2.25z" />
                  </svg>
                  <span>Code Block</span>
                </button>
                <span class="text-[11px] text-emerald-300 font-bold bg-emerald-950/50 px-2.5 py-1 rounded-lg border border-emerald-500/40 flex items-center gap-1.5 shadow-sm">
                  <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                  </span>
                  <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>CodeMirror Active</span>
                </span>
              </div>
            </div>

            <!-- CodeMirror Container -->
            <div class="relative rounded-xl border border-slate-700/80 bg-[#0a0d16] focus-within:border-purple-500 focus-within:ring-2 focus-within:ring-purple-500/20 transition-all overflow-hidden">
              <div class="flex items-center justify-between px-3 py-1 bg-slate-900/90 border-b border-slate-800 text-[10px] font-mono text-slate-400 select-none">
                <span class="flex items-center gap-1.5 text-purple-300 font-bold">
                  <span>📝</span> CODEMIRROR MARKDOWN & CODE EDITOR
                </span>
                <span class="text-slate-500">Plain Text + Markdown & Code Highlighting</span>
              </div>
              <Codemirror
                v-model="selectedQuestion.question"
                placeholder="// Type question prompt or C code here..."
                :style="{ minHeight: '110px' }"
                :autofocus="false"
                :indent-with-tab="true"
                :tab-size="2"
                :extensions="cmExtensions"
                class="text-xs font-mono"
              />
            </div>
          </div>

          <!-- 3. ANSWER OPTIONS CONFIGURATION (Dynamic per Type) -->
          <!-- A) Multiple Choice Options (MCQ) -->
          <div v-if="selectedQuestion.type === 'mcq'" class="space-y-3 bg-[#121827] p-3.5 rounded-xl border border-slate-700/80">
            <label class="block font-extrabold text-slate-200">
              Answer Options * <span class="text-[11px] font-normal text-slate-400">(Select radio button for the correct answer)</span>
            </label>

            <!-- Clean Option Rows with Uniform Height & Border Only on Input Box -->
            <div class="space-y-2.5">
              <div
                v-for="(opt, idx) in selectedQuestion.options"
                :key="idx"
                class="flex items-center gap-3"
              >
                <!-- Radio Selection for Correct Answer + Label (A., B., C.) -->
                <label class="flex items-center gap-1.5 cursor-pointer shrink-0 py-1">
                  <input
                    type="radio"
                    :name="'correct-ans-' + selectedQuestion.id"
                    :checked="selectedQuestion.correct_answer === opt"
                    @change="selectedQuestion.correct_answer = opt"
                    class="w-4 h-4 text-emerald-500 focus:ring-emerald-500 accent-emerald-500 cursor-pointer"
                  />
                  <span
                    class="font-extrabold text-xs w-5"
                    :class="selectedQuestion.correct_answer === opt ? 'text-emerald-400 font-black' : 'text-purple-300'"
                  >
                    {{ String.fromCharCode(65 + idx) }}.
                  </span>
                </label>

                <!-- Option Text Input Box with Green Border ON INPUT ONLY -->
                <input
                  type="text"
                  :value="opt"
                  @input="updateOptionVal(selectedQuestion, idx, ($event.target as HTMLInputElement).value)"
                  placeholder="Option content..."
                  class="flex-1 bg-slate-900/90 border rounded-xl px-3 py-2 text-xs text-white focus:border-purple-500 focus:outline-none transition-all shadow-sm"
                  :class="selectedQuestion.correct_answer === opt ? 'border-emerald-500/80 bg-emerald-950/20 font-bold ring-1 ring-emerald-500/40 text-emerald-200' : 'border-slate-700/80 hover:border-slate-600'"
                />

                <!-- Delete Option Button -->
                <button
                  v-if="(selectedQuestion.options?.length || 0) > 2"
                  @click="removeOption(selectedQuestion, idx)"
                  title="Remove Option"
                  class="p-1.5 text-slate-400 hover:text-red-400 transition-colors cursor-pointer shrink-0"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Compact Left-aligned Ghost Button -->
            <button
              @click="addOption(selectedQuestion)"
              class="px-4 py-1.5 bg-purple-950/40 hover:bg-purple-900/60 text-purple-300 border border-dashed border-purple-500/40 rounded-xl font-bold text-xs transition-all flex items-center gap-1.5 cursor-pointer mt-3 w-max shadow-sm"
            >
              <span>＋ Add Option</span>
            </button>
          </div>

          <!-- B) True / False Options -->
          <div v-else-if="selectedQuestion.type === 'true_false'" class="space-y-2 bg-[#121827] p-3.5 rounded-xl border border-slate-700/80">
            <label class="block font-extrabold text-slate-200 mb-1">Correct Answer Choice *</label>
            <div class="grid grid-cols-2 gap-3">
              <button
                @click="selectedQuestion.correct_answer = 'True'"
                :class="selectedQuestion.correct_answer === 'True' ? 'bg-emerald-600 text-white font-black border-emerald-400 shadow-md' : 'bg-slate-900 text-slate-300 border-slate-700 hover:bg-slate-800'"
                class="p-3 rounded-xl border text-xs flex items-center justify-center gap-2 cursor-pointer transition-all"
              >
                <span>👍 True</span>
              </button>
              <button
                @click="selectedQuestion.correct_answer = 'False'"
                :class="selectedQuestion.correct_answer === 'False' ? 'bg-red-600 text-white font-black border-red-400 shadow-md' : 'bg-slate-900 text-slate-300 border-slate-700 hover:bg-slate-800'"
                class="p-3 rounded-xl border text-xs flex items-center justify-center gap-2 cursor-pointer transition-all"
              >
                <span>👎 False</span>
              </button>
            </div>
          </div>

          <!-- C) Fill in the Blank -->
          <div v-else-if="selectedQuestion.type === 'fill_blank'" class="space-y-2 bg-[#121827] p-3.5 rounded-xl border border-slate-700/80">
            <label class="block font-extrabold text-slate-200">Correct Blank Key *</label>
            <input
              type="text"
              v-model="selectedQuestion.correct_answer"
              placeholder="e.g. malloc"
              class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2.5 text-xs text-emerald-400 font-mono focus:border-purple-500 focus:outline-none"
            />
          </div>

          <!-- D) Essay Word Limit & Rubric -->
          <div v-else-if="selectedQuestion.type === 'essay'" class="space-y-3 bg-[#121827] p-3.5 rounded-xl border border-slate-700/80">
            <div>
              <label class="block font-extrabold text-slate-200 mb-1">Word Limit</label>
              <input type="text" v-model="selectedQuestion.word_limit" placeholder="e.g. 250 - 500 words" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-xs text-white" />
            </div>
            <div>
              <label class="block font-extrabold text-slate-200 mb-1">Grading Rubric Guide</label>
              <textarea v-model="selectedQuestion.rubric" rows="2" placeholder="Detail rubric criteria..." class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-xs text-slate-200"></textarea>
            </div>
          </div>

          <!-- E) Coding Solution -->
          <div v-else-if="selectedQuestion.type === 'coding'" class="space-y-2 bg-[#121827] p-3.5 rounded-xl border border-slate-700/80">
            <label class="block font-extrabold text-slate-200">Sample Solution Code</label>
            <textarea v-model="selectedQuestion.sample_solution" rows="4" placeholder="// Write code solution here..." class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2.5 text-xs text-emerald-400 font-mono focus:border-purple-500 focus:outline-none"></textarea>
          </div>

          <!-- 4. EXPLANATION GUIDE -->
          <div>
            <label class="block font-extrabold text-slate-300 mb-1">Explanation Guide <span class="text-[11px] font-normal text-slate-400">(Shown after student submission)</span></label>
            <textarea
              v-model="selectedQuestion.explanation"
              rows="2"
              placeholder="Provide solution breakdown or explanation..."
              class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-slate-200 focus:border-purple-500 focus:outline-none"
            ></textarea>
          </div>
        </div>

        <!-- 3. FIXED FOOTER (Non-scrollable, shrink-0) -->
        <div class="flex items-center justify-end gap-3 border-t border-slate-700/60 p-4 shrink-0 bg-[#0e1322] rounded-b-2xl">
          <button
            @click="emit('closeAll')"
            class="px-4 py-2 bg-slate-800 hover:bg-slate-700 active:scale-95 text-slate-300 rounded-xl text-xs font-bold transition-all cursor-pointer"
          >
            Cancel
          </button>
          <button
            @click="emit('saveEditedQuestion', selectedQuestion)"
            class="px-5 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 active:scale-95 text-white rounded-xl text-xs font-black shadow-lg shadow-purple-600/30 transition-all cursor-pointer"
          >
            Save Changes
          </button>
        </div>
      </div>
    </div>

    <!-- ── ADD TO QUIZ ── -->
    <div v-if="showAddToQuiz && selectedQuestion" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-[#0e1322] border border-purple-500/30 rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
          <h3 class="font-bold text-sm text-white flex items-center gap-2">
            <div class="w-6 h-6 rounded-lg bg-purple-500/20 border border-purple-500/40 flex items-center justify-center">
              <svg class="w-3.5 h-3.5 text-purple-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
              </svg>
            </div>
            <span>ADD QUESTION TO QUIZ</span>
          </h3>
          <button @click="emit('closeAll')" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <div class="space-y-3 text-xs">
          <p class="text-slate-300">Add question <strong class="text-purple-400">{{ selectedQuestion.id }}</strong> to target quiz:</p>
          <select v-model="targetQuizForQuestion" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-slate-200">
            <option v-for="qz in quizzes" :key="qz.id" :value="qz.id" class="bg-[#121827] text-slate-200">{{ qz.title }} ({{ qz.type_label }})</option>
          </select>
        </div>

        <div class="flex justify-end gap-2 border-t border-slate-700/60 pt-4">
          <button @click="emit('closeAll')" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-semibold">Cancel</button>
          <button @click="emit('confirmAddToQuiz', targetQuizForQuestion)" class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl text-xs font-bold">Attach to Quiz</button>
        </div>
      </div>
    </div>

    <!-- ── QUIZ PREVIEW ── -->
    <div v-if="showQuizPreview && selectedQuiz" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-[#0e1322] border border-purple-500/30 rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
          <div>
            <span class="text-xs font-mono text-purple-400 font-bold">QUIZ PREVIEW</span>
            <h3 class="font-bold text-sm text-white">{{ selectedQuiz.title }}</h3>
          </div>
          <button @click="emit('closeAll')" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <div class="space-y-3 text-xs">
          <div class="grid grid-cols-3 gap-3">
            <div class="bg-[#121827] p-3 rounded-xl">
              <span class="text-slate-400 block text-[10px]">Type</span>
              <span class="font-bold text-purple-300">{{ selectedQuiz.type_label }}</span>
            </div>
            <div class="bg-[#121827] p-3 rounded-xl">
              <span class="text-slate-400 block text-[10px]">Duration</span>
              <span class="font-bold text-amber-300">{{ selectedQuiz.time_limit_minutes }} Mins</span>
            </div>
            <div class="bg-[#121827] p-3 rounded-xl">
              <span class="text-slate-400 block text-[10px]">Passing Score</span>
              <span class="font-bold text-emerald-400">{{ selectedQuiz.passing_score }}%</span>
            </div>
          </div>

          <div class="bg-[#121827] p-3 rounded-xl space-y-1">
            <p class="text-slate-300"><strong>Course:</strong> {{ selectedQuiz.course }}</p>
            <p class="text-slate-300"><strong>Module:</strong> {{ selectedQuiz.module }}</p>
            <p class="text-slate-300"><strong>Questions Count:</strong> {{ selectedQuiz.questions_count }}</p>
            <p class="text-slate-300"><strong>Total Marks:</strong> {{ selectedQuiz.total_marks }}</p>
          </div>
        </div>

        <div class="flex justify-end border-t border-slate-700/60 pt-4">
          <button @click="emit('closeAll')" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-semibold">Close</button>
        </div>
      </div>
    </div>

    <!-- ── QUIZ EDIT ── -->
    <div v-if="showQuizEdit && selectedQuiz" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-[#0e1322] border border-purple-500/30 rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
          <h3 class="font-bold text-sm text-white flex items-center gap-2">
            <div class="w-6 h-6 rounded-lg bg-purple-500/20 border border-purple-500/40 flex items-center justify-center">
              <svg class="w-3.5 h-3.5 text-purple-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
              </svg>
            </div>
            <span>EDIT QUIZ DETAILS</span>
          </h3>
          <button @click="emit('closeAll')" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="block font-semibold text-slate-300 mb-1">Quiz Title:</label>
            <input v-model="selectedQuiz.title" type="text" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-white" />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold text-slate-300 mb-1">Passing Score (%):</label>
              <input v-model="selectedQuiz.passing_score" type="number" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2 text-xs text-white" />
            </div>
            <div>
              <label class="block font-semibold text-slate-300 mb-1">Time Limit (mins):</label>
              <input v-model="selectedQuiz.time_limit_minutes" type="number" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2 text-xs text-white" />
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-2 border-t border-slate-700/60 pt-4">
          <button @click="emit('closeAll')" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-semibold">Cancel</button>
          <button @click="emit('saveEditedQuiz', selectedQuiz)" class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl text-xs font-bold">Save Quiz</button>
        </div>
      </div>
    </div>

    <!-- ── PRACTICE SIMULATION ── -->
    <div v-if="showPracticeSim" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-[#0e1322] border border-purple-500/30 rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
          <h3 class="font-bold text-sm text-amber-400 flex items-center gap-2">
            <span>✍️</span> Practice Quiz Drill Mode — Instant Feedback
          </h3>
          <button @click="emit('closeAll')" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <div class="space-y-3 text-xs">
          <div class="bg-[#121827] p-3 rounded-xl border border-slate-700/80">
            <p class="text-sm font-bold text-white mb-3">{{ practiceQuestionsList[practiceIndex].q }}</p>

            <div class="space-y-2">
              <label
                v-for="(opt, oIdx) in practiceQuestionsList[practiceIndex].opts"
                :key="oIdx"
                @click="selectedPracticeAns = opt"
                class="flex items-center justify-between p-3 rounded-xl bg-slate-800/80 border cursor-pointer transition-all"
                :class="selectedPracticeAns === opt ? 'border-amber-500 bg-amber-950/30 text-amber-300 font-bold' : 'border-slate-700 hover:border-slate-600 text-slate-200'"
              >
                <span>{{ opt }}</span>
                <input type="radio" name="practice_opt" :checked="selectedPracticeAns === opt" class="text-amber-500" />
              </label>
            </div>
          </div>

          <div v-if="practiceChecked" class="p-3 rounded-xl border space-y-1" :class="selectedPracticeAns === practiceQuestionsList[practiceIndex].correct ? 'bg-emerald-950/40 border-emerald-500 text-emerald-300' : 'bg-red-950/40 border-red-500 text-red-300'">
            <h4 class="font-bold flex items-center gap-1.5">
              <span>{{ selectedPracticeAns === practiceQuestionsList[practiceIndex].correct ? '✅ Excellent!' : '❌ Incorrect' }}</span>
            </h4>
            <p class="text-slate-200 text-xs">{{ practiceQuestionsList[practiceIndex].exp }}</p>
          </div>
        </div>

        <div class="flex justify-between items-center border-t border-slate-700/60 pt-4">
          <button @click="emit('closeAll')" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-semibold">Close</button>
          <div class="flex gap-2">
            <button
              v-if="!practiceChecked"
              @click="practiceChecked = true"
              :disabled="!selectedPracticeAns"
              class="px-4 py-2 bg-amber-600 hover:bg-amber-500 disabled:opacity-50 text-white rounded-xl text-xs font-bold"
            >
              Check Answer
            </button>
            <button
              v-else
              @click="practiceIndex = (practiceIndex + 1) % practiceQuestionsList.length; practiceChecked = false; selectedPracticeAns = null"
              class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl text-xs font-bold"
            >
              Next Practice Question →
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ── AI GENERATOR MODAL ── -->
    <div v-if="showAiGenerator" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-[#0e1322] border border-purple-500/30 rounded-2xl max-w-xl w-full p-6 space-y-5 shadow-2xl animate-fadeIn">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
          <div class="flex items-center gap-2.5">
            <div class="p-2 rounded-xl bg-purple-600/20 border border-purple-500/30 text-purple-300">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </div>
            <div>
              <h3 class="font-extrabold text-base text-white tracking-tight flex items-center gap-2">
                <span>🤖 AI Question Generator</span>
              </h3>
              <p class="text-[11px] text-slate-400 font-medium">Generate custom quiz & assessment questions instantly with AI</p>
            </div>
          </div>
          <button @click="emit('closeAll')" class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors cursor-pointer">✕</button>
        </div>

        <!-- Form Body -->
        <div class="space-y-4 text-xs font-sans">
          <!-- Row 1: Course / Subject | Question Type | Difficulty -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
              <label class="block font-bold text-slate-300 mb-1">Course / Subject *</label>
              <select v-model="aiCourse" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-slate-100 font-semibold focus:ring-2 focus:ring-purple-500 cursor-pointer">
                <option value="C Programming Basics">C Programming Basics</option>
                <option value="Web Development with Vue">Web Development with Vue</option>
                <option value="Data Structures & Algorithms">Data Structures & Algorithms</option>
                <option value="Database Systems">Database Systems</option>
                <option value="Tourism Management">Tourism Management</option>
              </select>
            </div>

            <div>
              <label class="block font-bold text-slate-300 mb-1">Question Type *</label>
              <select v-model="aiType" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-slate-100 font-semibold focus:ring-2 focus:ring-purple-500 cursor-pointer">
                <option value="mcq">Multiple Choice (MCQ)</option>
                <option value="true_false">True / False</option>
                <option value="essay">Essay / Open Ended</option>
                <option value="matching">Matching Pairs</option>
                <option value="fill_blank">Fill in the Blank</option>
                <option value="coding">Code Implementation</option>
              </select>
            </div>

            <div>
              <label class="block font-bold text-slate-300 mb-1">Difficulty *</label>
              <div class="grid grid-cols-3 gap-1 bg-[#121827] p-1 rounded-xl border border-slate-700/80">
                <!-- Easy -->
                <button
                  type="button"
                  @click="aiDifficulty = 'easy'"
                  :class="aiDifficulty === 'easy' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/50 shadow-sm font-bold ring-1 ring-emerald-500/30' : 'text-slate-400 hover:text-slate-200 border-transparent'"
                  class="py-1.5 px-2 rounded-lg border text-[11px] flex items-center justify-center gap-1 transition-all cursor-pointer"
                >
                  <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21V10m0 0C10.5 7.5 7.5 6 4 6c0 5 3.5 8 8 4zm0 0c1.5-2.5 4.5-4 8-4 0 5-3.5 8-8 4z" />
                  </svg>
                  <span>Easy</span>
                </button>

                <!-- Medium -->
                <button
                  type="button"
                  @click="aiDifficulty = 'medium'"
                  :class="aiDifficulty === 'medium' ? 'bg-amber-500/20 text-amber-300 border-amber-500/50 shadow-sm font-bold ring-1 ring-amber-500/30' : 'text-slate-400 hover:text-slate-200 border-transparent'"
                  class="py-1.5 px-2 rounded-lg border text-[11px] flex items-center justify-center gap-1 transition-all cursor-pointer"
                >
                  <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                  </svg>
                  <span>Medium</span>
                </button>

                <!-- Hard -->
                <button
                  type="button"
                  @click="aiDifficulty = 'hard'"
                  :class="aiDifficulty === 'hard' ? 'bg-rose-500/20 text-rose-300 border-rose-500/50 shadow-sm font-bold ring-1 ring-rose-500/30' : 'text-slate-400 hover:text-slate-200 border-transparent'"
                  class="py-1.5 px-2 rounded-lg border text-[11px] flex items-center justify-center gap-1 transition-all cursor-pointer"
                >
                  <svg class="w-3.5 h-3.5 text-rose-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 3.75 3.75 0 00-3.99 3.018A3.75 3.75 0 0012 18z" />
                  </svg>
                  <span>Hard</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Row 2: Number of Questions | Target Module / Topic -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-slate-300 mb-1">Number of Questions *</label>
              <select v-model="aiNumQuestions" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-slate-100 font-semibold focus:ring-2 focus:ring-purple-500 cursor-pointer">
                <option value="1">1 Question</option>
                <option value="3">3 Questions</option>
                <option value="5">5 Questions</option>
                <option value="10">10 Questions</option>
              </select>
            </div>

            <div>
              <label class="block font-bold text-slate-300 mb-1">Target Module / Topic *</label>
              <input
                v-model="aiTopic"
                type="text"
                placeholder="e.g. Pointers & Memory Allocation"
                class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-white focus:ring-2 focus:ring-purple-500 focus:outline-none"
              />
            </div>
          </div>

          <!-- Additional Prompt / Context (Optional Textarea) -->
          <div>
            <div class="flex items-center justify-between mb-1">
              <label class="font-bold text-slate-300">Additional Prompt / Context <span class="text-slate-400 font-normal">(Optional)</span></label>
              <button v-if="aiCustomContext" @click="aiCustomContext = ''" class="text-[10px] text-purple-400 hover:underline cursor-pointer">Clear Text</button>
            </div>
            <textarea
              v-model="aiCustomContext"
              rows="3"
              placeholder="e.g. Focus on pointer arithmetic, memory leaks, and dynamic allocation with malloc()..."
              class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-3 text-xs text-white focus:ring-2 focus:ring-purple-500 focus:outline-none transition-all placeholder:text-slate-500"
            ></textarea>
          </div>

          <!-- Quick Presets / Template Chips -->
          <div class="space-y-1.5">
            <label class="block font-bold text-slate-300 text-[11px] flex items-center gap-1">
              <span>💡 Quick Presets:</span>
              <span class="text-slate-400 font-normal">(Click to append to prompt)</span>
            </label>
            <div class="flex flex-wrap gap-1.5">
              <button
                v-for="p in presets"
                :key="p"
                @click="applyPreset(p)"
                class="px-2.5 py-1 rounded-lg bg-slate-800/90 hover:bg-purple-950/60 hover:text-purple-300 border border-slate-700/80 text-[11px] font-semibold text-slate-300 transition-all cursor-pointer shadow-sm active:scale-95 flex items-center gap-1"
              >
                <span>{{ p }}</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Modal Footer Actions -->
        <div class="flex items-center justify-end gap-2.5 border-t border-slate-700/60 pt-4">
          <button
            @click="emit('closeAll')"
            class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-bold transition-all cursor-pointer"
          >
            Cancel
          </button>

          <button
            @click="handleAiGen"
            :disabled="aiIsGenerating"
            class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:brightness-110 disabled:opacity-50 text-white rounded-xl text-xs font-extrabold flex items-center gap-2 shadow-lg shadow-purple-600/30 transition-all cursor-pointer active:scale-95 ring-1 ring-purple-400/40"
          >
            <svg v-if="aiIsGenerating" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-else>✨</span>
            <span>{{ aiIsGenerating ? 'Generating Questions...' : 'Generate with AI' }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- ── ADD CATEGORY MODAL ── -->
    <div v-if="showAddCategory" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-[#0e1322] border border-purple-500/30 rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
          <h3 class="font-bold text-sm text-white">📂 ADD QUESTION CATEGORY</h3>
          <button @click="emit('closeAll')" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="block font-semibold text-slate-300 mb-1">Category Name:</label>
            <input v-model="newCategoryName" type="text" placeholder="e.g. Advanced Data Structures" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-white" />
          </div>
        </div>

        <div class="flex justify-end gap-2 border-t border-slate-700/60 pt-4">
          <button @click="emit('closeAll')" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-semibold">Cancel</button>
          <button @click="handleAddCategory" class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl text-xs font-bold">Add Category</button>
        </div>
      </div>
    </div>
  </div>
</template>
